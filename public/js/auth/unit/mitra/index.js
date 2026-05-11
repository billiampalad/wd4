(function () {
    let isRefreshing = false;

    async function refreshMitraIndex() {
        const currentMain = document.getElementById('mainContent');

        if (!currentMain || isRefreshing) {
            return;
        }

        isRefreshing = true;
        currentMain.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Gagal memuat ulang data mitra.');
            }

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nextMain = doc.getElementById('mainContent');

            if (!nextMain) {
                throw new Error('Konten mitra tidak ditemukan.');
            }

            currentMain.replaceWith(nextMain);

            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                window.Alpine.initTree(nextMain);
            }

            if (typeof window.initDashboard === 'function') {
                window.initDashboard();
            }

            document.dispatchEvent(new CustomEvent('mitra-index:refreshed'));
        } catch (error) {
            console.error(error);
        } finally {
            const refreshedMain = document.getElementById('mainContent');
            if (refreshedMain) {
                refreshedMain.removeAttribute('aria-busy');
            }
            isRefreshing = false;
        }
    }

    async function deleteMitra(form) {
        const message = form.dataset.confirmMessage || 'Apakah Anda yakin ingin menghapus mitra ini?';

        const result = await Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Mitra gagal dihapus.');
            }

            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Mitra berhasil dihapus.',
                showConfirmButton: false,
                timer: 1500
            });

            refreshMitraIndex();
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Terjadi kesalahan saat menghapus data.'
            });
        }
    }

    function initMitraIndex() {
        if (document.documentElement.dataset.mitraIndexInitialized === '1') {
            return;
        }

        document.documentElement.dataset.mitraIndexInitialized = '1';

        document.addEventListener('submit', function (event) {
            const form = event.target.closest('[data-mitra-delete-form]');

            if (!form) {
                return;
            }

            event.preventDefault();
            deleteMitra(form);
        });
    }

    window.refreshMitraIndex = refreshMitraIndex;

    document.addEventListener('turbo:before-cache', function () {
        ['mitraModal', 'mitraEditModal'].forEach(function (id) {
            const modal = document.getElementById(id);
            if (!modal) {
                return;
            }

            modal.style.display = 'none';
            modal.setAttribute('hidden', '');
        });

        document.body.style.overflow = '';
    });

    document.addEventListener('turbo:load', initMitraIndex);

    if (!window.Turbo) {
        document.addEventListener('DOMContentLoaded', initMitraIndex);
    }
})();

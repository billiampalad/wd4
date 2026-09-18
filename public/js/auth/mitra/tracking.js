/**
 * Mitra Portal - Tracking Lulusan & Penyerapan Alumni POLIMDO (IKU 1)
 * File: public/js/auth/mitra/tracking.js
 * Logic handler for filtering, pagination, search, modal operations, and CRUD submission.
 */

function mitraTrackingApp() {
    return {
        prodiFilter: 'all',
        statusFilter: 'all',
        tahunFilter: 'all',

        createModalOpen: false,
        createMode: 'select', // 'select' | 'new'
        selectedAlumniId: '',
        selectedAlumniInfo: null,

        editModalOpen: false,
        editItem: {},

        detailModalOpen: false,
        detailItem: {},

        isSubmitting: false,

        resetFilters() {
            this.prodiFilter = 'all';
            this.statusFilter = 'all';
            this.tahunFilter = 'all';
        },

        matchesRow(row) {
            if (!row || !row.dataset) return true;
            const matchProdi = this.prodiFilter === 'all' || (row.dataset.prodi && row.dataset.prodi === this.prodiFilter.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (row.dataset.status && row.dataset.status.toLowerCase() === this.statusFilter.toLowerCase());
            const matchTahun = this.tahunFilter === 'all' || (row.dataset.tahun && String(row.dataset.tahun) === String(this.tahunFilter));

            return matchProdi && matchStatus && matchTahun;
        },

        openCreateModal() {
            this.createMode = 'select';
            this.selectedAlumniId = '';
            this.selectedAlumniInfo = null;
            this.createModalOpen = true;
        },

        onAlumniSelectChange(event, masterList) {
            const id = event.target.value;
            this.selectedAlumniId = id;
            if (!id) {
                this.selectedAlumniInfo = null;
                return;
            }
            const found = masterList.find(a => String(a.id) === String(id));
            this.selectedAlumniInfo = found || null;
        },

        openEditModal(item) {
            this.editItem = item;
            this.editModalOpen = true;
        },

        openDetailModal(item) {
            this.detailItem = item;
            this.detailModalOpen = true;
        },

        submitCreate(event) {
            const form = event.target;
            if (window.CustomAlert) {
                CustomAlert.confirm({
                    title: 'Simpan Data Alumni?',
                    message: 'Data alumni akan ditambahkan dan dihubungkan ke instansi Anda.',
                    type: 'primary',
                    confirmText: 'Ya, Simpan',
                    cancelText: 'Batal',
                    confirmColor: 'primary',
                    onConfirm: () => {
                        this.isSubmitting = true;
                        form.submit();
                    }
                });
            } else {
                if (confirm('Simpan data alumni ini?')) {
                    this.isSubmitting = true;
                    form.submit();
                }
            }
        },

        submitEdit(event) {
            const form = event.target;
            if (window.CustomAlert) {
                CustomAlert.confirm({
                    title: 'Perbarui Informasi Karir?',
                    message: `Anda akan memperbarui data karir untuk alumni <span class="custom-alert-highlight">${this.editItem.nama}</span>.`,
                    type: 'warning',
                    confirmText: 'Ya, Perbarui',
                    cancelText: 'Batal',
                    confirmColor: 'warning',
                    onConfirm: () => {
                        this.isSubmitting = true;
                        form.submit();
                    }
                });
            } else {
                if (confirm(`Perbarui informasi karir alumni ${this.editItem.nama}?`)) {
                    this.isSubmitting = true;
                    form.submit();
                }
            }
        },

        confirmDelete(deleteUrl, nama) {
            if (window.CustomAlert) {
                CustomAlert.confirm({
                    title: 'Hapus Data Alumni?',
                    message: `Apakah Anda yakin ingin menghapus data alumni <span class="custom-alert-highlight">${nama}</span> dari instansi Anda?`,
                    type: 'danger',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    confirmColor: 'danger',
                    onConfirm: () => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);
                        }
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            } else {
                if (confirm(`Hapus data alumni ${nama}?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);
                    }
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    };
}

// Expose globally on window
window.mitraTrackingApp = mitraTrackingApp;

// Register on Alpine.data
function registerMitraTrackingAlpine() {
    if (typeof Alpine !== 'undefined') {
        Alpine.data('mitraTrackingApp', mitraTrackingApp);
    }
}

if (typeof Alpine !== 'undefined') {
    registerMitraTrackingAlpine();
} else {
    document.addEventListener('alpine:init', registerMitraTrackingAlpine);
}

document.addEventListener('turbo:load', registerMitraTrackingAlpine);

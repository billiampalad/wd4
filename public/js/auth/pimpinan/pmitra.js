(function () {
    const boot = () => {
        const root = document.querySelector('.submission-dashboard');
        if (!root || root.dataset.pmitraReady === 'true') return;
        root.dataset.pmitraReady = 'true';

        const cards = Array.from(document.querySelectorAll('[data-submission-card]'));
        const searchInput = document.getElementById('submissionSearch');
        const categoryFilter = document.getElementById('submissionCategoryFilter');
        const emptyState = document.querySelector('.submission-filter-empty');

        const applyFilter = () => {
            const query = (searchInput?.value || '').trim().toLowerCase();
            const category = (categoryFilter?.value || 'all').toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const haystack = card.dataset.search || '';
                const cardCategory = card.dataset.category || '';
                const matchesQuery = !query || haystack.includes(query);
                const matchesCategory = category === 'all' || cardCategory === category;
                const isVisible = matchesQuery && matchesCategory;

                card.hidden = !isVisible;
                if (isVisible) visibleCount += 1;
            });

            if (emptyState) {
                emptyState.hidden = visibleCount > 0 || cards.length === 0;
            }
        };

        searchInput?.addEventListener('input', applyFilter);
        categoryFilter?.addEventListener('change', applyFilter);
        applyFilter();

        document.querySelectorAll('.submission-form').forEach((form) => {
            const textarea = form.querySelector('.submission-textarea');
            const counter = form.querySelector('[data-note-counter]');

            const updateCounter = () => {
                if (!counter || !textarea) return;
                const length = textarea.value.trim().length;
                counter.textContent = `${length} karakter`;
            };

            textarea?.addEventListener('input', () => {
                textarea.classList.remove('is-required');
                updateCounter();
            });
            updateCounter();

            form.querySelectorAll('button[type="submit"][name="keputusan"]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    const decision = button.value;
                    const note = textarea?.value.trim() || '';

                    if (decision === 'ditolak' && note.length === 0) {
                        event.preventDefault();
                        textarea?.classList.add('is-required');
                        textarea?.focus();

                        if (window.Swal) {
                            window.Swal.fire({
                                icon: 'warning',
                                title: 'Catatan wajib diisi',
                                text: 'Tambahkan alasan penolakan agar mitra memahami hasil validasi.',
                                confirmButtonText: 'Baik'
                            });
                        }

                        return;
                    }

                    if (!window.Swal) return;

                    event.preventDefault();
                    const isApproved = decision === 'disetujui';

                    window.Swal.fire({
                        icon: isApproved ? 'question' : 'warning',
                        title: isApproved ? 'Setujui pengajuan?' : 'Tolak pengajuan?',
                        text: isApproved
                            ? 'Data mitra akan disimpan ke master mitra.'
                            : 'Pastikan catatan penolakan sudah jelas dan dapat ditindaklanjuti.',
                        showCancelButton: true,
                        confirmButtonText: isApproved ? 'Ya, setujui' : 'Ya, tolak',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: isApproved ? '#10b981' : '#ef4444'
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        const hiddenDecision = document.createElement('input');
                        hiddenDecision.type = 'hidden';
                        hiddenDecision.name = button.name;
                        hiddenDecision.value = button.value;
                        form.appendChild(hiddenDecision);
                        form.submit();
                    });
                });
            });
        });
    };

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('turbo:load', boot);
})();

    document.addEventListener('alpine:init', () => {
        Alpine.data('mitraDashboard', () => ({
            activeTab: 'all',
            jenisFilter: 'all',
            periodeFilter: 'all',
            showReviewModal: false,
            reviewDocId: null,
            reviewDocNumber: '',
            reviewDocTitle: '',
            reviewPdfUrl: '',
            
            openReview(id, docNumber, title, pdfUrl) {
                this.reviewDocId = id;
                this.reviewDocNumber = docNumber;
                this.reviewDocTitle = title;
                this.reviewPdfUrl = pdfUrl;
                this.showReviewModal = true;
            }
        }));
    });

    document.addEventListener('turbo:load', function() {
        const toggleButtons = document.querySelectorAll('.dk-expand-toggle');
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const tr = this.closest('.dk-row');
                const detailRowId = this.getAttribute('aria-controls');
                const detailRow = document.getElementById(detailRowId);
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                if (isExpanded) {
                    this.setAttribute('aria-expanded', 'false');
                    detailRow.classList.remove('open');
                    setTimeout(() => {
                        detailRow.style.display = 'none';
                    }, 300);
                } else {
                    document.querySelectorAll('.dk-row-detail.open').forEach(row => {
                        row.classList.remove('open');
                        row.previousElementSibling.querySelector('.dk-expand-toggle').setAttribute('aria-expanded', 'false');
                        setTimeout(() => row.style.display = 'none', 300);
                    });

                    this.setAttribute('aria-expanded', 'true');
                    detailRow.style.display = 'table-row';
                    setTimeout(() => detailRow.classList.add('open'), 10);
                }
            });
        });
    });

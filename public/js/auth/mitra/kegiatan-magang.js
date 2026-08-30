/**
 * Mitra Portal - Kegiatan & Penilaian Mahasiswa Magang (UC21 & UC22)
 * Script handler for filtering, pagination, grading modal calculation, and submission.
 */

function mitraPenilaianApp() {
    return {
        searchQuery: '',
        prodiFilter: 'all',
        statusFilter: 'all',
        tahunFilter: 'all',

        currentPage: 1,
        perPage: 10,
        perPageOpen: false,
        perPageOptions: [5, 10, 25, 50],

        gradingModalOpen: false,
        detailModalOpen: false,
        isSubmitting: false,

        activeItem: {},
        detailItem: {},

        // Grading Sub-Aspects (0-100) based on Flowchart 6.3
        aspekKedisiplinan: 85,
        aspekTeknis: 85,
        aspekKerjasama: 85,
        aspekInisiatif: 85,
        aspekKomunikasi: 85,
        calculatedScore: 85.0,
        calculatedGrade: 'A',
        catatanMitra: '',

        setPerPage(value) {
            this.perPage = Number(value);
            this.currentPage = 1;
            this.perPageOpen = false;
        },

        init() {
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('prodiFilter', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('tahunFilter', () => this.currentPage = 1);
            this.$watch('perPage', () => this.currentPage = 1);
        },

        resetFilters() {
            this.searchQuery = '';
            this.prodiFilter = 'all';
            this.statusFilter = 'all';
            this.tahunFilter = 'all';
            this.currentPage = 1;
        },

        get rows() {
            const container = this.$refs.rows || (this.$el ? this.$el.querySelector('tbody[x-ref="rows"]') : null) || document.querySelector('tbody[x-ref="rows"]') || document.querySelector('#mainContent table.dk-table tbody');
            return container ? Array.from(container.querySelectorAll('tr[data-row]')) : [];
        },

        get filteredRows() {
            const allRows = this.rows;
            if (!allRows.length) return [];
            return allRows.filter(row => this.matchesRow(row));
        },

        get totalFiltered() {
            return this.filteredRows.length;
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.totalFiltered / this.perPage));
        },

        get startRange() {
            return this.totalFiltered === 0 ? 0 : ((this.currentPage - 1) * this.perPage) + 1;
        },

        get endRange() {
            return Math.min(this.currentPage * this.perPage, this.totalFiltered);
        },

        matchesRow(row) {
            if (!row || !row.dataset) return true;
            const q = (this.searchQuery || '').toLowerCase().trim();
            const matchSearch = q === '' ||
                (row.dataset.nim && row.dataset.nim.includes(q)) ||
                (row.dataset.nama && row.dataset.nama.includes(q)) ||
                (row.dataset.prodi && row.dataset.prodi.includes(q)) ||
                (row.dataset.kegiatan && row.dataset.kegiatan.includes(q));

            const matchProdi = this.prodiFilter === 'all' || (row.dataset.prodi && row.dataset.prodi.toLowerCase() === this.prodiFilter.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (row.dataset.status && row.dataset.status === this.statusFilter);
            const matchTahun = this.tahunFilter === 'all' || (row.dataset.tahun && String(row.dataset.tahun) === String(this.tahunFilter));

            return matchSearch && matchProdi && matchStatus && matchTahun;
        },

        isRowVisible(el) {
            const tr = el.tagName === 'TR' ? el : el.closest('tr');
            if (!tr) return true;
            
            // If matches row check fails, immediately hide
            if (!this.matchesRow(tr)) return false;

            const fRows = this.filteredRows;
            if (!fRows.length) return true; // Fallback during initial render

            const index = fRows.indexOf(tr);
            if (index === -1) return false;
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return index >= start && index < end;
        },

        rowNumber(el) {
            const tr = el.tagName === 'TR' ? el : el.closest('tr');
            if (!tr) return '01';
            const fRows = this.filteredRows;
            if (!fRows.length) return '01';
            const index = fRows.indexOf(tr);
            return index === -1 ? '01' : String(index + 1).padStart(2, '0');
        },

        pageNumbers() {
            const total = this.totalPages;
            if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);
            const pages = new Set([1, total, this.currentPage - 1, this.currentPage, this.currentPage + 1]);
            return Array.from(pages).filter(p => p >= 1 && p <= total).sort((a, b) => a - b);
        },

        goToPage(p) {
            this.currentPage = Math.min(Math.max(p, 1), this.totalPages);
        },

        openGradingModal(item) {
            this.activeItem = item;
            const baseScore = item.nilai || 85;
            this.aspekKedisiplinan = baseScore;
            this.aspekTeknis = baseScore;
            this.aspekKerjasama = baseScore;
            this.aspekInisiatif = baseScore;
            this.aspekKomunikasi = baseScore;
            this.catatanMitra = item.catatan || '';
            this.calculateTotalScore();
            this.gradingModalOpen = true;
        },

        openDetailModal(item) {
            this.detailItem = item;
            this.detailModalOpen = true;
        },

        calculateTotalScore() {
            const total = (this.aspekKedisiplinan * 0.20) +
                          (this.aspekTeknis * 0.30) +
                          (this.aspekKerjasama * 0.20) +
                          (this.aspekInisiatif * 0.15) +
                          (this.aspekKomunikasi * 0.15);

            this.calculatedScore = Math.min(100, Math.max(0, total));

            if (this.calculatedScore >= 85) this.calculatedGrade = 'A';
            else if (this.calculatedScore >= 75) this.calculatedGrade = 'B+';
            else if (this.calculatedScore >= 65) this.calculatedGrade = 'B';
            else if (this.calculatedScore >= 55) this.calculatedGrade = 'C';
            else this.calculatedGrade = 'D';
        },

        submitGrading(event) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Simpan Penilaian?',
                    text: `Anda akan memberikan nilai ${this.calculatedScore.toFixed(1)} (${this.calculatedGrade}) untuk mahasiswa ${this.activeItem.nama}.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.isSubmitting = true;
                        event.target.submit();
                    }
                });
            } else {
                if (confirm(`Simpan penilaian untuk mahasiswa ${this.activeItem.nama}?`)) {
                    this.isSubmitting = true;
                    event.target.submit();
                }
            }
        }
    };
}

// Expose globally on window
window.mitraPenilaianApp = mitraPenilaianApp;

// Register on Alpine.data
function registerMitraPenilaianAlpine() {
    if (typeof Alpine !== 'undefined') {
        Alpine.data('mitraPenilaianApp', mitraPenilaianApp);
    }
}

if (typeof Alpine !== 'undefined') {
    registerMitraPenilaianAlpine();
} else {
    document.addEventListener('alpine:init', registerMitraPenilaianAlpine);
}

document.addEventListener('turbo:load', registerMitraPenilaianAlpine);

/**
 * Mitra Portal - Umpan Balik & Evaluasi Kemitraan (UC26 / CSAT Survey)
 * File: public/js/auth/mitra/umpan-balik.js
 * Logic handler for interactive star rating, CSAT evaluation, filtering, pagination, and solid modals.
 */

function mitraUmpanBalikApp() {
    return {
        searchQuery: '',
        jenisFilter: 'all',
        statusFilter: 'all', // 'all' | 'sudah' | 'belum'
        tahunFilter: 'all',

        currentPage: 1,
        perPage: 10,
        perPageOpen: false,
        perPageOptions: [5, 10, 25, 50],

        feedbackModalOpen: false,
        isEditMode: false,

        formData: {
            id: null,
            cooperation_id: '',
            cooperation_title: '',
            cooperation_number: '',
            cooperation_jenis: '',
            kepuasan: 5,
            sesuai_rencana: 5,
            kualitas: 5,
            keterlibatan: 5,
            efisiensi: 5,
            ringkasan: '',
            kendala: '',
            rekomendasi: '',
            kesimpulan: 'Sangat Baik',
            tindak_lanjut: 'Sangat Bersedia Melanjutkan Kerjasama',
        },

        detailModalOpen: false,
        detailItem: {},

        isSubmitting: false,

        setPerPage(value) {
            this.perPage = Number(value);
            this.currentPage = 1;
            this.perPageOpen = false;
        },

        init() {
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('jenisFilter', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('tahunFilter', () => this.currentPage = 1);
            this.$watch('perPage', () => this.currentPage = 1);
        },

        resetFilters() {
            this.searchQuery = '';
            this.jenisFilter = 'all';
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
                (row.dataset.judul && row.dataset.judul.includes(q)) ||
                (row.dataset.nomor && row.dataset.nomor.includes(q)) ||
                (row.dataset.jenis && row.dataset.jenis.includes(q));

            const matchJenis = this.jenisFilter === 'all' || (row.dataset.jenis && row.dataset.jenis.toLowerCase() === this.jenisFilter.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (row.dataset.status && row.dataset.status.toLowerCase() === this.statusFilter.toLowerCase());
            const matchTahun = this.tahunFilter === 'all' || (row.dataset.tahun && String(row.dataset.tahun) === String(this.tahunFilter));

            return matchSearch && matchJenis && matchStatus && matchTahun;
        },

        isRowVisible(el) {
            const tr = el.tagName === 'TR' ? el : el.closest('tr');
            if (!tr) return true;

            if (!this.matchesRow(tr)) return false;

            const fRows = this.filteredRows;
            if (!fRows.length) return true;

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

        // ─── Modal Actions ───

        openCreateFeedbackModal(coop = null) {
            this.isEditMode = false;
            this.formData = {
                id: null,
                cooperation_id: coop ? coop.id : '',
                cooperation_title: coop ? coop.judul : '',
                cooperation_number: coop ? coop.nomor : '',
                cooperation_jenis: coop ? coop.jenis : '',
                kepuasan: 5,
                sesuai_rencana: 5,
                kualitas: 5,
                keterlibatan: 5,
                efisiensi: 5,
                ringkasan: '',
                kendala: '',
                rekomendasi: '',
                kesimpulan: 'Sangat Baik',
                tindak_lanjut: 'Sangat Bersedia Melanjutkan Kerjasama',
            };
            this.feedbackModalOpen = true;
        },

        openEditFeedbackModal(item) {
            this.isEditMode = true;
            this.formData = {
                id: item.evaluasi_id,
                cooperation_id: item.cooperation_id,
                cooperation_title: item.judul,
                cooperation_number: item.nomor,
                cooperation_jenis: item.jenis,
                kepuasan: Number(item.kepuasan) || 5,
                sesuai_rencana: Number(item.sesuai_rencana) || 5,
                kualitas: Number(item.kualitas) || 5,
                keterlibatan: Number(item.keterlibatan) || 5,
                efisiensi: Number(item.efisiensi) || 5,
                ringkasan: item.ringkasan || '',
                kendala: item.kendala || '',
                rekomendasi: item.rekomendasi || '',
                kesimpulan: item.kesimpulan || 'Baik',
                tindak_lanjut: item.tindak_lanjut || 'Bersedia Melanjutkan Kerjasama',
            };
            this.feedbackModalOpen = true;
        },

        openDetailModal(item) {
            this.detailItem = item;
            this.detailModalOpen = true;
        },

        onCooperationSelectChange(event, coopsList) {
            const id = event.target.value;
            this.formData.cooperation_id = id;
            if (!id) {
                this.formData.cooperation_title = '';
                this.formData.cooperation_number = '';
                this.formData.cooperation_jenis = '';
                return;
            }
            const found = coopsList.find(c => String(c.id) === String(id));
            if (found) {
                this.formData.cooperation_title = found.judul || '';
                this.formData.cooperation_number = found.doc_number || '-';
                this.formData.cooperation_jenis = found.jenis || 'Kerjasama';
            }
        },

        // ─── Rating Helpers ───

        get calculatedAverageScore() {
            const arr = [
                this.formData.sesuai_rencana,
                this.formData.kualitas,
                this.formData.keterlibatan,
                this.formData.efisiensi,
                this.formData.kepuasan
            ];
            const sum = arr.reduce((acc, v) => acc + Number(v), 0);
            return (sum / arr.length).toFixed(1);
        },

        get ratingLabel() {
            const score = Number(this.calculatedAverageScore);
            if (score >= 4.5) return 'Sangat Puas (Sangat Baik)';
            if (score >= 3.5) return 'Puas (Baik)';
            if (score >= 2.5) return 'Cukup Puas (Cukup)';
            return 'Kurang Puas (Perlu Perbaikan)';
        },

        submitFeedback(event) {
            const title = this.isEditMode ? 'Perbarui Umpan Balik?' : 'Kirim Umpan Balik?';
            const text = this.isEditMode
                ? 'Ulasan dan penilaian kepuasan kerja sama akan diperbarui.'
                : 'Ulasan dan penilaian kepuasan Anda akan dikirimkan ke pihak Politeknik Negeri Manado.';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
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
                if (confirm(`${title}\n${text}`)) {
                    this.isSubmitting = true;
                    event.target.submit();
                }
            }
        }
    };
}

// Expose globally on window
window.mitraUmpanBalikApp = mitraUmpanBalikApp;

// Register on Alpine.data
function registerMitraUmpanBalikAlpine() {
    if (typeof Alpine !== 'undefined') {
        Alpine.data('mitraUmpanBalikApp', mitraUmpanBalikApp);
    }
}

if (typeof Alpine !== 'undefined') {
    registerMitraUmpanBalikAlpine();
} else {
    document.addEventListener('alpine:init', registerMitraUmpanBalikAlpine);
}

document.addEventListener('turbo:load', registerMitraUmpanBalikAlpine);

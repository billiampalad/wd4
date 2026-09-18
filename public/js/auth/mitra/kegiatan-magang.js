/**
 * Mitra Portal - Kegiatan & Penilaian Mahasiswa Magang (UC21 & UC22)
 * Script handler for filtering, pagination, grading modal calculation, and submission.
 */

function mitraPenilaianApp() {
    return {
        prodiFilter: 'all',
        statusFilter: 'all',
        tahunFilter: 'all',

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

        resetFilters() {
            this.prodiFilter = 'all';
            this.statusFilter = 'all';
            this.tahunFilter = 'all';
        },

        matchesRow(row) {
            if (!row || !row.dataset) return true;
            const matchProdi = this.prodiFilter === 'all' || (row.dataset.prodi && row.dataset.prodi.toLowerCase() === this.prodiFilter.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (row.dataset.status && row.dataset.status === this.statusFilter);
            const matchTahun = this.tahunFilter === 'all' || (row.dataset.tahun && String(row.dataset.tahun) === String(this.tahunFilter));

            return matchProdi && matchStatus && matchTahun;
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
            const form = event.target;
            const message = `Anda akan memberikan nilai <span class="custom-alert-highlight">${this.calculatedScore.toFixed(1)} (${this.calculatedGrade})</span> untuk mahasiswa <span class="custom-alert-highlight">${this.activeItem.nama}</span>.`;
            
            if (window.CustomAlert) {
                CustomAlert.confirm({
                    title: 'Simpan Penilaian?',
                    message: message,
                    type: 'primary',
                    confirmText: 'Ya, Simpan',
                    cancelText: 'Batal',
                    confirmColor: 'success',
                    onConfirm: () => {
                        this.isSubmitting = true;
                        form.submit();
                    }
                });
            } else {
                if (confirm(`Simpan penilaian untuk mahasiswa ${this.activeItem.nama}?`)) {
                    this.isSubmitting = true;
                    form.submit();
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

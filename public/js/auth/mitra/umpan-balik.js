/**
 * Mitra Portal - Umpan Balik & Evaluasi Kemitraan (UC26 / CSAT Survey)
 * File: public/js/auth/mitra/umpan-balik.js
 * Logic handler for interactive star rating, CSAT evaluation, filtering, pagination, and solid modals.
 */

function mitraUmpanBalikApp() {
    return {
        jenisFilter: 'all',
        statusFilter: 'all', // 'all' | 'sudah' | 'belum'
        tahunFilter: 'all',

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

        resetFilters() {
            this.jenisFilter = 'all';
            this.statusFilter = 'all';
            this.tahunFilter = 'all';
        },

        matchesRow(row) {
            if (!row || !row.dataset) return true;
            const matchJenis = this.jenisFilter === 'all' || (row.dataset.jenis && row.dataset.jenis.toLowerCase() === this.jenisFilter.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (row.dataset.status && row.dataset.status.toLowerCase() === this.statusFilter.toLowerCase());
            const matchTahun = this.tahunFilter === 'all' || (row.dataset.tahun && String(row.dataset.tahun) === String(this.tahunFilter));

            return matchJenis && matchStatus && matchTahun;
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
            const form = event.target;
            const title = this.isEditMode ? 'Perbarui Umpan Balik?' : 'Kirim Umpan Balik?';
            const text = this.isEditMode
                ? 'Ulasan dan penilaian kepuasan kerja sama akan diperbarui.'
                : 'Ulasan dan penilaian kepuasan Anda akan dikirimkan ke pihak Politeknik Negeri Manado.';

            if (window.CustomAlert) {
                CustomAlert.confirm({
                    title: title,
                    message: text,
                    type: 'primary',
                    confirmText: 'Ya, Kirim',
                    cancelText: 'Batal',
                    confirmColor: 'success',
                    onConfirm: () => {
                        this.isSubmitting = true;
                        form.submit();
                    }
                });
            } else {
                if (confirm(`${title}\n${text}`)) {
                    this.isSubmitting = true;
                    form.submit();
                }
            }
        },
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

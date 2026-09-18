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

        currentPage: 1,
        perPage: 10,
        perPageOpen: false,
        perPageOptions: [5, 10, 25, 50],

        createModalOpen: false,
        createMode: 'select', // 'select' | 'new'
        selectedAlumniId: '',
        selectedAlumniInfo: null,

        editModalOpen: false,
        editItem: {},

        detailModalOpen: false,
        detailItem: {},

        isSubmitting: false,

        setPerPage(value) {
            this.perPage = Number(value);
            this.currentPage = 1;
            this.perPageOpen = false;
        },

        init() {
            this.$watch('prodiFilter', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('tahunFilter', () => this.currentPage = 1);
            this.$watch('perPage', () => this.currentPage = 1);
        },

        resetFilters() {
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
            const matchProdi = this.prodiFilter === 'all' || (row.dataset.prodi && row.dataset.prodi === this.prodiFilter.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (row.dataset.status && row.dataset.status.toLowerCase() === this.statusFilter.toLowerCase());
            const matchTahun = this.tahunFilter === 'all' || (row.dataset.tahun && String(row.dataset.tahun) === String(this.tahunFilter));

            return matchProdi && matchStatus && matchTahun;
        },

        isRowVisible(el) {
            const tr = el.tagName === 'TR' ? el : el.closest('tr');
            if (!tr) return true;

            // If match check fails, immediately hide
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

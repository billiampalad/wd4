/**
 * Mitra Portal - Tracking Lulusan & Penyerapan Alumni POLIMDO (IKU 1)
 * File: public/js/auth/mitra/tracking.js
 * Logic handler for filtering, pagination, search, and solid modals.
 */

function mitraTrackingApp() {
    return {
        searchQuery: '',
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
            return this.$refs.rows ? Array.from(this.$refs.rows.querySelectorAll('tr[data-row]')) : [];
        },

        get filteredRows() {
            return this.rows.filter(row => this.matchesRow(row));
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
            const q = this.searchQuery.toLowerCase().trim();
            const matchSearch = q === '' ||
                (row.dataset.nim && row.dataset.nim.includes(q)) ||
                (row.dataset.nama && row.dataset.nama.includes(q)) ||
                (row.dataset.prodi && row.dataset.prodi.includes(q)) ||
                (row.dataset.posisi && row.dataset.posisi.includes(q));

            const matchProdi = this.prodiFilter === 'all' || (row.dataset.prodi && row.dataset.prodi === this.prodiFilter.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (row.dataset.status && row.dataset.status.toLowerCase() === this.statusFilter.toLowerCase());
            const matchTahun = this.tahunFilter === 'all' || (row.dataset.tahun && row.dataset.tahun === String(this.tahunFilter));

            return matchSearch && matchProdi && matchStatus && matchTahun;
        },

        isRowVisible(el) {
            const tr = el.tagName === 'TR' ? el : el.closest('tr');
            if (!tr) return false;
            const index = this.filteredRows.indexOf(tr);
            if (index === -1) return false;
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return index >= start && index < end;
        },

        rowNumber(el) {
            const tr = el.tagName === 'TR' ? el : el.closest('tr');
            if (!tr) return '01';
            const index = this.filteredRows.indexOf(tr);
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
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Simpan Data Alumni?',
                    text: 'Data alumni akan ditambahkan dan dihubungkan ke instansi Anda.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.isSubmitting = true;
                        event.target.submit();
                    }
                });
            } else {
                if (confirm('Simpan data alumni ini?')) {
                    this.isSubmitting = true;
                    event.target.submit();
                }
            }
        },

        submitEdit(event) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Perbarui Informasi Karir?',
                    text: `Anda akan memperbarui data karir untuk alumni ${this.editItem.nama}.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Perbarui',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.isSubmitting = true;
                        event.target.submit();
                    }
                });
            } else {
                if (confirm(`Perbarui informasi karir alumni ${this.editItem.nama}?`)) {
                    this.isSubmitting = true;
                    event.target.submit();
                }
            }
        },

        confirmDelete(deleteUrl, nama) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Data Alumni?',
                    text: `Apakah Anda yakin ingin menghapus data alumni ${nama} dari instansi Anda?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
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

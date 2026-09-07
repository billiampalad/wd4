/**
 * ==========================================================================
 * PAGINAV COMPONENT JAVASCRIPT - CLIENT & SERVER-SIDE PAGINATION ENGINE
 * Features: Smart Ellipsis Truncation, Alpine.js Dropdown, Live Search Sync
 * ==========================================================================
 */

(function (global) {
    'use strict';

    class PaginavController {
        constructor(element) {
            this.wrapper = element;
            this.id = element.id || ('paginav_' + Math.random().toString(36).substring(2, 9));
            this.targetSelector = element.getAttribute('data-paginav-target');
            this.isServerSide = element.getAttribute('data-paginav-serverside') === 'true';

            this.perPage = parseInt(element.getAttribute('data-paginav-per-page') || 10, 10);
            this.currentPage = parseInt(element.getAttribute('data-paginav-current-page') || 1, 10);
            this.currentSearchQuery = '';
            this.totalItems = 0;
            this.totalPages = 1;
            this.isPaginating = false;

            // Elements
            this.pagesContainer = element.querySelector('.paginav-pages-container');
            this.btnFirst = element.querySelector('[data-page-action="first"]');
            this.btnPrev = element.querySelector('[data-page-action="prev"]');
            this.btnNext = element.querySelector('[data-page-action="next"]');
            this.btnLast = element.querySelector('[data-page-action="last"]');
            this.perPageSelect = element.querySelector('.paginav-perpage-select');
            this.jumpInput = element.querySelector('.paginav-jump-input');
            
            // Counter labels
            this.countFrom = element.querySelector('.paginav-count-from');
            this.countTo = element.querySelector('.paginav-count-to');
            this.countTotal = element.querySelector('.paginav-count-total');

            if (!this.isServerSide && this.targetSelector) {
                this.initClientSide();
            } else {
                this.initServerSide();
            }
        }

        /* ── Inisialisasi Client-Side Pagination ─────────────────────────────── */
        initClientSide() {
            this.bindEvents();
            this.refresh();
            this.listenForSearchSync();

            // Handle window resize untuk re-render nomor halaman jika breakpoint berubah
            window.addEventListener('resize', () => {
                clearTimeout(this._resizeTimer);
                this._resizeTimer = setTimeout(() => this.renderPageButtons(), 150);
            });
        }

        /* ── Inisialisasi Server-Side Pagination ─────────────────────────────── */
        initServerSide() {
            if (this.perPageSelect) {
                this.perPageSelect.addEventListener('change', (e) => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('per_page', e.target.value);
                    url.searchParams.set('page', '1');
                    if (window.Turbo) {
                        window.Turbo.visit(url.toString());
                    } else {
                        window.location.href = url.toString();
                    }
                });
            }
        }

        /* ── Bind Event Listeners ────────────────────────────────────────────── */
        bindEvents() {
            // Klik tombol navigasi (First, Prev, Next, Last)
            if (this.btnFirst) {
                this.btnFirst.addEventListener('click', () => this.goToPage(1));
            }
            if (this.btnPrev) {
                this.btnPrev.addEventListener('click', () => this.goToPage(this.currentPage - 1));
            }
            if (this.btnNext) {
                this.btnNext.addEventListener('click', () => this.goToPage(this.currentPage + 1));
            }
            if (this.btnLast) {
                this.btnLast.addEventListener('click', () => this.goToPage(this.totalPages));
            }

            // Pilihan rows per page (Native change event dari select atau Alpine.js)
            if (this.perPageSelect) {
                this.perPageSelect.addEventListener('change', (e) => {
                    this.perPage = parseInt(e.target.value, 10);
                    this.currentPage = 1;
                    this.refresh();
                });
            }

            // Jump to page input
            if (this.jumpInput) {
                this.jumpInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        let page = parseInt(this.jumpInput.value, 10);
                        if (!isNaN(page)) {
                            this.goToPage(page);
                            this.jumpInput.value = '';
                        }
                    }
                });
            }

            // Klik nomor halaman dinamis
            if (this.pagesContainer) {
                this.pagesContainer.addEventListener('click', (e) => {
                    const pageBtn = e.target.closest('.paginav-page-item');
                    if (pageBtn && !pageBtn.classList.contains('is-active')) {
                        const page = parseInt(pageBtn.getAttribute('data-page'), 10);
                        if (!isNaN(page)) {
                            this.goToPage(page);
                        }
                    }
                });
            }
        }

        /* ── Sinkronisasi dengan Pencarian / Filter Live (<x-search>) ───────── */
        listenForSearchSync() {
            // Tangkap event search:filter dari search.js
            document.addEventListener('search:filter', (e) => {
                const query = (e.detail?.query || '').toLowerCase().trim();
                this.currentSearchQuery = query;
                this.currentPage = 1;
                this.refresh();
            });

            // Tangkap event search:clear dari search.js
            document.addEventListener('search:clear', () => {
                this.currentSearchQuery = '';
                this.currentPage = 1;
                this.refresh();
            });
        }

        /* ── Filter Baris yang Cocok dengan Query Pencarian ──────────────────── */
        getMatchingItems() {
            if (!this.targetSelector) return [];
            const allItems = Array.from(document.querySelectorAll(this.targetSelector));
            
            // Kecualikan baris empty-state
            const validItems = allItems.filter(el => {
                return !el.classList.contains('um-search-empty') && 
                       !el.classList.contains('empty-state-row') &&
                       el.id !== 'userSearchEmptyRow' &&
                       el.id !== 'demoEmptyRow';
            });

            if (!this.currentSearchQuery) {
                return validItems;
            }

            return validItems.filter(item => {
                return item.textContent.toLowerCase().includes(this.currentSearchQuery);
            });
        }

        /* ── Refresh Tampilan Paginasi & Baris ────────────────────────────────── */
        refresh() {
            if (this.isServerSide || !this.targetSelector) return;
            this.isPaginating = true;

            const allItems = Array.from(document.querySelectorAll(this.targetSelector)).filter(el => {
                return !el.classList.contains('um-search-empty') && 
                       !el.classList.contains('empty-state-row') &&
                       el.id !== 'userSearchEmptyRow' &&
                       el.id !== 'demoEmptyRow';
            });

            const matchingItems = this.getMatchingItems();
            
            this.totalItems = matchingItems.length;
            this.totalPages = Math.max(1, Math.ceil(this.totalItems / this.perPage));

            // Pastikan currentPage dalam range valid
            if (this.currentPage > this.totalPages) {
                this.currentPage = this.totalPages;
            }
            if (this.currentPage < 1) {
                this.currentPage = 1;
            }

            const startIndex = (this.currentPage - 1) * this.perPage;
            const endIndex = startIndex + this.perPage;

            // Sembunyikan semua item dulu
            allItems.forEach(item => {
                item.style.display = 'none';
                item.classList.remove('paginav-row-transition');
            });

            // Tampilkan hanya baris yang cocok pada halaman aktif
            matchingItems.forEach((item, index) => {
                if (index >= startIndex && index < endIndex) {
                    item.style.display = '';
                    item.classList.add('paginav-row-transition');
                } else {
                    item.style.display = 'none';
                }
            });

            // Update Info Counter
            this.updateCounters(startIndex, endIndex);

            // Render Tombol Angka Halaman
            this.renderPageButtons();

            // Update Status Tombol Navigasi (Disabled / Enabled)
            this.updateControlButtons();

            this.isPaginating = false;
        }

        /* ── Update Info Angka Counter ───────────────────────────────────────── */
        updateCounters(startIndex, endIndex) {
            const from = this.totalItems === 0 ? 0 : startIndex + 1;
            const to = Math.min(endIndex, this.totalItems);

            if (this.countFrom) this.countFrom.textContent = from;
            if (this.countTo) this.countTo.textContent = to;
            if (this.countTotal) this.countTotal.textContent = this.totalItems;
        }

        /* ── Render Smart Page Buttons dengan Truncation Ellipsis ─────────────── */
        renderPageButtons() {
            if (!this.pagesContainer) return;

            let html = '';
            const cur = this.currentPage;
            const total = this.totalPages;
            const isMobile = window.innerWidth <= 576;

            if (isMobile) {
                // Di layar HP kecil: Tampilkan maksimal 3-4 item agar tidak overflow
                if (total <= 4) {
                    for (let i = 1; i <= total; i++) {
                        html += this.createPageButtonHtml(i, i === cur);
                    }
                } else {
                    if (cur === 1) {
                        html += this.createPageButtonHtml(1, true);
                        html += this.createPageButtonHtml(2, false);
                        html += '<span class="paginav-ellipsis" aria-hidden="true">&hellip;</span>';
                        html += this.createPageButtonHtml(total, false);
                    } else if (cur === total) {
                        html += this.createPageButtonHtml(1, false);
                        html += '<span class="paginav-ellipsis" aria-hidden="true">&hellip;</span>';
                        html += this.createPageButtonHtml(total - 1, false);
                        html += this.createPageButtonHtml(total, true);
                    } else {
                        html += this.createPageButtonHtml(1, false);
                        if (cur > 2) html += '<span class="paginav-ellipsis" aria-hidden="true">&hellip;</span>';
                        html += this.createPageButtonHtml(cur, true);
                        if (cur < total - 1) html += '<span class="paginav-ellipsis" aria-hidden="true">&hellip;</span>';
                        html += this.createPageButtonHtml(total, false);
                    }
                }
            } else {
                // Layar Desktop / Tablet: Tampilkan maksimal 7 tombol
                if (total <= 7) {
                    for (let i = 1; i <= total; i++) {
                        html += this.createPageButtonHtml(i, i === cur);
                    }
                } else {
                    // Smart sliding window truncation
                    html += this.createPageButtonHtml(1, cur === 1);

                    if (cur > 3) {
                        html += '<span class="paginav-ellipsis" aria-hidden="true">&hellip;</span>';
                    }

                    const start = Math.max(2, cur <= 3 ? 2 : (cur >= total - 2 ? total - 4 : cur - 1));
                    const end = Math.min(total - 1, cur >= total - 2 ? total - 1 : (cur <= 3 ? 4 : cur + 1));

                    for (let i = start; i <= end; i++) {
                        html += this.createPageButtonHtml(i, i === cur);
                    }

                    if (cur < total - 2) {
                        html += '<span class="paginav-ellipsis" aria-hidden="true">&hellip;</span>';
                    }

                    html += this.createPageButtonHtml(total, cur === total);
                }
            }

            this.pagesContainer.innerHTML = html;
        }

        createPageButtonHtml(pageNumber, isActive) {
            return `
                <button 
                    type="button" 
                    class="paginav-page-item ${isActive ? 'is-active' : ''}" 
                    data-page="${pageNumber}"
                    aria-label="Halaman ${pageNumber}"
                    aria-current="${isActive ? 'page' : 'false'}"
                >
                    ${pageNumber}
                </button>
            `;
        }

        /* ── Update State Tombol Navigasi ────────────────────────────────────── */
        updateControlButtons() {
            const isFirst = this.currentPage <= 1;
            const isLast = this.currentPage >= this.totalPages;

            if (this.btnFirst) this.btnFirst.disabled = isFirst;
            if (this.btnPrev) this.btnPrev.disabled = isFirst;
            if (this.btnNext) this.btnNext.disabled = isLast;
            if (this.btnLast) this.btnLast.disabled = isLast;

            if (this.jumpInput) {
                this.jumpInput.max = this.totalPages;
            }
        }

        /* ── Pindah Halaman ──────────────────────────────────────────────────── */
        goToPage(page) {
            if (page < 1 || page > this.totalPages || page === this.currentPage) return;
            this.currentPage = page;
            this.refresh();

            // Dispatch custom event
            this.wrapper.dispatchEvent(new CustomEvent('paginav:change', {
                bubbles: true,
                detail: {
                    page: this.currentPage,
                    perPage: this.perPage,
                    total: this.totalItems,
                    totalPages: this.totalPages
                }
            }));
        }
    }

    const CustomPaginav = {
        instances: new Map(),

        init: function (container = document) {
            const elements = container.querySelectorAll('[data-custom-paginav]');
            elements.forEach(el => {
                if (!el._paginavInstance) {
                    const inst = new PaginavController(el);
                    el._paginavInstance = inst;
                    if (el.id) {
                        this.instances.set(el.id, inst);
                    }
                } else {
                    el._paginavInstance.refresh();
                }
            });
        },

        get: function (id) {
            return this.instances.get(id);
        }
    };

    // Auto initialization on DOM ready and Turbo events
    document.addEventListener('DOMContentLoaded', () => CustomPaginav.init());
    document.addEventListener('turbo:load', () => CustomPaginav.init());
    document.addEventListener('turbo:render', () => CustomPaginav.init());

    global.CustomPaginav = CustomPaginav;

})(typeof window !== 'undefined' ? window : this);

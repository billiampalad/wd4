(() => {
    if (window.__wd4IdleSessionInitialized) return;
    window.__wd4IdleSessionInitialized = true;

    const idleLimit = 120 * 60 * 1000;
    const heartbeatInterval = 5 * 60 * 1000;
    const activityStorageKey = 'wd4:last-authenticated-activity';
    const activityEvents = ['pointerdown', 'keydown', 'scroll', 'touchstart'];
    let lastActivity = Number(localStorage.getItem(activityStorageKey)) || Date.now();
    let lastHeartbeat = 0;
    let activityWriteTimer = null;
    let isExpiring = false;

    const isAuthenticatedPage = () => Boolean(
        document.querySelector('meta[name="csrf-token"]') &&
        document.getElementById('logout-form')
    );

    const redirectToLogin = () => {
        if (isExpiring) return;
        isExpiring = true;

        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            logoutForm.requestSubmit();
            return;
        }

        window.location.assign('/login');
    };

    const sendHeartbeat = async () => {
        if (!isAuthenticatedPage() || document.hidden || isExpiring) return;

        const now = Date.now();
        if (now - lastHeartbeat < heartbeatInterval) return;
        lastHeartbeat = now;

        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) {
            redirectToLogin();
            return;
        }

        try {
            const response = await fetch('/session/heartbeat', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
            });

            if (response.status === 401 || response.status === 419 || response.redirected) {
                redirectToLogin();
            }
        } catch (error) {
            // Gangguan jaringan sementara tidak langsung mengeluarkan pengguna.
        }
    };

    const recordActivity = () => {
        if (!isAuthenticatedPage() || isExpiring) return;

        lastActivity = Date.now();
        if (activityWriteTimer) return;

        activityWriteTimer = window.setTimeout(() => {
            localStorage.setItem(activityStorageKey, String(lastActivity));
            activityWriteTimer = null;
        }, 1000);

        sendHeartbeat();
    };

    const checkIdleSession = () => {
        if (!isAuthenticatedPage() || isExpiring) return;

        const sharedActivity = Number(localStorage.getItem(activityStorageKey)) || lastActivity;
        lastActivity = Math.max(lastActivity, sharedActivity);

        if (Date.now() - lastActivity >= idleLimit) {
            redirectToLogin();
        }
    };

    const initializeIdleSession = () => {
        if (!isAuthenticatedPage()) return;

        lastActivity = Date.now();
        localStorage.setItem(activityStorageKey, String(lastActivity));

        activityEvents.forEach((eventName) => {
            document.addEventListener(eventName, recordActivity, { passive: true });
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                checkIdleSession();
                recordActivity();
            }
        });

        sendHeartbeat();
        window.setInterval(checkIdleSession, 30 * 1000);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeIdleSession, { once: true });
    } else {
        initializeIdleSession();
    }
})();
function initDashboard() {
    function swalAvailable() {
        return typeof Swal !== 'undefined';
    }

    function normalizeMessage(message, fallback) {
        const value = String(message || '').replace(/\s+/g, ' ').trim();
        return value || fallback;
    }

    function removeLegacyFlashAlerts(type) {
        const selector = type === 'success' ? '.dk-alert-success, .alert-success' : '.dk-alert-error, .alert-error';
        document.querySelectorAll(selector).forEach(alert => alert.remove());
    }

    function showFlashAlert(id, options) {
        const el = document.getElementById(id);
        if (!el || el.dataset.shown === '1') return;

        const message = normalizeMessage(el.dataset.message, options.fallbackText || 'Terjadi kesalahan.');
        if (!message || !swalAvailable()) return;

        el.dataset.shown = '1';
        removeLegacyFlashAlerts(options.icon);
        Swal.fire({
            icon: options.icon,
            title: options.title,
            text: message,
            width: options.width || 560,
            confirmButtonColor: options.confirmButtonColor || '#7c3aed',
        }).then(() => {
            el.remove();
        });
    }

    showFlashAlert('swal-flash-success', {
        icon: 'success',
        title: 'Berhasil!',
    });
    showFlashAlert('swal-flash-error', {
        icon: 'error',
        title: 'Gagal Memproses Data',
        fallbackText: 'Data belum berhasil diproses. Periksa kembali isian Anda.',
        confirmButtonColor: '#ef4444',
    });
    showFlashAlert('swal-flash-validation', {
        icon: 'warning',
        title: 'Data Tidak Valid',
        fallbackText: 'Periksa kembali data yang wajib diisi atau format yang belum sesuai.',
        confirmButtonColor: '#f59e0b',
    });

    /* ─ Dark Mode ─ */
    const html = document.documentElement;
    const dmBtn = document.getElementById('darkModeBtn');
    const dmIcon = document.getElementById('themeIcon');

    if (dmBtn && dmIcon) {
        function applyTheme(t) {
            if (t === 'dark') {
                html.setAttribute('data-theme', 'dark');
                dmIcon.className = 'fas fa-sun';
            } else {
                html.removeAttribute('data-theme');
                dmIcon.className = 'fas fa-moon';
            }
            localStorage.setItem('theme', t);
        }

        // Apply saved theme on first load
        applyTheme(localStorage.getItem('theme') || 'light');

        dmBtn.onclick = () => {
            applyTheme(localStorage.getItem('theme') === 'dark' ? 'light' : 'dark');
            if (window.Swal && Swal.isVisible()) {
                Swal.close();
            }
        };
    }

    /* ─ Sidebar Toggle (mobile) ─ */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('hamburger');

    if (sidebar && overlay && hamburger) {
        function toggleSidebar() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }

        hamburger.onclick = toggleSidebar;
        overlay.onclick = toggleSidebar;
    }

    /* ─ Sidebar Collapse Toggle (desktop) ─ */
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        function applySidebarState(collapsed) {
            if (collapsed) {
                document.body.classList.add('sidebar-collapsed');
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
            localStorage.setItem('sidebar_collapsed', collapsed ? '1' : '0');

            window.setTimeout(function () {
                window.dispatchEvent(new Event('resize'));
            }, 420);
        }

        // Apply saved state on first load
        applySidebarState(localStorage.getItem('sidebar_collapsed') === '1');

        sidebarToggle.onclick = () => {
            applySidebarState(localStorage.getItem('sidebar_collapsed') !== '1');
        };
    }

    /* ─ Sidebar Submenus ─ */
    const submenus = [
        { btn: 'dataMasterBtn', sub: 'dataMasterSub' },
        { btn: 'kerjasamaBtn', sub: 'kerjasamaSub' },
        { btn: 'analitikBtn', sub: 'analitikSub' },
        { btn: 'referensiBtn', sub: 'referensiSub' }
    ];

    submenus.forEach(item => {
        const btn = document.getElementById(item.btn);
        const sub = document.getElementById(item.sub);
        if (btn && sub) {
            btn.onclick = (e) => {
                e.preventDefault();
                btn.classList.toggle('submenu-open');
                sub.classList.toggle('open');
            };
        }
    });

    /* ─ Show navSearch on wider screens ─ */
    const navSearch = document.getElementById('navSearch');
    const searchInput = document.getElementById('navSearchInput');
    if (navSearch) {
        function checkSearch() {
            navSearch.style.display = window.innerWidth >= 1024 ? 'flex' : 'none';
        }
        checkSearch();
        window.onresize = checkSearch;
    }

    /* ─ Global search: filter tabel Data Kerjasama & Laporan ─ */
    const searchClear = document.getElementById('navSearchClear');

    function updatePagination(tbody) {
        const table = tbody.closest('table');
        if (!table || table.classList.contains('no-pagination')) return;

        const pageSize = parseInt(table.getAttribute('data-page-size') || '10');
        const currentPage = parseInt(table.getAttribute('data-current-page') || '1');

        // Get all rows that are NOT the "no results" row and are NOT hidden by search
        const allRows = Array.from(tbody.querySelectorAll('tr.um-row'));
        const visibleRows = allRows.filter(row => row.getAttribute('data-search-hidden') !== '1');

        const totalRows = visibleRows.length;
        const totalPages = Math.ceil(totalRows / pageSize) || 1;

        // Ensure current page is within bounds
        let page = currentPage;
        if (page > totalPages) page = totalPages;
        if (page < 1) page = 1;
        table.setAttribute('data-current-page', page);

        // Show/hide rows based on current page
        const start = (page - 1) * pageSize;
        const end = start + pageSize;

        visibleRows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update pagination controls UI
        renderPaginationControls(table, totalRows, page, totalPages, pageSize);
    }

    function renderPaginationControls(table, totalRows, currentPage, totalPages, pageSize) {
        const container = table.closest('.table-wrap') || table;
        let controls = container.parentNode.querySelector('.table-pagination-controls');
        if (!controls) {
            controls = document.createElement('div');
            controls.className = 'table-pagination-controls';
            container.parentNode.insertBefore(controls, container.nextSibling);
        }

        const startIdx = totalRows === 0 ? 0 : (currentPage - 1) * pageSize + 1;
        const endIdx = Math.min(currentPage * pageSize, totalRows);

        let paginationHtml = `
            <div class="pagination-info">
                Menampilkan ${startIdx} sampai ${endIdx} dari ${totalRows} data
            </div>
            <div class="pagination-buttons">
                <button class="pag-btn prev" ${currentPage === 1 ? 'disabled' : ''} data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </button>
        `;

        // Page numbers
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, startPage + 2);
        if (endPage - startPage < 2) startPage = Math.max(1, endPage - 2);

        if (startPage > 1) {
            paginationHtml += `<button class="pag-btn" data-page="1">1</button>`;
            if (startPage > 2) paginationHtml += `<span class="pag-dots">...</span>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <button class="pag-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) paginationHtml += `<span class="pag-dots">...</span>`;
            paginationHtml += `<button class="pag-btn" data-page="${totalPages}">${totalPages}</button>`;
        }

        paginationHtml += `
                <button class="pag-btn next" ${currentPage === totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        `;

        controls.innerHTML = paginationHtml;

        // Add event listeners to buttons
        controls.querySelectorAll('.pag-btn:not([disabled])').forEach(btn => {
            btn.onclick = () => {
                const newPage = parseInt(btn.getAttribute('data-page'));
                table.setAttribute('data-current-page', newPage);
                updatePagination(table.querySelector('tbody'));
            };
        });
    }

    function initTableFeatures() {
        const tables = document.querySelectorAll('#mainContent .um-table');
        tables.forEach(table => {
            if (table.classList.contains('no-pagination') || table.getAttribute('data-paginated')) return;

            table.setAttribute('data-paginated', '1');
            table.setAttribute('data-current-page', '1');
            table.setAttribute('data-page-size', '10');

            // Add "Show Entries" dropdown
            const header = table.closest('.card')?.querySelector('.card-header');
            if (header) {
                const entriesWrap = document.createElement('div');
                entriesWrap.className = 'table-entries-wrap';
                entriesWrap.innerHTML = `
                    <div class="custom-entries-wrap" x-data="{ 
                            open: false, 
                            selected: 10,
                            options: [10, 25, 50, 100],
                            selectOption(opt) {
                                this.selected = opt;
                                this.open = false;
                                this.$el.dispatchEvent(new CustomEvent('change-entries', { detail: opt, bubbles: true }));
                            }
                        }">
                        <span class="entries-label">Tampilkan</span>
                        <div class="custom-select-container" @click.away="open = false">
                            <button type="button" class="custom-select-button" @click="open = !open">
                                <span x-text="selected"></span>
                                <i class="fas fa-chevron-down custom-select-icon" :class="{'rotate': open}"></i>
                            </button>
                            <div class="custom-select-dropdown" x-show="open" x-transition.opacity.duration.200ms style="display: none;">
                                <template x-for="opt in options" :key="opt">
                                    <div class="custom-select-option" :class="{'active': selected === opt}" @click="selectOption(opt)" x-text="opt"></div>
                                </template>
                            </div>
                        </div>
                        <span class="entries-label">data</span>
                    </div>
                `;

                // Find where to insert in header
                const title = header.querySelector('.um-title, .card-title');
                if (title && title.nextSibling) {
                    header.insertBefore(entriesWrap, title.nextSibling);
                } else {
                    header.appendChild(entriesWrap);
                }

                entriesWrap.addEventListener('change-entries', (e) => {
                    table.setAttribute('data-page-size', e.detail);
                    table.setAttribute('data-current-page', '1');
                    updatePagination(table.querySelector('tbody'));
                });
            }

            updatePagination(table.querySelector('tbody'));
        });
    }

    if (searchInput) {
        let searchTimeout;

        function highlightText(element, query) {
            if (!query) return;

            // Recursive function to highlight text nodes
            function innerHighlight(node) {
                if (node.nodeType === 3) { // Text node
                    const text = node.nodeValue;
                    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');

                    if (text.match(regex)) {
                        const span = document.createElement('span');
                        span.innerHTML = text.replace(regex, '<mark class="search-highlight">$1</mark>');
                        node.parentNode.replaceChild(span, node);
                    }
                } else if (node.nodeType === 1 && node.childNodes && !/(script|style)/i.test(node.tagName) && node.className !== 'search-highlight') {
                    for (let i = 0; i < node.childNodes.length; i++) {
                        innerHighlight(node.childNodes[i]);
                    }
                }
            }
            innerHighlight(element);
        }

        function filterTableBySearch() {
            const q = (searchInput.value || '').trim().toLowerCase();

            // Toggle clear button visibility
            if (searchClear) {
                searchClear.style.display = q ? 'flex' : 'none';
            }

            window.dispatchEvent(new CustomEvent('pimpinan-global-search', {
                detail: q
            }));
            window.dispatchEvent(new CustomEvent('unit-dashboard-global-search', {
                detail: q
            }));

            // Select all relevant table bodies
            const tables = Array.from(document.querySelectorAll('#mainContent .um-table tbody'));
            const previewBody = document.getElementById('previewBody');
            if (previewBody) tables.push(previewBody);

            tables.forEach(tbody => {
                if (!tbody) return;

                // Reset to first page when searching
                const table = tbody.closest('table');
                if (table) table.setAttribute('data-current-page', '1');

                const rows = tbody.querySelectorAll('tr.um-row');
                const emptyRow = tbody.querySelector('tr.um-empty, tr[data-empty]');
                let visibleCount = 0;

                rows.forEach(row => {
                    // Save original HTML if not already saved
                    if (!row.hasAttribute('data-original-html')) {
                        row.setAttribute('data-original-html', row.innerHTML);
                    }

                    // Restore original HTML before search and highlight
                    row.innerHTML = row.getAttribute('data-original-html');

                    const cells = row.querySelectorAll('td');
                    let rowMatch = false;

                    cells.forEach(cell => {
                        const cellText = cell.textContent || '';
                        if (!q || cellText.toLowerCase().includes(q)) {
                            rowMatch = true;
                        }
                    });

                    // Set temporary attribute for search visibility
                    if (rowMatch) {
                        row.removeAttribute('data-search-hidden');
                        row.style.display = '';
                        visibleCount++;
                        if (q) {
                            cells.forEach(cell => highlightText(cell, q));
                        }
                    } else {
                        row.setAttribute('data-search-hidden', '1');
                        row.style.display = 'none';
                    }
                });

                // Update pagination after search
                updatePagination(tbody);

                // Tampilkan pesan "tidak ditemukan" saat search aktif dan tidak ada yang cocok
                let noResultsRow = tbody.querySelector('tr[data-search-no-results]');
                if (q && visibleCount === 0 && rows.length > 0) {
                    const table = tbody.closest('table');
                    const colCount = table ? table.querySelectorAll('thead th').length : 7;
                    if (!noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.setAttribute('data-search-no-results', '1');
                        noResultsRow.innerHTML = `
                            <td colspan="${colCount}" class="um-empty" style="padding: 24px; text-align: center;">
                                <div style="color: var(--text-sub);">
                                    <i class="fas fa-search" style="font-size: 24px; opacity: 0.5; margin-bottom: 8px;"></i>
                                    <p style="font-weight: 600; margin: 0;">Tidak ada hasil untuk "<span data-query></span>"</p>
                                    <p style="font-size: 12px; margin-top: 4px;">Coba kata kunci lain</p>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(noResultsRow);
                    }
                    const span = noResultsRow.querySelector('[data-query]');
                    if (span) span.textContent = q;
                    noResultsRow.style.display = '';
                    if (emptyRow) emptyRow.style.display = 'none';

                    // Hide pagination if no results
                    const controls = table.parentNode.querySelector('.table-pagination-controls');
                    if (controls) controls.style.display = 'none';
                } else {
                    if (noResultsRow) noResultsRow.style.display = 'none';
                    if (emptyRow && !q) emptyRow.style.display = '';

                    const table = tbody.closest('table');
                    const controls = table?.parentNode.querySelector('.table-pagination-controls');
                    if (controls) controls.style.display = '';
                }
            });

            filterPimpinanSummaryTables(q);
        }

        function filterPimpinanSummaryTables(q) {
            document.querySelectorAll('#mainContent .pimpinan-table tbody').forEach(tbody => {
                const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.hasAttribute('data-search-no-results'));
                const dataRows = rows.filter(row => row.querySelectorAll('td').length > 1);
                let visibleCount = 0;

                dataRows.forEach(row => {
                    if (!row.hasAttribute('data-original-html')) {
                        row.setAttribute('data-original-html', row.innerHTML);
                    }

                    row.innerHTML = row.getAttribute('data-original-html');

                    const rowMatch = !q || row.textContent.toLowerCase().includes(q);
                    row.style.display = rowMatch ? '' : 'none';

                    if (rowMatch) {
                        visibleCount++;
                        if (q) {
                            row.querySelectorAll('td').forEach(cell => highlightText(cell, q));
                        }
                    }
                });

                let noResultsRow = tbody.querySelector('tr[data-search-no-results]');
                if (q && visibleCount === 0 && dataRows.length > 0) {
                    const table = tbody.closest('table');
                    const colCount = table ? table.querySelectorAll('thead th').length : 1;

                    if (!noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.setAttribute('data-search-no-results', '1');
                        noResultsRow.innerHTML = `
                            <td colspan="${colCount}" class="pimpinan-table-empty">
                                Tidak ada hasil untuk "<span data-query></span>"
                            </td>
                        `;
                        tbody.appendChild(noResultsRow);
                    }

                    const queryLabel = noResultsRow.querySelector('[data-query]');
                    if (queryLabel) queryLabel.textContent = q;
                    noResultsRow.style.display = '';
                } else if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterTableBySearch, 200);
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                filterTableBySearch();
                searchInput.blur();
            }
        });

        if (searchClear) {
            searchClear.onclick = () => {
                searchInput.value = '';
                filterTableBySearch();
                searchInput.focus();
            };
        }

        initTableFeatures();
    }

    /* ─ Logout confirm ─ */
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.onclick = (e) => {
            e.preventDefault();
            const form = logoutBtn.closest('form');
            if (!form) return;

            Swal.fire({
                title: 'Apakah anda ingin keluar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        };
    }

    function getFormConfirmMessage(form) {
        if (form.dataset.confirmMessage) return form.dataset.confirmMessage;

        const originalOnSubmit = form.getAttribute('onsubmit');
        if (!originalOnSubmit || !originalOnSubmit.includes('confirm')) {
            return 'Yakin ingin melanjutkan proses ini?';
        }

        const match = originalOnSubmit.match(/confirm\((['"])(.*?)\1\)/);
        return match ? match[2] : 'Yakin ingin melanjutkan proses ini?';
    }

    function getFormConfirmTitle(form) {
        const method = (form.querySelector('input[name="_method"]')?.value || form.method || '').toUpperCase();
        if (method === 'DELETE' || form.classList.contains('dk-delete-form') || form.hasAttribute('data-mitra-delete-form')) {
            return 'Konfirmasi Hapus';
        }

        return 'Konfirmasi';
    }

    function markSubmitting(form) {
        form.dataset.submitting = '1';
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(button => {
            button.disabled = true;
        });
    }

    function bindFormConfirmation(form) {
        if (!form || form.dataset.swalConfirmBound === '1' || form.hasAttribute('data-mitra-delete-form')) return;
        form.dataset.swalConfirmBound = '1';

        const originalOnSubmit = form.getAttribute('onsubmit');
        if (originalOnSubmit && originalOnSubmit.includes('confirm')) {
            form.removeAttribute('onsubmit');
        }

        form.addEventListener('submit', function (e) {
            if (form.dataset.swalConfirmed === '1') {
                markSubmitting(form);
                return;
            }

            e.preventDefault();

            if (!swalAvailable()) {
                form.dataset.swalConfirmed = '1';
                markSubmitting(form);
                form.submit();
                return;
            }

            const message = normalizeMessage(getFormConfirmMessage(form), 'Yakin ingin melanjutkan proses ini?');
            Swal.fire({
                title: getFormConfirmTitle(form),
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.swalConfirmed = '1';
                    markSubmitting(form);
                    form.submit();
                }
            });
        });
    }

    /* ─ Global form confirmation with SweetAlert ─ */
    function getTrimmedFieldValue(form, selector) {
        const field = form.querySelector(selector);
        return field ? String(field.value || '').trim() : '';
    }

    function hasFilledField(form, selector) {
        return Array.from(form.querySelectorAll(selector)).some(field => String(field.value || '').trim() !== '');
    }

    function findKerjasamaGroupByLabel(form, labelText) {
        return Array.from(form.querySelectorAll('.mc-group')).find(group => {
            const label = group.querySelector('.mc-label');
            return label && label.textContent.replace(/\s+/g, ' ').trim().includes(labelText);
        });
    }

    function clearKerjasamaRequiredErrors(form) {
        form.querySelectorAll('.js-required-error').forEach(error => error.remove());
        form.querySelectorAll('.js-required-invalid').forEach(field => {
            field.classList.remove('is-invalid', 'js-required-invalid');
        });
    }

    function markKerjasamaRequiredError(check) {
        const group = check.group || check.target?.closest?.('.mc-group') || check.target;
        const targets = [];

        if (check.target && check.target.classList) {
            targets.push(check.target);
        }

        if (group && group.querySelectorAll) {
            targets.push(...group.querySelectorAll('.mc-input, .adp-input, .ad-trigger'));

            if (!targets.length) {
                targets.push(...group.querySelectorAll('button[type="button"]'));
            }
        }

        targets.forEach(target => {
            target.classList.add('is-invalid', 'js-required-invalid');
        });

        if (group && group.insertAdjacentHTML && !group.querySelector('.js-required-error')) {
            group.insertAdjacentHTML(
                'beforeend',
                `<span class="mc-error js-required-error"><i class="fas fa-circle-exclamation"></i> ${check.message}</span>`
            );
        }
    }

    function showRequiredKerjasamaAlert(message, target) {
        if (target && typeof target.focus === 'function') {
            setTimeout(() => target.focus({ preventScroll: false }), 80);
        }

        if (swalAvailable()) {
            Swal.fire({
                icon: 'warning',
                title: 'Kolom Wajib Diisi',
                text: message,
                confirmButtonColor: '#7c3aed'
            });
            return;
        }

        alert(message);
    }

    function bindKerjasamaRequiredValidation(form) {
        if (form.dataset.kerjasamaRequiredBound === '1') return;
        if (!form.matches('[data-kerjasama-create-form]')) return;

        form.dataset.kerjasamaRequiredBound = '1';
        form.addEventListener('input', () => clearKerjasamaRequiredErrors(form));
        form.addEventListener('change', () => clearKerjasamaRequiredErrors(form));
        form.addEventListener('click', event => {
            if (event.target.closest('.ad-item, .ad-trigger, button[type="button"]')) {
                setTimeout(() => clearKerjasamaRequiredErrors(form), 0);
            }
        });

        form.addEventListener('submit', function (event) {
            clearKerjasamaRequiredErrors(form);

            const jenis = getTrimmedFieldValue(form, 'input[name="jenis"]');
            const inputType = getTrimmedFieldValue(form, 'input[name="input_type"]');
            const dokumenGroup = findKerjasamaGroupByLabel(form, 'Dokumen Kerjasama');
            const startDateGroup = findKerjasamaGroupByLabel(form, 'Tanggal Mulai');
            const endDateGroup = findKerjasamaGroupByLabel(form, 'Tanggal Selesai');
            const statusGroup = findKerjasamaGroupByLabel(form, 'Status');
            const tipePelaksanaGroup = findKerjasamaGroupByLabel(form, 'Tipe Pelaksana');
            const namaMitraGroup = findKerjasamaGroupByLabel(form, 'Nama Mitra');
            const ruangLingkupGroup = findKerjasamaGroupByLabel(form, 'Bentuk Kegiatan Kerjasama');
            const checks = [
                {
                    invalid: !jenis,
                    message: 'Dokumen Kerjasama wajib dipilih.',
                    group: dokumenGroup,
                    target: dokumenGroup?.querySelector('.ad-trigger')
                },
                {
                    invalid: !getTrimmedFieldValue(form, 'input[name="doc_number"]'),
                    message: 'Nomor Dokumen Kerjasama wajib diisi.',
                    target: form.querySelector('input[name="doc_number"]')
                },
                {
                    invalid: !getTrimmedFieldValue(form, 'input[name="start_date"]'),
                    message: 'Tanggal Mulai pada Periode Kerjasama wajib diisi.',
                    group: startDateGroup,
                    target: form.querySelector('input[name="start_date"]')
                },
                {
                    invalid: !getTrimmedFieldValue(form, 'input[name="end_date"]'),
                    message: 'Tanggal Selesai pada Periode Kerjasama wajib diisi.',
                    group: endDateGroup,
                    target: form.querySelector('input[name="end_date"]')
                },
                {
                    invalid: !getTrimmedFieldValue(form, 'input[name="document_link"]'),
                    message: 'Link Google Drive wajib diisi.',
                    target: form.querySelector('input[name="document_link"]')
                },
                {
                    invalid: inputType === 'arsip' && !getTrimmedFieldValue(form, 'input[name="status"]'),
                    message: 'Status Kerjasama wajib dipilih.',
                    group: statusGroup,
                    target: statusGroup?.querySelector('.ad-trigger')
                },
                {
                    invalid: (jenis.includes('MoA') || jenis.includes('IA')) && !hasFilledField(form, 'input[name="tipe_pelaksana[]"]'),
                    message: 'Tipe Pelaksana wajib dipilih.',
                    group: tipePelaksanaGroup,
                    target: tipePelaksanaGroup
                },
                {
                    invalid: !hasFilledField(form, 'input[name="penggiat_mitra_ids[]"]'),
                    message: 'Nama Mitra wajib dipilih.',
                    group: namaMitraGroup,
                    target: namaMitraGroup?.querySelector('.ad-trigger')
                },
                {
                    invalid: !hasFilledField(form, 'input[name="id_jenis[]"]'),
                    message: 'Bentuk Kegiatan Kerjasama (Ruang Lingkup) wajib dipilih.',
                    group: ruangLingkupGroup,
                    target: ruangLingkupGroup?.querySelector('.ad-trigger') || ruangLingkupGroup
                }
            ];
            const failed = checks.find(check => check.invalid);

            if (!failed) return;

            event.preventDefault();
            event.stopImmediatePropagation();
            form.dataset.submitting = '0';
            markKerjasamaRequiredError(failed);
            showRequiredKerjasamaAlert(failed.message, failed.target);
        }, true);
    }

    document.querySelectorAll('form[data-kerjasama-create-form]').forEach(bindKerjasamaRequiredValidation);

    document.querySelectorAll('form[onsubmit*="confirm"], form[data-confirm-message]').forEach(bindFormConfirmation);

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form[onsubmit*="confirm"], form[data-confirm-message]');
        if (!form || form.dataset.swalConfirmBound === '1' || form.hasAttribute('data-mitra-delete-form')) return;

        bindFormConfirmation(form);
        event.preventDefault();
        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }, true);

    /* ─ Prevent double submit on create/edit forms ─ */
    document.querySelectorAll('form[method="POST"]:not([data-no-submit-lock]):not([data-mitra-delete-form])').forEach(form => {
        if (form.dataset.submitLockBound === '1' || form.dataset.swalConfirmBound === '1') return;
        form.dataset.submitLockBound = '1';

        form.addEventListener('submit', function (e) {
            if (form.hasAttribute('data-mitra-delete-form')) {
                return;
            }

            if (form.dataset.submitting === '1') {
                e.preventDefault();
                return;
            }

            markSubmitting(form);

            if (swalAvailable() && !form.closest('.swal2-container')) {
                AppLoading.swal('Memproses Data', 'Mohon tunggu, data sedang disimpan.');
            }
        });
    });

    /* ─ Dashboard Charts ─ */
    initCharts();

    initLaporan();

    initStarRatings();
    initCustomDropdown();
}

function initCharts() {
    const dataContainer = document.getElementById('dashboardStatsData');
    if (typeof Chart === 'undefined' || !dataContainer) return;

    // Common chart defaults
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';

    // Parse data from data-attributes
    const mitra = JSON.parse(dataContainer.getAttribute('data-mitra') || '{}');
    const jenis = JSON.parse(dataContainer.getAttribute('data-jenis') || '[]');
    const tren = JSON.parse(dataContainer.getAttribute('data-tren') || '[]');

    // Mitra Chart
    const mitraCtx = document.getElementById('mitraChart');
    if (mitraCtx) {
        new Chart(mitraCtx, {
            type: 'doughnut',
            data: {
                labels: ['Nasional', 'Internasional'],
                datasets: [{
                    data: [mitra['nasional'] || 0, mitra['internasional'] || 0],
                    backgroundColor: ['#4f46e5', '#0ea5e9'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '70%'
            }
        });
    }

    // Jenis Chart
    const jenisCtx = document.getElementById('jenisChart');
    if (jenisCtx) {
        const labels = jenis.map(item => item.nama_kerjasama);
        const data = jenis.map(item => item.total);
        new Chart(jenisCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kerjasama',
                    data: data,
                    backgroundColor: '#7c3aed',
                    borderRadius: 8,
                    barThickness: 25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { borderDash: [5, 5], drawBorder: false }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Tren Chart
    const trenCtx = document.getElementById('trenChart');
    if (trenCtx) {
        const labels = tren.map(item => item.tahun);
        const data = tren.map(item => item.total);
        new Chart(trenCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kerjasama',
                    data: data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { borderDash: [5, 5], drawBorder: false }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}

function initLaporan() {
    const filterForm = document.getElementById('filterForm');
    const previewBody = document.getElementById('previewBody');
    const btnTampilkan = document.getElementById('btnTampilkan');
    const btnCetakPdf = document.getElementById('btnCetakPdf');
    const btnExportExcel = document.getElementById('btnExportExcel');

    if (!filterForm || !previewBody) return;
    if (!filterForm.dataset.showUrlTemplate) return;
    if (filterForm.dataset.filterBound === 'true') return;
    filterForm.dataset.filterBound = 'true';

    const previewUrl = filterForm.getAttribute('data-preview-url');
    const pdfUrl = filterForm.getAttribute('data-pdf-url');
    const excelUrl = filterForm.getAttribute('data-excel-url');
    const countLabel = document.getElementById('dkerjasamaCount');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function routeFromTemplate(template, id) {
        return String(template || '').replace('__ID__', encodeURIComponent(id));
    }

    function getFilterParams() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        formData.forEach(function(val, key) {
            if (val && val !== 'all') params.append(key, val);
        });
        return params.toString();
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const str = String(dateStr).split('T')[0];
        const d = new Date(str);
        if (isNaN(d.getTime())) return escapeHtml(dateStr);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        return String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function setCount(total) {
        if (countLabel) countLabel.textContent = total + ' data ditemukan';
    }

    function showLoading() {
        previewBody.innerHTML = AppLoading.tableRow(7, 'Memuat data kerjasama...');
    }

    function showEmpty() {
        setCount(0);
        previewBody.innerHTML =
            '<tr data-empty><td colspan="7" class="um-empty"><div class="um-empty-state dk-empty-state"><div class="um-empty-icon dk-empty-icon"><i class="fas fa-folder-open"></i></div><p class="um-empty-title">Tidak ada data ditemukan</p><p class="um-empty-sub">Coba ubah filter untuk menampilkan data lain.</p></div></td></tr>';
    }

    function showError() {
        previewBody.innerHTML =
            '<tr><td colspan="7" class="um-empty"><div class="um-empty-state dk-empty-state"><p class="um-empty-title" style="color:#ef4444;">Gagal memuat data</p><p class="um-empty-sub">Terjadi kesalahan. Silakan coba lagi.</p></div></td></tr>';
    }

    function buildRow(item, idx) {
        const title = escapeHtml(item.title || '-');
        const docNumber = escapeHtml(item.doc_number || '-');
        const jenis = escapeHtml(item.jenis || '-');
        const mitraName = escapeHtml((item.mitra && item.mitra.nama_mitra) ? item.mitra.nama_mitra : '-');

        let pelaksanaIcon = 'fa-building';
        let pelaksanaClass = 'dk-entity-indigo';
        let pelaksanaName = '-';
        if (item.tipe_pelaksana === 'jurusan') {
            pelaksanaIcon = 'fa-microchip';
            pelaksanaName = (item.jurusan && item.jurusan.nama_jurusan) ? item.jurusan.nama_jurusan : '-';
        } else if (item.tipe_pelaksana === 'upa') {
            pelaksanaIcon = 'fa-building-columns';
            pelaksanaClass = 'dk-entity-cyan';
            pelaksanaName = (item.upa && item.upa.nama_upa) ? item.upa.nama_upa : '-';
        } else if (item.tipe_pelaksana === 'pusat') {
            pelaksanaIcon = 'fa-landmark';
            pelaksanaClass = 'dk-entity-violet';
            pelaksanaName = (item.pusat && item.pusat.nama_pusat) ? item.pusat.nama_pusat : '-';
        }

        const status = (item.status || '').toLowerCase();
        const isExpired = ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'].indexOf(status) !== -1;
        const isExtended = status.indexOf('perpanjangan') !== -1;
        let statusClass = 'dk-status-neutral';
        let statusIcon = 'fa-circle-info';
        let statusLabel = 'Belum Diatur';

        if (status === 'aktif') {
            statusClass = 'dk-status-active';
            statusIcon = 'fa-circle-check';
            statusLabel = 'Aktif';
        } else if (status === 'proses' || status === 'menunggu_validasi') {
            statusClass = 'dk-status-info';
            statusIcon = 'fa-spinner fa-spin';
            statusLabel = status === 'proses' ? 'Proses' : 'Menunggu Validasi';
        } else if (isExtended) {
            statusClass = 'dk-status-warning';
            statusIcon = 'fa-clock';
            statusLabel = 'Perpanjangan';
        } else if (isExpired) {
            statusClass = 'dk-status-danger';
            statusIcon = 'fa-circle-xmark';
            statusLabel = 'Kadarluarsa';
        } else if (status === 'tidak aktif') {
            statusClass = 'dk-status-muted';
            statusIcon = 'fa-circle-minus';
            statusLabel = 'Tidak Aktif';
        } else if (status !== '') {
            statusLabel = status.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        }

        const showUrl = routeFromTemplate(filterForm.dataset.showUrlTemplate, item.id);
        const editUrl = routeFromTemplate(filterForm.dataset.editUrlTemplate, item.id);
        const deleteUrl = routeFromTemplate(filterForm.dataset.deleteUrlTemplate, item.id);

        const tr = document.createElement('tr');
        tr.className = 'um-row dk-row';
        tr.innerHTML =
            '<td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;"><span class="um-num dk-num">' + String(idx + 1).padStart(2, '0') + '</span></td>' +
            '<td class="um-td dk-title-cell" style="width: 450px; min-width: 400px; vertical-align: top; padding-top: 15px;"><div class="dk-doc-cell" style="white-space: normal; word-break: break-word;"><span class="dk-doc-number">#' + docNumber + '</span><span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">' + title + '</span><span class="dk-doc-kind">' + jenis + '</span></div></td>' +
            '<td class="um-td" style="vertical-align: top; padding-top: 15px;"><div class="dk-entity" style="align-items: flex-start;"><span class="dk-entity-icon ' + pelaksanaClass + '" style="flex-shrink: 0;"><i class="fas ' + pelaksanaIcon + '"></i></span><span class="dk-entity-text" style="padding-top: 4px;">' + escapeHtml(pelaksanaName) + '</span></div></td>' +
            '<td class="um-td" style="vertical-align: top; padding-top: 15px;"><div class="dk-entity" style="align-items: flex-start;"><span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;"><i class="fas fa-building"></i></span><span class="dk-entity-text" style="padding-top: 4px;">' + mitraName + '</span></div></td>' +
            '<td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;"><div class="dk-date-range-compact"><span class="date-val">' + formatDate(item.start_date) + '</span><span class="date-sep">s/d</span><span class="date-val">' + formatDate(item.end_date) + '</span></div></td>' +
            '<td class="um-td" style="vertical-align: top; padding-top: 15px;"><span class="dk-status ' + statusClass + '"><i class="fas ' + statusIcon + '"></i> ' + statusLabel + '</span></td>' +
            '<td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;"><div class="um-actions dk-actions-compact"><a href="' + showUrl + '" class="dk-action-btn view" title="Detail"><i class="fas fa-eye"></i></a><a href="' + editUrl + '" class="dk-action-btn edit" title="Edit"><i class="fas fa-pen-to-square"></i></a><form action="' + deleteUrl + '" method="POST" class="dk-delete-form" style="display: inline;" onsubmit="return confirm(&quot;Yakin ingin menghapus data kerjasama ini?&quot;)"><input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="dk-action-btn delete" title="Hapus"><i class="fas fa-trash-can"></i></button></form></div></td>';
        return tr;
    }

    function loadData() {
        showLoading();
        AppLoading.setButton(btnTampilkan, 'Memuat...');

        const query = getFilterParams();
        const url = previewUrl + (query ? '?' + query : '');

        fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function(data) {
                if (!data || data.length === 0) {
                    showEmpty();
                    return;
                }

                setCount(data.length);
                const fragment = document.createDocumentFragment();
                data.forEach(function(item, idx) {
                    fragment.appendChild(buildRow(item, idx));
                });
                previewBody.innerHTML = '';
                previewBody.appendChild(fragment);
            })
            .catch(function(err) {
                console.error(err);
                showError();
            })
            .finally(function() {
                AppLoading.resetButton(btnTampilkan);
            });
    }

    filterForm.addEventListener('submit', function(event) {
        event.preventDefault();
        loadData();
    });

    if (btnCetakPdf) {
        btnCetakPdf.addEventListener('click', function() {
            const query = getFilterParams();
            window.open(pdfUrl + (query ? '?' + query : ''), '_blank');
        });
    }

    if (btnExportExcel) {
        btnExportExcel.addEventListener('click', function() {
            const query = getFilterParams();
            window.open(excelUrl + (query ? '?' + query : ''), '_blank');
        });
    }
}

function initCustomDropdown() {
    const trigger = document.getElementById('customDropdownTrigger');
    const menu = document.getElementById('customDropdownMenu');
    const select = document.getElementById('status_validasi');
    const options = document.querySelectorAll('.custom-dropdown-option');
    if (!trigger || !menu || !select) return;
    let isOpen = false;
    let originalParent = menu.parentElement;
    let placeholder = null;
    function positionMenu() {
        const r = trigger.getBoundingClientRect();
        menu.style.width = r.width + 'px';
        menu.style.left = r.left + 'px';
        menu.style.top = (r.bottom + 8) + 'px';
    }
    function openMenu() {
        if (isOpen) return;
        placeholder = document.createElement('div');
        placeholder.style.display = 'none';
        originalParent.insertBefore(placeholder, menu.nextSibling);
        document.body.appendChild(menu);
        menu.classList.add('dropdown-portal');
        trigger.classList.add('open');
        positionMenu();
        requestAnimationFrame(() => {
            menu.classList.add('open');
        });
        document.addEventListener('click', onDocClick);
        window.addEventListener('scroll', onReposition, true);
        window.addEventListener('resize', onReposition);
        isOpen = true;
    }
    function closeMenu() {
        if (!isOpen) return;
        menu.classList.remove('open');
        trigger.classList.remove('open');
        document.removeEventListener('click', onDocClick);
        window.removeEventListener('scroll', onReposition, true);
        window.removeEventListener('resize', onReposition);
        if (originalParent && placeholder) {
            originalParent.insertBefore(menu, placeholder);
            placeholder.remove();
        } else {
            originalParent.appendChild(menu);
        }
        menu.classList.remove('dropdown-portal');
        isOpen = false;
    }
    function onDocClick(e) {
        if (!menu.contains(e.target) && !trigger.contains(e.target)) {
            closeMenu();
        }
    }
    function onReposition() {
        if (!isOpen) return;
        positionMenu();
    }
    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        if (isOpen) closeMenu(); else openMenu();
    });
    options.forEach(function (opt) {
        opt.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            select.value = value;
            options.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            let iconHtml = '';
            if (value === 'layak') {
                iconHtml = '<div style="display:flex; align-items:center; gap:10px;"><div style="width:24px; height:24px; border-radius:50%; background:#dcfce7; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:11px;"><i class="fas fa-check"></i></div><span style="font-weight:700; color:#15803d; font-size:14px;">Layak / Disetujui</span></div>';
            } else if (value === 'tidak_layak') {
                iconHtml = '<div style="display:flex; align-items:center; gap:10px;"><div style="width:24px; height:24px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:11px;"><i class="fas fa-times"></i></div><span style="font-weight:700; color:#b91c1c; font-size:14px;">Tidak Layak / Perlu Revisi</span></div>';
            }
            const span = trigger.querySelector('span');
            if (span) span.innerHTML = iconHtml || 'Pilihan';
            closeMenu();
        });
    });
}

function initStarRatings() {
    document.querySelectorAll('.star-rating').forEach(function (group) {
        const name = group.getAttribute('data-name');
        const buttons = group.querySelectorAll('.star-btn');
        const input = document.getElementById('input-' + name);
        const display = document.getElementById('score-display-' + name);
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const value = parseInt(this.getAttribute('data-value'));
                if (input) input.value = value;
                buttons.forEach(function (b, idx) {
                    if (idx < value) {
                        b.classList.add('active');
                        b.style.color = '#f59e0b';
                    } else {
                        b.classList.remove('active');
                        b.style.color = 'rgba(0,0,0,.15)';
                    }
                });
                if (display) {
                    display.textContent = value + '/5';
                    display.style.color = value >= 4 ? '#10b981' : (value >= 3 ? '#f59e0b' : '#ef4444');
                }
            });
            btn.addEventListener('mouseenter', function () {
                const hv = parseInt(this.getAttribute('data-value'));
                buttons.forEach(function (b, idx) {
                    if (idx < hv) {
                        b.style.color = '#fbbf24';
                    }
                });
            });
            btn.addEventListener('mouseleave', function () {
                const cv = parseInt((input && input.value) || '0');
                buttons.forEach(function (b, idx) {
                    b.style.color = idx < cv ? '#f59e0b' : 'rgba(0,0,0,.15)';
                });
            });
        });
    });
}

function datepicker(initialDate = '') {
    return {
        show: false,
        showMonthPicker: false,
        showYearPicker: false,
        formattedDate: '',
        year: '',
        month: '',
        date: '',
        dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        days: [],
        blanks: [],
        years: [],
        yearSearch: '',

        init() {
            let today = initialDate ? new Date(initialDate) : new Date();
            this.year = today.getFullYear();
            this.month = today.getMonth();
            this.date = today.getDate();
            if (initialDate) {
                this.formattedDate = this.formatDate(today);
            }
            this.getDays();
            this.generateYears();
        },

        get filteredYears() {
            if (!this.yearSearch) return this.years;
            return this.years.filter(y => y.toString().includes(this.yearSearch));
        },

        generateYears() {
            const currentYear = new Date().getFullYear();
            this.years = [];
            for (let i = currentYear - 50; i <= currentYear + 10; i++) {
                this.years.push(i);
            }
        },

        isToday(date) {
            const today = new Date();
            const d = new Date(this.year, this.month, date);
            return today.toDateString() === d.toDateString();
        },

        isSelected(date) {
            if (!this.formattedDate) return false;
            const selected = new Date(this.formattedDate);
            const d = new Date(this.year, this.month, date);
            return selected.toDateString() === d.toDateString();
        },

        formatDate(date) {
            let d = date.getDate();
            let m = date.getMonth() + 1;
            let y = date.getFullYear();
            return `${y}-${m < 10 ? '0' + m : m}-${d < 10 ? '0' + d : d}`;
        },

        selectDate(date) {
            let selectedDate = new Date(this.year, this.month, date);
            this.formattedDate = this.formatDate(selectedDate);
            this.show = false;
        },

        getDays() {
            let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            let dayOfWeek = new Date(this.year, this.month).getDay();
            let blankdaysArray = [];
            for (let i = 1; i <= dayOfWeek; i++) { blankdaysArray.push(i); }
            let daysArray = [];
            for (let i = 1; i <= daysInMonth; i++) { daysArray.push(i); }
            this.blanks = blankdaysArray;
            this.days = daysArray;
        },

        nextMonth() {
            if (this.month == 11) {
                this.month = 0;
                this.year++;
            } else {
                this.month++;
            }
            this.getDays();
        },

        prevMonth() {
            if (this.month == 0) {
                this.month = 11;
                this.year--;
            } else {
                this.month--;
            }
            this.getDays();
        },

        toggleMonthPicker() {
            this.showMonthPicker = !this.showMonthPicker;
            this.showYearPicker = false;
        },

        toggleYearPicker() {
            this.showYearPicker = !this.showYearPicker;
            this.showMonthPicker = false;
        },

        selectMonth(m) {
            this.month = m;
            this.getDays();
            this.showMonthPicker = false;
        },

        selectYear(y) {
            this.year = parseInt(y);
            this.getDays();
            this.showYearPicker = false;
        }
    }
}

function initNotifikasi() {
    const notifBtn = document.getElementById('notificationBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const notifBadge = document.getElementById('notifBadge');
    const markAllReadBtn = document.getElementById('markAllRead');
    const expiryNotifications = readExpiryNotifications();

    if (!notifBtn || !notifDropdown) return;

    // Prevent double initialization
    if (notifBtn.dataset.initialized) return;
    notifBtn.dataset.initialized = 'true';

    // Toggle dropdown
    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('show');
        if (notifDropdown.classList.contains('show')) {
            fetchNotifications();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
            notifDropdown.classList.remove('show');
        }
    });

    // Mark all as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fetch('/api/notifikasi/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        // Segera kosongkan UI untuk pengalaman yang lebih responsif
                        renderNotifications([]);
                        updateBadge(0);
                        fetchNotifications();
                    }
                });
        });
    }

    // Fetch and render notifications
    function fetchNotifications() {
        fetch('/api/notifikasi')
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    renderNotifications(res.data);
                    updateBadge(res.unread_count);
                }
            })
            .catch(err => console.error('Gagal mengambil notifikasi:', err));
    }

    function updateBadge(count) {
        const totalCount = Number(count || 0) + expiryNotifications.length;

        if (totalCount > 0) {
            notifBadge.textContent = totalCount > 9 ? '9+' : totalCount;
            notifBadge.style.display = 'flex';
            if (markAllReadBtn) markAllReadBtn.style.display = Number(count || 0) > 0 ? 'block' : 'none';
        } else {
            notifBadge.style.display = 'none';
            if (markAllReadBtn) markAllReadBtn.style.display = 'none';
        }
    }

    function readExpiryNotifications() {
        const dataScript = document.getElementById('expiryNotificationsData')
            || document.getElementById('unitExpiryNotificationsData');

        if (!dataScript) return [];

        try {
            const parsed = JSON.parse(dataScript.textContent || '[]');
            const unique = new Map();

            parsed.forEach(item => {
                const key = item.system_id || `expiry-${item.id}`;

                if (!unique.has(key)) {
                    unique.set(key, item);
                }
            });

            return Array.from(unique.values());
        } catch (error) {
            console.error('Gagal membaca notifikasi masa aktif:', error);
            return [];
        }
    }

    function escapeNotifHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderNotifications(data) {
        const apiData = Array.isArray(data) ? data : [];
        const systemItems = expiryNotifications.map(item => ({
            ...item,
            id: item.system_id || `expiry-${item.id}`,
            is_system_expiry: true,
        }));
        const items = [...systemItems, ...apiData];

        if (items.length === 0) {
            notifList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>Tidak ada notifikasi baru</p>
                </div>
            `;
            return;
        }

        notifList.innerHTML = items.map(item => {
            if (item.is_system_expiry) {
                return `
                    <a href="${item.link || '#'}" class="notification-item unread notification-expiry-item" data-system-notification="true" data-system-id="${escapeNotifHtml(item.id)}">
                        <div class="notification-icon-wrapper" style="background:rgba(245, 158, 11, 0.12); color:#d97706;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="notification-content">
                            <span class="notification-sender">Masa Aktif Kerjasama</span>
                            <span class="notification-message">${escapeNotifHtml(item.title || 'Kerjasama Tanpa Judul')} akan berakhir ${escapeNotifHtml(item.remaining_label || '')}.</span>
                            <div class="notification-meta">
                                <span class="notification-time">Selesai ${escapeNotifHtml(item.end_date_label || '-')}</span>
                                <span class="notification-badge-type badge-masa-aktif">Masa Aktif</span>
                            </div>
                        </div>
                    </a>
                `;
            }

            const isUnread = item.is_read === 0;
            const timeAgoStr = timeAgo(new Date(item.created_at));
            const typeKey = (item.type || 'evaluasi').toString().toLowerCase();

            // Map type to badge class and label
            let typeBadgeClass = typeKey;
            let typeLabel = typeKey.replace(/_/g, ' ');
            switch (typeKey) {
                case 'revisi':
                    typeBadgeClass = 'revisi';
                    typeLabel = 'Perlu Revisi';
                    break;
                case 'sudah_revisi':
                    typeBadgeClass = 'sudah_revisi';
                    typeLabel = 'Sudah Direvisi';
                    break;
                case 'disahkan':
                    typeBadgeClass = 'disahkan';
                    typeLabel = 'Disahkan';
                    break;
                case 'evaluasi':
                    typeBadgeClass = 'evaluasi';
                    typeLabel = 'Evaluasi';
                    break;
                case 'pengajuan_mitra':
                    typeBadgeClass = 'pengajuan_mitra';
                    typeLabel = 'Pengajuan Mitra';
                    break;
                case 'data_baru':
                    typeBadgeClass = 'data_baru';
                    typeLabel = 'Data Baru';
                    break;
            }

            // Tentukan ikon & warna berdasarkan tipe notifikasi dan pengirim
            let icon = 'fa-building';
            let iconBg = 'rgba(79, 70, 229, 0.1)';
            let iconColor = 'var(--accent)';
            let senderName = '-';

            // Notifikasi dari Pimpinan (revisi / disahkan)
            if (typeKey === 'revisi') {
                icon = 'fa-pen-to-square';
                iconBg = 'rgba(245, 158, 11, 0.12)';
                iconColor = '#d97706';
            } else if (typeKey === 'sudah_revisi') {
                icon = 'fa-rotate';
                iconBg = 'rgba(6, 182, 212, 0.12)';
                iconColor = '#0891b2';
            } else if (typeKey === 'disahkan') {
                icon = 'fa-circle-check';
                iconBg = 'rgba(16, 185, 129, 0.12)';
                iconColor = '#059669';
            } else if (typeKey === 'pengajuan_mitra') {
                icon = 'fa-handshake-angle';
                iconBg = 'rgba(14, 165, 233, 0.12)';
                iconColor = '#0284c7';
                senderName = 'Portal Publik';
            } else if (typeKey === 'data_baru') {
                icon = 'fa-file-circle-plus';
                iconBg = 'rgba(16, 185, 129, 0.12)';
                iconColor = '#059669';
            }

            if (item.sender && item.sender.profile) {
                const profile = item.sender.profile;
                if (profile.jurusan) {
                    if (typeKey !== 'revisi' && typeKey !== 'disahkan' && typeKey !== 'sudah_revisi') {
                        icon = 'fa-book';
                        iconBg = 'rgba(124, 58, 237, 0.1)';
                        iconColor = 'var(--accent2)';
                    }
                    senderName = profile.jurusan.nama_jurusan;
                } else if (profile.unit_kerja || profile.unit_kerja_id || profile.unitKerja) {
                    if (typeKey !== 'revisi' && typeKey !== 'disahkan' && typeKey !== 'sudah_revisi') {
                        icon = 'fa-building';
                        iconBg = 'rgba(14, 165, 233, 0.1)';
                        iconColor = 'var(--accent3)';
                    }
                    const unit = profile.unit_kerja || profile.unitKerja;
                    senderName = unit ? unit.nama_unit_pelaksana : 'Unit Kerja';
                } else if (profile.upa) {
                    if (typeKey !== 'revisi' && typeKey !== 'disahkan' && typeKey !== 'sudah_revisi') {
                        icon = 'fa-sitemap';
                        iconBg = 'rgba(14, 165, 233, 0.12)';
                        iconColor = '#0284c7';
                    }
                    senderName = profile.upa.nama_upa;
                } else if (profile.pusat) {
                    if (typeKey !== 'revisi' && typeKey !== 'disahkan' && typeKey !== 'sudah_revisi') {
                        icon = 'fa-landmark';
                        iconBg = 'rgba(99, 102, 241, 0.12)';
                        iconColor = '#4f46e5';
                    }
                    senderName = profile.pusat.nama_pusat;
                }
            }

            // Fallback sender name if profile logic fails
            if (senderName === '-' && item.sender) {
                senderName = item.sender.name;
            }

            const contentBlock = `
                <span class="notification-sender">${escapeNotifHtml(senderName)}</span>
                <span class="notification-message">${escapeNotifHtml(item.pesan || '')}</span>
            `;

            return `
                <a href="${item.link || '#'}" class="notification-item ${isUnread ? 'unread' : ''}" data-id="${item.id}">
                    <div class="notification-icon-wrapper" style="background:${iconBg}; color:${iconColor};">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="notification-content">
                        ${contentBlock}
                        <div class="notification-meta">
                            <span class="notification-time">${timeAgoStr}</span>
                            <span class="notification-badge-type badge-${typeBadgeClass}">${typeLabel}</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');

        // Add click event to mark as read
        document.querySelectorAll('.notification-item').forEach(el => {
            el.addEventListener('click', function (e) {
                if (this.dataset.systemNotification === 'true') {
                    return;
                }

                const id = this.getAttribute('data-id');

                if (id) {
                    markAsRead(id);
                }
            });
        });
    }

    function markAsRead(id) {
        fetch(`/api/notifikasi/${id}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    // Segera hilangkan item dari UI
                    const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                    if (item) {
                        item.remove();
                        // Jika tidak ada lagi item, tampilkan state kosong
                        if (notifList.querySelectorAll('.notification-item').length === 0) {
                            renderNotifications([]);
                        }
                    }

                    fetchNotifications(); // Refresh to update count and empty state if needed
                }
            });
    }

    function timeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " tahun lalu";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " bulan lalu";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " hari lalu";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " jam lalu";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " menit lalu";
        return "Baru saja";
    }

    // Initial load for badge count
    fetchNotifications();

    // Poll for new notifications every 30 seconds (hanya jika belum ada interval)
    if (!window.notifInterval) {
        window.notifInterval = setInterval(fetchNotifications, 30000);
    }
}

// Jalankan saat pertama kali, setiap kali Turbo navigasi, dan sebagai fallback DOMContentLoaded
document.addEventListener('turbo:load', () => {
    initDashboard();
    initNotifikasi();
});

// Fallback jika Turbo tidak aktif
if (!window.Turbo) {
    document.addEventListener('DOMContentLoaded', () => {
        initDashboard();
        initNotifikasi();
    });
}


/* ==========================================================================
   EVALUASI & VALIDASI MODULE (ALPINE COMPONENT)
   ========================================================================== */
function registerAlpineComponents() {
    if (typeof Alpine === 'undefined') return;

    // Hanya daftarkan komponen jika belum ada (atau override dengan aman di Alpine 3)
    Alpine.data('evalDashboard', () => ({
        isLoading: true,
        isDetailLoading: false,
        activeTab: 'jurusan',
        activeId: null,
        comments: {},
        showErrors: {},
        status: '',

        init() {
            setTimeout(() => {
                this.isLoading = false;
            }, 600);
        },

        openDetail(id) {
            if (this.activeId === id) return;
            this.isDetailLoading = true;
            this.activeId = id;
            if (this.comments[id] === undefined) this.comments[id] = '';
            if (this.showErrors[id] === undefined) this.showErrors[id] = false;

            setTimeout(() => {
                this.isDetailLoading = false;
            }, 300);
        },

        appendComment(text, id) {
            let current = this.comments[id] || '';
            this.comments[id] = current.length > 0 ? current + ' ' + text : text;
            this.showErrors[id] = false;
        },

        handleAction(actionStatus, id) {
            this.status = actionStatus;

            // Both 'tidak_layak' and 'revisi' require a comment
            if ((actionStatus === 'tidak_layak' || actionStatus === 'revisi') && (!this.comments[id] || this.comments[id].trim() === '')) {
                this.showErrors[id] = true;
                setTimeout(() => { this.showErrors[id] = false; }, 3000);
                return;
            }

            // Use $nextTick to ensure Alpine updates the hidden input before submission
            this.$nextTick(() => {
                if (actionStatus === 'layak') {
                    this.triggerConfetti();
                    setTimeout(() => {
                        document.getElementById('form_' + id).submit();
                    }, 1500);
                } else {
                    document.getElementById('form_' + id).submit();
                }
            });
        },

        triggerConfetti() {
            if (typeof confetti === 'undefined') return;
            var duration = 2000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

            function randomInRange(min, max) { return Math.random() * (max - min) + min; }

            var interval = setInterval(function () {
                var timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) { return clearInterval(interval); }
                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }
    }));

    Alpine.data('evaluationForm', () => ({
        isLoading: true,
        comment: '',
        status: '',
        showError: false,

        init() {
            setTimeout(() => {
                this.isLoading = false;
            }, 800);
        },

        appendComment(text) {
            if (this.comment.length > 0) {
                this.comment += ' ' + text;
            } else {
                this.comment = text;
            }
            this.showError = false;
        },

        handleAction(actionStatus) {
            this.status = actionStatus;

            if (actionStatus === 'tidak_layak' && this.comment.trim() === '') {
                this.showError = true;
                setTimeout(() => { this.showError = false; }, 2000);
                return;
            }

            if (actionStatus === 'layak') {
                this.triggerConfetti();
                setTimeout(() => {
                    document.getElementById('evaluateForm').submit();
                }, 1200);
            } else {
                document.getElementById('evaluateForm').submit();
            }
        },

        triggerConfetti() {
            if (typeof confetti === 'undefined') return;
            var duration = 15 * 100;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

            function randomInRange(min, max) { return Math.random() * (max - min) + min; }

            var interval = setInterval(function () {
                var timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) { return clearInterval(interval); }
                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }
    }));
}

// Daftarkan saat awal (initial load)
document.addEventListener('alpine:init', registerAlpineComponents);

// Daftarkan ulang saat navigasi dengan Turbo Hotwired
document.addEventListener('turbo:load', () => {
    if (typeof Alpine !== 'undefined') {
        registerAlpineComponents();
    }
});

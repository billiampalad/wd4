function initDashboard() {
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
        }

        // Apply saved state on first load
        applySidebarState(localStorage.getItem('sidebar_collapsed') === '1');

        sidebarToggle.onclick = () => {
            applySidebarState(localStorage.getItem('sidebar_collapsed') !== '1');
        };
    }

    /* Sidebar Submenus */
    document.querySelectorAll('[data-submenu-toggle]').forEach(toggle => {
        const submenu = document.getElementById(toggle.dataset.submenuToggle);
        if (!submenu) return;

        toggle.onclick = (event) => {
            event.preventDefault();
            toggle.classList.toggle('submenu-open');
            submenu.classList.toggle('open');
        };
    });

    /* ─ Global search: filter tabel Data Kerjasama & Laporan ─ */
    const searchInput = document.getElementById('navSearchInput');
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
        const tables = document.querySelectorAll('#mainContent .um-table, main .um-table');
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
                    <label>Tampilkan</label>
                    <select class="entries-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label>data</label>
                `;
                
                // Find where to insert in header
                const title = header.querySelector('.um-title, .card-title');
                if (title && title.nextSibling) {
                    header.insertBefore(entriesWrap, title.nextSibling);
                } else {
                    header.appendChild(entriesWrap);
                }

                entriesWrap.querySelector('.entries-select').onchange = (e) => {
                    table.setAttribute('data-page-size', e.target.value);
                    table.setAttribute('data-current-page', '1');
                    updatePagination(table.querySelector('tbody'));
                };
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

            // Select all relevant table bodies
            const tables = Array.from(document.querySelectorAll('#mainContent .um-table tbody, main .um-table tbody'));
            
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
    }

    initTableFeatures();

    /* ─ Logout confirm ─ */
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.onclick = (e) => {
            e.preventDefault();
            const form = logoutBtn.closest('form');
            if (!form) return;

            if (typeof Swal !== 'undefined') {
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
            } else {
                if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                    form.submit();
                }
            }
        };
    }

    /* ─ Global Delete Confirm with SweetAlert ─ */
    if (typeof Swal !== 'undefined') {
        document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
            const originalOnSubmit = form.getAttribute('onsubmit');
            if (originalOnSubmit && originalOnSubmit.includes('confirm')) {
                const match = originalOnSubmit.match(/confirm\(['"](.+)['"]\)/);
                const message = match ? match[1] : 'Yakin ingin melanjutkan?';

                form.removeAttribute('onsubmit');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c3aed',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    }

    /* Live preview (untuk halaman create/edit user) */
    const createForm = document.querySelector('form[action*="users"]');
    if (createForm && document.getElementById('previewAvatar')) {
        updateProfileFields();
        updatePreview();
        // Tambahkan event listener untuk input
        const inputs = createForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.oninput = function() {
                refreshUserForm(input);
            };
            input.onchange = function() {
                refreshUserForm(input);
            };
        });
    }
}

// Global functions for modals and preview
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }
}

function togglePass(btnOrId) {
    // Jika argumen adalah element (untuk halaman list user)
    if (btnOrId && typeof btnOrId === 'object') {
        const wrap = btnOrId.closest('.um-pass-wrap');
        if (wrap) {
            const dots = wrap.querySelector('.um-pass-dots');
            const real = wrap.querySelector('.um-pass-real');
            const icon = btnOrId.querySelector('i');

            if (dots && real) {
                const isHidden = real.style.display === 'none';
                dots.style.display = isHidden ? 'none' : 'inline';
                real.style.display = isHidden ? 'inline' : 'none';
                if (icon) {
                    icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
                }
            }
        }
        return;
    }

    // Jika argumen adalah ID atau kosong (untuk halaman create/edit)
    const id = btnOrId || 'password';
    const el = document.getElementById(id);
    const eye = document.getElementById('passEye') || (el ? document.getElementById(el.id + 'Eye') : null);

    if (el) {
        const isPass = el.type === 'password';
        el.type = isPass ? 'text' : 'password';
        if (eye) {
            eye.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
        }
    }
}

function adminUserSelect(config) {
    return {
        open: false,
        disabled: false,
        placeholder: config.placeholder || 'Pilih data',
        selectedValue: config.selectedValue || '',
        selectedLabel: '',
        items: config.items || [],

        init() {
            this.syncLabel();
        },

        toggle() {
            if (this.disabled) return;
            this.open = !this.open;
        },

        choose(item) {
            if (this.disabled) return;
            this.selectedValue = item.value;
            this.selectedLabel = item.label;
            this.open = false;

            this.$nextTick(() => {
                const select = this.$root.querySelector('select');
                if (!select) return;

                select.value = this.selectedValue;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },

        syncFromNative() {
            this.syncLabel();
        },

        syncLabel() {
            const selected = this.items.find(item => item.value === this.selectedValue);
            this.selectedLabel = selected ? selected.label : '';
        },

        setDisabled(isDisabled) {
            this.disabled = isDisabled;
            if (isDisabled) {
                this.open = false;
            }
        },
    };
}

function getSelectedOptionText(selectId) {
    const el = document.getElementById(selectId);
    if (!el || el.disabled || !el.value) return '';

    return (el.options[el.selectedIndex]?.text ?? '').trim();
}

function getSelectedRoleName() {
    const roleEl = document.getElementById('role_id');
    if (!roleEl || !roleEl.value) return '';

    const selected = roleEl.options[roleEl.selectedIndex];
    const roleName = (selected?.dataset.roleName || selected?.text || '')
        .trim()
        .toLowerCase()
        .replace(/[\s-]+/g, '_');

    return roleName === 'humas' ? 'unit_kerja' : roleName;
}

function updateProfileFields() {
    const fields = document.querySelectorAll('[data-profile-field]');
    const previewRows = document.querySelectorAll('[data-preview-field]');
    const pointer = document.getElementById('profileRolePointer');
    if (!fields.length && !previewRows.length) return;

    const roleName = getSelectedRoleName();
    const visibleFields = {
        pimpinan: ['jabatan'],
        admin: ['jabatan'],
        jurusan: ['jabatan', 'jurusan'],
        unit_kerja: ['jabatan', 'unit'],
        upa: ['jabatan', 'upa'],
        pusat: ['jabatan', 'pusat'],
    }[roleName] || ['jabatan', 'jurusan', 'unit', 'upa', 'pusat'];

    fields.forEach(field => {
        const isVisible = visibleFields.includes(field.dataset.profileField);
        const controls = field.querySelectorAll('input, select, textarea');

        field.hidden = !isVisible;
        field.querySelectorAll('.uc-alpine-select').forEach(selectWrap => {
            if (selectWrap._x_dataStack?.[0]?.setDisabled) {
                selectWrap._x_dataStack[0].setDisabled(!isVisible);
            }
        });

        controls.forEach(control => {
            control.disabled = !isVisible;
            if (!isVisible && control.value !== '') {
                control.value = '';
                control.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });

    previewRows.forEach(row => {
        row.hidden = !visibleFields.includes(row.dataset.previewField);
    });

    if (pointer) {
        const messages = {
            pimpinan: 'Role pimpinan hanya dapat mengisi Jabatan. Nama Jurusan dan Nama Unit tidak digunakan untuk role ini.',
            admin: 'Role admin hanya dapat mengisi Jabatan. Nama Jurusan dan Nama Unit tidak digunakan untuk role ini.',
            jurusan: 'Role jurusan dapat mengisi Jabatan dan Nama Jurusan. Nama Unit tidak digunakan untuk role ini.',
            unit_kerja: 'Role unit kerja dapat mengisi Jabatan dan Nama Unit. Nama Jurusan tidak digunakan untuk role ini.',
            upa: 'Role upa dapat mengisi Jabatan dan Nama Upa. Nama Jurusan, Nama Unit, dan Nama Pusat tidak digunakan untuk role ini.',
            pusat: 'Role pusat dapat mengisi Jabatan dan Nama Pusat. Nama Jurusan, Nama Unit, dan Nama Upa tidak digunakan untuk role ini.',
        };

        pointer.innerHTML = '<i class="fas fa-circle-info"></i><span>' +
            (messages[roleName] || 'Pilih role terlebih dahulu untuk melihat form profil yang dapat digunakan.') +
            '</span>';
    }
}

function refreshUserForm(input) {
    updateProfileFields();

    if (input && input.id === 'password') {
        checkStrength(input.value);
        return;
    }

    updatePreview();
}

function resetPreview() {
    setTimeout(() => {
        document.querySelectorAll('.uc-native-select').forEach(select => {
            select.dispatchEvent(new Event('change', { bubbles: true }));
        });
        updateProfileFields();
        updatePreview();
        checkStrength('');
    }, 0);
}

function restorePreview() {
    resetPreview();
}

function updatePreview() {
    const nameInput = document.getElementById('name');
    const nikInput = document.getElementById('nik');
    const roleEl = document.getElementById('role_id');
    const jabatanInput = document.getElementById('jabatan');

    if (!nameInput) return;

    const name = nameInput.value.trim();
    const nik = nikInput ? nikInput.value.trim() : '';
    const jabatan = jabatanInput && !jabatanInput.disabled ? jabatanInput.value.trim() : '';
    const jurusan = getSelectedOptionText('jurusan_id');
    const unit = getSelectedOptionText('unit_kerja_id');
    const upa = getSelectedOptionText('upa_id');
    const pusat = getSelectedOptionText('pusat_id');
    const roleText = roleEl ? (roleEl.options[roleEl.selectedIndex]?.text ?? '') : '';

    const previewAvatar = document.getElementById('previewAvatar');
    if (previewAvatar) {
        const initials = name.length >= 2
            ? (name.split(' ').length > 1
                ? (name.split(' ')[0][0] + name.split(' ')[1][0]).toUpperCase()
                : name.substring(0, 2).toUpperCase())
            : (name.length === 1 ? name[0].toUpperCase() + '?' : '??');
        previewAvatar.textContent = initials;
    }

    if (document.getElementById('previewName')) document.getElementById('previewName').textContent = name || 'Nama Pengguna';
    if (document.getElementById('previewNik')) document.getElementById('previewNik').textContent = nik ? 'NIK: ' + nik : 'NIK: —';
    if (document.getElementById('previewJabatan')) document.getElementById('previewJabatan').textContent = jabatan || '—';
    if (document.getElementById('previewJurusan')) document.getElementById('previewJurusan').textContent = jurusan || '—';
    if (document.getElementById('previewUnit')) document.getElementById('previewUnit').textContent = unit || '—';
    if (document.getElementById('previewUpa')) document.getElementById('previewUpa').textContent = upa || '—';
    if (document.getElementById('previewPusat')) document.getElementById('previewPusat').textContent = pusat || '—';

    const badge = document.getElementById('previewRole');
    if (badge) {
        if (roleEl && roleEl.value) {
            badge.textContent = roleText;
            badge.style.background = 'rgba(79,70,229,.12)';
            badge.style.color = '#4f46e5';
        } else {
            badge.textContent = 'Role Belum Dipilih';
            badge.style.background = 'rgba(100,116,139,.1)';
            badge.style.color = '#64748b';
        }
    }
}

function checkStrength(val) {
    updatePreview();
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    if (fill && label) {
        const map = [
            { w: '0%', bg: 'transparent', text: '', color: 'transparent' },
            { w: '25%', bg: '#ef4444', text: 'Sangat Lemah', color: '#ef4444' },
            { w: '50%', bg: '#f59e0b', text: 'Cukup', color: '#f59e0b' },
            { w: '75%', bg: '#0ea5e9', text: 'Kuat', color: '#0ea5e9' },
            { w: '100%', bg: '#10b981', text: 'Sangat Kuat', color: '#10b981' },
        ];
        const m = val.length === 0 ? map[0] : map[score] ?? map[1];
        fill.style.width = m.w;
        fill.style.background = m.bg;
        label.textContent = m.text;
        label.style.color = m.color;
    }
}

// Jalankan saat pertama kali dan setiap kali Turbo navigasi
document.addEventListener('turbo:load', initDashboard);

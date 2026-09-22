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



    /* ─ Logout confirm ─ */
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.onclick = (e) => {
            e.preventDefault();
            const form = logoutBtn.closest('form');
            if (!form) return;

            if (window.CustomAlert) {
                CustomAlert.confirm({
                    title: 'Konfirmasi Keluar',
                    message: 'Apakah Anda yakin ingin keluar dari sistem?',
                    type: 'danger',
                    confirmText: 'Keluar',
                    confirmColor: 'danger',
                    onConfirm: () => form.submit()
                });
            } else if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                form.submit();
            }
        };
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

    /* User Detail Tab Auto-Restore */
    initUserDetail();

    /* Dynamic Notifications */
    initNotifikasi();
}

/**
 * ─── Universal Admin Modal Helper ───
 */
const AdminModal = {
    open(modalId, options = {}) {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (!modal) return;

        if (options.resetForm) {
            const form = modal.querySelector('form');
            if (form) form.reset();
        }

        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            modal.classList.add('active');
            // Auto focus first input / textarea / select
            if (options.autoFocus !== false) {
                const focusEl = modal.querySelector('input:not([type="hidden"]), textarea, select');
                if (focusEl) focusEl.focus();
            }
        }, 10);
    },

    close(modalId) {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (!modal) return;

        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 250);
    },

    initGlobalListeners() {
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const activeModals = document.querySelectorAll('.adm-modal-overlay.active, .role-modal-overlay.active, .jkerjasama-modal-overlay.active, .premium-modal-overlay.active');
                activeModals.forEach(modal => AdminModal.close(modal));
            }
        });

        // Close on overlay backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('adm-modal-overlay') || 
                e.target.classList.contains('role-modal-overlay') || 
                e.target.classList.contains('jkerjasama-modal-overlay')) {
                AdminModal.close(e.target);
            }
        });
    }
};

// Global backward-compatible aliases
function openModal(id, options) {
    AdminModal.open(id, options);
}

function closeModal(id) {
    AdminModal.close(id);
}

// Initialize global modal listeners immediately
AdminModal.initGlobalListeners();

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

function adminUserSelect(config = {}) {
    return {
        open: false,
        disabled: false,
        placeholder: config.placeholder || 'Pilih data',
        selectedValue: config.selectedValue || '',
        selectedLabel: '',
        allItems: config.items || [],
        items: config.items || [],
        lastJurusanId: undefined,

        init() {
            if (config.filterJurusan && config.currentJurusanId) {
                this.items = this.allItems.filter(item => String(item.jurusan_id) === String(config.currentJurusanId));
            }
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

            const select = this.$root ? this.$root.querySelector('select') : null;
            if (select) {
                select.value = this.selectedValue;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },

        filterByJurusan(jurusanId) {
            if (this.lastJurusanId === jurusanId) return;
            this.lastJurusanId = jurusanId;

            if (!jurusanId) {
                this.items = [];
                this.selectedValue = '';
                this.selectedLabel = '';
            } else {
                this.items = this.allItems.filter(item => String(item.jurusan_id) === String(jurusanId));
                if (!this.items.some(item => item.value === this.selectedValue)) {
                    this.selectedValue = '';
                    this.selectedLabel = '';
                } else {
                    this.syncLabel();
                }
            }

            const select = this.$root ? this.$root.querySelector('select') : null;
            if (select) {
                select.value = this.selectedValue;
            }
        },

        syncFromNative() {
            this.syncLabel();
        },

        syncLabel() {
            const selected = this.items.find(item => item.value === this.selectedValue) || this.allItems.find(item => item.value === this.selectedValue);
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

let isUpdatingProfileFields = false;

function updateProfileFields() {
    if (isUpdatingProfileFields) return;
    isUpdatingProfileFields = true;

    try {
        const fields = document.querySelectorAll('[data-profile-field]');
        const previewRows = document.querySelectorAll('[data-preview-field]');
        const pointer = document.getElementById('profileRolePointer');
        if (!fields.length && !previewRows.length) return;

        const roleName = getSelectedRoleName();
        const visibleFields = {
            pimpinan: ['jabatan'],
            admin: ['jabatan'],
            mitra: ['jabatan'],
            jurusan: ['jabatan', 'jurusan'],
            prodi: ['jabatan', 'jurusan'],
            unit_kerja: ['jabatan', 'unit'],
            upa: ['jabatan', 'upa'],
            pusat: ['jabatan', 'pusat'],
        }[roleName] || [];

        fields.forEach(field => {
            const isVisible = visibleFields.includes(field.dataset.profileField);
            const controls = field.querySelectorAll('input, select, textarea');

            field.hidden = !isVisible;
            field.style.display = isVisible ? '' : 'none';

            field.querySelectorAll('.uc-alpine-select').forEach(selectWrap => {
                if (selectWrap._x_dataStack?.[0]?.setDisabled) {
                    selectWrap._x_dataStack[0].setDisabled(!isVisible);
                }
            });

            controls.forEach(control => {
                control.disabled = !isVisible;
                if (!isVisible && control.value !== '') {
                    control.value = '';
                }
            });
        });

        // Handle dependent Jurusan -> Prodi for role 'prodi'
        if (roleName === 'prodi') {
            const jurusanSelect = document.getElementById('jurusan_id');
            const prodiField = document.querySelector('[data-profile-field="prodi"]');
            const previewProdiRow = document.querySelector('[data-preview-field="prodi"]');
            const jurusanId = jurusanSelect ? jurusanSelect.value : '';
            const shouldShowProdi = Boolean(jurusanId);

            if (prodiField) {
                const prodiAlpine = prodiField.querySelector('.uc-alpine-select');
                if (prodiAlpine?._x_dataStack?.[0]?.filterByJurusan) {
                    prodiAlpine._x_dataStack[0].filterByJurusan(jurusanId);
                }

                prodiField.hidden = !shouldShowProdi;
                prodiField.style.display = shouldShowProdi ? '' : 'none';

                const controls = prodiField.querySelectorAll('input, select, textarea');
                controls.forEach(control => {
                    control.disabled = !shouldShowProdi;
                    if (!shouldShowProdi && control.value !== '') {
                        control.value = '';
                    }
                });

                if (prodiAlpine?._x_dataStack?.[0]?.setDisabled) {
                    prodiAlpine._x_dataStack[0].setDisabled(!shouldShowProdi);
                }
            }

            if (previewProdiRow) {
                previewProdiRow.hidden = !shouldShowProdi;
                previewProdiRow.style.display = shouldShowProdi ? '' : 'none';
            }
        }

        previewRows.forEach(row => {
            if (row.dataset.previewField === 'prodi') {
                const jurusanSelect = document.getElementById('jurusan_id');
                const jurusanId = jurusanSelect ? jurusanSelect.value : '';
                const shouldShow = roleName === 'prodi' && Boolean(jurusanId);
                row.hidden = !shouldShow;
                row.style.display = shouldShow ? '' : 'none';
            } else {
                const isVisible = visibleFields.includes(row.dataset.previewField);
                row.hidden = !isVisible;
                row.style.display = isVisible ? '' : 'none';
            }
        });

        if (pointer) {
            const messages = {
                pimpinan: 'Role pimpinan hanya dapat mengisi Jabatan. Nama Jurusan, Program Studi, dan Unit tidak digunakan untuk role ini.',
                admin: 'Role admin hanya dapat mengisi Jabatan. Nama Jurusan, Program Studi, dan Unit tidak digunakan untuk role ini.',
                mitra: 'Role Mitra DUDIKA hanya dapat mengisi Jabatan pada institusi/perusahaan mitra.',
                jurusan: 'Role Jurusan dapat mengisi Jabatan dan Nama Jurusan. Nama Program Studi dan Unit tidak digunakan untuk role ini.',
                prodi: 'Role Prodi dapat mengisi Jabatan, Nama Jurusan, dan Program Studi terkait. Pilihan Program Studi akan disesuaikan otomatis dengan Jurusan yang dipilih.',
                unit_kerja: 'Role unit kerja dapat mengisi Jabatan dan Nama Unit. Nama Jurusan tidak digunakan untuk role ini.',
                upa: 'Role upa dapat mengisi Jabatan dan Nama Upa. Nama Jurusan, Nama Unit, dan Nama Pusat tidak digunakan untuk role ini.',
                pusat: 'Role pusat dapat mengisi Jabatan dan Nama Pusat. Nama Jurusan, Nama Unit, dan Nama Upa tidak digunakan untuk role ini.',
            };

            pointer.innerHTML = '<i class="fas fa-circle-info"></i><span>' +
                (messages[roleName] || 'Pilih role terlebih dahulu untuk melihat form profil yang dapat digunakan.') +
                '</span>';
        }
    } finally {
        isUpdatingProfileFields = false;
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
    const prodi = getSelectedOptionText('prodi_id');
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
    if (document.getElementById('previewProdi')) document.getElementById('previewProdi').textContent = prodi || '—';
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

    updateStepsGuide();
}

function updateStepsGuide() {
    const stepsCard = document.querySelector('.uc-steps-card');
    if (!stepsCard) return;

    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const step4 = document.getElementById('step4');

    if (!step1 || !step2 || !step3 || !step4) return;

    // 1. Identitas (Nama & NIK, dan Email)
    const nameVal = document.getElementById('name')?.value.trim() || '';
    const nikVal = document.getElementById('nik')?.value.trim() || '';
    const emailVal = document.getElementById('email')?.value.trim() || '';
    const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal);
    const isStep1Done = (nameVal.length >= 2) && (nikVal.length >= 3) && (emailVal.length > 0 ? isEmailValid : false);

    // 2. Role
    const roleEl = document.getElementById('role_id');
    const isStep2Done = Boolean(roleEl && roleEl.value);

    // 3. Password
    const passVal = document.getElementById('password')?.value || '';
    const isStep3Done = passVal.length >= 8;

    // 4. Data Profil
    let isStep4Done = false;
    if (isStep2Done) {
        const roleName = getSelectedRoleName();
        switch (roleName) {
            case 'jurusan':
                isStep4Done = Boolean(document.getElementById('jurusan_id')?.value);
                break;
            case 'prodi':
                isStep4Done = Boolean(document.getElementById('jurusan_id')?.value && document.getElementById('prodi_id')?.value);
                break;
            case 'unit_kerja':
                isStep4Done = Boolean(document.getElementById('unit_kerja_id')?.value);
                break;
            case 'upa':
                isStep4Done = Boolean(document.getElementById('upa_id')?.value);
                break;
            case 'pusat':
                isStep4Done = Boolean(document.getElementById('pusat_id')?.value);
                break;
            case 'pimpinan':
            case 'admin':
            case 'mitra':
            default:
                isStep4Done = true;
                break;
        }
    }

    const steps = [
        { el: step1, done: isStep1Done, num: 1 },
        { el: step2, done: isStep2Done, num: 2 },
        { el: step3, done: isStep3Done, num: 3 },
        { el: step4, done: isStep4Done, num: 4 },
    ];

    let activeFound = false;
    steps.forEach(step => {
        const dot = step.el.querySelector('.uc-step-dot');
        step.el.classList.remove('uc-step-done', 'uc-step-active');

        if (step.done) {
            step.el.classList.add('uc-step-done');
            if (dot && dot.innerHTML !== '<i class="fas fa-check"></i>') {
                dot.innerHTML = '<i class="fas fa-check"></i>';
            }
        } else {
            if (!activeFound) {
                step.el.classList.add('uc-step-active');
                activeFound = true;
            }
            if (dot && dot.textContent !== String(step.num)) {
                dot.textContent = step.num;
            }
        }
    });
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
document.addEventListener('DOMContentLoaded', initDashboard);

/* ─ User Detail Page: Tabs & Clipboard ─ */
let udToastTimer = null;

function switchTab(tabId, btnElement) {
    const panes = document.querySelectorAll('.ud-tab-pane');
    panes.forEach(pane => pane.classList.remove('active'));

    const buttons = document.querySelectorAll('.ud-tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    const targetPane = document.getElementById(tabId);
    if (targetPane) {
        targetPane.classList.add('active');
    }
    if (btnElement) {
        btnElement.classList.add('active');
    }

    sessionStorage.setItem('ud_active_tab', tabId);
}

function copyToClipboard(text, message) {
    if (!text || text === '-' || text === '—') return;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast(message || 'Teks berhasil disalin!');
        }).catch(() => {
            fallbackCopyToClipboard(text, message);
        });
    } else {
        fallbackCopyToClipboard(text, message);
    }
}

function fallbackCopyToClipboard(text, message) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.left = '-999999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    try {
        document.execCommand('copy');
        showToast(message || 'Teks berhasil disalin!');
    } catch (err) {
        console.error('Gagal menyalin teks:', err);
    }
    document.body.removeChild(textarea);
}

function showToast(text) {
    const toast = document.getElementById('udToast');
    const toastText = document.getElementById('udToastText');
    if (!toast || !toastText) return;

    toastText.innerText = text;
    toast.classList.add('show');

    if (udToastTimer) clearTimeout(udToastTimer);
    udToastTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

function initUserDetail() {
    const savedTab = sessionStorage.getItem('ud_active_tab');
    if (savedTab && document.getElementById(savedTab)) {
        const btn = document.querySelector(`.ud-tab-btn[data-tab="${savedTab}"]`);
        if (btn) {
            switchTab(savedTab, btn);
        }
    }
}

/* ── Admin Dynamic Notifications ── */
function initNotifikasi() {
    const notifBtn = document.getElementById('notificationBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const notifBadge = document.getElementById('notifBadge');
    const markAllReadBtn = document.getElementById('markAllRead');

    if (!notifBtn || !notifDropdown) return;

    // Prevent double initialization
    if (notifBtn.dataset.initialized === 'true') return;
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
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    renderNotifications([]);
                    updateBadge(0);
                    fetchNotifications();
                }
            })
            .catch(err => console.error('Gagal menandai semua notifikasi dibaca:', err));
        });
    }

    // Fetch and render notifications
    function fetchNotifications() {
        fetch('/api/notifikasi', {
            headers: {
                'Accept': 'application/json'
            }
        })
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
        const totalCount = Number(count || 0);
        if (notifBadge) {
            if (totalCount > 0) {
                notifBadge.textContent = totalCount > 9 ? '9+' : totalCount;
                notifBadge.style.display = 'flex';
                if (markAllReadBtn) markAllReadBtn.style.display = 'block';
            } else {
                notifBadge.style.display = 'none';
                if (markAllReadBtn) markAllReadBtn.style.display = 'none';
            }
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
        if (!notifList) return;
        const items = Array.isArray(data) ? data : [];

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
            const senderName = item.sender ? (item.sender.name || item.sender.email) : 'Sistem Kerjasama';
            const unreadClass = item.is_read ? '' : 'unread';
            const timeAgo = item.time_ago || (item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID') : 'Baru saja');
            const targetUrl = item.link || item.url || '/admin/mitra';
            const itemTitle = item.title || 'Pengajuan Kerjasama Baru';
            const itemMsg = item.message || '';

            return `
                <a href="${escapeNotifHtml(targetUrl)}" class="notification-item ${unreadClass}" data-id="${item.id}">
                    <div class="notification-icon-wrapper icon-pengajuan">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="notification-content">
                        <span class="notification-sender">${escapeNotifHtml(senderName)}</span>
                        <span class="notification-message">${escapeNotifHtml(itemMsg)}</span>
                        <div class="notification-meta">
                            <span class="notification-time">${escapeNotifHtml(timeAgo)}</span>
                            <span class="notification-badge-type">Pengajuan</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');

        // Event listener saat item notifikasi diklik
        notifList.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                const notifId = this.dataset.id;
                if (notifId) {
                    fetch(`/api/notifikasi/${notifId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).catch(err => console.error('Gagal update status dibaca:', err));
                }
            });
        });
    }

    // Initial fetch on load
    fetchNotifications();
}

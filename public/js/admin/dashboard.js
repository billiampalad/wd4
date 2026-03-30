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

    /* ─ Data Master Submenu ─ */
    const dmBtn2 = document.getElementById('dataMasterBtn');
    const dmSub = document.getElementById('dataMasterSub');

    if (dmBtn2 && dmSub) {
        dmBtn2.onclick = (e) => {
            e.preventDefault();
            dmBtn2.classList.toggle('submenu-open');
            dmSub.classList.toggle('open');
        };
    }

    /* ─ Show navSearch on wider screens ─ */
    const navSearch = document.getElementById('navSearch');
    if (navSearch) {
        function checkSearch() {
            navSearch.style.display = window.innerWidth > 900 ? 'flex' : 'none';
        }
        checkSearch();
        window.onresize = checkSearch;
    }

    /* ─ Logout confirm ─ */
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.onclick = () => {
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                // Biarkan form logout submit jika ada, atau redirect
            }
        };
    }

    /* Live preview (untuk halaman create/edit user) */
    const createForm = document.querySelector('form[action*="users"]');
    if (createForm && document.getElementById('previewAvatar')) {
        updatePreview();
        // Tambahkan event listener untuk input
        const inputs = createForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.oninput = updatePreview;
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

function updatePreview() {
    const nameInput = document.getElementById('name');
    const nikInput = document.getElementById('nik');
    const roleEl = document.getElementById('role_id');
    const jabatanInput = document.getElementById('jabatan');
    const jurusanInput = document.getElementById('nama_jurusan');
    const unitInput = document.getElementById('nama_unit');

    if (!nameInput) return;

    const name = nameInput.value.trim();
    const nik = nikInput ? nikInput.value.trim() : '';
    const jabatan = jabatanInput ? jabatanInput.value.trim() : '';
    const jurusan = jurusanInput ? jurusanInput.value.trim() : '';
    const unit = unitInput ? unitInput.value.trim() : '';
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
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
    const searchInput = document.getElementById('navSearchInput');
    if (navSearch) {
        function checkSearch() {
            navSearch.style.display = window.innerWidth > 900 ? 'flex' : 'none';
        }
        checkSearch();
        window.onresize = checkSearch;
    }

    /* ─ Global search: filter tabel Data Kerjasama & Laporan ─ */
    if (searchInput) {
        function filterTableBySearch() {
            const q = (searchInput.value || '').trim().toLowerCase();
            const tables = [
                document.querySelector('#mainContent .um-table tbody'),  // Data Kerjasama
                document.getElementById('previewBody')                   // Laporan preview
            ].filter(Boolean);

            tables.forEach(tbody => {
                if (!tbody) return;
                const rows = tbody.querySelectorAll('tr.um-row');
                const emptyRow = tbody.querySelector('tr.um-empty, tr[data-empty]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent || '';
                    const match = !q || text.toLowerCase().includes(q);
                    row.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

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
                } else {
                    if (noResultsRow) noResultsRow.style.display = 'none';
                    if (emptyRow && !q) emptyRow.style.display = '';
                }
            });
        }

        searchInput.addEventListener('input', filterTableBySearch);
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                filterTableBySearch();
                searchInput.blur();
            }
        });
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

    /* ─ Dashboard Charts ─ */
    initCharts();

    /* ─ Laporan Data ─ */
    initLaporan();
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

    const previewUrl = filterForm.getAttribute('data-preview-url');
    const pdfUrl = filterForm.getAttribute('data-pdf-url');
    const excelUrl = filterForm.getAttribute('data-excel-url');

    function getFilterParams() {
        const formData = new FormData(filterForm);
        return new URLSearchParams(formData).toString();
    }

    if (btnTampilkan) {
        btnTampilkan.addEventListener('click', function () {
            previewBody.innerHTML = '<tr><td colspan="6" class="text-center py-4" style="color:var(--text-sub);"><i class="fas fa-spinner fa-spin me-2" style="color:var(--accent);"></i> Memuat data...</td></tr>';

            fetch(`${previewUrl}?${getFilterParams()}`)
                .then(response => response.json())
                .then(data => {
                    previewBody.innerHTML = '';
                    if (data.length === 0) {
                        previewBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state" style="padding: 30px 0;">
                                        <div class="um-empty-icon">
                                            <i class="fas fa-folder-open" style="font-size: 28px; opacity: 0.3; color: var(--text-sub);"></i>
                                        </div>
                                        <p class="um-empty-title">Tidak ada data</p>
                                        <p class="um-empty-sub">Silakan sesuaikan filter pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    const formatTgl = (tglStr) => {
                        if (!tglStr) return '-';
                        const date = new Date(tglStr);
                        if (isNaN(date.getTime())) return tglStr;
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        return `${String(date.getDate()).padStart(2, '0')} ${months[date.getMonth()]} ${date.getFullYear()}`;
                    };

                    data.forEach((item, index) => {
                        // Status yang berasal dari backend: draft | menunggu_evaluasi | revisi | selesai
                        // (tetap toleran terhadap value lama "menunggu")
                        let statusColor = 'orange';
                        let statusIconColor = '#f59e0b';
                        let statusBg = 'rgba(245,158,11,.12)';
                        let statusLabel = 'Draft';

                        if (item.status === 'selesai') {
                            statusColor = 'green';
                            statusIconColor = '#10b981';
                            statusBg = 'rgba(16,185,129,.12)';
                            statusLabel = 'Selesai/Layak';
                        // } else if (item.status === 'revisi') {
                        //     statusColor = 'red';
                        //     statusIconColor = '#ef4444';
                        //     statusBg = 'rgba(239,68,68,.12)';
                        //     statusLabel = 'Revisi';
                        } else if (item.status === 'menunggu' || item.status === 'menunggu_evaluasi') {
                            statusColor = 'blue';
                            statusIconColor = '#0ea5e9';
                            statusBg = 'rgba(14,165,233,.12)';
                            statusLabel = 'Menunggu Evaluasi';
                        }

                        const statusStyle = `background: ${statusBg}; color: ${statusIconColor}; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;`;

                        let mitraHtml = '<span class="um-meta">-</span>';
                        if (item.mitras && item.mitras.length > 0) {
                            const allMitras = item.mitras.map(m => m.nama_mitra).join(', ');
                            const firstMitra = item.mitras[0].nama_mitra;
                            const extra = item.mitras.length > 1 ? ` +${item.mitras.length - 1} mitra lainnya` : '';
                            mitraHtml = `<span class="um-meta" title="${allMitras}">${firstMitra}${extra}</span>`;
                        }

                        const tglMulai = formatTgl(item.periode_mulai);
                        const tglSelesai = formatTgl(item.periode_selesai);

                        const row = `
                            <tr class="um-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num">${String(index + 1).padStart(3, '0')}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-name">${item.nama_kegiatan || '-'}</span>
                                </td>
                                <td class="um-td">
                                    <span class="tag tag-purple" style="font-size: 11px;">
                                        <i class="fas fa-handshake" style="font-size:9px; margin-right:4px;"></i>
                                        ${item.jenis_kerjasama && item.jenis_kerjasama.length > 0 ? item.jenis_kerjasama.map(j => j.nama_kerjasama).join(', ') : '-'}
                                    </span>
                                </td>
                                <td class="um-td">
                                    ${mitraHtml}
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">${tglMulai} s/d ${tglSelesai}</span>
                                </td>
                                <td class="um-td">
                                    <span class="tag tag-${statusColor}" style="${statusStyle}">
                                        <i class="fas fa-circle" style="font-size:6px;"></i> ${statusLabel}
                                    </span>
                                </td>
                            </tr>
                        `;
                        previewBody.insertAdjacentHTML('beforeend', row);
                    });
                    // Terapkan filter search jika ada input
                    const searchInput = document.getElementById('navSearchInput');
                    if (searchInput && searchInput.value.trim()) {
                        searchInput.dispatchEvent(new Event('input'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    previewBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>';
                });
        });

        // Trigger on load
        btnTampilkan.click();
    }

    if (btnCetakPdf) {
        btnCetakPdf.addEventListener('click', function () {
            window.open(`${pdfUrl}?${getFilterParams()}`, '_blank');
        });
    }

    if (btnExportExcel) {
        btnExportExcel.addEventListener('click', function () {
            window.location.href = `${excelUrl}?${getFilterParams()}`;
        });
    }
}

function datepicker(initialDate = '') {
    return {
        show: false,
        formattedDate: '',
        year: '',
        month: '',
        date: '',
        dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        days: [],
        blanks: [],

        init() {
            let today = initialDate ? new Date(initialDate) : new Date();
            this.year = today.getFullYear();
            this.month = today.getMonth();
            this.date = today.getDate();
            if (initialDate) {
                this.formattedDate = this.formatDate(today);
            }
            this.getDays();
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
        }
    }
}

function initNotifikasi() {
    const notifBtn = document.getElementById('notificationBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const notifBadge = document.getElementById('notifBadge');
    const markAllReadBtn = document.getElementById('markAllRead');

    if (!notifBtn || !notifDropdown) return;

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
        if (count > 0) {
            notifBadge.textContent = count > 9 ? '9+' : count;
            notifBadge.style.display = 'flex';
        } else {
            notifBadge.style.display = 'none';
        }
    }

    function renderNotifications(data) {
        if (data.length === 0) {
            notifList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>Tidak ada notifikasi baru</p>
                </div>
            `;
            return;
        }

        notifList.innerHTML = data.map(item => {
            const isUnread = item.is_read === 0;
            const timeAgoStr = timeAgo(new Date(item.created_at));
            
            // Tentukan ikon & warna berdasarkan pengirim
            let icon = 'fa-building';
            let iconBg = 'rgba(79, 70, 229, 0.1)';
            let iconColor = 'var(--accent)';
            let senderName = '-';

            if (item.sender && item.sender.profile) {
                const profile = item.sender.profile;
                if (profile.jurusan) {
                    icon = 'fa-book';
                    iconBg = 'rgba(124, 58, 237, 0.1)';
                    iconColor = 'var(--accent2)';
                    senderName = profile.jurusan.nama_jurusan;
                } else if (profile.unit_kerja || profile.unit_kerja_id || profile.unitKerja) {
                    icon = 'fa-building';
                    iconBg = 'rgba(14, 165, 233, 0.1)';
                    iconColor = 'var(--accent3)';
                    const unit = profile.unit_kerja || profile.unitKerja;
                    senderName = unit ? unit.nama_unit_pelaksana : 'Unit Kerja';
                }
            }

            // Fallback sender name if profile logic fails
            if (senderName === '-' && item.sender) {
                senderName = item.sender.name;
            }

            return `
                <a href="${item.link || '#'}" class="notification-item ${isUnread ? 'unread' : ''}" data-id="${item.id}">
                    <div class="notification-icon-wrapper" style="background:${iconBg}; color:${iconColor};">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="notification-content">
                        <span class="notification-sender">${senderName}</span>
                        <span class="notification-message">${item.pesan}</span>
                        <div class="notification-meta">
                            <span class="notification-time">${timeAgoStr}</span>
                            <span class="notification-badge-type badge-${item.type || 'evaluasi'}">${item.type || 'evaluasi'}</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');

        // Add click event to mark as read
        document.querySelectorAll('.notification-item').forEach(el => {
            el.addEventListener('click', function(e) {
                const id = this.getAttribute('data-id');
                markAsRead(id);
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
                fetchNotifications(); // Refresh
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
    
    // Poll for new notifications every 30 seconds
    setInterval(fetchNotifications, 30000);
}

// Jalankan saat pertama kali dan setiap kali Turbo navigasi
document.addEventListener('turbo:load', () => {
    initDashboard();
    initNotifikasi();
});
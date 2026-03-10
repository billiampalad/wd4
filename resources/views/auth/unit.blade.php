<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Jurusan — Sistem Informasi Kerjasama Polimdo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/auth/jurusan.css') }}">
</head>

<body>
    <!-- navbar -->
    <nav>
        <div class="nav-inner">
            <div class="nav-brand">
                <button id="hamburger" aria-label="Toggle sidebar">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div class="brand-icon"><img src="{{ asset('img/logo.png') }}" alt="Handshake" width="35" height="35"></div>
                <div class="brand-text">
                    <h1>POLIMDO &amp; DUDIKA</h1>
                    <p>Sistem Informasi Kerjasama</p>
                </div>
            </div>

            <div class="nav-actions">
                <!-- Search hint (desktop) -->
                <div class="search-bar" style="width:220px; display:none; align-items:center;" id="navSearch">
                    <i class="fas fa-search"></i>
                    <span>Cari data...</span>
                </div>

                <button class="icon-btn" id="darkModeBtn" title="Toggle dark mode">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>

                <button class="icon-btn" id="notificationBtn" title="Notifications">
                    <i class="fas fa-bell" id="notificationIcon"></i>
                    <span class="notification-badge">3</span>
                </button>

                <div class="user-chip">
                    <div class="user-avatar" id="userAvatar">{{ auth()->user()->name }}</div>
                    <div class="user-info">
                        <div class="name" id="userName">{{ auth()->user()->profile->jabatan }}</div>
                    </div>
                </div>

                <form method="POST" action="/logout" style="display: inline;">
                    @csrf
                    <button type="submit" class="icon-btn danger" id="logoutBtn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- sidebar -->
    <div class="layout">

        <!-- ── SIDEBAR ──────────────────────────────────────────── -->
        <aside id="sidebar">

            <div class="menu-section">Navigasi</div>

            <a class="menu-item active" href="#" data-page="dashboard">
                <div class="menu-icon"><i class="fas fa-tachometer-alt"></i></div>
                Dashboard
            </a>

            <!-- Data Master dropdown -->
            <div id="dataMasterParent" class="menu-item" style="flex-direction:column; align-items:stretch; padding:0;">
                <div id="dataMasterBtn" class="menu-item" style="margin:0;">
                    <div class="menu-icon"><i class="fas fa-database"></i></div>
                    <span style="flex:1; font-size:13px; font-weight:600;">Data Master</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu" id="dataMasterSub">
                    <a class="submenu-item" href="#" data-page="mitra">
                        <span class="submenu-dot"></span>Mitra Kerjasama
                    </a>
                    <a class="submenu-item" href="#" data-page="jenis_kerjasama">
                        <span class="submenu-dot"></span>Jenis Kerjasama
                    </a>
                    <a class="submenu-item" href="#" data-page="unit_pelaksana">
                        <span class="submenu-dot"></span>Unit Pelaksana
                    </a>
                </div>
            </div>

            <div class="menu-section">Kerjasama</div>

            <a class="menu-item" href="#" data-page="program_kerjasama">
                <div class="menu-icon"><i class="fas fa-handshake"></i></div>
                Program Kerjasama
            </a>

            <a class="menu-item" href="#" data-page="hasil_capaian">
                <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
                Hasil &amp; Capaian
            </a>

            <div class="menu-section">Monitoring</div>

            <a class="menu-item" href="#" data-page="evaluasi_kinerja">
                <div class="menu-icon"><i class="fas fa-star-half-alt"></i></div>
                Evaluasi Kinerja
            </a>

            <a class="menu-item" href="#" data-page="permasalahan_solusi">
                <div class="menu-icon"><i class="fas fa-tools"></i></div>
                Solusi &amp; Masalah
            </a>

        </aside>

        <div id="sidebarOverlay"></div>

        <!-- ── MAIN ──────────────────────────────────────────────── -->
        <main>

            <!-- Page Header -->
            <div class="page-header">
                <div class="breadcrumb">
                    <i class="fas fa-home" style="font-size:11px;"></i>
                    <span class="sep">/</span>
                    <span class="current" id="breadcrumbCurrent">Dashboard</span>
                </div>
                <h2 id="pageTitle">Dashboard</h2>
                <p id="pageDesc">Ringkasan data sistem informasi kerjasama Polimdo &amp; DUDIKA</p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon" style="background:rgba(79,70,229,.12); color:var(--accent);">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="stat-badge badge-up"><i class="fas fa-arrow-up" style="font-size:8px;"></i> 12%</span>
                    </div>
                    <div>
                        <div class="stat-value">128</div>
                        <div class="stat-label">Total Mitra</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon" style="background:rgba(14,165,233,.12); color:#0ea5e9;">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <span class="stat-badge badge-up"><i class="fas fa-arrow-up" style="font-size:8px;"></i> 8%</span>
                    </div>
                    <div>
                        <div class="stat-value">54</div>
                        <div class="stat-label">Program Aktif</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon" style="background:rgba(16,185,129,.12); color:#10b981;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <span class="stat-badge badge-up"><i class="fas fa-arrow-up" style="font-size:8px;"></i> 5%</span>
                    </div>
                    <div>
                        <div class="stat-value">91%</div>
                        <div class="stat-label">Capaian Target</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon" style="background:rgba(245,158,11,.12); color:#f59e0b;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <span class="stat-badge badge-down"><i class="fas fa-arrow-down" style="font-size:8px;"></i> 3%</span>
                    </div>
                    <div>
                        <div class="stat-value">7</div>
                        <div class="stat-label">Masalah Aktif</div>
                    </div>
                </div>
            </div>

            <!-- Content Row -->
            <div class="content-row">

                <!-- Tabel Program -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-list-alt"></i> Program Kerjasama Terkini</div>
                        <span class="card-action">Lihat Semua</span>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Program</th>
                                    <th>Mitra</th>
                                    <th>Status</th>
                                    <th>Tim</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">001</span></td>
                                    <td>
                                        <div style="font-weight:600; font-size:13px;">Magang Industri 2025</div>
                                        <div style="font-size:11px; color:var(--text-sub);">Unit: Jurusan Teknik</div>
                                    </td>
                                    <td>PT Telkom Indonesia</td>
                                    <td><span class="tag tag-green"><i class="fas fa-circle" style="font-size:6px;"></i> Aktif</span></td>
                                    <td>
                                        <div class="avatar-group">
                                            <div class="avatar" style="background:#4f46e5;">AS</div>
                                            <div class="avatar" style="background:#0ea5e9;">BR</div>
                                            <div class="avatar" style="background:#10b981;">CD</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">002</span></td>
                                    <td>
                                        <div style="font-weight:600; font-size:13px;">Riset Bersama AI</div>
                                        <div style="font-size:11px; color:var(--text-sub);">Unit: P3M</div>
                                    </td>
                                    <td>BRIN Sulawesi Utara</td>
                                    <td><span class="tag tag-orange"><i class="fas fa-circle" style="font-size:6px;"></i> Proses</span></td>
                                    <td>
                                        <div class="avatar-group">
                                            <div class="avatar" style="background:#7c3aed;">DF</div>
                                            <div class="avatar" style="background:#f59e0b;">EG</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">003</span></td>
                                    <td>
                                        <div style="font-weight:600; font-size:13px;">Pelatihan SDM 2025</div>
                                        <div style="font-size:11px; color:var(--text-sub);">Unit: SDM Polimdo</div>
                                    </td>
                                    <td>Dinas Nakertrans</td>
                                    <td><span class="tag tag-blue"><i class="fas fa-circle" style="font-size:6px;"></i> Rencana</span></td>
                                    <td>
                                        <div class="avatar-group">
                                            <div class="avatar" style="background:#ef4444;">FH</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">004</span></td>
                                    <td>
                                        <div style="font-weight:600; font-size:13px;">Sertifikasi Profesi</div>
                                        <div style="font-size:11px; color:var(--text-sub);">Unit: LSP Polimdo</div>
                                    </td>
                                    <td>BNSP</td>
                                    <td><span class="tag tag-green"><i class="fas fa-circle" style="font-size:6px;"></i> Aktif</span></td>
                                    <td>
                                        <div class="avatar-group">
                                            <div class="avatar" style="background:#0ea5e9;">GI</div>
                                            <div class="avatar" style="background:#4f46e5;">HJ</div>
                                            <div class="avatar" style="background:#10b981;">IK</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">005</span></td>
                                    <td>
                                        <div style="font-weight:600; font-size:13px;">Teaching Factory</div>
                                        <div style="font-size:11px; color:var(--text-sub);">Unit: Jurusan Bisnis</div>
                                    </td>
                                    <td>PT Bank Sulut Go</td>
                                    <td><span class="tag tag-red"><i class="fas fa-circle" style="font-size:6px;"></i> Selesai</span></td>
                                    <td>
                                        <div class="avatar-group">
                                            <div class="avatar" style="background:#7c3aed;">JL</div>
                                            <div class="avatar" style="background:#f59e0b;">KM</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right column -->
                <div style="display:flex; flex-direction:column; gap:20px;">

                    <!-- Capaian per Unit -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fas fa-chart-pie"></i> Capaian per Unit</div>
                        </div>
                        <div class="card-body">
                            <div class="progress-row">
                                <div class="progress-label"><span>Jurusan Teknik</span><span>94%</span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width:94%;"></div>
                                </div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-label"><span>Jurusan Bisnis</span><span>78%</span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill sky" style="width:78%;"></div>
                                </div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-label"><span>P3M</span><span>85%</span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill green" style="width:85%;"></div>
                                </div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-label"><span>LSP Polimdo</span><span>62%</span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill amber" style="width:62%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aktivitas Terbaru -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fas fa-bell"></i> Aktivitas Terbaru</div>
                            <span class="card-action">Semua</span>
                        </div>
                        <div class="card-body" style="padding-top:4px; padding-bottom:4px;">
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(79,70,229,.12); color:var(--accent);">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Mitra baru ditambahkan — PT Manado Jaya</div>
                                    <div class="activity-meta">2 jam yang lalu · Admin</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(16,185,129,.12); color:#10b981;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Teaching Factory dinyatakan selesai</div>
                                    <div class="activity-meta">5 jam yang lalu · Koordinator</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(245,158,11,.12); color:#f59e0b;">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Laporan masalah diterima — Riset AI</div>
                                    <div class="activity-meta">Kemarin · P3M</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(14,165,233,.12); color:#0ea5e9;">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Evaluasi kinerja Q2 diperbarui</div>
                                    <div class="activity-meta">2 hari lalu · WD4</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>

    <script src="{{ asset('js/auth/jurusan.js') }}"></script>
</body>

</html>
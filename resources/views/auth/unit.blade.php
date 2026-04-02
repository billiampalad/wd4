<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Unit — Sistem Informasi Kerjasama Polimdo</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/auth/user.css') }}" data-turbo-track="reload">
    <script src="https://unpkg.com/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js" data-turbo-track="reload"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                showConfirmButton: true
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: "{{ $errors->first() }}",
                showConfirmButton: true
            });
        </script>
    @endif
    <!-- navbar -->
    <nav>
        <div class="nav-inner">
            <div class="nav-brand">
                <button id="hamburger" aria-label="Toggle sidebar">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div class="brand-icon"><img src="{{ asset('img/logo.png') }}" alt="Handshake" width="35" height="35">
                </div>
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

                <div class="notification-container">
                    <button class="icon-btn" id="notificationBtn" title="Notifications">
                        <i class="fas fa-bell" id="notificationIcon"></i>
                        <span class="notification-badge" id="notifBadge" style="display: none;">0</span>
                    </button>
                    
                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="notification-header">
                            <h3>Notifikasi</h3>
                            <button id="markAllRead" style="background:none; border:none; color:var(--accent); font-size:11px; font-weight:700; cursor:pointer;">Tandai semua dibaca</button>
                        </div>
                        <div class="notification-list" id="notifList">
                            <div class="notification-empty">
                                <i class="fas fa-bell-slash"></i>
                                <p>Tidak ada notifikasi baru</p>
                            </div>
                        </div>
                        <div class="notification-footer">
                            <a href="#">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>

                <div class="user-chip">
                    <div class="user-avatar" id="userAvatar">{{ auth()->user()->name }}</div>
                    <div class="user-info">
                        <div class="name" id="userName">{{ auth()->user()->profile?->jabatan ?? '-' }}</div>
                        <div class="role">{{ auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? '-' }}</div>
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

            <div class="menu-section">KERJASAMA UNIT</div>

            <a class="menu-item {{ request()->routeIs('unit.dashboard') ? 'active' : '' }}"
                href="{{ route('unit.dashboard') }}">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <span>Dashboard</span>
            </a>

            <a class="menu-item {{ request()->routeIs('unit.dkerjasama', 'unit.kerjasama.*') ? 'active' : '' }}"
                href="{{ route('unit.dkerjasama') }}">
                <div class="menu-icon"><i class="fas fa-folder"></i></div>
                <span>Data Kerjasama</span>
            </a>

            <a class="menu-item {{ request()->routeIs('unit.evaluasi', 'unit.evaluasi.*') ? 'active' : '' }}"
                href="{{ route('unit.evaluasi') }}">
                <div class="menu-icon"><i class="fas fa-check-double"></i></div>
                <span>Evaluasi Kinerja</span>
            </a>

            <a class="menu-item {{ request()->routeIs('unit.laporan') ? 'active' : '' }}"
                href="{{ route('unit.laporan') }}">
                <div class="menu-icon"><i class="fas fa-file-signature"></i></div>
                <span>Laporan Data</span>
            </a>
        </aside>

        <!-- Sidebar Toggle (Floating on Border) -->
        <button id="sidebarToggle" class="sidebar-toggle-floating" title="Toggle Sidebar">
            <i class="fas fa-arrow-right-to-bracket"></i>
        </button>

        <!-- Main Content -->
        @yield('content')
        @if(!View::hasSection('content'))
            @if(request()->routeIs('unit.kerjasama.create'))
                @include('auth.layout.unit.create_kerjasama')
            @elseif(request()->routeIs('unit.kerjasama.edit'))
                @include('auth.layout.unit.edit_kerjasama')
            @elseif(request()->routeIs('unit.kerjasama.show'))
                @include('auth.layout.unit.detail_kerjasama')
            @elseif(request()->routeIs('unit.dkerjasama'))
                @include('auth.layout.unit.dkerjasama')
            @elseif(request()->routeIs('unit.evaluasi.form'))
                @include('auth.layout.unit.form_evaluasi')
            @elseif(request()->routeIs('unit.evaluasi'))
                @include('auth.layout.unit.evaluasi_kinerja')
            @elseif(request()->routeIs('unit.laporan'))
                @include('auth.layout.unit.laporan')
            @else
                @include('auth.layout.unit.dashboard')
            @endif
        @endif

        <div id="sidebarOverlay"></div>
    </div>

    <script src="{{ asset('js/auth/user.js') }}" data-turbo-track="reload"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Admin — Sistem Informasi Kerjasama Polimdo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}" data-turbo-track="reload">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js" data-turbo-track="reload"></script>
    <meta name="turbo-cache-control" content="no-preview">
    @yield('styles')
</head>

<body>
    <!-- navbar -->
    <nav id="navbar">
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
                <!-- Search (desktop) -->
                <div class="search-bar" id="navSearch" style="display:none;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="navSearchInput" placeholder="Cari data..." class="search-input" autocomplete="off" />
                    <button type="button" id="navSearchClear" class="search-clear-btn" style="display:none;" title="Bersihkan pencarian">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>

                <button class="icon-btn" id="darkModeBtn" title="Toggle dark mode">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>

                <button class="icon-btn" id="notificationBtn" title="Notifications">
                    <i class="fas fa-bell" id="notificationIcon"></i>
                    <span class="notification-badge">3</span>
                </button>

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
            <div class="menu-section">Administration</div>

            <a class="menu-item {{ request()->routeIs('dashboard', 'admin.dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <div class="menu-icon"><i class="fas fa-tachometer-alt"></i></div>
                <span>Dashboard</span>
            </a>

            @php
                $isUserManagementActive = request()->routeIs('users', 'users.*', 'roles', 'roles.*', 'profiles', 'profiles.*');
            @endphp
            <div id="dataMasterParent" style="display:flex; flex-direction:column; align-items:stretch;">
                <div id="dataMasterBtn" class="menu-item {{ $isUserManagementActive ? 'active submenu-open' : '' }}" style="margin:0; cursor: pointer;">
                    <div class="menu-icon"><i class="fas fa-users"></i></div>
                    <span class="menu-text" style="flex:1; font-size:13px; font-weight:600;">User Management</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu {{ $isUserManagementActive ? 'open' : '' }}" id="dataMasterSub">
                    <div class="submenu-inner">
                        <a class="submenu-item {{ request()->routeIs('users', 'users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <span class="submenu-dot"></span><span>Users</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('roles', 'roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                            <span class="submenu-dot"></span><span>Roles</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('profiles', 'profiles.*') ? 'active' : '' }}" href="{{ route('profiles') }}">
                            <span class="submenu-dot"></span><span>Profiles</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="menu-section">MASTER DATA</div>
            <a class="menu-item {{ request()->routeIs('mitra.*') ? 'active' : '' }}" href="{{ route('mitra.index') }}">
                <div class="menu-icon"><i class="fas fa-handshake"></i></div>
                <span>Mitra Kerjasama</span>
            </a>
            <a class="menu-item {{ request()->routeIs('jkerjasama.*') ? 'active' : '' }}" href="{{ route('jkerjasama.index') }}">
                <div class="menu-icon"><i class="fas fa-tags"></i></div>
                <span>Jenis Kerjasama</span>
            </a>
            <a class="menu-item {{ request()->routeIs('upelaksana.*') ? 'active' : '' }}" href="{{ route('upelaksana.index') }}">
                <div class="menu-icon"><i class="fas fa-sitemap"></i></div>
                <span>Unit Pelaksana</span>
            </a>
            <a class="menu-item {{ request()->routeIs('jurusan.*') ? 'active' : '' }}" href="{{ route('jurusan.index') }}">
                <div class="menu-icon"><i class="fas fa-microchip"></i></div>
                <span>Jurusan</span>
            </a>

            <!-- <div class="menu-section">KERJASAMA</div>
            <a class="menu-item" href="#" data-page="data_kerjasama">
                <div class="menu-icon"><i class="fas fa-briefcase"></i></div>
                <span>Data Kerjasama</span>
            </a>
            <a class="menu-item" href="#" data-page="hasil_capaian">
                <div class="menu-icon"><i class="fas fa-chart-bar"></i></div>
                <span>Hasil &amp; Capaian</span>
            </a>
            <a class="menu-item" href="#" data-page="evaluasi_kinerja">
                <div class="menu-icon"><i class="fas fa-star"></i></div>
                <span>Evaluasi Kinerja</span>
            </a>
            <a class="menu-item" href="#" data-page="permasalahan_solusi">
                <div class="menu-icon"><i class="fas fa-tools"></i></div>
                <span>Solusi &amp; Masalah</span>
            </a>

            <div class="menu-section">SYSTEM</div>
            <a class="menu-item" href="#" data-page="notifikasi">
                <div class="menu-icon"><i class="fas fa-bell"></i></div>
                <span>Notifikasi Mitra</span>
            </a>

            <a class="menu-item" href="#" data-page="laporan">
                <div class="menu-icon"><i class="fas fa-file-signature"></i></div>
                <span>Laporan Data</span>
            </a>
            <a class="menu-item" href="#" data-page="laporan">
                <div class="menu-icon"><i class="fas fa-chart-simple"></i></div>
                <span>Statistik Data</span>
            </a> -->
        </aside>

        <!-- Sidebar Toggle (Floating on Border) -->
        <button id="sidebarToggle" class="sidebar-toggle-floating" title="Toggle Sidebar">
            <i class="fas fa-arrow-right-to-bracket"></i>
        </button>

        <!-- Main Content -->
        @yield('content')

        <div id="sidebarOverlay"></div>
    </div>

    <script src="{{ asset('js/admin/dashboard.js') }}" data-turbo-track="reload"></script>
    @yield('scripts')
</body>

</html>
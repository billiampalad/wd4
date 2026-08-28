<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Mitra | Sistem Informasi Kerjasama Politeknik Negeri Manado</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/auth/user.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="{{ asset('css/auth/unit/mitra/modal_create.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="{{ asset('css/auth/unit/mitra/modal_edit.css') }}" data-turbo-track="reload">
    <script src="https://unpkg.com/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js" data-turbo-track="reload"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    {{-- SweetAlert flash messages --}}
    @if (session('success'))
        <div id="swal-flash-success" data-message="{{ session('success') }}" style="display:none;"></div>
    @endif
    @if (session('error'))
        <div id="swal-flash-error" data-message="{{ session('error') }}" style="display:none;"></div>
    @endif
    @if ($errors->any())
        <div id="swal-flash-validation" data-message="{{ implode(' ', $errors->all()) }}" style="display:none;"></div>
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
                        </div>
                        <div class="notification-list" id="notifList">
                            <div class="notification-empty">
                                <i class="fas fa-bell-slash"></i>
                                <p>Tidak ada notifikasi baru</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="user-chip">
                    <div class="user-avatar" id="userAvatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-info">
                        <div class="name" id="userName">{{ auth()->user()->name }}</div>
                        <div class="role">{{ auth()->user()->mitra?->nama_mitra ?? 'Mitra Eksternal' }}</div>
                    </div>
                </div>

                <form id="logout-form" method="POST" action="/logout" style="display: inline;">
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
            <div class="menu-section">PORTAL MITRA</div>

            <a class="menu-item {{ request()->routeIs('mitra.dashboard') ? 'active' : '' }}"
                href="{{ route('mitra.dashboard') }}">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <span>Dashboard</span>
            </a>

            <a class="menu-item {{ request()->routeIs('mitra.dokumen.*') ? 'active' : '' }}"
                href="{{ route('mitra.dokumen.index') ?? '#' }}">
                <div class="menu-icon"><i class="fas fa-folder-open"></i></div>
                <span>Dokumen Kerjasama</span>
            </a>

            <a class="menu-item {{ request()->routeIs('mitra.pengajuan.*') ? 'active' : '' }}"
                href="{{ route('mitra.pengajuan.create') ?? '#' }}">
                <div class="menu-icon"><i class="fas fa-file-signature"></i></div>
                <span>Pengajuan Kerja Sama</span>
            </a>

            <a class="menu-item {{ request()->routeIs('mitra.penilaian.*') ? 'active' : '' }}"
                href="{{ route('mitra.penilaian.index') ?? '#' }}">
                <div class="menu-icon"><i class="fas fa-user-graduate"></i></div>
                <span>Kegiatan & Magang</span>
            </a>

            <a class="menu-item {{ request()->routeIs('mitra.alumni.*') ? 'active' : '' }}"
                href="{{ route('mitra.alumni.index') ?? '#' }}">
                <div class="menu-icon"><i class="fas fa-briefcase"></i></div>
                <span>Tracking Lulusan</span>
            </a>

            <a class="menu-item {{ request()->routeIs('mitra.umpan_balik.*') ? 'active' : '' }}"
                href="{{ route('mitra.umpan_balik.index') ?? '#' }}">
                <div class="menu-icon"><i class="fas fa-star-half-stroke"></i></div>
                <span>Umpan Balik & Evaluasi</span>
            </a>

            <div class="menu-section" style="margin-top: 20px;">DUKUNGAN</div>
            <a class="menu-item" href="mailto:admin@polimdo.ac.id">
                <div class="menu-icon"><i class="fas fa-headset"></i></div>
                <span>Bantuan / Hubungi Admin</span>
            </a>
        </aside>

        <!-- Sidebar Toggle (Floating on Border) -->
        <button id="sidebarToggle" class="sidebar-toggle-floating" title="Toggle Sidebar">
            <i class="fas fa-arrow-right-to-bracket"></i>
        </button>

        <!-- Main Content -->
        @yield('content')
        @if (!View::hasSection('content'))
            @if (request()->routeIs('mitra.dashboard'))
                @include('auth.layout.mitra.dashboard')
            @elseif (request()->routeIs('mitra.dokumen.*'))
                @include('auth.layout.mitra.dkerjasama')
            @else
                <main id="mainContent" class="dk-page">
                    <div style="padding: 100px 40px; text-align: center; color: var(--text-sub);">
                        <i class="fas fa-hammer" style="font-size: 64px; margin-bottom: 24px; color: #cbd5e1;"></i>
                        <h2 style="font-size: 24px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Halaman Dalam Pengembangan</h2>
                        <p style="font-size: 16px;">Fitur untuk halaman ini sedang dibangun dan belum tersedia.</p>
                    </div>
                </main>
            @endif
        @endif

        <div id="sidebarOverlay"></div>
    </div>

    @include('partials.loading-system')
    <script src="{{ asset('js/auth/user.js') }}" data-turbo-track="reload"></script>
</body>

</html>
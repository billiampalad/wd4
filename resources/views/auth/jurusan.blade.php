<!DOCTYPE html>
<html lang="id" data-theme="light"
    class="{{ request()->routeIs('jurusan.analitik.status-kerjasama') ? 'status-kerjasama-page' : '' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Jurusan | Sistem Informasi Kerjasama Politeknik Negeri Manado</title>

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

<body class="{{ request()->routeIs('jurusan.analitik.status-kerjasama') ? 'status-kerjasama-page' : '' }}">
    {{-- SweetAlert flash messages (Turbo-compatible: fires once, then self-removes) --}}
    @if (session('success'))
        <div id="swal-flash-success" data-message="{{ session('success') }}" style="display:none;"></div>
    @endif
    @if (session('error'))
        <div id="swal-flash-error" data-message="{{ session('error') }}" style="display:none;"></div>
    @endif
    @if ($errors->any())
        <div id="swal-flash-validation" data-message="{{ implode(' ', $errors->all()) }}" style="display:none;"></div>
    @endif
    @php
        $unitExpiryNotifications = collect();
        $notificationUser = auth()->user();

        if ($notificationUser) {
            $notificationUser->loadMissing('profile.jurusan');
            $notificationProfile = $notificationUser->profile;
            $notificationToday = now()->startOfDay();
            $notificationLimit = $notificationToday->copy()->addMonthsNoOverflow(3)->endOfDay();

            $expiryQuery = \App\Models\Cooperation::query()
                ->select(['id', 'title', 'doc_number', 'jenis', 'end_date', 'status'])
                ->whereNotNull('end_date')
                ->whereDate('end_date', '>=', $notificationToday->toDateString())
                ->whereDate('end_date', '<=', $notificationLimit->toDateString());

            if ($notificationProfile?->jurusan_id) {
                $expiryQuery->where(function ($query) use ($notificationProfile) {
                    $query->where('jurusan_id', $notificationProfile->jurusan_id)
                        ->orWhereHas('jurusans', fn($subQuery) => $subQuery->where('jurusans.id', $notificationProfile->jurusan_id));
                });
            } else {
                $expiryQuery->whereRaw('1 = 0');
            }

            $unitExpiryNotifications = $expiryQuery
                ->orderBy('end_date')
                ->get()
                ->unique('id')
                ->map(function ($cooperation) use ($notificationToday) {
                    $endDate = \Carbon\Carbon::parse($cooperation->end_date)->startOfDay();
                    $daysRemaining = max(0, (int) $notificationToday->diffInDays($endDate, false));
                    $remainingLabel = $daysRemaining === 0
                        ? 'berakhir hari ini'
                        : ($daysRemaining . ' hari lagi');

                    return [
                        'id' => $cooperation->id,
                        'system_id' => 'expiry-' . $cooperation->id,
                        'title' => $cooperation->title ?: 'Kerjasama Tanpa Judul',
                        'doc_number' => $cooperation->doc_number,
                        'jenis' => $cooperation->jenis,
                        'days_remaining' => $daysRemaining,
                        'remaining_label' => $remainingLabel,
                        'end_date_label' => $endDate->format('d M Y'),
                        'link' => route('jurusan.kerjasama.show', $cooperation->id),
                    ];
                })
                ->values();
        }
    @endphp
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
                <!-- Search (desktop) -->
                <div class="search-bar" id="navSearch" style="display:none;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="navSearchInput" placeholder="Cari data..." class="search-input"
                        autocomplete="off" />
                    <button type="button" id="navSearchClear" class="search-clear-btn" style="display:none;"
                        title="Bersihkan pencarian">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>

                <button class="icon-btn" id="darkModeBtn" title="Toggle dark mode">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>

                <div class="notification-container">
                    <button class="icon-btn" id="notificationBtn" title="Notifications">
                        <i class="fas fa-bell" id="notificationIcon"></i>
                        <span class="notification-badge" id="notifBadge"
                            style="{{ $unitExpiryNotifications->count() > 0 ? 'display: flex;' : 'display: none;' }}">
                            {{ $unitExpiryNotifications->count() > 9 ? '9+' : $unitExpiryNotifications->count() }}
                        </span>
                    </button>

                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="notification-header">
                            <h3>Notifikasi</h3>
                            <button id="markAllRead" class="notification-mark-read" style="display: none;">Tandai
                                semua dibaca</button>
                        </div>
                        <div class="notification-list" id="notifList">
                            @forelse ($unitExpiryNotifications as $expiryNotification)
                                <a href="{{ $expiryNotification['link'] }}"
                                    class="notification-item unread notification-expiry-item"
                                    data-system-notification="true" data-system-id="{{ $expiryNotification['system_id'] }}">
                                    <div class="notification-icon-wrapper"
                                        style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="notification-content">
                                        <span class="notification-sender">Masa Aktif Kerjasama</span>
                                        <span class="notification-message">
                                            {{ $expiryNotification['title'] }} akan berakhir
                                            {{ $expiryNotification['remaining_label'] }}.
                                        </span>
                                        <div class="notification-meta">
                                            <span class="notification-time">
                                                Selesai {{ $expiryNotification['end_date_label'] }}
                                            </span>
                                            <span class="notification-badge-type badge-masa-aktif">Masa Aktif</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="notification-empty">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>Tidak ada notifikasi baru</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="notification-footer">
                            <a href="#">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                    <script type="application/json" id="expiryNotificationsData">
                        @json($unitExpiryNotifications)
                    </script>
                </div>

                <div class="user-chip">
                    <div class="user-avatar" id="userAvatar">{{ auth()->user()->name }}</div>
                    <div class="user-info">
                        <div class="name" id="userName">{{ auth()->user()->profile?->jabatan ?? '-' }}</div>
                        <div class="role">{{ auth()->user()->profile?->jurusan?->nama_jurusan ?? '-' }}
                        </div>
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

            <div class="menu-section">KERJASAMA UNIT</div>

            <a class="menu-item {{ request()->routeIs('jurusan.dashboard') ? 'active' : '' }}"
                href="{{ route('jurusan.dashboard') }}">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <span>Beranda</span>
            </a>

            @php
                $isAnalitikActive = request()->routeIs(
                    'jurusan.analitik.*',
                );
            @endphp
            <div id="analitikParent" class="sidebar-dropdown"
                style="display:flex; flex-direction:column; align-items:stretch;">
                <div id="analitikBtn" class="menu-item {{ $isAnalitikActive ? 'active submenu-open' : '' }}"
                    style="margin:0; cursor: pointer;">
                    <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
                    <span class="menu-text" style="flex:1; font-size:13px; font-weight:600;">Analitik</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu {{ $isAnalitikActive ? 'open' : '' }}" id="analitikSub">
                    <div class="submenu-inner">
                        <a class="submenu-item {{ request()->routeIs('jurusan.analitik.status-kerjasama') ? 'active' : '' }}"
                            href="{{ route('jurusan.analitik.status-kerjasama') }}">
                            <span class="submenu-dot"></span><span>Status Kerjasama</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('jurusan.analitik.klasifikasi-mitra') ? 'active' : '' }}"
                            href="{{ route('jurusan.analitik.klasifikasi-mitra') }}">
                            <span class="submenu-dot"></span><span>Klarifikasi Mitra</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('jurusan.analitik.geo-mitra') ? 'active' : '' }}"
                            href="{{ route('jurusan.analitik.geo-mitra') }}">
                            <span class="submenu-dot"></span><span>Geo Mitra</span>
                        </a>
                    </div>
                </div>
            </div>

            <a class="menu-item {{ request()->routeIs('jurusan.institusi') ? 'active' : '' }}"
                href="{{ route('jurusan.institusi') }}">
                <div class="menu-icon"><i class="fas fa-university"></i></div>
                <span>Institusi</span>
            </a>

            @php
                $isDataKerjasamaActive = request()->routeIs(
                    'jurusan.dkerjasama',
                    'jurusan.kerjasama.*',
                    'jurusan.mitra',
                    'jurusan.mitra.*',
                    'jurusan.form',
                    'jurusan.form.*',
                );
            @endphp
            <div id="kerjasamaParent" class="sidebar-dropdown"
                style="display:flex; flex-direction:column; align-items:stretch;">
                <div id="kerjasamaBtn" class="menu-item {{ $isDataKerjasamaActive ? 'active submenu-open' : '' }}"
                    style="margin:0; cursor: pointer;">
                    <div class="menu-icon"><i class="fas fa-folder"></i></div>
                    <span class="menu-text" style="flex:1; font-size:13px; font-weight:600;">Kerjasama</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu {{ $isDataKerjasamaActive ? 'open' : '' }}" id="kerjasamaSub">
                    <div class="submenu-inner">
                        <a class="submenu-item {{ request()->routeIs('jurusan.dkerjasama', 'jurusan.kerjasama.*') ? 'active' : '' }}"
                            href="{{ route('jurusan.dkerjasama') }}">
                            <span class="submenu-dot"></span><span>Repositori</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('jurusan.mitra', 'jurusan.mitra.*') ? 'active' : '' }}"
                            href="{{ route('jurusan.mitra') }}">
                            <span class="submenu-dot"></span><span>Mitra</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('jurusan.form', 'jurusan.form.*') ? 'active' : '' }}"
                            href="{{ route('jurusan.form') }}">
                            <span class="submenu-dot"></span><span>Form Laporan</span>
                        </a>
                    </div>
                </div>
            </div>

            <a class="menu-item {{ request()->routeIs('jurusan.evaluasi', 'jurusan.evaluasi.*') ? 'active' : '' }}"
                href="{{ route('jurusan.evaluasi') }}">
                <div class="menu-icon"><i class="fas fa-check-double"></i></div>
                <span>Evaluasi Kinerja</span>
            </a>

            <a class="menu-item {{ request()->routeIs('jurusan.laporan', 'jurusan.laporan.*') ? 'active' : '' }}"
                href="{{ route('jurusan.laporan') }}">
                <div class="menu-icon"><i class="fas fa-file-lines"></i></div>
                <span>Laporan Data</span>
            </a>

            @php
                $isReferensiActive = request()->routeIs(
                    'jurusan.referensi.*',
                );
            @endphp
            <div id="referensiParent" class="sidebar-dropdown"
                style="display:flex; flex-direction:column; align-items:stretch;">
                <div id="referensiBtn" class="menu-item {{ $isReferensiActive ? 'active submenu-open' : '' }}"
                    style="margin:0; cursor: pointer;">
                    <div class="menu-icon"><i class="fas fa-book-open"></i></div>
                    <span class="menu-text" style="flex:1; font-size:13px; font-weight:600;">Referensi</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu {{ $isReferensiActive ? 'open' : '' }}" id="referensiSub">
                    <div class="submenu-inner">
                        <a class="submenu-item {{ request()->routeIs('jurusan.referensi.bentuk-kegiatan') ? 'active' : '' }}"
                            href="{{ route('jurusan.referensi.bentuk-kegiatan') }}">
                            <span class="submenu-dot"></span><span>Bentuk Kegiatan</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('jurusan.referensi.status-kerjasama') ? 'active' : '' }}"
                            href="{{ route('jurusan.referensi.status-kerjasama') }}">
                            <span class="submenu-dot"></span><span>Status Kerjasama</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('jurusan.referensi.status-evaluasi') ? 'active' : '' }}"
                            href="{{ route('jurusan.referensi.status-evaluasi') }}">
                            <span class="submenu-dot"></span><span>Status Evaluasi</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('jurusan.referensi.kriteria-mitra') ? 'active' : '' }}"
                            href="{{ route('jurusan.referensi.kriteria-mitra') }}">
                            <span class="submenu-dot"></span><span>Kriteria Mitra</span>
                        </a>
                        <!-- <a class="submenu-item {{ request()->routeIs('jurusan.referensi.sumber-dana') ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <span class="submenu-dot"></span><span>Sumber Dana</span>
                        </a> -->
                    </div>
                </div>
            </div>
        </aside>

        <!-- Sidebar Toggle (Floating on Border) -->
        <button id="sidebarToggle" class="sidebar-toggle-floating" title="Toggle Sidebar">
            <i class="fas fa-arrow-right-to-bracket"></i>
        </button>

        <!-- Main Content -->
        @yield('content')
        @if (!View::hasSection('content'))
            @if (request()->routeIs('jurusan.kerjasama.create'))
                @include('auth.layout.jurusan.create_kerjasama')
            @elseif(request()->routeIs('jurusan.kerjasama.edit'))
                @include('auth.layout.jurusan.edit_kerjasama')
            @elseif(request()->routeIs('jurusan.kerjasama.show'))
                @include('auth.layout.jurusan.detail_kerjasama')
            @elseif(request()->routeIs('jurusan.dkerjasama'))
                @include('auth.layout.jurusan.dkerjasama')
            @elseif(request()->routeIs('jurusan.analitik.status-kerjasama'))
                @include('auth.layout.jurusan.analitik.status_kerjasama')
            @elseif(request()->routeIs('jurusan.analitik.klasifikasi-mitra'))
                @include('auth.layout.jurusan.analitik.klarifikasi-mitra')
            @elseif(request()->routeIs('jurusan.analitik.geo-mitra'))
                @include('auth.layout.jurusan.analitik.geo-mitra')
            @elseif(request()->routeIs('jurusan.mitra.create'))
                @include('auth.layout.jurusan.mitra.create')
            @elseif(request()->routeIs('jurusan.mitra.edit'))
                @include('auth.layout.jurusan.mitra.edit')
            @elseif(request()->routeIs('jurusan.mitra.show'))
                @include('auth.layout.jurusan.mitra.detail')
            @elseif(request()->routeIs('jurusan.form'))
                @include('auth.layout.jurusan.form.index')
            @elseif(request()->routeIs('jurusan.mitra'))
                @include('auth.layout.jurusan.mitra.index')

                {{-- menu institusi --}}
            @elseif(request()->routeIs('jurusan.institusi'))
                @include('auth.layout.jurusan.institusi')

            @elseif(request()->routeIs('jurusan.referensi.bentuk-kegiatan'))
                @include('auth.layout.jurusan.referensi.bentuk-kegiatan')

            @elseif(request()->routeIs('jurusan.referensi.status-kerjasama'))
                @include('auth.layout.jurusan.referensi.status-kerjasama')

            @elseif(request()->routeIs('jurusan.referensi.status-evaluasi'))
                @include('auth.layout.jurusan.referensi.status-evaluasi')

            @elseif(request()->routeIs('jurusan.referensi.kriteria-mitra'))
                @include('auth.layout.jurusan.referensi.kriteria-mitra')

            @elseif(request()->routeIs('jurusan.evaluasi.form', 'jurusan.evaluasi.form_unit'))
                @include('auth.layout.jurusan.form_evaluasi')
            @elseif(request()->routeIs('jurusan.evaluasi'))
                @include('auth.layout.jurusan.evaluasi_kinerja')
            @elseif(request()->routeIs('jurusan.laporan'))
                @include('auth.layout.jurusan.laporan')
            @elseif(request()->routeIs('jurusan.hasil_evaluasi'))
                @include('auth.layout.jurusan.hasil_evaluasi')
            @else
                @include('auth.layout.jurusan.dashboard')
            @endif
        @endif

        <div id="sidebarOverlay"></div>
    </div>

    <script src="{{ asset('js/auth/user.js') }}" data-turbo-track="reload"></script>
    <script src="{{ asset('js/auth/unit/mitra/modal_create.js') }}" data-turbo-track="reload"></script>
    <script src="{{ asset('js/auth/unit/mitra/modal_edit.js') }}" data-turbo-track="reload"></script>
    <script src="{{ asset('js/auth/unit/mitra/index.js') }}" data-turbo-track="reload"></script>
</body>

</html>

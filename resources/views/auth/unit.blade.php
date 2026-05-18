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
    <link rel="stylesheet" href="{{ asset('css/auth/unit/mitra/modal_create.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="{{ asset('css/auth/unit/mitra/modal_edit.css') }}" data-turbo-track="reload">
    <script src="https://unpkg.com/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js" data-turbo-track="reload"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
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
            $notificationUser->loadMissing('profile.unitKerja');
            $notificationProfile = $notificationUser->profile;
            $notificationUnitName = $notificationProfile?->unitKerja?->nama_unit_pelaksana;
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
                        ->orWhereHas('jurusans', fn ($subQuery) => $subQuery->where('jurusans.id', $notificationProfile->jurusan_id));
                });
            } elseif ($notificationUnitName) {
                $expiryQuery->where(function ($query) use ($notificationUnitName) {
                    $query->whereHas('jurusans', fn ($subQuery) => $subQuery->where('nama_jurusan', $notificationUnitName))
                        ->orWhereHas('upas', fn ($subQuery) => $subQuery->where('nama_upa', $notificationUnitName))
                        ->orWhereHas('pusats', fn ($subQuery) => $subQuery->where('nama_pusat', $notificationUnitName))
                        ->orWhereHas('jurusan', fn ($subQuery) => $subQuery->where('nama_jurusan', $notificationUnitName))
                        ->orWhereHas('upa', fn ($subQuery) => $subQuery->where('nama_upa', $notificationUnitName))
                        ->orWhereHas('pusat', fn ($subQuery) => $subQuery->where('nama_pusat', $notificationUnitName));
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
                        'link' => route('unit.kerjasama.show', $cooperation->id),
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
                <div class="brand-icon"><img src="{{ asset('img/logo.png') }}" alt="Handshake" width="35"
                        height="35">
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
                                    data-system-notification="true"
                                    data-system-id="{{ $expiryNotification['system_id'] }}">
                                    <div class="notification-icon-wrapper"
                                        style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="notification-content">
                                        <span class="notification-sender">Masa Aktif Kerjasama</span>
                                        <span class="notification-message">
                                            {{ $expiryNotification['title'] }} akan berakhir {{ $expiryNotification['remaining_label'] }}.
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
                        <div class="role">{{ auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? '-' }}
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

            <a class="menu-item {{ request()->routeIs('unit.dashboard') ? 'active' : '' }}"
                href="{{ route('unit.dashboard') }}">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <span>Beranda</span>
            </a>

            @php
                $isAnalitikActive = request()->routeIs(
                    'unit.analitik.*',
                );
            @endphp
            <div id="analitikParent" class="sidebar-dropdown" style="display:flex; flex-direction:column; align-items:stretch;">
                <div id="analitikBtn" class="menu-item {{ $isAnalitikActive ? 'active submenu-open' : '' }}"
                    style="margin:0; cursor: pointer;">
                    <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
                    <span class="menu-text" style="flex:1; font-size:13px; font-weight:600;">Analitik</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu {{ $isAnalitikActive ? 'open' : '' }}" id="analitikSub">
                    <div class="submenu-inner">
                        <a class="submenu-item {{ request()->routeIs('unit.analitik.status-kerjasama') ? 'active' : '' }}"
                            href="{{ route('unit.analitik.status-kerjasama') }}">
                            <span class="submenu-dot"></span><span>Status Kerjasama</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('unit.analitik.klasifikasi-mitra') ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <span class="submenu-dot"></span><span>Klarifikasi Mitra</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('unit.analitik.geo-mitra') ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <span class="submenu-dot"></span><span>Geo Mitra</span>
                        </a>
                    </div>
                </div>
            </div>

            <a class="menu-item {{ request()->routeIs('unit.institusi') ? 'active' : '' }}"
                href="{{ route('unit.institusi') }}">
                <div class="menu-icon"><i class="fas fa-university"></i></div>
                <span>Institusi</span>
            </a>

            @php
                $isDataKerjasamaActive = request()->routeIs(
                    'unit.dkerjasama',
                    'unit.kerjasama.*',
                    'unit.mitra',
                    'unit.mitra.*',
                    'unit.form',
                    'unit.form.*',
                );
            @endphp
            <div id="kerjasamaParent" class="sidebar-dropdown" style="display:flex; flex-direction:column; align-items:stretch;">
                <div id="kerjasamaBtn" class="menu-item {{ $isDataKerjasamaActive ? 'active submenu-open' : '' }}"
                    style="margin:0; cursor: pointer;">
                    <div class="menu-icon"><i class="fas fa-folder"></i></div>
                    <span class="menu-text" style="flex:1; font-size:13px; font-weight:600;">Kerjasama</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu {{ $isDataKerjasamaActive ? 'open' : '' }}" id="kerjasamaSub">
                    <div class="submenu-inner">
                        <a class="submenu-item {{ request()->routeIs('unit.dkerjasama', 'unit.kerjasama.*') ? 'active' : '' }}"
                            href="{{ route('unit.dkerjasama') }}">
                            <span class="submenu-dot"></span><span>Repositori</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('unit.mitra', 'unit.mitra.*') ? 'active' : '' }}"
                            href="{{ route('unit.mitra') }}">
                            <span class="submenu-dot"></span><span>Mitra</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('unit.form', 'unit.form.*') ? 'active' : '' }}"
                            href="{{ route('unit.form') }}">
                            <span class="submenu-dot"></span><span>Form Laporan</span>
                        </a>
                    </div>
                </div>
            </div>

            <a class="menu-item {{ request()->routeIs('unit.evaluasi', 'unit.evaluasi.*') ? 'active' : '' }}"
                href="{{ route('unit.evaluasi') }}">
                <div class="menu-icon"><i class="fas fa-check-double"></i></div>
                <span>Evaluasi Kinerja</span>
            </a>

            @php
                $isReferensiActive = request()->routeIs(
                    'unit.referensi.*',
                );
            @endphp
            <div id="referensiParent" class="sidebar-dropdown" style="display:flex; flex-direction:column; align-items:stretch;">
                <div id="referensiBtn" class="menu-item {{ $isReferensiActive ? 'active submenu-open' : '' }}"
                    style="margin:0; cursor: pointer;">
                    <div class="menu-icon"><i class="fas fa-book-open"></i></div>
                    <span class="menu-text" style="flex:1; font-size:13px; font-weight:600;">Referensi</span>
                    <i class="fas fa-chevron-down menu-chevron"></i>
                </div>
                <div class="submenu {{ $isReferensiActive ? 'open' : '' }}" id="referensiSub">
                    <div class="submenu-inner">
                        <a class="submenu-item {{ request()->routeIs('unit.referensi.bentuk-kegiatan') ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <span class="submenu-dot"></span><span>Bentuk Kegiatan</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('unit.referensi.status-kerjasama') ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <span class="submenu-dot"></span><span>Status Kerjasama</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('unit.referensi.kriteria-mitra') ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <span class="submenu-dot"></span><span>Kriteria Mitra</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('unit.referensi.sumber-dana') ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <span class="submenu-dot"></span><span>Sumber Dana</span>
                        </a>
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
            @if (request()->routeIs('unit.kerjasama.create'))
                @include('auth.layout.unit.create_kerjasama')
            @elseif(request()->routeIs('unit.kerjasama.edit'))
                @include('auth.layout.unit.edit_kerjasama')
            @elseif(request()->routeIs('unit.kerjasama.show'))
                @include('auth.layout.unit.detail_kerjasama')
            @elseif(request()->routeIs('unit.dkerjasama'))
                @include('auth.layout.unit.dkerjasama')
            @elseif(request()->routeIs('unit.analitik.status-kerjasama'))
                @include('auth.layout.unit.analitik.status_kerjasama')
            @elseif(request()->routeIs('unit.mitra.create'))
                @include('auth.layout.unit.mitra.create')
            @elseif(request()->routeIs('unit.mitra.edit'))
                @include('auth.layout.unit.mitra.edit')
            @elseif(request()->routeIs('unit.mitra.show'))
                @include('auth.layout.unit.mitra.detail')
            @elseif(request()->routeIs('unit.form'))
                @include('auth.layout.unit.form.index')
            @elseif(request()->routeIs('unit.mitra'))
                @include('auth.layout.unit.mitra.index')

            {{-- menu institusi --}}
            @elseif(request()->routeIs('unit.institusi'))
                @include('auth.layout.unit.institusi')

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
    <script src="{{ asset('js/auth/unit/mitra/modal_create.js') }}" data-turbo-track="reload"></script>
    <script src="{{ asset('js/auth/unit/mitra/modal_edit.js') }}" data-turbo-track="reload"></script>
    <script src="{{ asset('js/auth/unit/mitra/index.js') }}" data-turbo-track="reload"></script>
</body>

</html>

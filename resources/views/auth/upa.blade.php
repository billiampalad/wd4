<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Upa | Sistem Informasi Kerjasama Politeknik Negeri Manado</title>

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
                        'link' => route('upa.kerjasama.show', $cooperation->id),
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
                        <div class="role">{{ auth()->user()->profile?->upa?->nama_upa ?? '-' }}
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

            <a class="menu-item {{ request()->routeIs('upa.dashboard') ? 'active' : '' }}"
                href="{{ route('upa.dashboard') }}">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <span>Beranda</span>
            </a>

            @php
                $isAnalitikActive = request()->routeIs(
                    'upa.analitik.*',
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
                        <a class="submenu-item {{ request()->routeIs('upa.analitik.status-kerjasama') ? 'active' : '' }}"
                            href="{{ route('upa.analitik.status-kerjasama') }}">
                            <span class="submenu-dot"></span><span>Status Kerjasama</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('upa.analitik.klasifikasi-mitra') ? 'active' : '' }}"
                            href="{{ route('upa.analitik.klasifikasi-mitra') }}">
                            <span class="submenu-dot"></span><span>Klarifikasi Mitra</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('upa.analitik.geo-mitra') ? 'active' : '' }}"
                            href="{{ route('upa.analitik.geo-mitra') }}">
                            <span class="submenu-dot"></span><span>Geo Mitra</span>
                        </a>
                    </div>
                </div>
            </div>

            <a class="menu-item {{ request()->routeIs('upa.institusi') ? 'active' : '' }}"
                href="{{ route('upa.institusi') }}">
                <div class="menu-icon"><i class="fas fa-university"></i></div>
                <span>Institusi</span>
            </a>

            @php
                $isDataKerjasamaActive = request()->routeIs(
                    'upa.dkerjasama',
                    'upa.kerjasama.*',
                    'upa.mitra',
                    'upa.mitra.*',
                    'upa.form',
                    'upa.form.*',
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
                        <a class="submenu-item {{ request()->routeIs('upa.dkerjasama', 'upa.kerjasama.*') ? 'active' : '' }}"
                            href="{{ route('upa.dkerjasama') }}">
                            <span class="submenu-dot"></span><span>Repositori</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('upa.mitra', 'upa.mitra.*') ? 'active' : '' }}"
                            href="{{ route('upa.mitra') }}">
                            <span class="submenu-dot"></span><span>Mitra</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('upa.form', 'upa.form.*') ? 'active' : '' }}"
                            href="{{ route('upa.form') }}">
                            <span class="submenu-dot"></span><span>Form Laporan</span>
                        </a>
                    </div>
                </div>
            </div>

            <a class="menu-item {{ request()->routeIs('upa.evaluasi', 'upa.evaluasi.*') ? 'active' : '' }}"
                href="{{ route('upa.evaluasi') }}">
                <div class="menu-icon"><i class="fas fa-check-double"></i></div>
                <span>Evaluasi Kinerja</span>
            </a>

            @php
                $isReferensiActive = request()->routeIs(
                    'upa.referensi.*',
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
                        <a class="submenu-item {{ request()->routeIs('upa.referensi.bentuk-kegiatan') ? 'active' : '' }}"
                            href="{{ route('upa.referensi.bentuk-kegiatan') }}">
                            <span class="submenu-dot"></span><span>Bentuk Kegiatan</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('upa.referensi.status-kerjasama') ? 'active' : '' }}"
                            href="{{ route('upa.referensi.status-kerjasama') }}">
                            <span class="submenu-dot"></span><span>Status Kerjasama</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('upa.referensi.status-evaluasi') ? 'active' : '' }}"
                            href="{{ route('upa.referensi.status-evaluasi') }}">
                            <span class="submenu-dot"></span><span>Status Evaluasi</span>
                        </a>
                        <a class="submenu-item {{ request()->routeIs('upa.referensi.kriteria-mitra') ? 'active' : '' }}"
                            href="{{ route('upa.referensi.kriteria-mitra') }}">
                            <span class="submenu-dot"></span><span>Kriteria Mitra</span>
                        </a>
                        <!-- <a class="submenu-item {{ request()->routeIs('upa.referensi.sumber-dana') ? 'active' : '' }}"
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
            @if (request()->routeIs('upa.kerjasama.create'))
                @include('auth.layout.upa.create_kerjasama')
            @elseif(request()->routeIs('upa.kerjasama.edit'))
                @include('auth.layout.upa.edit_kerjasama')
            @elseif(request()->routeIs('upa.kerjasama.show'))
                @include('auth.layout.upa.detail_kerjasama')
            @elseif(request()->routeIs('upa.dkerjasama'))
                @include('auth.layout.upa.dkerjasama')
            @elseif(request()->routeIs('upa.analitik.status-kerjasama'))
                @include('auth.layout.upa.analitik.status_kerjasama')
            @elseif(request()->routeIs('upa.analitik.klasifikasi-mitra'))
                @include('auth.layout.upa.analitik.klarifikasi-mitra')
            @elseif(request()->routeIs('upa.analitik.geo-mitra'))
                @include('auth.layout.upa.analitik.geo-mitra')
            @elseif(request()->routeIs('upa.mitra.create'))
                @include('auth.layout.upa.mitra.create')
            @elseif(request()->routeIs('upa.mitra.edit'))
                @include('auth.layout.upa.mitra.edit')
            @elseif(request()->routeIs('upa.mitra.show'))
                @include('auth.layout.upa.mitra.detail')
            @elseif(request()->routeIs('upa.form'))
                @include('auth.layout.upa.form.index')
            @elseif(request()->routeIs('upa.mitra'))
                @include('auth.layout.upa.mitra.index')

                {{-- menu institusi --}}
            @elseif(request()->routeIs('upa.institusi'))
                @include('auth.layout.upa.institusi')

            @elseif(request()->routeIs('upa.referensi.bentuk-kegiatan'))
                @include('auth.layout.upa.referensi.bentuk-kegiatan')

            @elseif(request()->routeIs('upa.referensi.status-kerjasama'))
                @include('auth.layout.upa.referensi.status-kerjasama')

            @elseif(request()->routeIs('upa.referensi.status-evaluasi'))
                @include('auth.layout.upa.referensi.status-evaluasi')

            @elseif(request()->routeIs('upa.referensi.kriteria-mitra'))
                @include('auth.layout.upa.referensi.kriteria-mitra')

            @elseif(request()->routeIs('upa.evaluasi.form', 'upa.evaluasi.form_unit'))
                @include('auth.layout.upa.form_evaluasi')
            @elseif(request()->routeIs('upa.evaluasi'))
                @include('auth.layout.upa.evaluasi_kinerja')
            @elseif(request()->routeIs('upa.laporan'))
                @include('auth.layout.upa.laporan')
            @elseif(request()->routeIs('upa.hasil_evaluasi'))
                @include('auth.layout.upa.hasil_evaluasi')
            @else
                @include('auth.layout.upa.dashboard')
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

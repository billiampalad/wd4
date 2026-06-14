<!DOCTYPE html>
<html lang="id" data-theme="light"
    class="{{ request()->routeIs('pimpinan.dashboard') ? 'pimpinan-dashboard-page' : '' }} {{ request()->routeIs('pimpinan.monitoring') ? 'pimpinan-monitoring-page' : '' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Pimpinan | Sistem Informasi Kerjasama Politeknik Negeri Manado</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/auth/user.css') }}" data-turbo-track="reload">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js" data-turbo-track="reload"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body
    class="{{ request()->routeIs('pimpinan.dashboard') ? 'pimpinan-dashboard-page' : '' }} {{ request()->routeIs('pimpinan.monitoring') ? 'pimpinan-monitoring-page' : '' }}">
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
    @php
        $pimpinanExpiryNotifications = collect();
        $pimpinanNotificationToday = now()->startOfDay();
        $pimpinanNotificationLimit = $pimpinanNotificationToday->copy()->addMonthsNoOverflow(3)->endOfDay();

        $pimpinanExpiryNotifications = \App\Models\Cooperation::query()
            ->select(['id', 'title', 'doc_number', 'jenis', 'end_date', 'status'])
            ->where('status', 'aktif')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '>=', $pimpinanNotificationToday->toDateString())
            ->whereDate('end_date', '<=', $pimpinanNotificationLimit->toDateString())
            ->orderBy('end_date')
            ->get()
            ->unique('id')
            ->map(function ($cooperation) use ($pimpinanNotificationToday) {
                $endDate = \Carbon\Carbon::parse($cooperation->end_date)->startOfDay();
                $daysRemaining = max(0, (int) $pimpinanNotificationToday->diffInDays($endDate, false));
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
                    'link' => route('pimpinan.monitoring.detail', $cooperation->id),
                ];
            })
            ->values();
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
                    <input type="text" id="navSearchInput" placeholder="Cari data..." class="search-input" autocomplete="off" />
                    <button type="button" id="navSearchClear" class="search-clear-btn" style="display:none;" title="Bersihkan pencarian">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>

                <button class="icon-btn" id="darkModeBtn" title="Toggle dark mode">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>

                <div class="notification-container">
                    <button class="icon-btn" id="notificationBtn" title="Notifications">
                        <i class="fas fa-bell" id="notificationIcon"></i>
                        @php
                        $user = auth()->user();
                        $query = \App\Models\Notifikasi::where('user_id', $user->id)->where('is_read', 0);

                        // Filter khusus pimpinan agar angka sinkron dengan data yang butuh evaluasi
                        if (strtolower($user->role->role_name ?? '') === 'pimpinan') {
                        $query->where(function($q) {
                        $q->where(function($sourceQuery) {
                        $sourceQuery
                        ->where(function($typedQuery) {
                        $typedQuery
                        ->where(function($typeQuery) {
                        $typeQuery
                        ->whereNull('source_type')
                        ->orWhere('source_type', 'cooperation');
                        })
                        ->whereHas('cooperation', function($sq) {
                        $sq->where('status_dokumen', 'Menunggu Evaluasi');
                        });
                        })
                        ->orWhere(function($typedQuery) {
                        $typedQuery
                        ->where('source_type', 'pengajuan_mitra')
                        ->whereHas('pengajuanKerjasamaMitra', function($sq) {
                        $sq->where('status', 'diajukan');
                        });
                        });
                        })
                        ->orWhereNull('source_id')
                        ->orWhereIn('type', ['evaluasi', 'revisi', 'sudah_revisi']);
                        });
                        }
                        $notifCount = $query->count();
                        $totalNotifCount = $notifCount + $pimpinanExpiryNotifications->count();
                        @endphp
                        <span class="notification-badge" id="notifBadge" style="{{ $totalNotifCount > 0 ? 'display: flex;' : 'display: none;' }}">
                            {{ $totalNotifCount > 9 ? '9+' : $totalNotifCount }}
                        </span>
                    </button>

                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="notification-header">
                            <h3>Notifikasi</h3>
                            <button id="markAllRead"
                                style="background:none; border:none; color:var(--accent); font-size:11px; font-weight:700; cursor:pointer; display:none;">Tandai
                                semua dibaca</button>
                        </div>
                        <div class="notification-list" id="notifList">
                            @forelse ($pimpinanExpiryNotifications as $expiryNotification)
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
                        <!-- <div class="notification-footer">
                            <a href="#">Lihat Semua Notifikasi</a>
                        </div> -->
                    </div>
                    <script type="application/json" id="expiryNotificationsData">
                        @json($pimpinanExpiryNotifications)
                    </script>
                </div>

                <div class="user-chip">
                    <div class="user-avatar" id="userAvatar">{{ auth()->user()->name }}</div>
                    <div class="user-info">
                        <div class="name" id="userName">{{ auth()->user()->profile?->jabatan ?? '-' }}</div>
                        <div class="role">
                            {{ auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? ucwords(str_replace('_', ' ', auth()->user()->role?->role_name ?? 'Pimpinan')) }}
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

            <div class="menu-section">MONITORING</div>

            <a class="menu-item {{ request()->routeIs('pimpinan.dashboard') ? 'active' : '' }}"
                href="{{ route('pimpinan.dashboard') }}">
                <div class="menu-icon"><i class="fas fa-tachometer-alt"></i></div>
                <span>Beranda</span>
            </a>

            <a class="menu-item {{ request()->routeIs('pimpinan.monitoring', 'pimpinan.monitoring.*') || (isset($view) && $view === 'detail_monitoring') ? 'active' : '' }}"
                href="{{ route('pimpinan.monitoring') }}">
                <div class="menu-icon"><i class="fas fa-folder-open"></i></div>
                <span>Monitoring Data</span>
            </a>

            <a class="menu-item {{ request()->routeIs('pimpinan.evaluasi*') ? 'active' : '' }}"
                href="{{ route('pimpinan.evaluasi') }}">
                <div class="menu-icon"><i class="fas fa-file-signature"></i></div>
                <span>Evaluasi Kerjasama</span>
            </a>
            <a class="menu-item {{ request()->routeIs('pimpinan.pengajuan_mitra*') ? 'active' : '' }}"
                href="{{ route('pimpinan.pengajuan_mitra') }}">
                <div class="menu-icon"><i class="fas fa-handshake-angle"></i></div>
                <span>Pengajuan Mitra</span>
            </a>
            <a class="menu-item {{ request()->routeIs('pimpinan.laporan') ? 'active' : '' }}"
                href="{{ route('pimpinan.laporan') }}">
                <div class="menu-icon"><i class="fas fa-chart-simple"></i></div>
                <span>Laporan Global</span>
            </a>
        </aside>

        <!-- Sidebar Toggle (Floating on Border) -->
        <button id="sidebarToggle" class="sidebar-toggle-floating" title="Toggle Sidebar">
            <i class="fas fa-arrow-right-to-bracket"></i>
        </button>

        <!-- ── MAIN ──────────────────────────────────────────────── -->
        @yield('content')
        @if(!View::hasSection('content'))
        @if(request()->routeIs('pimpinan.monitoring'))
        @include('auth.layout.pimpinan.monitoring')
        @elseif(request()->routeIs('pimpinan.evaluasi'))
        @include('auth.layout.pimpinan.evaluasivalidasi')
        @elseif(request()->routeIs('pimpinan.pengajuan_mitra') || (isset($view) && $view == 'pengajuan_mitra'))
        @include('auth.layout.pimpinan.pengajuan_mitra')
        @elseif(request()->routeIs('pimpinan.monitoring.detail') || (isset($view) && $view == 'detail_monitoring'))
        @include('auth.layout.pimpinan.detail_monitoring')
        @elseif(isset($view) && $view == 'detail_evaluasi')
        @include('auth.layout.pimpinan.detail_evaluasi')
        @elseif(request()->routeIs('pimpinan.laporan') || (isset($view) && $view == 'laporan'))
        @include('auth.layout.pimpinan.laporan')
        @else
        @include('auth.layout.pimpinan.dashboard')
        @endif
        @endif

        <div id="sidebarOverlay"></div>
    </div>

    <script src="{{ asset('js/auth/user.js') }}" data-turbo-track="reload"></script>
</body>

</html>

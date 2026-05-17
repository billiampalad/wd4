<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DUDIKA — Sistem Informasi Kerjasama</title>
    <script>
        (() => {
            try {
                const savedTheme = localStorage.getItem('welcome-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme === 'dark' || savedTheme === 'light' ?
                    savedTheme :
                    (prefersDark ? 'dark' : 'light');

                document.documentElement.dataset.theme = theme;
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Serif+Display:ital@0;1&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="{{ asset('css/auth/welcome-stats.css') }}" data-turbo-track="reload">
</head>

<body>

    <!-- ═══ NAV ═══════════════════════════════════════════════ -->
    <nav class="top-nav">
        <div class="nav-inner">
            <button type="button" class="mobile-menu-toggle" data-mobile-menu-toggle aria-controls="mobileSidebar"
                aria-expanded="false" aria-label="Buka menu navigasi">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.4">
                    <path d="M4 7h16" />
                    <path d="M4 12h16" />
                    <path d="M4 17h16" />
                </svg>
            </button>
            <a href="#home" class="logo">
                <div class="logo-mark">
                    <img src="{{ asset('img/logo.png') }}" alt="Handshake" width="30" height="30">
                </div>
                <span class="logo-text">POLIMDO <span>&</span> DUDIKA</span>
            </a>
            <div class="nav-menu" aria-label="Navigasi utama">
                <a href="#home" class="nav-link is-active" data-nav-link>Home</a>
                <a href="#ringkasan" class="nav-link" data-nav-link>Ringkasan</a>
                <a href="#data-kerjasama" class="nav-link" data-nav-link>Data Kerjasama</a>
            </div>
            <div class="nav-right">
                <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false"
                    aria-label="Ubah ke mode gelap">
                    <span class="theme-toggle-orb" aria-hidden="true">
                        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z" />
                        </svg>
                        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2" />
                            <path d="M12 20v2" />
                            <path d="m4.93 4.93 1.41 1.41" />
                            <path d="m17.66 17.66 1.41 1.41" />
                            <path d="M2 12h2" />
                            <path d="M20 12h2" />
                            <path d="m6.34 17.66-1.41 1.41" />
                            <path d="m19.07 4.93-1.41 1.41" />
                        </svg>
                    </span>
                    <span class="theme-toggle-text" data-theme-toggle-label>Mode Gelap</span>
                </button>
                <a href="{{ route('login') }}" class="btn-nav">Login Sistem</a>
            </div>
        </div>
    </nav>

    <aside class="mobile-sidebar" id="mobileSidebar" aria-label="Menu navigasi mobile" aria-hidden="true">
        <div class="mobile-sidebar-head">
            <a href="#home" class="mobile-sidebar-brand" data-mobile-menu-close>
                <span class="logo-mark">
                    <img src="{{ asset('img/logo.png') }}" alt="Handshake" width="30" height="30">
                </span>
                <span class="logo-text">POLIMDO <span>&</span> DUDIKA</span>
            </a>
            <button type="button" class="mobile-sidebar-close" data-mobile-menu-close aria-label="Tutup menu navigasi">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.6">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>
        <div class="mobile-sidebar-section">Navigasi</div>
        <div class="mobile-sidebar-menu">
            <a href="#home" class="mobile-sidebar-link is-active" data-nav-link>
                <span class="mobile-sidebar-icon" aria-hidden="true">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="m3 10.5 9-7 9 7" />
                        <path d="M5 10v10h14V10" />
                    </svg>
                </span>
                <span>Home</span>
            </a>
            <a href="#ringkasan" class="mobile-sidebar-link" data-nav-link>
                <span class="mobile-sidebar-icon" aria-hidden="true">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M4 19V5" />
                        <path d="M8 19V9" />
                        <path d="M12 19V7" />
                        <path d="M16 19v-5" />
                        <path d="M20 19V3" />
                    </svg>
                </span>
                <span>Ringkasan</span>
            </a>
            <a href="#data-kerjasama" class="mobile-sidebar-link" data-nav-link>
                <span class="mobile-sidebar-icon" aria-hidden="true">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M4 7h16" />
                        <path d="M4 12h16" />
                        <path d="M4 17h10" />
                    </svg>
                </span>
                <span>Data Kerjasama</span>
            </a>
        </div>
        <a href="{{ route('login') }}" class="mobile-sidebar-login">Login Sistem</a>
    </aside>
    <div class="mobile-sidebar-overlay" data-mobile-menu-overlay></div>

    <!-- ═══ HERO ════════════════════════════════════════════════ -->
    <header class="hero" id="home">
        <div class="hero-grid-bg"></div>
        <div class="hero-blob"></div>
        <div class="hero-inner">
            <div class="hero-text">
                <div class="hero-eyebrow">Platform Resmi Kerjasama Kampus</div>
                <h1>Sistem Informasi Kerjasama<br><em>Politeknik Negeri Manado</em></h1>
                <p class="hero-desc">
                    Sistem informasi untuk memantau dan menelusuri seluruh aktivitas kerjasama antara Kampus dengan Dunia
                    Usaha, Dunia Industri, dan Institusi mitra lainnya.
                </p>
                <div class="hero-cta">
                    <a href="#data-kerjasama" class="btn-primary">Telusuri Data Kerjasama</a>
                    <a href="{{ route('login') }}" class="btn-ghost">
                        Login untuk pengelola
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Preview card (decorative / illustrative) -->
            <div class="hero-visual">
                <div class="hero-card-preview">
                    <div class="preview-header">
                        <div class="preview-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span class="preview-label">Data Kerjasama · Public View</span>
                    </div>

                    <div class="preview-mini-card" style="border-left-color: var(--amber-500);">
                        <div class="pmcard-top">
                            <span class="pmcard-title">Pelatihan Vokasi Industri Manufaktur 2024</span>
                            <span class="pmcard-badge badge-draft">Draft</span>
                        </div>
                        <div class="pmcard-meta">PT Industri Sulawesi Utara &nbsp;·&nbsp; PKS &nbsp;·&nbsp; Jan – Des
                            2024</div>
                    </div>

                    <div class="preview-mini-card" style="border-left-color: var(--blue-500);">
                        <div class="pmcard-top">
                            <span class="pmcard-title">Kerjasama Riset dan Pengembangan Produk</span>
                            <span class="pmcard-badge badge-menunggu">Menunggu Evaluasi</span>
                        </div>
                        <div class="pmcard-meta">Balai Litbang Kemenristek &nbsp;·&nbsp; Penelitian &nbsp;·&nbsp; 2025
                        </div>
                    </div>

                    <div class="preview-mini-card" style="border-left-color: var(--green-500);">
                        <div class="pmcard-top">
                            <span class="pmcard-title">MoU Rekrutmen Alumni Program Teknik</span>
                            <span class="pmcard-badge badge-selesai">Disahkan</span>
                        </div>
                        <div class="pmcard-meta">CV Maju Bersama &nbsp;·&nbsp; MoU &nbsp;·&nbsp; Mar 2023 – Mar 2025
                        </div>
                    </div>
                </div>

                <div class="hero-floating-stat">
                    <div class="hfs-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#378ADD"
                            stroke-width="2">
                            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                            <polyline points="16 7 22 7 22 13" />
                        </svg>
                    </div>
                    <div>
                        <div class="hfs-num">{{ $stats['total_kerjasama'] ?? 0 }}</div>
                        <div class="hfs-lbl">Total Kerjasama</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ═══ STATS CARD ═══════════════════════════════════════ -->
    <section class="stats-strip" id="ringkasan" aria-labelledby="stats-overview-title">
        <div class="stats-inner">
            <div class="stats-heading">
                <h2 id="stats-overview-title">Ringkasan data kerjasama</h2>
                <p>Statistik terkini dari seluruh kegiatan kerja sama institusi kami. Berikut informasi ringkas mengenai jumlah kerjasama, jumlah mitra yang tergabung, lalu status pelaksanaan program, hingga persebaran skala kemitraan kami.</p>
            </div>

            <div class="stats-card-grid">
                <article class="stat-card">
                    <div class="stat-card-icon stat-card-icon-blue" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M3 12h18" />
                            <path d="M3 6h18" />
                            <path d="M3 18h12" />
                        </svg>
                    </div>
                    <div class="stat-card-meta">Jumlah Kerjasama</div>
                    <div class="stat-num">{{ $stats['total_kerjasama'] ?? 0 }}</div>
                    <p class="stat-desc">Jumlah kerjasama Politeknik sampai saat ini</p>
                </article>

                <article class="stat-card">
                    <div class="stat-card-icon stat-card-icon-green" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div class="stat-card-meta">Jumlah Mitra</div>
                    <div class="stat-num">{{ $stats['total_mitra'] ?? 0 }}</div>
                    <p class="stat-desc">Organisasi, industri, dan institusi yang sudah tercatat sebagai mitra.</p>
                </article>

                <article class="stat-card">
                    <div class="stat-card-icon stat-card-icon-amber" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                            <polyline points="16 7 22 7 22 13" />
                        </svg>
                    </div>
                    <div class="stat-card-meta">Status Berjalan</div>
                    <div class="stat-num">{{ $stats['total_aktif'] ?? 0 }}</div>
                    <div class="stat-lbl">Aktif</div>
                    <p class="stat-desc">Kerjasama yang sedang berjalan.</p>
                </article>

                <article class="stat-card stat-card-breakdown">
                    <div class="stat-card-icon stat-card-icon-purple" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M12 21c4.97 0 9-4.03 9-9s-4.03-9-9-9-9 4.03-9 9 4.03 9 9 9Z" />
                            <path d="M3.6 9h16.8" />
                            <path d="M3.6 15h16.8" />
                            <path d="M12 3a15.3 15.3 0 0 1 0 18" />
                            <path d="M12 3a15.3 15.3 0 0 0 0 18" />
                        </svg>
                    </div>
                    <div class="stat-card-meta">Cakupan Mitra</div>
                    <div class="stat-lbl">Mitra Nasional & Internasional</div>

                    <div class="stat-split" aria-label="Rincian kategori mitra">
                        <div class="stat-split-item">
                            <span class="stat-split-num">{{ $stats['mitra_nasional'] ?? 0 }}</span>
                            <span class="stat-split-lbl">Nasional</span>
                        </div>
                        <div class="stat-split-item">
                            <span class="stat-split-num">{{ $stats['mitra_internasional'] ?? 0 }}</span>
                            <span class="stat-split-lbl">Internasional</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ═══ TRUST STRIP ════════════════════════════════════════ -->
    <div class="trust-strip">
        <div class="trust-inner">
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Data terverifikasi & akurat
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path
                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
                Akses publik & transparan
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <path d="M3 9h18M9 21V9" />
                </svg>
                Diperbarui secara berkala
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Multi-mitra & multi-bidang
            </div>
        </div>
    </div>

    <!-- ═══ MAIN DATA SECTION ══════════════════════════════════ -->
    <section class="data-section" id="data-kerjasama">
        <main class="main-wrap">
            @php
                $selectedKategoriMitra = request('kategori_mitra', 'all');
                $selectedKategoriMitra = in_array($selectedKategoriMitra, ['all', 'nasional', 'internasional'], true)
                    ? $selectedKategoriMitra
                    : 'all';
                $searchTerm = trim((string) request('search', ''));
                $selectedSort = request('sort', 'latest');
                $selectedSort = in_array($selectedSort, ['latest', 'oldest', 'title', 'ending_soon'], true)
                    ? $selectedSort
                    : 'latest';
                $kategoriLabels = [
                    'nasional' => 'Mitra Nasional',
                    'internasional' => 'Mitra Internasional',
                ];
                $sortLabels = [
                    'latest' => 'Terbaru',
                    'oldest' => 'Terlama',
                    'title' => 'A-Z',
                    'ending_soon' => 'Segera Berakhir',
                ];
                $activeFilterChips = [];

                if ($selectedKategoriMitra !== 'all') {
                    $activeFilterChips[] = $kategoriLabels[$selectedKategoriMitra] ?? ucfirst($selectedKategoriMitra);
                }

                if ($searchTerm !== '') {
                    $activeFilterChips[] = 'Pencarian: "' . $searchTerm . '"';
                }

                if ($selectedSort !== 'latest') {
                    $activeFilterChips[] = 'Urutan: ' . ($sortLabels[$selectedSort] ?? ucfirst($selectedSort));
                }

                $totalResults = isset($kerjasama) ? $kerjasama->total() : 0;
            @endphp

            <div class="section-top">
                <div>
                    <div class="section-eyebrow">Data Kerjasama</div>
                    <h2 class="section-title">Eksplorasi Aktivitas Kerjasama</h2>
                    <p class="section-sub">Daftar kegiatan kerjasama yang telah berjalan</p>
                </div>

                <form action="/" method="GET" class="filter-bar" data-landing-filter>
                <div class="filter-toggle" aria-label="Filter kategori kerjasama">
                    <label class="filter-option {{ $selectedKategoriMitra === 'all' ? 'is-active' : '' }}">
                        <input type="radio" name="kategori_mitra" value="all"
                            {{ $selectedKategoriMitra === 'all' ? 'checked' : '' }}>
                        <span>Semua</span>
                    </label>
                    <label class="filter-option {{ $selectedKategoriMitra === 'nasional' ? 'is-active' : '' }}">
                        <input type="radio" name="kategori_mitra" value="nasional"
                            {{ $selectedKategoriMitra === 'nasional' ? 'checked' : '' }}>
                        <span>Nasional</span>
                    </label>
                    <label class="filter-option {{ $selectedKategoriMitra === 'internasional' ? 'is-active' : '' }}">
                        <input type="radio" name="kategori_mitra" value="internasional"
                            {{ $selectedKategoriMitra === 'internasional' ? 'checked' : '' }}>
                        <span>Internasional</span>
                    </label>
                </div>

                <label class="sort-wrap">
                    <span class="sort-label">Urutkan</span>
                    <select name="sort" class="sort-select">
                        <option value="latest" {{ $selectedSort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ $selectedSort === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="title" {{ $selectedSort === 'title' ? 'selected' : '' }}>A-Z</option>
                        <option value="ending_soon" {{ $selectedSort === 'ending_soon' ? 'selected' : '' }}>Segera Berakhir</option>
                    </select>
                </label>

                <div class="search-wrap">
                    <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="search" class="search-input"
                        placeholder="Cari data kerjasama Anda..." value="{{ request('search') }}">
                </div>
                <button type="button" class="btn-reset-search" data-search-reset @if ($searchTerm === '') hidden @endif>
                    Reset pencarian
                </button>
                <button type="submit" class="btn-search">Cari</button>
                </form>
            </div>

            <div class="results-overview" aria-live="polite">
                <div class="results-overview-copy">
                    <p class="results-count">{{ number_format($totalResults, 0, ',', '.') }} kerjasama ditemukan</p>
                    <p class="results-caption">
                        @if ($activeFilterChips)
                            Menampilkan hasil yang sesuai dengan filter dan kata kunci aktif.
                        @else
                            Menampilkan seluruh data kerjasama publik yang tersedia saat ini.
                        @endif
                    </p>
                </div>
                <div class="results-chips" aria-label="Filter aktif">
                    @forelse ($activeFilterChips as $chip)
                        <span class="result-chip is-active">{{ $chip }}</span>
                    @empty
                        <span class="result-chip">Semua data publik</span>
                    @endforelse

                    @if ($activeFilterChips)
                        <button type="button" class="result-chip result-chip-reset" data-reset-filters>Reset filter</button>
                    @endif
                </div>
            </div>

        @if (isset($kerjasama) && $kerjasama->count() > 0)
            <div class="cards-grid">
                @foreach ($kerjasama as $item)
                    @php
                        $status = trim(strtolower(str_replace(['_', '-'], ' ', $item->status ?? '')));
                        $statusClass = match (true) {
                            $status === 'aktif' => 'badge-active',
                            str_contains($status, 'perpanjangan') => 'badge-warning',
                            in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa']) => 'badge-expired',
                            $status === 'tidak aktif' => 'badge-inactive',
                            $status === 'proses' => 'badge-process',
                            default => 'badge-inactive',
                        };
                        $statusLabel = ucwords($item->status ?? 'Draft');

                        $mitraNames = $item->mitra ? $item->mitra->nama_mitra : 'Mitra belum ditentukan';
                        $mitraInit = strtoupper(substr($mitraNames, 0, 2));
                        $hasDates = $item->start_date && $item->end_date;
                    @endphp

                    <div class="kcard {{ $statusClass }}"
                        onclick="openModal(
                                                    {{ $item->id }},
                                                    `{{ addslashes($item->title) }}`,
                                                    `{{ addslashes($mitraNames) }}`,
                                                    `{{ addslashes($item->doc_number ?? 'Belum ada') }}`,
                                                    `{{ $hasDates ? $item->start_date->format('d M Y') . ' – ' . $item->end_date->format('d M Y') : 'Tanggal belum lengkap' }}`,
                                                    `{{ addslashes($statusLabel) }}`,
                                                    `{{ $statusClass }}`
                                                )">
                        <div class="kcard-accent"></div>

                        <div class="kcard-top">
                            <h3 class="kcard-title">{{ $item->title }}</h3>
                            <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        <div class="kcard-mitra">
                            <div class="mitra-dot">{{ $mitraInit }}</div>
                            {{ $mitraNames }}
                        </div>

                        <div class="kcard-meta">
                            <div class="meta-row">
                                <span class="meta-key">No. Dokumen</span>
                                <span class="meta-val">{{ $item->doc_number ?? 'Belum ada' }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-key">Durasi</span>
                                <span class="meta-val">
                                    @if ($hasDates)
                                        {{ $item->start_date->format('d M Y') }} –
                                        {{ $item->end_date->format('d M Y') }}
                                    @else
                                        Tanggal belum lengkap
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="kcard-footer">
                            <button class="btn-detail"
                                onclick="event.stopPropagation(); openModal(
                            {{ $item->id }},
                                `{{ addslashes($item->title) }}`,
                                `{{ addslashes($mitraNames) }}`,
                                `{{ addslashes($item->doc_number ?? 'Belum ada') }}`,
                                `{{ $hasDates ? $item->start_date->format('d M Y') . ' – ' . $item->end_date->format('d M Y') : 'Tanggal belum lengkap' }}`,
                                `{{ addslashes($statusLabel) }}`,
                                `{{ $statusClass }}`
                            )">
                                Lihat Detail
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $kerjasama->links('pagination::simple-bootstrap-4') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
                <h3>Belum ada data kerjasama</h3>
                <p>Data kerjasama yang dipublikasikan belum tersedia atau tidak ditemukan untuk kata kunci atau filter
                    yang dipilih.</p>
            </div>
        @endif
        </main>
    </section>

    <!-- ═══ MODAL DETAIL ════════════════════════════════════════ -->
    <div class="modal-overlay" id="detailModal" onclick="closeModal(event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="modal-head">
                <div>
                    <p
                        style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--ink-faint);margin-bottom:0.35rem;">
                        Detail Kerjasama</p>
                    <p style="font-size:0.68rem;color:var(--ink-faint);margin-bottom:0.5rem;">Judul Kegiatan</p>
                    <h2 id="modal-title"
                        style="font-family:'DM Serif Display',serif;font-size:1.35rem;color:var(--ink);line-height:1.3;">
                    </h2>
                </div>
                <button class="modal-close" onclick="document.getElementById('detailModal').classList.remove('open')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-section-label">Informasi Mitra</p>
                <div class="modal-field">
                    <span class="modal-fkey">Nama Mitra</span>
                    <span class="modal-fval" id="modal-mitra"></span>
                </div>

                <p class="modal-section-label">Rincian Kerjasama</p>
                <div class="modal-field">
                    <span class="modal-fkey">Nomor MoU</span>
                    <span class="modal-fval" id="modal-nomou"></span>
                </div>
                <div class="modal-field">
                    <span class="modal-fkey">Periode</span>
                    <span class="modal-fval" id="modal-periode"></span>
                </div>
                <div class="modal-field">
                    <span class="modal-fkey">Status</span>
                    <span class="modal-fval">
                        <span id="modal-status-badge" class="status-pill"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ FOOTER ══════════════════════════════════════════════ -->
    <footer>
        <p>&copy; {{ date('Y') }} Sistem Informasi Kerjasama DUDIKA &nbsp;·&nbsp; Hak cipta dilindungi</p>
    </footer>

    <script src="{{ asset('js/index.js') }}" data-turbo-track="reload"></script>
</body>

</html>

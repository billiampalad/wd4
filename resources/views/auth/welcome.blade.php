<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Sistem Informasi Kerjasama Politeknik Negeri Manado</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="{{ asset('css/auth/welcome-stats.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>

    <!-- ═══ NAV ═══════════════════════════════════════════════ -->
    <header class="nav-wrapper">
        <nav class="top-nav" aria-label="Navigasi Utama">
            <div class="nav-inner">
                <button type="button" class="mobile-menu-toggle" data-mobile-menu-toggle aria-controls="mobileSidebar"
                    aria-expanded="false" aria-label="Buka menu navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                </button>
                
                <a href="#home" class="logo">
                    <div class="logo-mark-wrap">
                        <div class="logo-mark">
                            <img src="{{ asset('img/logo.png') }}" alt="Polimdo Logo" width="28" height="28">
                        </div>
                        <span class="logo-pulse-ring"></span>
                    </div>
                    <div class="logo-brand-meta">
                        <span class="logo-text">POLIMDO <span class="logo-amp">&amp;</span> DUDIKA</span>
                        <span class="logo-tagline">Sistem Informasi Kerjasama</span>
                    </div>
                </a>

                <div class="nav-menu" aria-label="Navigasi utama">
                    <a href="#home" class="nav-link is-active" data-nav-link>
                        <svg class="nav-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Home</span>
                    </a>
                    <a href="#ringkasan" class="nav-link" data-nav-link>
                        <svg class="nav-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                        <span>Ringkasan</span>
                    </a>
                    <a href="#visualisasi-data" class="nav-link" data-nav-link>
                        <svg class="nav-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10h-10z"/></svg>
                        <span>Visualisasi</span>
                    </a>
                    <a href="#data-kerjasama" class="nav-link" data-nav-link>
                        <svg class="nav-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        <span>Data Kerjasama</span>
                    </a>
                </div>

                <div class="nav-right">
                    <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false"
                        aria-label="Ubah ke mode gelap">
                        <span class="theme-toggle-orb" aria-hidden="true">
                            <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z" />
                            </svg>
                            <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="4" />
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
                            </svg>
                        </span>
                        <span class="theme-toggle-text" data-theme-toggle-label>Mode Gelap</span>
                    </button>

                    <a href="{{ route('login') }}" class="btn-nav">
                        <span>Login Sistem</span>
                        <svg class="btn-nav-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <aside class="mobile-sidebar" id="mobileSidebar" aria-label="Menu navigasi mobile" aria-hidden="true">
        <div class="mobile-sidebar-head">
            <a href="#home" class="mobile-sidebar-brand" data-mobile-menu-close>
                <div class="logo-mark-wrap">
                    <div class="logo-mark">
                        <img src="{{ asset('img/logo.png') }}" alt="Polimdo Logo" width="26" height="26">
                    </div>
                    <span class="logo-pulse-ring"></span>
                </div>
                <div class="logo-brand-meta">
                    <span class="logo-text">POLIMDO <span class="logo-amp">&amp;</span> DUDIKA</span>
                    <span class="logo-tagline">Menu Navigasi Mobile</span>
                </div>
            </a>
            <button type="button" class="mobile-sidebar-close" data-mobile-menu-close aria-label="Tutup menu navigasi">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <div class="mobile-sidebar-body">
            <div class="mobile-sidebar-section">
                <span class="sidebar-section-dot"></span>
                <span>JELAJAH PLATFORM</span>
            </div>

            <nav class="mobile-sidebar-menu" aria-label="Menu navigasi seluler">
                <a href="#home" class="mobile-sidebar-link is-active" data-nav-link data-mobile-menu-close>
                    <span class="mobile-sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </span>
                    <span class="mobile-link-text">Home</span>
                    <svg class="mobile-link-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <a href="#ringkasan" class="mobile-sidebar-link" data-nav-link data-mobile-menu-close>
                    <span class="mobile-sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 20V10M12 20V4M6 20v-6" />
                        </svg>
                    </span>
                    <span class="mobile-link-text">Ringkasan</span>
                    <svg class="mobile-link-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <a href="#visualisasi-data" class="mobile-sidebar-link" data-nav-link data-mobile-menu-close>
                    <span class="mobile-sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 2a10 10 0 0 1 10 10h-10z" />
                        </svg>
                    </span>
                    <span class="mobile-link-text">Visualisasi</span>
                    <svg class="mobile-link-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <a href="#data-kerjasama" class="mobile-sidebar-link" data-nav-link data-mobile-menu-close>
                    <span class="mobile-sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                    </span>
                    <span class="mobile-link-text">Data Kerjasama</span>
                    <svg class="mobile-link-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </nav>
        </div>

        <div class="mobile-sidebar-footer">
            <a href="{{ route('login') }}" class="mobile-sidebar-login">
                <span>Login Sistem</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    <polyline points="10 17 15 12 10 7" />
                    <line x1="15" y1="12" x2="3" y2="12" />
                </svg>
            </a>
            <div class="mobile-sidebar-info">
                <span>Politeknik Negeri Manado</span>
                <span class="dot-separator">•</span>
                <span>Kemitraan Vokasi</span>
            </div>
        </div>
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
                    <a href="javascript:void(0)" onclick="openSubmissionChoiceModal()" class="btn-ghost">
                        Ajukan kerja sama
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
    <section class="stats-new-strip" id="ringkasan" aria-labelledby="stats-overview-title">
        <!-- Decorative Glow Backgrounds -->
        <div class="stats-glow glow-top-right"></div>
        <div class="stats-glow glow-bottom-left"></div>

        <div class="stats-new-inner">
            <header class="stats-new-header">
                <div class="stats-title-wrap">
                    <div class="stats-title-accent"></div>
                    <h2 id="stats-overview-title" class="stats-new-title">
                        Ringkasan data <span class="stats-text-gradient">kerjasama</span>
                    </h2>
                </div>
                <p class="stats-new-desc">
                    <span class="stats-desc-highlight">Statistik terkini</span> dari seluruh kegiatan kerja sama institusi kami. Berikut informasi ringkas mengenai jumlah kerjasama, jumlah mitra yang tergabung, lalu status pelaksanaan program, hingga persebaran skala kemitraan kami.
                </p>
            </header>

            <div class="stats-new-grid">
                <!-- Card 1: Total Kerjasama -->
                <a href="#data-kerjasama" class="stats-glass-card stat-card-hover-blue group" data-landing-stat data-stat-scope="kerjasama" data-stat-kategori="all" data-stat-status="all">
                    <div class="card-glow glow-blue"></div>
                    <div class="card-header">
                        <div class="card-icon-box text-blue">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">view_headline</span>
                        </div>
                        <h3 class="card-title">TOTAL KERJASAMA</h3>
                        <span class="material-symbols-outlined card-arrow group-hover:arrow-active">arrow_outward</span>
                    </div>
                    <div class="card-content">
                        <div class="card-stats">
                            <div class="card-value">{{ $stats['total_kerjasama'] ?? 0 }}</div>
                            <div class="card-trend trend-up">
                                <span class="material-symbols-outlined">trending_up</span>
                                +12% bulan ini
                            </div>
                        </div>
                        <!-- Mini Sparkline Area Chart -->
                        <div class="card-chart">
                            <svg class="chart-svg" viewBox="0 0 100 40">
                                <defs>
                                    <linearGradient id="gradient-area" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="var(--stats-blue)" stop-opacity="0.4"></stop>
                                        <stop offset="100%" stop-color="var(--stats-blue)" stop-opacity="0"></stop>
                                    </linearGradient>
                                </defs>
                                <path d="M0,40 L0,30 L20,25 L40,35 L60,15 L80,20 L100,5 L100,40 Z" fill="url(#gradient-area)"></path>
                                <path d="M0,30 L20,25 L40,35 L60,15 L80,20 L100,5" fill="none" stroke="var(--stats-blue)" stroke-width="2" vector-effect="non-scaling-stroke"></path>
                                <circle cx="100" cy="5" fill="var(--stats-blue)" r="3"></circle>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Card 2: Total Mitra -->
                <a href="#data-kerjasama" class="stats-glass-card stat-card-hover-yellow group" data-landing-stat data-stat-scope="mitra" data-stat-kategori="all" data-stat-status="all">
                    <div class="card-glow glow-yellow"></div>
                    <div class="card-header">
                        <div class="card-icon-box text-yellow">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">groups</span>
                        </div>
                        <h3 class="card-title">TOTAL MITRA</h3>
                        <span class="material-symbols-outlined card-arrow group-hover:arrow-active">arrow_outward</span>
                    </div>
                    <div class="card-content">
                        <div class="card-stats">
                            <div class="card-value">{{ $stats['total_mitra'] ?? 0 }}</div>
                            <div class="card-trend trend-up">
                                <span class="material-symbols-outlined">trending_up</span>
                                +3% bulan ini
                            </div>
                        </div>
                        <!-- Mini Bar Chart -->
                        <div class="card-chart chart-bar">
                            <div class="bar-col col-low"><div class="bar-tooltip">Ind</div></div>
                            <div class="bar-col col-mid"><div class="bar-tooltip">Ins</div></div>
                            <div class="bar-col col-high"><div class="bar-tooltip">Org</div></div>
                        </div>
                    </div>
                </a>

                <!-- Card 3: Status Berjalan -->
                <a href="#data-kerjasama" class="stats-glass-card stat-card-hover-blue group" data-landing-stat data-stat-scope="kerjasama" data-stat-kategori="all" data-stat-status="aktif">
                    <div class="card-glow glow-blue"></div>
                    <div class="card-header">
                        <div class="card-icon-box text-blue">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">trending_up</span>
                        </div>
                        <h3 class="card-title">STATUS BERJALAN</h3>
                        <span class="material-symbols-outlined card-arrow group-hover:arrow-active">arrow_outward</span>
                    </div>
                    <div class="card-content items-center h-full">
                        <div class="card-stats">
                            <div class="card-value-group">
                                <div class="card-value">{{ $stats['total_aktif'] ?? 0 }}</div>
                                <div class="card-lbl">Aktif</div>
                            </div>
                            <div class="card-subtext">Dari {{ $stats['total_kerjasama'] ?? 0 }} program total</div>
                        </div>
                        <!-- Progress Ring -->
                        <div class="card-ring">
                            <svg class="ring-svg" viewBox="0 0 36 36">
                                <path class="ring-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="4"></path>
                                <path class="ring-fg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-dasharray="100, 100" stroke-width="4"></path>
                            </svg>
                            <div class="ring-text">
                                <span class="ring-val">{{ $stats['total_aktif'] ?? 0 }}</span>
                                <span class="ring-lbl">Nas</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Cakupan Mitra -->
            <div class="stats-new-breakdown">
                <div class="breakdown-header">
                    <div class="breakdown-icon">
                        <span class="material-symbols-outlined">public</span>
                    </div>
                    <h2 class="breakdown-title">Cakupan Mitra</h2>
                </div>
                
                <div class="breakdown-grid">
                    <!-- Mitra Nasional -->
                    <div class="stats-glass-card break-card break-hover-blue">
                        <div class="break-head">
                            <div class="break-head-left">
                                <p class="break-subtitle text-blue">Mitra Nasional</p>
                                <div class="break-value-group">
                                    <h3 class="break-value">{{ $stats['mitra_nasional'] ?? 0 }}</h3>
                                    <span class="break-trend trend-up"><span class="material-symbols-outlined">trending_up</span>+5%</span>
                                </div>
                            </div>
                            <div class="break-badge badge-green">Dominan</div>
                        </div>
                        <div class="break-map-box">
                            <div class="map-bg-pattern"></div>
                            <div class="map-bg-icon"><span class="material-symbols-outlined">map</span></div>
                            <div class="map-bars">
                                <div class="map-bar-item">
                                    <div class="map-bar-lbl"><span>Jawa &amp; Bali</span><span>9</span></div>
                                    <div class="map-bar-track"><div class="map-bar-fill fill-blue" style="width: 53%"></div></div>
                                </div>
                                <div class="map-bar-item">
                                    <div class="map-bar-lbl"><span>Sumatra</span><span>5</span></div>
                                    <div class="map-bar-track"><div class="map-bar-fill fill-blue-60" style="width: 29%"></div></div>
                                </div>
                                <div class="map-bar-item">
                                    <div class="map-bar-lbl"><span>Lainnya</span><span>{{ ($stats['mitra_nasional'] ?? 0) - 14 }}</span></div>
                                    <div class="map-bar-track"><div class="map-bar-fill fill-blue-30" style="width: 18%"></div></div>
                                </div>
                            </div>
                            <div class="map-footer"><p>Sebaran Wilayah Indonesia</p></div>
                        </div>
                    </div>

                    <!-- Mitra Internasional -->
                    <div class="stats-glass-card break-card break-hover-yellow">
                        <div class="break-head">
                            <div class="break-head-left">
                                <p class="break-subtitle text-yellow">Mitra Internasional</p>
                                <div class="break-value-group">
                                    <h3 class="break-value">{{ $stats['mitra_internasional'] ?? 0 }}</h3>
                                    <span class="break-trend text-yellow-60">Stabil</span>
                                </div>
                            </div>
                            <div class="break-badge badge-yellow">Berkembang</div>
                        </div>
                        <div class="break-map-box horizontal">
                            <div class="map-bg-icon center"><span class="material-symbols-outlined">public</span></div>
                            <div class="globe-ring">
                                <svg viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" fill="none" r="15.9155" stroke="rgba(var(--stats-white-rgb), 0.05)" stroke-width="3"></circle>
                                    <circle cx="18" cy="18" fill="none" r="15.9155" stroke="var(--stats-yellow)" stroke-dasharray="66 100" stroke-width="3"></circle>
                                    <circle cx="18" cy="18" fill="none" opacity="0.4" r="15.9155" stroke="var(--stats-yellow)" stroke-dasharray="34 100" stroke-dashoffset="-66" stroke-width="3"></circle>
                                </svg>
                                <div class="globe-text">
                                    <span>100%</span>
                                    <small>Global</small>
                                </div>
                            </div>
                            <div class="globe-bars">
                                <div class="map-bar-item">
                                    <div class="map-bar-lbl"><span>Asia Pasifik</span><span class="text-yellow">2</span></div>
                                    <div class="map-bar-track"><div class="map-bar-fill fill-yellow" style="width: 66%"></div></div>
                                </div>
                                <div class="map-bar-item">
                                    <div class="map-bar-lbl"><span>Eropa</span><span class="text-yellow-60">1</span></div>
                                    <div class="map-bar-track"><div class="map-bar-fill fill-yellow-40" style="width: 33%"></div></div>
                                </div>
                                <div class="globe-badges">
                                    <span>Global Reach</span>
                                    <span>2 Continents</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ TRUST STRIP ════════════════════════════════════════ -->
    <div class="trust-new-strip">
        <div class="trust-marquee-container">
            <div class="trust-marquee-mask">
                <div class="trust-animate-marquee">
                    <!-- Badge -->
                    <div class="trust-badge">
                        <span class="trust-badge-dot">
                            <span class="trust-badge-ping"></span>
                            <span class="trust-badge-core"></span>
                        </span>
                        <span class="trust-badge-text">System Integrity</span>
                    </div>
                    
                    <!-- Marquee Content Set 1 -->
                    <div class="trust-item-group">
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">verified_user</span>
                            <span class="trust-text">Data terverifikasi &amp; akurat</span>
                        </div>
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">public</span>
                            <span class="trust-text">Akses publik &amp; transparan</span>
                        </div>
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">update</span>
                            <span class="trust-text">Diperbarui secara berkala</span>
                        </div>
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">groups</span>
                            <span class="trust-text">Multi-mitra &amp; multi-bidang</span>
                        </div>
                    </div>

                    <!-- Marquee Content Set 2 (Duplicate for seamless loop) -->
                    <div class="trust-item-group">
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">verified_user</span>
                            <span class="trust-text">Data terverifikasi &amp; akurat</span>
                        </div>
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">public</span>
                            <span class="trust-text">Akses publik &amp; transparan</span>
                        </div>
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">update</span>
                            <span class="trust-text">Diperbarui secara berkala</span>
                        </div>
                        <div class="trust-item">
                            <span class="material-symbols-outlined trust-icon">groups</span>
                            <span class="trust-text">Multi-mitra &amp; multi-bidang</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ MAIN DATA SECTION ══════════════════════════════════ -->
    @php
        $analytics = $landingAnalytics ?? [];
        $analyticsContext = $analytics['context'] ?? null;
        $statusBreakdown = $analytics['status_breakdown'] ?? ['has_data' => false, 'items' => []];
        $trendByYear = $analytics['trend_by_year'] ?? ['has_data' => false, 'points' => []];
        $mitraComposition = $analytics['mitra_composition'] ?? ['has_data' => false, 'items' => []];
        $topClassifications = $analytics['top_classifications'] ?? ['has_data' => false, 'items' => []];
        $topFields = $analytics['top_fields'] ?? ['has_data' => false, 'items' => []];
        $attentionPanel = $analytics['attention'] ?? ['has_data' => false, 'items' => []];
        $chartPayload = $analytics['chart_payload'] ?? [];
    @endphp

    @include('auth.partials.landing-analytics')

    <section class="data-section" id="data-kerjasama">
        <main class="main-wrap">
            @php
                $selectedDataScope = $dataScope ?? request('data_scope', 'kerjasama');
                $selectedDataScope = in_array($selectedDataScope, ['kerjasama', 'mitra'], true)
                    ? $selectedDataScope
                    : 'kerjasama';
                $selectedKategoriMitra = request('kategori_mitra', 'all');
                $selectedKategoriMitra = in_array($selectedKategoriMitra, ['all', 'nasional', 'internasional'], true)
                    ? $selectedKategoriMitra
                    : 'all';
                $searchTerm = trim((string) request('search', ''));
                $selectedSort = request('sort', 'latest');
                $selectedSort = $selectedDataScope === 'mitra'
                    ? (in_array($selectedSort, ['latest', 'oldest', 'title', 'title_desc', 'most_cooperations'], true)
                        ? $selectedSort
                        : 'latest')
                    : (in_array($selectedSort, ['latest', 'oldest', 'title', 'ending_soon'], true)
                        ? $selectedSort
                        : 'latest');
                $selectedStatusScope = request('status_scope', 'all');
                $selectedStatusScope = in_array($selectedStatusScope, ['all', 'aktif'], true)
                    ? $selectedStatusScope
                    : 'all';
                $selectedStatusScope = $selectedDataScope === 'kerjasama' ? $selectedStatusScope : 'all';
                $dataScopeLabels = [
                    'kerjasama' => 'Data Kerjasama',
                    'mitra' => 'Data Mitra',
                ];
                $kategoriLabels = [
                    'nasional' => 'Mitra Nasional',
                    'internasional' => 'Mitra Internasional',
                ];
                $sortLabels = $selectedDataScope === 'mitra'
                    ? [
                        'latest' => 'Terbaru',
                        'oldest' => 'Terlama',
                        'title' => 'Nama A-Z',
                        'title_desc' => 'Nama Z-A',
                        'most_cooperations' => 'Kerjasama Terbanyak',
                    ]
                    : [
                        'latest' => 'Terbaru',
                        'oldest' => 'Terlama',
                        'title' => 'A-Z',
                        'ending_soon' => 'Segera Berakhir',
                    ];
                $statusScopeLabels = [
                    'aktif' => 'Status Aktif',
                ];
                $activeFilterChips = [];

                if ($selectedDataScope !== 'kerjasama') {
                    $activeFilterChips[] = $dataScopeLabels[$selectedDataScope] ?? ucfirst($selectedDataScope);
                }

                if ($selectedKategoriMitra !== 'all') {
                    $activeFilterChips[] = $kategoriLabels[$selectedKategoriMitra] ?? ucfirst($selectedKategoriMitra);
                }

                if ($searchTerm !== '') {
                    $activeFilterChips[] = 'Pencarian: "' . $searchTerm . '"';
                }

                if ($selectedSort !== 'latest') {
                    $activeFilterChips[] = 'Urutan: ' . ($sortLabels[$selectedSort] ?? ucfirst($selectedSort));
                }

                if ($selectedStatusScope !== 'all') {
                    $activeFilterChips[] = $statusScopeLabels[$selectedStatusScope] ?? ucfirst($selectedStatusScope);
                }

                $geoCountry = trim((string) request('geo_country', ''));
                $geoProvince = trim((string) request('geo_province', ''));

                if ($geoCountry !== '') {
                    $activeFilterChips[] = 'Negara: ' . ucwords($geoCountry);
                }

                if ($geoProvince !== '') {
                    $activeFilterChips[] = 'Provinsi: ' . ucwords($geoProvince);
                }

                $totalResults = $selectedDataScope === 'mitra'
                    ? (isset($mitras) ? $mitras->total() : 0)
                    : (isset($kerjasama) ? $kerjasama->total() : 0);
                $resultLabel = $selectedDataScope === 'mitra' ? 'mitra' : 'kerjasama';
                $sectionEyebrow = $selectedDataScope === 'mitra' ? 'Data Mitra' : 'Data Kerjasama';
                $sectionTitle = $selectedDataScope === 'mitra'
                    ? 'Eksplorasi Profil Mitra'
                    : 'Eksplorasi Aktivitas Kerjasama';
                $sectionSubtitle = $selectedDataScope === 'mitra'
                    ? 'Telusuri organisasi, industri, dan institusi yang sudah tercatat sebagai mitra.'
                    : 'Daftar kegiatan kerjasama yang telah berjalan';
                $searchPlaceholder = $selectedDataScope === 'mitra'
                    ? 'Cari nama mitra, kategori, negara, atau klasifikasi...'
                    : 'Cari data kerjasama Anda...';
            @endphp

            <div class="section-top">
                <div class="section-top-copy">
                    <div class="section-eyebrow">{{ $sectionEyebrow }}</div>
                    <h2 class="section-title">{{ $sectionTitle }}</h2>
                    <p class="section-sub">{{ $sectionSubtitle }}</p>
                </div>

                 <form action="/" method="GET" class="filter-panel" data-landing-filter>
                    <input type="hidden" name="status_scope" value="{{ $selectedStatusScope }}">
                    <input type="hidden" name="geo_country" value="{{ request('geo_country', '') }}">
                    <input type="hidden" name="geo_province" value="{{ request('geo_province', '') }}">
                    <input type="hidden" name="geo_country_code" value="{{ request('geo_country_code', '') }}">
                    <input type="hidden" name="geo_province_code" value="{{ request('geo_province_code', '') }}">

                     <div class="filter-bar">
                        <div class="filter-stack filter-stack-primary">
                            <div class="filter-group">
                                <span class="filter-group-label">Tampilkan</span>
                                <div class="filter-toggle scope-toggle" aria-label="Pilih tipe data publik">
                                    <label class="filter-option {{ $selectedDataScope === 'kerjasama' ? 'is-active' : '' }}">
                                        <input type="radio" name="data_scope" value="kerjasama"
                                            {{ $selectedDataScope === 'kerjasama' ? 'checked' : '' }}>
                                        <span>Kerjasama</span>
                                    </label>
                                    <label class="filter-option {{ $selectedDataScope === 'mitra' ? 'is-active' : '' }}">
                                        <input type="radio" name="data_scope" value="mitra"
                                            {{ $selectedDataScope === 'mitra' ? 'checked' : '' }}>
                                        <span>Mitra</span>
                                    </label>
                                </div>
                            </div>

                            <div class="filter-group">
                                <span class="filter-group-label">Kategori Mitra</span>
                                <div class="filter-toggle category-toggle" aria-label="Filter kategori publik">
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
                            </div>
                        </div>

                        <div class="filter-stack filter-stack-secondary">
                            <div class="search-wrap filter-search">
                                <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                </svg>
                                <input type="text" name="search" class="search-input"
                                    placeholder="{{ $searchPlaceholder }}" value="{{ request('search') }}">
                            </div>

                            @php
                                $sortOptions = $selectedDataScope === 'mitra'
                                    ? [
                                        'latest' => 'Terbaru',
                                        'oldest' => 'Terlama',
                                        'title' => 'Nama A-Z',
                                        'title_desc' => 'Nama Z-A',
                                        'most_cooperations' => 'Kerjasama Terbanyak',
                                    ]
                                    : [
                                        'latest' => 'Terbaru',
                                        'oldest' => 'Terlama',
                                        'title' => 'A-Z',
                                        'ending_soon' => 'Segera Berakhir',
                                    ];
                            @endphp

                            <div class="sort-wrap filter-sort">
                                <span class="sort-label">Urutkan</span>
                                <div class="sort-dropdown"
                                    x-data="{ open: false, value: @js($selectedSort), label: @js($sortOptions[$selectedSort] ?? reset($sortOptions)) }"
                                    @click.outside="open = false" @keydown.escape.window="open = false">
                                    <input type="hidden" name="sort" :value="value">
                                    <button type="button" class="sort-trigger" x-ref="trigger" @click="open = !open"
                                        @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.menu.querySelector('[aria-selected=true]')?.focus())"
                                        :aria-expanded="open" aria-haspopup="listbox">
                                        <span x-text="label"></span>
                                        <svg class="sort-chevron" :class="{ 'is-open': open }" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                    <div class="sort-menu" x-ref="menu" x-show="open" x-cloak x-transition.origin.top
                                        role="listbox" aria-label="Pilihan urutan">
                                        @foreach ($sortOptions as $optionValue => $optionLabel)
                                            <button type="button" class="sort-option"
                                                :class="{ 'is-selected': value === @js($optionValue) }"
                                                :aria-selected="value === @js($optionValue)"
                                                @click="value = @js($optionValue); label = @js($optionLabel); open = false; $refs.trigger.focus()"
                                                @keydown.arrow-down.prevent="$el.nextElementSibling?.focus()"
                                                @keydown.arrow-up.prevent="$el.previousElementSibling?.focus()"
                                                @keydown.home.prevent="$el.parentElement.firstElementChild?.focus()"
                                                @keydown.end.prevent="$el.parentElement.lastElementChild?.focus()"
                                                @keydown.escape.prevent="open = false; $refs.trigger.focus()" role="option">
                                                <span>{{ $optionLabel }}</span>
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                                    <path d="m5 12 4 4L19 6" />
                                                </svg>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="filter-buttons">
                                <button type="submit" class="btn-search">Cari</button>
                                <button type="button" class="btn-reset-search" data-search-reset @if ($searchTerm === '') hidden @endif>
                                    Reset pencarian
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="results-overview" aria-live="polite">
                <div class="results-overview-copy">
                    <span class="results-mode">{{ $dataScopeLabels[$selectedDataScope] ?? 'Data Publik' }}</span>
                    <p class="results-count">{{ number_format($totalResults, 0, ',', '.') }} {{ $resultLabel }} ditemukan</p>
                    <p class="results-caption">
                        @if ($activeFilterChips)
                            Menampilkan {{ $resultLabel }} yang sesuai dengan filter dan kata kunci aktif.
                        @else
                            Menampilkan seluruh data {{ $resultLabel }} publik yang tersedia saat ini.
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

            @if (false)
            <section class="analytics-wrap" aria-labelledby="analytics-title">
                <div class="analytics-wrap-head">
                    <div>
                        <span class="analytics-kicker">Visualisasi Data</span>
                        <h3 class="analytics-heading" id="analytics-title">Pola penting dari portofolio kerjasama publik</h3>
                    </div>
                    @if ($analyticsContext)
                        <p class="analytics-context">{{ $analyticsContext }}</p>
                    @endif
                </div>

                <div class="analytics-grid">
                    <article class="analytics-card analytics-card-wide">
                        <div class="analytics-card-head">
                            <div>
                                <span class="analytics-card-label">Breakdown Status</span>
                                <h4 class="analytics-card-title">Sebaran status kerja sama saat ini</h4>
                            </div>
                            @if ($statusBreakdown['has_data'])
                                <p class="analytics-card-note">Dominan: {{ $statusBreakdown['dominant_label'] }} ({{ number_format($statusBreakdown['dominant_share'], 0, ',', '.') }}%)</p>
                            @endif
                        </div>

                        @if ($statusBreakdown['has_data'])
                            <div class="status-strip" role="img"
                                aria-label="Breakdown status dari {{ $statusBreakdown['total'] }} portofolio kerja sama">
                                @foreach ($statusBreakdown['items'] as $item)
                                    <span class="status-segment tone-{{ $item['tone'] }}"
                                        style="--segment-share: {{ $item['share'] }}%;"
                                        title="{{ $item['label'] }}: {{ number_format($item['count'], 0, ',', '.') }}"></span>
                                @endforeach
                            </div>

                            <div class="analytics-legend-grid">
                                @foreach ($statusBreakdown['items'] as $item)
                                    <div class="analytics-legend-item tone-{{ $item['tone'] }} {{ $item['count'] === 0 ? 'is-muted' : '' }}">
                                        <span class="analytics-legend-dot" aria-hidden="true"></span>
                                        <div class="analytics-legend-copy">
                                            <span class="analytics-legend-label">{{ $item['label'] }}</span>
                                            <span class="analytics-legend-meta">
                                                {{ number_format($item['count'], 0, ',', '.') }}
                                                data ·
                                                {{ number_format($item['share'], 0, ',', '.') }}%
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="analytics-empty">
                                Belum ada portofolio kerja sama yang bisa divisualkan untuk kombinasi filter ini.
                            </div>
                        @endif
                    </article>

                    <article class="analytics-card">
                        <div class="analytics-card-head">
                            <div>
                                <span class="analytics-card-label">Tren Tahunan</span>
                                <h4 class="analytics-card-title">Pergerakan kerja sama per tahun</h4>
                            </div>
                            @if ($trendByYear['has_data'])
                                <p class="analytics-card-note">{{ $trendByYear['range_label'] }}</p>
                            @endif
                        </div>

                        @if ($trendByYear['has_data'])
                            <div class="trend-bars" role="img" aria-label="Grafik batang tren kerja sama per tahun">
                                @foreach ($trendByYear['points'] as $point)
                                    <div class="trend-bar-item">
                                        <span class="trend-bar-value">{{ number_format($point['count'], 0, ',', '.') }}</span>
                                        <div class="trend-bar-shell">
                                            <span class="trend-bar-fill {{ $point['count'] === 0 ? 'is-empty' : '' }}"
                                                style="--bar-share: {{ $point['count'] > 0 ? max($point['share'], 14) : 0 }}%;"></span>
                                        </div>
                                        <span class="trend-bar-label">{{ $point['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="analytics-empty">
                                Belum ada data tahun pelaksanaan yang cukup untuk menampilkan tren.
                            </div>
                        @endif
                    </article>

                    <article class="analytics-card">
                        <div class="analytics-card-head">
                            <div>
                                <span class="analytics-card-label">Komposisi Mitra</span>
                                <h4 class="analytics-card-title">Sebaran nasional dan internasional</h4>
                            </div>
                            @if ($mitraComposition['has_data'])
                                <p class="analytics-card-note">{{ number_format($mitraComposition['total'], 0, ',', '.') }} mitra</p>
                            @endif
                        </div>

                        @if ($mitraComposition['has_data'])
                            <div class="composition-strip" role="img" aria-label="Komposisi kategori mitra">
                                @foreach ($mitraComposition['items'] as $item)
                                    <span class="composition-segment tone-{{ $item['tone'] }}"
                                        style="--segment-share: {{ $item['share'] }}%;"></span>
                                @endforeach
                            </div>

                            <div class="analytics-legend-stack">
                                @foreach ($mitraComposition['items'] as $item)
                                    <div class="analytics-legend-item tone-{{ $item['tone'] }} {{ $item['count'] === 0 ? 'is-muted' : '' }}">
                                        <span class="analytics-legend-dot" aria-hidden="true"></span>
                                        <div class="analytics-legend-copy">
                                            <span class="analytics-legend-label">{{ $item['label'] }}</span>
                                            <span class="analytics-legend-meta">
                                                {{ number_format($item['count'], 0, ',', '.') }}
                                                mitra ·
                                                {{ number_format($item['share'], 0, ',', '.') }}%
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="analytics-empty">
                                Komposisi mitra akan muncul setelah ada mitra yang sesuai dengan filter aktif.
                            </div>
                        @endif
                    </article>

                    <article class="analytics-card">
                        <div class="analytics-card-head">
                            <div>
                                <span class="analytics-card-label">Jenis Mitra</span>
                                <h4 class="analytics-card-title">Klasifikasi yang paling sering muncul</h4>
                            </div>
                        </div>

                        @if ($topClassifications['has_data'])
                            <div class="rank-list" aria-label="Klasifikasi mitra teratas">
                                @foreach ($topClassifications['items'] as $item)
                                    <div class="rank-row">
                                        <div class="rank-row-head">
                                            <span class="rank-label">{{ $item['label'] }}</span>
                                            <span class="rank-value">{{ number_format($item['count'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="rank-track">
                                            <span class="rank-fill" style="--track-share: {{ max($item['share'], 12) }}%;"></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="analytics-empty">
                                Belum ada klasifikasi mitra yang bisa dirangkum dari hasil saat ini.
                            </div>
                        @endif
                    </article>

                    <article class="analytics-card">
                        <div class="analytics-card-head">
                            <div>
                                <span class="analytics-card-label">Bidang Kerjasama</span>
                                <h4 class="analytics-card-title">Top area kolaborasi yang paling aktif</h4>
                            </div>
                        </div>

                        @if ($topFields['has_data'])
                            <div class="rank-list" aria-label="Bidang kerja sama teratas">
                                @foreach ($topFields['items'] as $item)
                                    <div class="rank-row">
                                        <div class="rank-row-head">
                                            <span class="rank-label">{{ $item['label'] }}</span>
                                            <span class="rank-value">{{ number_format($item['count'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="rank-track">
                                            <span class="rank-fill tone-emerald"
                                                style="--track-share: {{ max($item['share'], 12) }}%;"></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="analytics-empty">
                                Bidang kerja sama akan terpetakan setelah detail kegiatan tersedia.
                            </div>
                        @endif
                    </article>

                    <article class="analytics-card analytics-card-full">
                        <div class="analytics-card-head">
                            <div>
                                <span class="analytics-card-label">Upcoming Attention</span>
                                <h4 class="analytics-card-title">{{ $attentionPanel['headline'] ?? 'Sorotan portofolio terbaru' }}</h4>
                            </div>
                            @if (! empty($attentionPanel['description']))
                                <p class="analytics-card-note">{{ $attentionPanel['description'] }}</p>
                            @endif
                        </div>

                        @if ($attentionPanel['has_data'])
                            <div class="attention-list" aria-label="Daftar perhatian portofolio publik">
                                @foreach ($attentionPanel['items'] as $item)
                                    <article class="attention-item tone-{{ $item['tone'] }}">
                                        <div class="attention-copy">
                                            <h5 class="attention-title" title="{{ $item['judul'] }}">{{ $item['judul'] }}</h5>
                                            <p class="attention-subtitle">{{ $item['mitra'] }}</p>
                                        </div>
                                        <div class="attention-meta">
                                            <span class="attention-date">{{ $item['meta_label'] }}</span>
                                            <span class="attention-badge">{{ $item['supporting_label'] }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="analytics-empty">
                                {{ $attentionPanel['description'] ?? 'Belum ada item perhatian untuk ditampilkan.' }}
                            </div>
                        @endif
                    </article>
                </div>
            </section>
            @endif

        @if ($selectedDataScope === 'mitra')
            @if (isset($mitras) && $mitras->count() > 0)
                <div class="cards-grid cards-grid-mitra">
                    @foreach ($mitras as $mitra)
                        @php
                            $mitraName = trim((string) ($mitra->nama_mitra ?? 'Mitra'));
                            $mitraInitials = strtoupper(substr(preg_replace('/\s+/', '', $mitraName), 0, 2));
                            $klasifikasiLabel = $mitra->klasifikasi?->nama ?? 'Klasifikasi belum ditetapkan';
                            $kategoriLabel = $mitra->kategori ? ucfirst($mitra->kategori) : 'Kategori belum diisi';
                            $negaraLabel = $mitra->negara ?: 'Belum diisi';
                            $telpLabel = $mitra->telepon ?: 'Belum diisi';
                            $alamatLabel = $mitra->alamat ?: 'Alamat belum tersedia';
                            $websiteUrl = $mitra->website;
                            $websiteLabel = $websiteUrl ? \Illuminate\Support\Str::limit($websiteUrl, 30) : 'Belum diisi';
                        @endphp

                        <article class="mcard">
                            <div class="mcard-top">
                                <div class="mcard-avatar">{{ $mitraInitials ?: 'MT' }}</div>
                                <div class="mcard-copy">
                                    <h3 class="mcard-title">{{ $mitraName }}</h3>
                                    <p class="mcard-subtitle">{{ $klasifikasiLabel }}</p>
                                </div>
                                <span class="mcard-category">{{ $kategoriLabel }}</span>
                            </div>

                            <div class="mcard-highlights">
                                <div class="mcard-highlight">
                                    <span class="mcard-highlight-key">Total Kerjasama</span>
                                    <span class="mcard-highlight-val">{{ number_format($mitra->cooperations_count ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="mcard-highlight">
                                    <span class="mcard-highlight-key">Negara</span>
                                    <span class="mcard-highlight-val">{{ $negaraLabel }}</span>
                                </div>
                            </div>

                            <div class="mcard-meta">
                                <div class="meta-row">
                                    <span class="meta-key">Telepon</span>
                                    <span class="meta-val">{{ $telpLabel }}</span>
                                </div>
                                <div class="meta-row">
                                    <span class="meta-key">Alamat</span>
                                    <span class="meta-val">{{ $alamatLabel }}</span>
                                </div>
                                <div class="meta-row">
                                    <span class="meta-key">Website</span>
                                    <span class="meta-val">
                                        @if ($websiteUrl)
                                            <a href="{{ $websiteUrl }}" class="inline-link" target="_blank" rel="noreferrer">
                                                {{ $websiteLabel }}
                                            </a>
                                        @else
                                            {{ $websiteLabel }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $mitras->links('pagination::simple-bootstrap-4') }}
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
                    <h3>Belum ada data mitra</h3>
                    <p>Data mitra yang dipublikasikan belum tersedia atau tidak ditemukan untuk kata kunci dan filter
                        yang dipilih.</p>
                </div>
            @endif
        @elseif (isset($kerjasama) && $kerjasama->count() > 0)
            <div class="cards-grid">
                @foreach ($kerjasama as $item)
                    @php
                        $status = trim(strtolower(str_replace(['_', '-'], ' ', $item->status_dokumen ?? '')));
                        $statusClass = match (true) {
                            $status === 'disahkan' => 'badge-active',
                            str_contains($status, 'perpanjangan') => 'badge-warning',
                            in_array($status, ['revisi', 'kadarluarsa', 'kadaluarsa', 'kedaluwarsa']) => 'badge-expired',
                            $status === 'tidak aktif' => 'badge-inactive',
                            str_contains($status, 'menunggu') => 'badge-process',
                            default => 'badge-inactive',
                        };
                        $statusLabel = ucfirst($status !== '' ? $status : 'draft');

                        $mitraNames = $item->mitra ? $item->mitra->nama_mitra : 'Mitra belum ditentukan';
                        $mitraInit = strtoupper(substr($mitraNames, 0, 2));
                        $hasDates = $item->start_date && $item->end_date;
                    @endphp

                    <div class="kcard {{ $statusClass }}"
                        onclick="openModal(
                                                    {{ $item->id }},
                                                    `{{ addslashes($item->judul) }}`,
                                                    `{{ addslashes($mitraNames) }}`,
                                                    `{{ addslashes($item->doc_number ?? 'Belum ada') }}`,
                                                    `{{ $hasDates ? $item->start_date->format('d M Y') . ' – ' . $item->end_date->format('d M Y') : 'Tanggal belum lengkap' }}`,
                                                    `{{ addslashes($statusLabel) }}`,
                                                    `{{ $statusClass }}`
                                                )">
                        <div class="kcard-accent"></div>

                        <div class="kcard-top">
                            <h3 class="kcard-title">{{ $item->judul }}</h3>
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
                                `{{ addslashes($item->judul) }}`,
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/topojson-client@3"></script>
    <script src="{{ asset('js/index.js') }}" data-turbo-track="reload"></script>

    <!-- ═══ CHOICE MODAL ════════════════════════════════════════ -->
    <div class="modal-overlay" id="submissionChoiceModal" onclick="closeSubmissionChoiceModal(event)">
        <div class="choice-modal-box" onclick="event.stopPropagation()">
            <div class="choice-modal-head">
                <div>
                    <span class="choice-kicker">Layanan Kemitraan</span>
                    <h2 class="choice-title">Bagaimana kami dapat membantu Anda?</h2>
                    <p class="choice-subtitle">Pilih opsi di bawah untuk mengajukan kerja sama baru atau memperpanjang kerja sama yang sudah ada.</p>
                </div>
                <button class="modal-close" onclick="closeSubmissionChoiceModal(null)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            
            <div class="choice-cards-container">
                <!-- Card 1: Pengajuan Baru -->
                <a href="{{ route('pengajuan.kerjasama.create') }}" class="choice-card">
                    <div class="choice-icon-wrap new-partner">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" y1="8" x2="19" y2="14" />
                            <line x1="16" y1="11" x2="22" y2="11" />
                        </svg>
                    </div>
                    <div class="choice-card-content">
                        <h3>Ajukan Kerja Sama Baru</h3>
                        <p>Pilih ini jika instansi/perusahaan Anda belum terdaftar sebagai mitra resmi Politeknik Negeri Manado.</p>
                        <span class="choice-action-btn">Mulai Pengajuan <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- Card 2: Perpanjangan -->
                <a href="{{ route('pengajuan.perpanjangan.create') }}" class="choice-card">
                    <div class="choice-icon-wrap renewal-partner">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67" />
                        </svg>
                    </div>
                    <div class="choice-card-content">
                        <h3>Perpanjang Kerja Sama</h3>
                        <p>Pilih ini jika instansi/perusahaan Anda sudah terdaftar dan ingin memperbarui MoU atau PKS yang ada.</p>
                        <span class="choice-action-btn">Ajukan Perpanjangan <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <style>
        .choice-modal-box {
            background: var(--surface);
            border-radius: 20px;
            width: min(720px, calc(100% - 32px));
            padding: 2.5rem;
            position: relative;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.08);
            animation: choiceModalShow 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            color: var(--ink);
        }

        .choice-modal-head {
            padding-right: 2.5rem;
        }

        .choice-modal-head .modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
        }
        [data-theme="dark"] .choice-modal-box {
            background: #151c2c;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .choice-kicker {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent, #d4a938);
            display: block;
            margin-bottom: 0.5rem;
        }

        .choice-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.75rem;
            line-height: 1.25;
            margin-bottom: 0.5rem;
            color: var(--ink);
        }

        .choice-subtitle {
            font-size: 0.9rem;
            color: var(--ink-faint);
            line-height: 1.5;
            margin-bottom: 2rem;
        }

        .choice-cards-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 600px) {
            .choice-cards-container {
                grid-template-columns: 1fr;
            }
            .choice-modal-box {
                padding: 1.75rem;
            }
            .choice-modal-head .modal-close {
                top: 1rem;
                right: 1rem;
            }
        }

        .choice-card {
            display: flex;
            flex-direction: column;
            padding: 1.75rem;
            border-radius: 16px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        [data-theme="light"] .choice-card {
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        [data-theme="dark"] .choice-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .choice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            border-color: var(--primary, #0f3f7f);
            background: rgba(15, 63, 127, 0.04);
        }

        .choice-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            transition: transform 0.3s ease;
        }

        .choice-card:hover .choice-icon-wrap {
            transform: scale(1.1);
        }

        .new-partner {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .renewal-partner {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .choice-card-content h3 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: color 0.2s ease;
            color: var(--ink);
        }

        .choice-card:hover h3 {
            color: var(--primary, #0f3f7f);
        }

        .choice-card-content p {
            font-size: 0.85rem;
            color: var(--ink-faint);
            line-height: 1.5;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .choice-action-btn {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary, #0f3f7f);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: gap 0.2s ease;
        }

        .choice-card:hover .choice-action-btn {
            gap: 0.75rem;
        }

        @keyframes choiceModalShow {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        function openSubmissionChoiceModal() {
            const modal = document.getElementById('submissionChoiceModal');
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSubmissionChoiceModal(event) {
            if (!event || event.target.id === 'submissionChoiceModal') {
                const modal = document.getElementById('submissionChoiceModal');
                if (modal) {
                    modal.classList.remove('open');
                    document.body.style.overflow = '';
                }
            }
        }
    </script>
</body>

</html>
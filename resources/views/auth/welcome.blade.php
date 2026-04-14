<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DUDIKA — Sistem Informasi Kerjasama</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Serif+Display:ital@0;1&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" data-turbo-track="reload">
</head>

<body>

    <!-- ═══ NAV ═══════════════════════════════════════════════ -->
    <nav class="top-nav">
        <div class="nav-inner">
            <a href="/" class="logo">
                <div class="logo-mark">
                    <img src="{{ asset('img/logo.png') }}" alt="Handshake" width="30" height="30">
                </div>
                <span class="logo-text">POLIMDO <span>&</span> DUDIKA</span>
            </a>
            <div class="nav-right">
                <a href="#data-kerjasama" class="nav-link">Data Kerjasama</a>
                <a href="{{ route('login') }}" class="btn-nav">Login Sistem</a>
            </div>
        </div>
    </nav>

    <!-- ═══ HERO ════════════════════════════════════════════════ -->
    <header class="hero">
        <div class="hero-grid-bg"></div>
        <div class="hero-blob"></div>
        <div class="hero-inner">
            <div class="hero-text">
                <div class="hero-eyebrow">Platform Resmi Kerjasama Kampus</div>
                <h1>Transparansi Data<br><em>Kerjasama Strategis</em></h1>
                <p class="hero-desc">
                    Sistem terpadu untuk memantau dan menelusuri seluruh aktivitas kerjasama antara Kampus dengan Dunia
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
                            <span class="pmcard-badge badge-selesai">Selesai</span>
                        </div>
                        <div class="pmcard-meta">CV Maju Bersama &nbsp;·&nbsp; MoU &nbsp;·&nbsp; Mar 2023 – Mar 2025
                        </div>
                    </div>
                </div>

                <div class="hero-floating-stat">
                    <div class="hfs-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#378ADD" stroke-width="2">
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

    <!-- ═══ STATS STRIP ═══════════════════════════════════════ -->
    <div class="stats-strip">
        <div class="stats-inner">
            <div class="stat-item">
                <div class="stat-num">{{ $stats['total_kerjasama'] ?? 0 }}</div>
                <div class="stat-lbl">Total Kerjasama</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <div class="stat-num">{{ $stats['total_mitra'] ?? 0 }}</div>
                <div class="stat-lbl">Mitra Terdaftar</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <div class="stat-num">{{ $stats['total_aktif'] ?? 0 }}</div>
                <div class="stat-lbl">Kerjasama Aktif / Selesai</div>
            </div>
        </div>
    </div>

    <!-- ═══ TRUST STRIP ════════════════════════════════════════ -->
    <div class="trust-strip">
        <div class="trust-inner">
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Data terverifikasi & akurat
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path
                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
                Akses publik & transparan
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <path d="M3 9h18M9 21V9" />
                </svg>
                Diperbarui secara berkala
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
    <main class="main-wrap" id="data-kerjasama">

        <div class="section-top">
            <div>
                <div class="section-eyebrow">Data Kerjasama</div>
                <h2 class="section-title">Eksplorasi Aktivitas Kerjasama</h2>
                <p class="section-sub">Daftar kegiatan kerjasama yang sedang dan telah berjalan · Tampilan publik</p>
            </div>

            <form action="/" method="GET" class="filter-bar">
                <div class="search-wrap">
                    <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="search" class="search-input" placeholder="Cari nama kegiatan…"
                        value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn-search">Cari</button>
            </form>
        </div>

        @if(isset($kerjasama) && $kerjasama->count() > 0)
            <div class="cards-grid">
                @foreach($kerjasama as $item)
                    @php
                        $statusClass = $item->status_class ?? 'badge-draft';
                        $statusLabel = $item->status_label ?? 'Draft';
                        $mitraNames = $item->mitras->pluck('nama_mitra')->join(', ') ?: 'Mitra belum ditentukan';
                        $mitraInit = strtoupper(substr($mitraNames, 0, 2));
                        $jenis = $item->jenisKerjasama->pluck('nama_jenis')->join(', ') ?: '-';
                        $bidang = $item->bidang_kerjasama ?? '-';
                        $hasDates = $item->periode_mulai && $item->periode_selesai;
                    @endphp

                    <div class="kcard" onclick="openModal(
                                    {{ $item->id }},
                                    `{{ addslashes($item->nama_kegiatan) }}`,
                                    `{{ addslashes($mitraNames) }}`,
                                    `{{ addslashes($jenis) }}`,
                                    `{{ addslashes($bidang) }}`,
                                    `{{ addslashes($item->nomor_mou ?? 'Belum ada') }}`,
                                    `{{ $hasDates ? $item->periode_mulai->format('d M Y') . ' – ' . $item->periode_selesai->format('d M Y') : 'Tanggal belum lengkap' }}`,
                                    `{{ addslashes($statusLabel) }}`,
                                    `{{ $statusClass }}`
                                )">
                        <div class="kcard-accent"></div>

                        <div class="kcard-top">
                            <h3 class="kcard-title">{{ $item->nama_kegiatan }}</h3>
                            <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        <div class="kcard-mitra">
                            <div class="mitra-dot">{{ $mitraInit }}</div>
                            {{ $mitraNames }}
                        </div>

                        <div class="kcard-meta">
                            <div class="meta-row">
                                <span class="meta-key">Jenis</span>
                                <span class="meta-val">{{ $jenis }}</span>
                            </div>
                            @if($bidang && $bidang !== '-')
                                <div class="meta-row">
                                    <span class="meta-key">Bidang</span>
                                    <span class="meta-val">{{ $bidang }}</span>
                                </div>
                            @endif
                            <div class="meta-row">
                                <span class="meta-key">No. MoU</span>
                                <span class="meta-val">{{ $item->nomor_mou ?? 'Belum ada' }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-key">Durasi</span>
                                <span class="meta-val">
                                    @if($hasDates)
                                        {{ $item->periode_mulai->format('d M Y') }} – {{ $item->periode_selesai->format('d M Y') }}
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
                                                                                                                                                                        `{{ addslashes($item->nama_kegiatan) }}`,
                                                                                                                                                                        `{{ addslashes($mitraNames) }}`,
                                                                                                                                                                        `{{ addslashes($jenis) }}`,
                                                                                                                                                                        `{{ addslashes($bidang) }}`,
                                                                                                                                                                        `{{ addslashes($item->nomor_mou ?? 'Belum ada') }}`,
                                                                                                                                                                        `{{ $hasDates ? $item->periode_mulai->format('d M Y') . ' – ' . $item->periode_selesai->format('d M Y') : 'Tanggal belum lengkap' }}`,
                                                                                                                                                                        `{{ addslashes($statusLabel) }}`,
                                                                                                                                                                        `{{ $statusClass }}`
                                                                                                                                                                    )">
                                Lihat Detail
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
                <h3>Belum ada data kerjasama</h3>
                <p>Data kerjasama yang dipublikasikan belum tersedia atau tidak ditemukan.</p>
            </div>
        @endif
    </main>

    <!-- ═══ MODAL DETAIL ════════════════════════════════════════ -->
    <div class="modal-overlay" id="detailModal" onclick="closeModal(event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="modal-head">
                <div>
                    <p
                        style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--ink-faint);margin-bottom:0.35rem;">
                        Detail Kerjasama</p>
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
                    <span class="modal-fkey">Jenis Kerjasama</span>
                    <span class="modal-fval" id="modal-jenis"></span>
                </div>
                <div class="modal-field">
                    <span class="modal-fkey">Bidang</span>
                    <span class="modal-fval" id="modal-bidang"></span>
                </div>
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
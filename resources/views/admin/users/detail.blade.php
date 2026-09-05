@extends('admin.dashboard')

@section('content')
@php
    $roleLabels = [
        'pimpinan'   => 'Pimpinan',
        'jurusan'    => 'Jurusan',
        'unit_kerja' => 'Humas (Unit Kerja)',
        'upa'        => 'Unit Pelaksana Akademik (UPA)',
        'pusat'      => 'Pusat / Lembaga',
        'admin'      => 'Administrator',
        'mitra'      => 'Mitra DUDIKA',
    ];

    $roleKey = strtolower($user->role?->role_name ?? 'default');
    
    // Role color themes & configurations
    $roleThemes = [
        'admin' => [
            'class'        => 'role-admin',
            'badge_bg'     => 'rgba(124, 58, 237, 0.12)',
            'badge_text'   => '#7c3aed',
            'badge_border' => 'rgba(124, 58, 237, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%)',
            'icon'         => 'fas fa-shield-halved',
            'role_desc'    => 'Memiliki akses penuh untuk mengelola seluruh master data, pengaturan sistem, manajemen role, dan manajemen akun staf serta mitra.',
            'access_tier'  => 'Super Administrator (Level 1)'
        ],
        'pimpinan' => [
            'class'        => 'role-pimpinan',
            'badge_bg'     => 'rgba(217, 119, 6, 0.12)',
            'badge_text'   => '#d97706',
            'badge_border' => 'rgba(217, 119, 6, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
            'icon'         => 'fas fa-crown',
            'role_desc'    => 'Memiliki kewenangan monitoring eksekutif, memvalidasi dan mengesahkan dokumen kerjasama serta menerima pengajuan strategis.',
            'access_tier'  => 'Eksekutif & Pengambil Keputusan (Level 2)'
        ],
        'jurusan' => [
            'class'        => 'role-jurusan',
            'badge_bg'     => 'rgba(14, 165, 233, 0.12)',
            'badge_text'   => '#0284c7',
            'badge_border' => 'rgba(14, 165, 233, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
            'icon'         => 'fas fa-graduation-cap',
            'role_desc'    => 'Mengelola kegiatan kerjasama di tingkat jurusan dan program studi, monitoring mahasiswa magang, dan mengisi evaluasi berkala.',
            'access_tier'  => 'Unit Akademik Jurusan (Level 3)'
        ],
        'unit_kerja' => [
            'class'        => 'role-unit-kerja',
            'badge_bg'     => 'rgba(16, 185, 129, 0.12)',
            'badge_text'   => '#059669',
            'badge_border' => 'rgba(16, 185, 129, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
            'icon'         => 'fas fa-building',
            'role_desc'    => 'Membantu administrasi kerjasama institusi, menjalin komunikasi dengan mitra industri, verifikasi berkas, dan penyusunan draf kerjasama.',
            'access_tier'  => 'Unit Kerja Humas / KUI (Level 3)'
        ],
        'upa' => [
            'class'        => 'role-upa',
            'badge_bg'     => 'rgba(6, 182, 212, 0.12)',
            'badge_text'   => '#0891b2',
            'badge_border' => 'rgba(6, 182, 212, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #0891b2 0%, #0e7490 100%)',
            'icon'         => 'fas fa-building-columns',
            'role_desc'    => 'Mengelola program kerjasama dan layanan teknis di lingkup Unit Pelaksana Akademik (Perpustakaan, Lab, Bahasa, dll).',
            'access_tier'  => 'Unit Pelaksana Akademik (Level 3)'
        ],
        'pusat' => [
            'class'        => 'role-pusat',
            'badge_bg'     => 'rgba(168, 85, 247, 0.12)',
            'badge_text'   => '#9333ea',
            'badge_border' => 'rgba(168, 85, 247, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #9333ea 0%, #7e22ce 100%)',
            'icon'         => 'fas fa-landmark',
            'role_desc'    => 'Mengelola program kerjasama strategis tingkat pusat (P3M, Pusat Karir, Pusat Penjaminan Mutu, dsb).',
            'access_tier'  => 'Unit Pusat / Lembaga Khusus (Level 3)'
        ],
        'mitra' => [
            'class'        => 'role-mitra',
            'badge_bg'     => 'rgba(37, 99, 235, 0.12)',
            'badge_text'   => '#2563eb',
            'badge_border' => 'rgba(37, 99, 235, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)',
            'icon'         => 'fas fa-handshake',
            'role_desc'    => 'Akses portal khusus industri untuk mengajukan kerjasama baru, mereview draf online, serta memberi penilaian mahasiswa magang.',
            'access_tier'  => 'Aktor Eksternal Mitra Industri (Level 4)'
        ],
    ];

    $currentTheme = $roleThemes[$roleKey] ?? [
        'class'        => 'role-default',
        'badge_bg'     => 'rgba(100, 116, 139, 0.12)',
        'badge_text'   => '#475569',
        'badge_border' => 'rgba(100, 116, 139, 0.25)',
        'gradient'     => 'linear-gradient(135deg, #64748b 0%, #475569 100%)',
        'icon'         => 'fas fa-user',
        'role_desc'    => 'Pengguna sistem dengan hak akses standar.',
        'access_tier'  => 'Pengguna Standar'
    ];

    // Inisial Nama (maks 2 huruf)
    $words = explode(' ', trim($user->name));
    $initials = count($words) >= 2 
        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
        : strtoupper(substr($user->name, 0, 2));

    // Unit info logic
    $unitType = 'Belum Ditetapkan';
    $unitName = '—';
    $unitIcon = 'fas fa-building-circle-exclamation';

    if ($user->profile?->jurusan) {
        $unitType = 'Jurusan';
        $unitName = $user->profile->jurusan->nama_jurusan . ($user->profile->jurusan->kode_jurusan ? ' (' . $user->profile->jurusan->kode_jurusan . ')' : '');
        $unitIcon = 'fas fa-graduation-cap';
    } elseif ($user->profile?->unitKerja) {
        $unitType = 'Unit Kerja (Humas)';
        $unitName = $user->profile->unitKerja->nama_unit_pelaksana;
        $unitIcon = 'fas fa-building';
    } elseif ($user->profile?->upa) {
        $unitType = 'Unit Pelaksana Akademik (UPA)';
        $unitName = $user->profile->upa->nama_upa;
        $unitIcon = 'fas fa-building-columns';
    } elseif ($user->profile?->pusat) {
        $unitType = 'Pusat / Lembaga';
        $unitName = $user->profile->pusat->nama_pusat;
        $unitIcon = 'fas fa-landmark';
    } elseif ($user->mitra) {
        $unitType = 'Mitra Perusahaan (DUDIKA)';
        $unitName = $user->mitra->nama_mitra ?? 'Mitra Industri';
        $unitIcon = 'fas fa-handshake';
    } elseif ($roleKey === 'admin') {
        $unitType = 'Sistem Pusat';
        $unitName = 'Administrator Sistem WD4';
        $unitIcon = 'fas fa-server';
    } elseif ($roleKey === 'pimpinan') {
        $unitType = 'Direksi / Pimpinan Kampus';
        $unitName = 'Pimpinan Politeknik Negeri Manado';
        $unitIcon = 'fas fa-crown';
    }
@endphp

<main class="main-content admin-dashboard ud-detail-page {{ $currentTheme['class'] }}">
    {{-- ── Topbar Breadcrumbs ───────────────────────────────────────── --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <a href="{{ route('users.index') }}" class="ud-breadcrumb-link">Pengguna</a>
                <span>/</span>
                <span>Detail Profil</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon ud-theme-icon-box">
                    <i class="{{ $currentTheme['icon'] }}"></i>
                </span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Detail Data Pengguna</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Informasi lengkap profil akun, kewenangan peran, keterkaitan unit, serta dokumen kerjasama terkait.
                    </p>
                </div>
            </div>
        </div>

        <div class="ud-topbar-actions">
            <a href="{{ route('users.index') }}" class="ud-btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('users.edit', $user->id) }}" class="ud-btn-edit">
                <i class="fas fa-pen-to-square"></i>
                <span>Edit Pengguna</span>
            </a>
        </div>
    </section>

    {{-- ── Hero Spotlight Card ─────────────────────────────────────── --}}
    <div class="ud-hero-card">
        <div class="ud-hero-glow"></div>
        <div class="ud-hero-pattern"></div>
        
        <div class="ud-hero-content">
            <div class="ud-avatar-wrapper">
                <div class="ud-avatar" style="background: {{ $currentTheme['gradient'] }};">
                    {{ $initials }}
                </div>
                <div class="ud-status-pulse" title="Akun Aktif"></div>
            </div>

            <div class="ud-user-meta">
                <div class="ud-user-top">
                    <h1 class="ud-user-name">{{ $user->name }}</h1>
                    <span class="ud-role-pill ud-theme-badge">
                        <i class="{{ $currentTheme['icon'] }}"></i>
                        {{ $roleLabels[$user->role?->role_name] ?? $user->role?->role_name ?? 'Tanpa Role' }}
                    </span>
                    @if($user->email_verified_at)
                        <span class="ud-verified-pill" title="Email telah diverifikasi">
                            <i class="fas fa-circle-check"></i> Terverifikasi
                        </span>
                    @else
                        <span class="ud-unverified-pill" title="Email belum diverifikasi">
                            <i class="fas fa-circle-question"></i> Belum Verifikasi
                        </span>
                    @endif
                </div>

                <div class="ud-quick-badges">
                    <div class="ud-quick-item" onclick="copyToClipboard('{{ $user->nik }}', 'NIK berhasil disalin!')" title="Klik untuk menyalin NIK">
                        <i class="fas fa-id-card"></i>
                        <span class="ud-mono">{{ $user->nik ?? '-' }}</span>
                        <i class="fas fa-copy ud-copy-icon"></i>
                    </div>
                    <div class="ud-quick-item" onclick="copyToClipboard('{{ $user->email }}', 'Email berhasil disalin!')" title="Klik untuk menyalin Email">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $user->email }}</span>
                        <i class="fas fa-copy ud-copy-icon"></i>
                    </div>
                    <div class="ud-quick-item">
                        <i class="fas fa-hashtag"></i>
                        <span class="ud-mono">UID #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>

            <div class="ud-hero-stats">
                <div class="ud-stat-box">
                    <span class="ud-stat-label">Jabatan Struktural</span>
                    <span class="ud-stat-value">{{ $user->profile?->jabatan ?: '—' }}</span>
                </div>
                <div class="ud-stat-divider"></div>
                <div class="ud-stat-box">
                    <span class="ud-stat-label">Terdaftar Sejak</span>
                    <span class="ud-stat-value">{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</span>
                    <span class="ud-stat-sub">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Metric Summary Counters ─────────────────────────────────── --}}
    <div class="ud-metric-grid">
        <div class="ud-metric-card">
            <div class="ud-metric-icon icon-primary">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="ud-metric-info">
                <span class="ud-metric-label">Total Kerjasama</span>
                <span class="ud-metric-num">{{ $stats['total_cooperations'] ?? 0 }}</span>
                <span class="ud-metric-sub">Dokumen terkait entitas ini</span>
            </div>
        </div>

        <div class="ud-metric-card">
            <div class="ud-metric-icon icon-success">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="ud-metric-info">
                <span class="ud-metric-label">Kerjasama Aktif</span>
                <span class="ud-metric-num text-success">{{ $stats['active_cooperations'] ?? 0 }}</span>
                <span class="ud-metric-sub">Masa berlaku masih berjalan</span>
            </div>
        </div>

        <div class="ud-metric-card">
            <div class="ud-metric-icon icon-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="ud-metric-info">
                <span class="ud-metric-label">Akan Berakhir</span>
                <span class="ud-metric-num text-warning">{{ $stats['expiring_cooperations'] ?? 0 }}</span>
                <span class="ud-metric-sub">Perlu evaluasi/perpanjangan</span>
            </div>
        </div>

        <div class="ud-metric-card">
            <div class="ud-metric-icon icon-purple">
                <i class="fas fa-file-signature"></i>
            </div>
            <div class="ud-metric-info">
                <span class="ud-metric-label">Usulan & Pengajuan</span>
                <span class="ud-metric-num text-purple">{{ $stats['total_proposals'] ?? 0 }}</span>
                <span class="ud-metric-sub">Pengajuan baru & perpanjangan</span>
            </div>
        </div>
    </div>

    {{-- ── Main Grid Section with Horizontal Scroll Wrapper ──────── --}}
    <div class="ud-grid-container">

        {{-- ── Left Column: Security, Account & Role Tier (360px) ── --}}
        <div class="ud-col-side">
            
            {{-- Card 1: Kredensial & Info Login --}}
            <div class="card ud-card">
                <div class="ud-card-header">
                    <div class="ud-card-title-group">
                        <span class="ud-card-icon icon-cyan">
                            <i class="fas fa-key"></i>
                        </span>
                        <div>
                            <h3 class="ud-card-title">Kredensial & Login</h3>
                            <p class="ud-card-subtitle">Informasi autentikasi akun</p>
                        </div>
                    </div>
                </div>
                
                <div class="ud-card-body">
                    <div class="ud-info-stack">
                        <div class="ud-info-tile">
                            <div class="ud-tile-icon"><i class="fas fa-at"></i></div>
                            <div class="ud-tile-content">
                                <span class="ud-tile-label">Email Login</span>
                                <span class="ud-tile-value ud-selectable">{{ $user->email }}</span>
                            </div>
                        </div>

                        <div class="ud-info-tile">
                            <div class="ud-tile-icon"><i class="fas fa-shield-halved"></i></div>
                            <div class="ud-tile-content">
                                <span class="ud-tile-label">Status Verifikasi</span>
                                @if($user->email_verified_at)
                                    <span class="ud-tile-badge badge-green">
                                        <i class="fas fa-check-circle"></i> Terverifikasi ({{ $user->email_verified_at->format('d/m/Y H:i') }})
                                    </span>
                                @else
                                    <span class="ud-tile-badge badge-amber">
                                        <i class="fas fa-clock"></i> Menunggu Verifikasi
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="ud-info-tile">
                            <div class="ud-tile-icon"><i class="fas fa-lock"></i></div>
                            <div class="ud-tile-content">
                                <span class="ud-tile-label">Kata Sandi</span>
                                <span class="ud-tile-value text-muted">•••••••••••••••• (Terenkripsi Bcrypt)</span>
                            </div>
                        </div>

                        <div class="ud-info-tile">
                            <div class="ud-tile-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="ud-tile-content">
                                <span class="ud-tile-label">Terakhir Diperbarui</span>
                                <span class="ud-tile-value">{{ $user->updated_at ? $user->updated_at->format('d M Y, H:i') . ' WITA' : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Hak Akses & Peran --}}
            <div class="card ud-card">
                <div class="ud-card-header">
                    <div class="ud-card-title-group">
                        <span class="ud-card-icon icon-purple">
                            <i class="fas fa-user-shield"></i>
                        </span>
                        <div>
                            <h3 class="ud-card-title">Kewenangan Peran</h3>
                            <p class="ud-card-subtitle">{{ $currentTheme['access_tier'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="ud-card-body">
                    <div class="ud-role-banner ud-theme-badge">
                        <i class="{{ $currentTheme['icon'] }}"></i>
                        <span class="ud-role-name">{{ $roleLabels[$user->role?->role_name] ?? $user->role?->role_name ?? 'Tanpa Role' }}</span>
                    </div>

                    <p class="ud-role-description">
                        {{ $currentTheme['role_desc'] }}
                    </p>

                    <div class="ud-role-meta-list">
                        <div class="ud-role-meta-item">
                            <i class="fas fa-check-double" style="color: #10b981;"></i>
                            <span>Akses Login Web Portal Aktif</span>
                        </div>
                        <div class="ud-role-meta-item">
                            <i class="fas fa-bell" style="color: #3b82f6;"></i>
                            <span>Notifikasi Transaksi Sistem Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Danger Zone (Hapus) --}}
            <div class="card ud-card ud-card-danger">
                <div class="ud-card-header">
                    <div class="ud-card-title-group">
                        <span class="ud-card-icon icon-danger">
                            <i class="fas fa-triangle-exclamation"></i>
                        </span>
                        <div>
                            <h3 class="ud-card-title text-danger">Tindakan Khusus</h3>
                            <p class="ud-card-subtitle">Pengelolaan akun sensitif</p>
                        </div>
                    </div>
                </div>
                <div class="ud-card-body">
                    <p class="ud-danger-text">
                        Menghapus pengguna ini akan mencabut seluruh hak akses login secara permanen.
                    </p>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ud-btn-danger-block">
                            <i class="fas fa-trash-can"></i>
                            <span>Hapus Pengguna Ini</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- ── Right Column: Interactive Tabs with Horizontal Scroll Support ── --}}
        <div class="ud-col-main">

            {{-- Tab Navigasi Interaktif (Horizontal Scrollable on Small Viewport) --}}
            <div class="ud-tabs-nav-wrapper">
                <div class="ud-tabs-nav">
                    <button type="button" class="ud-tab-btn active" data-tab="tab-profile" onclick="switchTab('tab-profile', this)">
                        <i class="fas fa-id-card-clip"></i>
                        <span>Profil & Penempatan</span>
                    </button>
                    <button type="button" class="ud-tab-btn" data-tab="tab-cooperation" onclick="switchTab('tab-cooperation', this)">
                        <i class="fas fa-handshake"></i>
                        <span>Kerjasama Terkait</span>
                        @if(isset($stats['total_cooperations']) && $stats['total_cooperations'] > 0)
                            <span class="ud-tab-count">{{ $stats['total_cooperations'] }}</span>
                        @endif
                    </button>
                    <button type="button" class="ud-tab-btn" data-tab="tab-proposals" onclick="switchTab('tab-proposals', this)">
                        <i class="fas fa-file-signature"></i>
                        <span>Usulan & Pengajuan</span>
                        @if(isset($stats['total_proposals']) && $stats['total_proposals'] > 0)
                            <span class="ud-tab-count count-purple">{{ $stats['total_proposals'] }}</span>
                        @endif
                    </button>
                    <button type="button" class="ud-tab-btn" data-tab="tab-audit" onclick="switchTab('tab-audit', this)">
                        <i class="fas fa-shield-alt"></i>
                        <span>Audit & Keamanan</span>
                    </button>
                </div>
            </div>

            {{-- ── Horizontal Scroll Wrapper for Main Content Panes ── --}}
            <div class="ud-main-scroll-wrapper">
                <div class="ud-content-inner-container">

                    {{-- ══════════════════════════════════════════════════════ --}}
                    {{-- TAB 1: PROFIL & PENEMPATAN                             --}}
                    {{-- ══════════════════════════════════════════════════════ --}}
                    <div id="tab-profile" class="ud-tab-pane active">

                        {{-- Card: Data Diri Pengguna --}}
                        <div class="card ud-card mb-4">
                            <div class="ud-card-header">
                                <div class="ud-card-title-group">
                                    <span class="ud-card-icon icon-primary">
                                        <i class="fas fa-user-gear"></i>
                                    </span>
                                    <div>
                                        <h3 class="ud-card-title">Biodata & Informasi Pribadi</h3>
                                        <p class="ud-card-subtitle">Data identitas resmi pengguna dalam sistem</p>
                                    </div>
                                </div>
                            </div>

                            <div class="ud-card-body">
                                <div class="ud-data-grid">
                                    <div class="ud-field-group">
                                        <label class="ud-field-label">
                                            <i class="fas fa-user"></i> Nama Lengkap
                                        </label>
                                        <div class="ud-field-value ud-highlight">{{ $user->name }}</div>
                                    </div>

                                    <div class="ud-field-group">
                                        <label class="ud-field-label">
                                            <i class="fas fa-id-card"></i> NIK / NIP Pegawai
                                        </label>
                                        <div class="ud-field-value ud-mono">
                                            {{ $user->nik ?? '— Belum diisi —' }}
                                        </div>
                                    </div>

                                    <div class="ud-field-group">
                                        <label class="ud-field-label">
                                            <i class="fas fa-envelope"></i> Alamat Email Resmi
                                        </label>
                                        <div class="ud-field-value">
                                            <a href="mailto:{{ $user->email }}" class="ud-link">
                                                {{ $user->email }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="ud-field-group">
                                        <label class="ud-field-label">
                                            <i class="fas fa-user-tag"></i> Role / Peran Akun
                                        </label>
                                        <div class="ud-field-value">
                                            <span class="ud-inline-badge ud-theme-badge">
                                                <i class="{{ $currentTheme['icon'] }}"></i>
                                                {{ $roleLabels[$user->role?->role_name] ?? $user->role?->role_name ?? 'Tanpa Role' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card: Penempatan Unit & Organisasi --}}
                        <div class="card ud-card">
                            <div class="ud-card-header">
                                <div class="ud-card-title-group">
                                    <span class="ud-card-icon icon-emerald">
                                        <i class="fas fa-sitemap"></i>
                                    </span>
                                    <div>
                                        <h3 class="ud-card-title">Penempatan Struktural & Organisasi</h3>
                                        <p class="ud-card-subtitle">Unit pelaksana, institusi mitra, dan rincian jabatan</p>
                                    </div>
                                </div>
                            </div>

                            <div class="ud-card-body">
                                <div class="ud-placement-box">
                                    <div class="ud-placement-icon">
                                        <i class="{{ $unitIcon }}"></i>
                                    </div>
                                    <div class="ud-placement-details">
                                        <span class="ud-placement-type">{{ $unitType }}</span>
                                        <h4 class="ud-placement-name">{{ $unitName }}</h4>
                                        <span class="ud-placement-role">Jabatan: <strong>{{ $user->profile?->jabatan ?: '— Belum diisi —' }}</strong></span>
                                    </div>
                                </div>

                                <div class="ud-subgrid mt-4">
                                    <div class="ud-subbox">
                                        <span class="ud-subbox-label"><i class="fas fa-briefcase"></i> Jabatan Terdaftar</span>
                                        <span class="ud-subbox-value">{{ $user->profile?->jabatan ?: '—' }}</span>
                                    </div>

                                    <div class="ud-subbox">
                                        <span class="ud-subbox-label"><i class="fas fa-network-wired"></i> Entitas Induk</span>
                                        <span class="ud-subbox-value">
                                            @if($user->mitra)
                                                {{ $user->mitra->nama_mitra }}
                                            @else
                                                Politeknik Negeri Manado
                                            @endif
                                        </span>
                                    </div>

                                    <div class="ud-subbox">
                                        <span class="ud-subbox-label"><i class="fas fa-layer-group"></i> Tingkat Struktur</span>
                                        <span class="ud-subbox-value">
                                            @if($user->profile?->jurusan)
                                                Unit Pelaksana Jurusan (Akademik)
                                            @elseif($user->profile?->unitKerja)
                                                Humas / Unit Kerja Pusat
                                            @elseif($user->profile?->upa)
                                                Unit Pelaksana Akademik (UPA)
                                            @elseif($user->profile?->pusat)
                                                Pusat / Lembaga Khusus
                                            @elseif($user->mitra)
                                                Industri / Lembaga Mitra Eksternal
                                            @elseif($roleKey === 'pimpinan')
                                                Direksi & Pimpinan Utama
                                            @else
                                                Pengelola Sistem
                                            @endif
                                        </span>
                                    </div>

                                    <div class="ud-subbox">
                                        <span class="ud-subbox-label"><i class="fas fa-toggle-on"></i> Status Entitas</span>
                                        <span class="ud-subbox-value text-success">
                                            <i class="fas fa-circle-check"></i> Terintegrasi Aktif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ══════════════════════════════════════════════════════ --}}
                    {{-- TAB 2: KERJASAMA TERKAIT                             --}}
                    {{-- ══════════════════════════════════════════════════════ --}}
                    <div id="tab-cooperation" class="ud-tab-pane">
                        <div class="card ud-card">
                            <div class="ud-card-header">
                                <div class="ud-card-title-group">
                                    <span class="ud-card-icon icon-primary">
                                        <i class="fas fa-handshake"></i>
                                    </span>
                                    <div>
                                        <h3 class="ud-card-title">Daftar Dokumen Kerjasama Terkait</h3>
                                        <p class="ud-card-subtitle">
                                            @if($roleKey === 'mitra')
                                                Seluruh MoU, MoA, dan IA resmi antara Polimdo dengan {{ $user->mitra->nama_mitra ?? 'Mitra ini' }}
                                            @elseif($roleKey === 'jurusan')
                                                Kerjasama yang mencakup Jurusan {{ $user->profile?->jurusan?->nama_jurusan }}
                                            @elseif($roleKey === 'upa')
                                                Kerjasama di lingkup UPA {{ $user->profile?->upa?->nama_upa }}
                                            @elseif($roleKey === 'pusat')
                                                Kerjasama di lingkup Pusat {{ $user->profile?->pusat?->nama_pusat }}
                                            @else
                                                Dokumen kerjasama yang dikelola atau terdaftar dalam sistem
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="ud-card-body">
                                @if(isset($cooperations) && $cooperations->count() > 0)
                                    <div class="ud-coop-list">
                                        @foreach($cooperations as $coop)
                                            @php
                                                $jenisClass = match(strtoupper($coop->jenis)) {
                                                    'MOU' => 'badge-mou',
                                                    'MOA' => 'badge-moa',
                                                    'IA'  => 'badge-ia',
                                                    default => 'badge-spk',
                                                };

                                                $statusClass = match($coop->status_berlaku) {
                                                    'Aktif' => 'badge-status-active',
                                                    'Akan Berakhir' => 'badge-status-warning',
                                                    'Kadaluarsa' => 'badge-status-danger',
                                                    default => 'badge-status-default',
                                                };
                                            @endphp
                                            <div class="ud-coop-item">
                                                <div class="ud-coop-header">
                                                    <div class="ud-coop-tags">
                                                        <span class="ud-badge-jenis {{ $jenisClass }}">{{ $coop->jenis }}</span>
                                                        <span class="ud-badge-status {{ $statusClass }}">
                                                            <i class="fas fa-circle"></i> {{ $coop->status_berlaku ?? 'Aktif' }}
                                                        </span>
                                                        @if($coop->tingkat)
                                                            <span class="ud-badge-tingkat">
                                                                <i class="fas fa-layer-group"></i> {{ $coop->tingkat }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="ud-coop-doc-no">
                                                        <i class="fas fa-hashtag"></i> {{ $coop->doc_number ?: 'Tanpa Nomor' }}
                                                    </span>
                                                </div>

                                                <h4 class="ud-coop-title">{{ $coop->judul }}</h4>

                                                <div class="ud-coop-meta-row">
                                                    <div class="ud-coop-meta-col">
                                                        <i class="fas fa-building"></i>
                                                        <span>Mitra: <strong>{{ $coop->mitra->nama_mitra ?? $coop->internal_instansi }}</strong></span>
                                                    </div>
                                                    <div class="ud-coop-meta-col">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <span>Periode: 
                                                            {{ $coop->start_date ? $coop->start_date->format('d M Y') : '—' }}
                                                            s.d 
                                                            {{ $coop->end_date ? $coop->end_date->format('d M Y') : '—' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                @if($coop->ruang_lingkup)
                                                    <p class="ud-coop-scope">
                                                        {{ Str::limit($coop->ruang_lingkup, 140) }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="ud-empty-state">
                                        <div class="ud-empty-icon">
                                            <i class="fas fa-folder-open"></i>
                                        </div>
                                        <h4 class="ud-empty-title">Belum Ada Dokumen Kerjasama</h4>
                                        <p class="ud-empty-desc">
                                            Pengguna ini belum memiliki atau belum terkait langsung dengan dokumen kerjasama yang tercatat di sistem.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════ --}}
                    {{-- TAB 3: USULAN & PENGAJUAN                              --}}
                    {{-- ══════════════════════════════════════════════════════ --}}
                    <div id="tab-proposals" class="ud-tab-pane">
                        <div class="card ud-card">
                            <div class="ud-card-header">
                                <div class="ud-card-title-group">
                                    <span class="ud-card-icon icon-purple">
                                        <i class="fas fa-file-signature"></i>
                                    </span>
                                    <div>
                                        <h3 class="ud-card-title">Riwayat Usulan & Pengajuan Kerjasama</h3>
                                        <p class="ud-card-subtitle">Pengajuan kerjasama baru dan usulan perpanjangan</p>
                                    </div>
                                </div>
                            </div>

                            <div class="ud-card-body">
                                @php
                                    $hasProposals = (isset($proposals) && $proposals->count() > 0) || (isset($perpanjangans) && $perpanjangans->count() > 0);
                                @endphp

                                @if($hasProposals)
                                    <div class="ud-coop-list">
                                        {{-- Pengajuan Kerjasama Baru --}}
                                        @foreach($proposals as $prop)
                                            @php
                                                $propStatus = match(strtolower($prop->status ?? 'diajukan')) {
                                                    'disetujui' => 'badge-status-active',
                                                    'ditolak'   => 'badge-status-danger',
                                                    default     => 'badge-status-warning',
                                                };
                                            @endphp
                                            <div class="ud-coop-item">
                                                <div class="ud-coop-header">
                                                    <div class="ud-coop-tags">
                                                        <span class="ud-badge-jenis badge-mou">Usulan Baru</span>
                                                        <span class="ud-badge-status {{ $propStatus }}">
                                                            <i class="fas fa-circle"></i> {{ ucfirst($prop->status ?? 'Diajukan') }}
                                                        </span>
                                                    </div>
                                                    <span class="ud-coop-doc-no">
                                                        Kode: {{ $prop->kode_pengajuan ?? 'PRO-#' . $prop->id }}
                                                    </span>
                                                </div>

                                                <h4 class="ud-coop-title">{{ $prop->judul_pengajuan ?? $prop->nama_mitra }}</h4>

                                                <div class="ud-coop-meta-row">
                                                    <div class="ud-coop-meta-col">
                                                        <i class="fas fa-building"></i>
                                                        <span>Mitra: <strong>{{ $prop->nama_mitra }}</strong></span>
                                                    </div>
                                                    <div class="ud-coop-meta-col">
                                                        <i class="fas fa-clock"></i>
                                                        <span>Diajukan: {{ $prop->submitted_at ? $prop->submitted_at->format('d M Y') : $prop->created_at->format('d M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Pengajuan Perpanjangan --}}
                                        @foreach($perpanjangans as $perp)
                                            @php
                                                $perpStatus = match(strtolower($perp->status ?? 'diajukan')) {
                                                    'disetujui' => 'badge-status-active',
                                                    'ditolak'   => 'badge-status-danger',
                                                    default     => 'badge-status-warning',
                                                };
                                            @endphp
                                            <div class="ud-coop-item">
                                                <div class="ud-coop-header">
                                                    <div class="ud-coop-tags">
                                                        <span class="ud-badge-jenis badge-moa">Perpanjangan</span>
                                                        <span class="ud-badge-status {{ $perpStatus }}">
                                                            <i class="fas fa-circle"></i> {{ ucfirst($perp->status ?? 'Diajukan') }}
                                                        </span>
                                                    </div>
                                                    <span class="ud-coop-doc-no">
                                                        Kode: {{ $perp->kode_pengajuan ?? 'EXT-#' . $perp->id }}
                                                    </span>
                                                </div>

                                                <h4 class="ud-coop-title">{{ $perp->judul_pengajuan ?? ('Perpanjangan Dokumen ' . ($perp->doc_number ?: 'Kerjasama')) }}</h4>

                                                <div class="ud-coop-meta-row">
                                                    <div class="ud-coop-meta-col">
                                                        <i class="fas fa-building"></i>
                                                        <span>Mitra: <strong>{{ $perp->nama_mitra }}</strong></span>
                                                    </div>
                                                    <div class="ud-coop-meta-col">
                                                        <i class="fas fa-clock"></i>
                                                        <span>Diajukan: {{ $perp->created_at ? $perp->created_at->format('d M Y') : '—' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="ud-empty-state">
                                        <div class="ud-empty-icon">
                                            <i class="fas fa-file-circle-question"></i>
                                        </div>
                                        <h4 class="ud-empty-title">Belum Ada Riwayat Pengajuan</h4>
                                        <p class="ud-empty-desc">
                                            Tidak ada usulan permohonan kerjasama baru maupun perpanjangan yang terhubung dengan akun ini.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════ --}}
                    {{-- TAB 4: AUDIT & KEAMANAN AKUN                           --}}
                    {{-- ══════════════════════════════════════════════════════ --}}
                    <div id="tab-audit" class="ud-tab-pane">
                        <div class="card ud-card">
                            <div class="ud-card-header">
                                <div class="ud-card-title-group">
                                    <span class="ud-card-icon icon-cyan">
                                        <i class="fas fa-shield-halved"></i>
                                    </span>
                                    <div>
                                        <h3 class="ud-card-title">Audit Log & Keamanan Akun</h3>
                                        <p class="ud-card-subtitle">Jejak integritas data dan riwayat keamanan kredensial</p>
                                    </div>
                                </div>
                            </div>

                            <div class="ud-card-body">
                                <div class="ud-audit-grid">
                                    <div class="ud-audit-card">
                                        <div class="ud-audit-icon"><i class="fas fa-calendar-plus"></i></div>
                                        <div class="ud-audit-info">
                                            <span class="ud-audit-label">Waktu Registrasi Akun</span>
                                            <span class="ud-audit-val">{{ $user->created_at ? $user->created_at->format('d F Y, H:i:s') . ' WITA' : '—' }}</span>
                                            <span class="ud-audit-sub">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="ud-audit-card">
                                        <div class="ud-audit-icon"><i class="fas fa-user-pen"></i></div>
                                        <div class="ud-audit-info">
                                            <span class="ud-audit-label">Pembaruan Data Terakhir</span>
                                            <span class="ud-audit-val">{{ $user->updated_at ? $user->updated_at->format('d F Y, H:i:s') . ' WITA' : '—' }}</span>
                                            <span class="ud-audit-sub">{{ $user->updated_at ? $user->updated_at->diffForHumans() : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="ud-audit-card">
                                        <div class="ud-audit-icon"><i class="fas fa-envelope-circle-check"></i></div>
                                        <div class="ud-audit-info">
                                            <span class="ud-audit-label">Waktu Verifikasi Email</span>
                                            <span class="ud-audit-val">{{ $user->email_verified_at ? $user->email_verified_at->format('d F Y, H:i:s') . ' WITA' : 'Belum Diverifikasi' }}</span>
                                            <span class="ud-audit-sub">{{ $user->email_verified_at ? 'Email resmi terverifikasi' : 'Harap kirim ulang verifikasi jika diperlukan' }}</span>
                                        </div>
                                    </div>

                                    <div class="ud-audit-card">
                                        <div class="ud-audit-icon"><i class="fas fa-fingerprint"></i></div>
                                        <div class="ud-audit-info">
                                            <span class="ud-audit-label">Token Remember Me</span>
                                            <span class="ud-audit-val ud-mono">{{ $user->remember_token ? 'Aktif (' . substr($user->remember_token, 0, 12) . '...)' : 'Tidak Tersimpan' }}</span>
                                            <span class="ud-audit-sub">Perlindungan sesi login berkelanjutan</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="ud-security-notice">
                                    <i class="fas fa-shield-check"></i>
                                    <div>
                                        <h5>Integritas Enkripsi Terjaga</h5>
                                        <p>Seluruh password disimpan dengan algoritma hashing standar Bcrypt berkeamanan tinggi. Tidak ada plaintext password yang disimpan di database.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Toast Notifikasi Copy Clipboard --}}
    <div id="udToast" class="ud-toast">
        <i class="fas fa-circle-check"></i>
        <span id="udToastText">Teks berhasil disalin!</span>
    </div>
</main>

{{-- ── STYLING (DUAL-THEME: LIGHT & DARK MODE + HORIZONTAL SCROLL) ──── --}}
<style>
/* ─── CSS Variables & Design Tokens ───────────────────────────────── */
:root {
    --ud-bg: #f8fafc;
    --ud-card-bg: #ffffff;
    --ud-card-border: #e2e8f0;
    --ud-card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
    --ud-card-hover-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.09), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
    
    --ud-text-title: #0f172a;
    --ud-text-body: #334155;
    --ud-text-muted: #64748b;
    --ud-text-sub: #94a3b8;
    
    --ud-item-bg: #f8fafc;
    --ud-item-border: #e2e8f0;
    --ud-item-hover-bg: #f1f5f9;
    
    --ud-accent-primary: #0284c7;
    --ud-accent-primary-rgb: 2, 132, 199;
}

/* Dark Mode Tokens */
[data-theme="dark"] .ud-detail-page,
[data-theme="dark"] {
    --ud-bg: #0f1117;
    --ud-card-bg: #181c27;
    --ud-card-border: #2a2f45;
    --ud-card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4);
    --ud-card-hover-shadow: 0 12px 30px -4px rgba(0, 0, 0, 0.6);
    
    --ud-text-title: #f8fafc;
    --ud-text-body: #cbd5e1;
    --ud-text-muted: #94a3b8;
    --ud-text-sub: #64748b;
    
    --ud-item-bg: #1e2333;
    --ud-item-border: #2e354d;
    --ud-item-hover-bg: #262c40;
    
    --ud-accent-primary: #38bdf8;
    --ud-accent-primary-rgb: 56, 189, 248;
}

/* ─── Base Page Layout ────────────────────────────────────────────── */
.ud-detail-page {
    padding: 1.5rem 1.75rem 3rem 1.75rem;
    color: var(--ud-text-body);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* ─── Topbar & Actions ────────────────────────────────────────────── */
.ud-topbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 1.5rem;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.ud-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: var(--ud-text-muted);
    margin-bottom: 0.5rem;
}

.ud-breadcrumb-link {
    color: var(--ud-text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
}

.ud-breadcrumb-link:hover {
    color: var(--ud-accent-primary);
}

.ud-title-row {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.ud-title-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: rgba(var(--ud-accent-primary-rgb), 0.12);
    color: var(--ud-accent-primary);
    border: 1px solid rgba(var(--ud-accent-primary-rgb), 0.2);
}

.ud-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--ud-text-title);
    margin: 0;
    line-height: 1.25;
    letter-spacing: -0.02em;
}

.ud-subtitle {
    font-size: 0.875rem;
    color: var(--ud-text-muted);
    margin: 0.25rem 0 0 0;
}

.ud-topbar-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ud-btn-back,
.ud-btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.125rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.ud-btn-back {
    background: var(--ud-card-bg);
    color: var(--ud-text-body);
    border: 1px solid var(--ud-card-border);
}

.ud-btn-back:hover {
    background: var(--ud-item-hover-bg);
    color: var(--ud-text-title);
    transform: translateY(-1px);
}

.ud-btn-edit {
    background: var(--ud-accent-primary);
    color: #ffffff !important;
    border: 1px solid transparent;
    box-shadow: 0 4px 12px rgba(var(--ud-accent-primary-rgb), 0.25);
}

.ud-btn-edit:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(var(--ud-accent-primary-rgb), 0.35);
}

/* ─── Hero Spotlight Card ─────────────────────────────────────────── */
.ud-hero-card {
    position: relative;
    background: var(--ud-card-bg);
    border: 1px solid var(--ud-card-border);
    border-radius: 20px;
    padding: 1.75rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--ud-card-shadow);
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.ud-hero-glow {
    position: absolute;
    top: -80px;
    right: -80px;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(var(--ud-accent-primary-rgb), 0.15) 0%, rgba(var(--ud-accent-primary-rgb), 0) 70%);
    pointer-events: none;
}

.ud-hero-pattern {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(var(--ud-card-border) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.4;
    pointer-events: none;
}

.ud-hero-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 1.75rem;
    flex-wrap: wrap;
}

.ud-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.ud-avatar {
    width: 84px;
    height: 84px;
    border-radius: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    letter-spacing: -0.05em;
}

.ud-status-pulse {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #10b981;
    border: 3px solid var(--ud-card-bg);
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.4);
}

.ud-user-meta {
    flex: 1;
    min-width: 260px;
}

.ud-user-top {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 0.625rem;
}

.ud-user-name {
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--ud-text-title);
    margin: 0;
    letter-spacing: -0.02em;
}

.ud-role-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 700;
    background: rgba(var(--ud-accent-primary-rgb), 0.12);
    color: var(--ud-accent-primary);
    border: 1px solid rgba(var(--ud-accent-primary-rgb), 0.25);
}

.ud-verified-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.25);
}

.ud-unverified-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.25);
}

.ud-quick-badges {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.ud-quick-item {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.75rem;
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--ud-text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
}

.ud-quick-item:hover {
    background: var(--ud-item-hover-bg);
    color: var(--ud-text-title);
    border-color: var(--ud-accent-primary);
}

.ud-copy-icon {
    font-size: 0.75rem;
    opacity: 0.5;
}

.ud-quick-item:hover .ud-copy-icon {
    opacity: 1;
    color: var(--ud-accent-primary);
}

.ud-hero-stats {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding-left: 1.5rem;
    border-left: 1px solid var(--ud-card-border);
}

.ud-stat-box {
    display: flex;
    flex-direction: column;
}

.ud-stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ud-text-sub);
    margin-bottom: 0.2rem;
}

.ud-stat-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ud-text-title);
}

.ud-stat-sub {
    font-size: 0.75rem;
    color: var(--ud-text-muted);
}

.ud-stat-divider {
    width: 1px;
    height: 36px;
    background: var(--ud-card-border);
}

/* ─── Metric Summary Counters ─────────────────────────────────────── */
.ud-metric-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.ud-metric-card {
    background: var(--ud-card-bg);
    border: 1px solid var(--ud-card-border);
    border-radius: 16px;
    padding: 1.25rem 1.25rem;
    box-shadow: var(--ud-card-shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s ease;
}

.ud-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--ud-card-hover-shadow);
}

.ud-metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.icon-primary { background: rgba(2, 132, 199, 0.12); color: #0284c7; }
.icon-success { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.icon-warning { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.icon-purple  { background: rgba(168, 85, 247, 0.12); color: #a855f7; }
.icon-cyan    { background: rgba(6, 182, 212, 0.12); color: #0891b2; }
.icon-emerald { background: rgba(5, 150, 105, 0.12); color: #059669; }
.icon-danger  { background: rgba(239, 68, 68, 0.12); color: #ef4444; }

.ud-metric-info {
    display: flex;
    flex-direction: column;
}

.ud-metric-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ud-text-muted);
}

.ud-metric-num {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--ud-text-title);
    line-height: 1.2;
}

.ud-metric-sub {
    font-size: 0.75rem;
    color: var(--ud-text-sub);
}

/* ─── Grid Layout (Side 360px + Main) ─────────────────────────────── */
.ud-grid-container {
    display: grid;
    grid-template-columns: 360px minmax(0, 1fr);
    gap: 1.5rem;
    align-items: start;
}

.ud-col-side {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.ud-col-main {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

/* ─── Horizontal Scroll Containers (Responsive Polish) ───────────── */
.ud-tabs-nav-wrapper {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 1.25rem;
    scrollbar-width: thin;
}

.ud-tabs-nav-wrapper::-webkit-scrollbar {
    height: 4px;
}

.ud-tabs-nav-wrapper::-webkit-scrollbar-thumb {
    background: var(--ud-card-border);
    border-radius: 999px;
}

.ud-main-scroll-wrapper {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 0.5rem;
    scrollbar-width: thin;
}

/* Sleek Scrollbar for Light & Dark Mode */
.ud-main-scroll-wrapper::-webkit-scrollbar {
    height: 6px;
}

.ud-main-scroll-wrapper::-webkit-scrollbar-track {
    background: var(--ud-item-bg);
    border-radius: 999px;
}

.ud-main-scroll-wrapper::-webkit-scrollbar-thumb {
    background: var(--ud-card-border);
    border-radius: 999px;
}

.ud-main-scroll-wrapper::-webkit-scrollbar-thumb:hover {
    background: var(--ud-accent-primary);
}

.ud-content-inner-container {
    min-width: 100%;
    width: auto;
}

/* ─── Tab Navigation Bar ─────────────────────────────────────────── */
.ud-tabs-nav {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--ud-card-bg);
    border: 1px solid var(--ud-card-border);
    border-radius: 14px;
    padding: 0.375rem;
    box-shadow: var(--ud-card-shadow);
    min-width: max-content;
}

.ud-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.125rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ud-text-muted);
    background: transparent;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.ud-tab-btn:hover {
    color: var(--ud-text-title);
    background: var(--ud-item-hover-bg);
}

.ud-tab-btn.active {
    color: var(--ud-accent-primary);
    background: rgba(var(--ud-accent-primary-rgb), 0.12);
}

.ud-tab-count {
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
    background: var(--ud-accent-primary);
    color: #ffffff;
}

.ud-tab-count.count-purple {
    background: #a855f7;
}

.ud-tab-pane {
    display: none;
    animation: fadeIn 0.25s ease-in-out;
}

.ud-tab-pane.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ─── Cards Styling ──────────────────────────────────────────────── */
.ud-card {
    background: var(--ud-card-bg);
    border: 1px solid var(--ud-card-border);
    border-radius: 18px;
    box-shadow: var(--ud-card-shadow);
    overflow: hidden;
    margin-bottom: 1.25rem;
    transition: box-shadow 0.2s ease;
}

.ud-card:hover {
    box-shadow: var(--ud-card-hover-shadow);
}

.ud-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--ud-card-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ud-card-title-group {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.ud-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.ud-card-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--ud-text-title);
    margin: 0;
    letter-spacing: -0.01em;
}

.ud-card-subtitle {
    font-size: 0.8125rem;
    color: var(--ud-text-muted);
    margin: 0.15rem 0 0 0;
}

.ud-card-body {
    padding: 1.5rem;
}

/* ─── Info Stack (Left Column) ────────────────────────────────────── */
.ud-info-stack {
    display: flex;
    flex-direction: column;
    gap: 0.875rem;
}

.ud-info-tile {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    padding: 0.75rem 0.875rem;
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    border-radius: 12px;
    transition: background 0.15s ease;
}

.ud-info-tile:hover {
    background: var(--ud-item-hover-bg);
}

.ud-tile-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(var(--ud-accent-primary-rgb), 0.12);
    color: var(--ud-accent-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    flex-shrink: 0;
    margin-top: 0.1rem;
}

.ud-tile-content {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}

.ud-tile-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--ud-text-muted);
}

.ud-tile-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ud-text-title);
    word-break: break-word;
}

.ud-tile-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    width: fit-content;
}

.badge-green { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.badge-amber { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }

/* ─── Role Card Special ───────────────────────────────────────────── */
.ud-role-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.ud-role-description {
    font-size: 0.875rem;
    line-height: 1.5;
    color: var(--ud-text-muted);
    margin-bottom: 1rem;
}

.ud-role-meta-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.ud-role-meta-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.8125rem;
    color: var(--ud-text-body);
}

/* ─── Danger Zone ─────────────────────────────────────────────────── */
.ud-card-danger {
    border-color: rgba(239, 68, 68, 0.3);
}

.ud-danger-text {
    font-size: 0.8125rem;
    color: var(--ud-text-muted);
    margin-bottom: 1rem;
    line-height: 1.4;
}

.ud-btn-danger-block {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.25);
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.ud-btn-danger-block:hover {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* ─── Right Column: Data Grid ─────────────────────────────────────── */
.ud-data-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.ud-field-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.ud-field-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ud-text-muted);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ud-field-value {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--ud-text-title);
    padding: 0.75rem 1rem;
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    border-radius: 10px;
    min-height: 44px;
    display: flex;
    align-items: center;
}

.ud-field-value.ud-highlight {
    font-weight: 700;
    color: var(--ud-accent-primary);
}

.ud-link {
    color: var(--ud-accent-primary);
    text-decoration: none;
}

.ud-link:hover {
    text-decoration: underline;
}

.ud-inline-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 600;
}

/* ─── Placement Box ───────────────────────────────────────────────── */
.ud-placement-box {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem 1.5rem;
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    border-radius: 14px;
}

.ud-placement-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    background: rgba(var(--ud-accent-primary-rgb), 0.12);
    color: var(--ud-accent-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.ud-placement-details {
    display: flex;
    flex-direction: column;
}

.ud-placement-type {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ud-accent-primary);
}

.ud-placement-name {
    font-size: 1.125rem;
    font-weight: 800;
    color: var(--ud-text-title);
    margin: 0.15rem 0 0.25rem 0;
}

.ud-placement-role {
    font-size: 0.8125rem;
    color: var(--ud-text-muted);
}

.ud-subgrid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.ud-subbox {
    padding: 1rem;
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.ud-subbox-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ud-text-muted);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.ud-subbox-value {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--ud-text-title);
}

/* ─── Cooperation List & Cards ────────────────────────────────────── */
.ud-coop-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.ud-coop-item {
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    transition: all 0.2s ease;
}

.ud-coop-item:hover {
    background: var(--ud-item-hover-bg);
    border-color: var(--ud-accent-primary);
    transform: translateY(-1px);
}

.ud-coop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.625rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.ud-coop-tags {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.ud-badge-jenis {
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.badge-mou { background: rgba(2, 132, 199, 0.15); color: #0284c7; }
.badge-moa { background: rgba(124, 58, 237, 0.15); color: #7c3aed; }
.badge-ia  { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.badge-spk { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

.ud-badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.ud-badge-status i {
    font-size: 0.5rem;
}

.badge-status-active { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.badge-status-warning { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.badge-status-danger { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
.badge-status-default { background: rgba(100, 116, 139, 0.12); color: #64748b; }

.ud-badge-tingkat {
    font-size: 0.75rem;
    color: var(--ud-text-muted);
}

.ud-coop-doc-no {
    font-size: 0.8125rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: var(--ud-text-muted);
}

.ud-coop-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--ud-text-title);
    margin: 0 0 0.625rem 0;
    line-height: 1.4;
}

.ud-coop-meta-row {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    font-size: 0.8125rem;
    color: var(--ud-text-muted);
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}

.ud-coop-meta-col {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.ud-coop-meta-col strong {
    color: var(--ud-text-title);
}

.ud-coop-scope {
    font-size: 0.8125rem;
    color: var(--ud-text-muted);
    margin: 0;
    line-height: 1.5;
}

/* ─── Empty State ─────────────────────────────────────────────────── */
.ud-empty-state {
    padding: 3rem 1.5rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.ud-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    color: var(--ud-text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 1rem;
}

.ud-empty-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--ud-text-title);
    margin: 0 0 0.25rem 0;
}

.ud-empty-desc {
    font-size: 0.875rem;
    color: var(--ud-text-muted);
    max-width: 420px;
    margin: 0;
}

/* ─── Audit & Security Tab ────────────────────────────────────────── */
.ud-audit-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.ud-audit-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.125rem;
    background: var(--ud-item-bg);
    border: 1px solid var(--ud-item-border);
    border-radius: 14px;
}

.ud-audit-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(var(--ud-accent-primary-rgb), 0.12);
    color: var(--ud-accent-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.ud-audit-info {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.ud-audit-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ud-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.ud-audit-val {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--ud-text-title);
}

.ud-audit-sub {
    font-size: 0.75rem;
    color: var(--ud-text-muted);
}

.ud-security-notice {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.125rem 1.25rem;
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 12px;
}

.ud-security-notice i {
    font-size: 1.5rem;
    color: #10b981;
    margin-top: 0.1rem;
}

.ud-security-notice h5 {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #10b981;
    margin: 0 0 0.2rem 0;
}

.ud-security-notice p {
    font-size: 0.8125rem;
    color: var(--ud-text-body);
    margin: 0;
    line-height: 1.4;
}

/* ─── Typography & Helpers ────────────────────────────────────────── */
.ud-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

.ud-selectable {
    user-select: all;
}

.text-success { color: #10b981 !important; }
.text-warning { color: #f59e0b !important; }
.text-danger  { color: #ef4444 !important; }
.text-purple  { color: #a855f7 !important; }
.text-muted   { color: var(--ud-text-muted) !important; }

/* ─── Role Theme Colors ───────────────────────────────────────────── */
.role-admin { --ud-accent-primary: #7c3aed; --ud-accent-primary-rgb: 124, 58, 237; }
.role-pimpinan { --ud-accent-primary: #d97706; --ud-accent-primary-rgb: 217, 119, 6; }
.role-jurusan { --ud-accent-primary: #0284c7; --ud-accent-primary-rgb: 2, 132, 199; }
.role-unit-kerja { --ud-accent-primary: #059669; --ud-accent-primary-rgb: 5, 150, 105; }
.role-upa { --ud-accent-primary: #0891b2; --ud-accent-primary-rgb: 8, 145, 178; }
.role-pusat { --ud-accent-primary: #9333ea; --ud-accent-primary-rgb: 147, 51, 234; }
.role-mitra { --ud-accent-primary: #2563eb; --ud-accent-primary-rgb: 37, 99, 235; }

/* ─── Toast Notification ──────────────────────────────────────────── */
.ud-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: #0f172a;
    color: #f8fafc;
    padding: 0.875rem 1.25rem;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    z-index: 9999;
    opacity: 0;
    transform: translateY(20px);
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

[data-theme="dark"] .ud-toast {
    background: #ffffff;
    color: #0f172a;
}

.ud-toast.show {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

.ud-toast i {
    color: #10b981;
    font-size: 1.125rem;
}

/* ─── Responsive Media Queries ────────────────────────────────────── */
@media (max-width: 1200px) {
    .ud-metric-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .ud-grid-container {
        grid-template-columns: 1fr;
    }
    .ud-hero-stats {
        padding-left: 0;
        border-left: none;
        width: 100%;
        justify-content: flex-start;
        gap: 2rem;
        padding-top: 1rem;
        border-top: 1px solid var(--ud-card-border);
    }
}

@media (max-width: 768px) {
    .ud-detail-page {
        padding: 1rem;
    }
    .ud-data-grid,
    .ud-subgrid,
    .ud-audit-grid {
        grid-template-columns: 1fr;
    }
    .ud-metric-grid {
        grid-template-columns: 1fr;
    }
    .ud-topbar {
        flex-direction: column;
        align-items: flex-start;
    }
    .ud-topbar-actions {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

{{-- ── JAVASCRIPT (MICRO-INTERACTIONS & TABS) ───────────────────────── --}}
<script>
// Switch Tab Function
function switchTab(tabId, btnElement) {
    // Hide all panes
    const panes = document.querySelectorAll('.ud-tab-pane');
    panes.forEach(pane => pane.classList.remove('active'));

    // Deactivate all buttons
    const buttons = document.querySelectorAll('.ud-tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    // Activate selected
    const targetPane = document.getElementById(tabId);
    if (targetPane) {
        targetPane.classList.add('active');
    }
    if (btnElement) {
        btnElement.classList.add('active');
    }

    // Save active tab in session storage
    sessionStorage.setItem('ud_active_tab', tabId);
}

// Clipboard Copy Helper
let toastTimer = null;
function copyToClipboard(text, message) {
    if (!text || text === '-' || text === '—') return;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast(message || 'Teks berhasil disalin!');
        });
    } else {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-999999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        try {
            document.execCommand('copy');
            showToast(message || 'Teks berhasil disalin!');
        } catch (err) {
            console.error('Gagal menyalin:', err);
        }
        document.body.removeChild(textarea);
    }
}

function showToast(text) {
    const toast = document.getElementById('udToast');
    const toastText = document.getElementById('udToastText');
    if (!toast || !toastText) return;

    toastText.innerText = text;
    toast.classList.add('show');

    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

// Restore Tab on Page Load
document.addEventListener('DOMContentLoaded', () => {
    const savedTab = sessionStorage.getItem('ud_active_tab');
    if (savedTab) {
        const btn = document.querySelector(`.ud-tab-btn[data-tab="${savedTab}"]`);
        if (btn) {
            switchTab(savedTab, btn);
        }
    }
});
</script>
@endsection

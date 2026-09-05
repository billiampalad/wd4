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
                <span class="ud-metric-num text-primary">{{ $stats['total_cooperations'] ?? 0 }}</span>
                <span class="ud-metric-label">Total Kerjasama</span>
            </div>
        </div>

        <div class="ud-metric-card">
            <div class="ud-metric-icon icon-success">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="ud-metric-info">
                <span class="ud-metric-num text-success">{{ $stats['active_cooperations'] ?? 0 }}</span>
                <span class="ud-metric-label">Kerjasama Aktif</span>
            </div>
        </div>

        <div class="ud-metric-card">
            <div class="ud-metric-icon icon-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="ud-metric-info">
                <span class="ud-metric-num text-warning">{{ $stats['expiring_cooperations'] ?? 0 }}</span>
                <span class="ud-metric-label">Akan Berakhir</span>
            </div>
        </div>

        <div class="ud-metric-card">
            <div class="ud-metric-icon icon-purple">
                <i class="fas fa-file-signature"></i>
            </div>
            <div class="ud-metric-info">
                <span class="ud-metric-num text-purple">{{ $stats['total_proposals'] ?? 0 }}</span>
                <span class="ud-metric-label">Usulan & Pengajuan</span>
            </div>
        </div>
    </div>

    {{-- ── Main Grid Section with Horizontal Scroll Wrapper ──────── --}}
    <div class="ud-grid-container">

        {{-- ── Left Column: Security, Account & Role Tier (340px) ── --}}
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
                                <span class="ud-tile-value text-muted">•••••••••••••••• (Bcrypt)</span>
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
                            <span>Notifikasi Transaksi Aktif</span>
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
                        <div class="card ud-card">
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

                                <div class="ud-subgrid">
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
                                                Unit Jurusan (Akademik)
                                            @elseif($user->profile?->unitKerja)
                                                Humas / Unit Kerja Pusat
                                            @elseif($user->profile?->upa)
                                                Unit Pelaksana Akademik (UPA)
                                            @elseif($user->profile?->pusat)
                                                Pusat / Lembaga Khusus
                                            @elseif($user->mitra)
                                                Mitra Industri Eksternal
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
                                                        {{ Str::limit($coop->ruang_lingkup, 130) }}
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
                                            <span class="ud-audit-label">Pembaruan Terakhir</span>
                                            <span class="ud-audit-val">{{ $user->updated_at ? $user->updated_at->format('d F Y, H:i:s') . ' WITA' : '—' }}</span>
                                            <span class="ud-audit-sub">{{ $user->updated_at ? $user->updated_at->diffForHumans() : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="ud-audit-card">
                                        <div class="ud-audit-icon"><i class="fas fa-envelope-circle-check"></i></div>
                                        <div class="ud-audit-info">
                                            <span class="ud-audit-label">Verifikasi Email</span>
                                            <span class="ud-audit-val">{{ $user->email_verified_at ? $user->email_verified_at->format('d F Y, H:i:s') . ' WITA' : 'Belum Diverifikasi' }}</span>
                                            <span class="ud-audit-sub">{{ $user->email_verified_at ? 'Email resmi terverifikasi' : 'Harap verifikasi jika diperlukan' }}</span>
                                        </div>
                                    </div>

                                    <div class="ud-audit-card">
                                        <div class="ud-audit-icon"><i class="fas fa-fingerprint"></i></div>
                                        <div class="ud-audit-info">
                                            <span class="ud-audit-label">Token Remember Me</span>
                                            <span class="ud-audit-val ud-mono">{{ $user->remember_token ? 'Aktif (' . substr($user->remember_token, 0, 10) . '...)' : 'Tidak Tersimpan' }}</span>
                                            <span class="ud-audit-sub">Perlindungan sesi aktif</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="ud-security-notice">
                                    <i class="fas fa-shield-check"></i>
                                    <div>
                                        <h5>Integritas Enkripsi Terjaga</h5>
                                        <p>Seluruh password disimpan dengan algoritma hashing standar Bcrypt berkeamanan tinggi.</p>
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
@endsection

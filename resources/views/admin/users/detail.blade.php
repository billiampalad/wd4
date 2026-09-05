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
    
    // Theme accents per role
    $roleThemes = [
        'admin' => [
            'badge_bg'     => 'rgba(124, 58, 237, 0.12)',
            'badge_text'   => '#7c3aed',
            'badge_border' => 'rgba(124, 58, 237, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%)',
            'icon'         => 'fas fa-shield-halved',
            'role_desc'    => 'Memiliki akses penuh untuk mengelola seluruh master data, pengaturan sistem, manajemen role, dan manajemen akun staf serta mitra.',
            'access_tier'  => 'Super Administrator (Level 1)'
        ],
        'pimpinan' => [
            'badge_bg'     => 'rgba(217, 119, 6, 0.12)',
            'badge_text'   => '#d97706',
            'badge_border' => 'rgba(217, 119, 6, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
            'icon'         => 'fas fa-crown',
            'role_desc'    => 'Memiliki kewenangan monitoring eksekutif, memvalidasi dan mengesahkan dokumen kerjasama serta menerima pengajuan strategis.',
            'access_tier'  => 'Eksekutif & Pengambil Keputusan (Level 2)'
        ],
        'jurusan' => [
            'badge_bg'     => 'rgba(14, 165, 233, 0.12)',
            'badge_text'   => '#0284c7',
            'badge_border' => 'rgba(14, 165, 233, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
            'icon'         => 'fas fa-graduation-cap',
            'role_desc'    => 'Mengelola kegiatan kerjasama di tingkat jurusan dan program studi, monitoring mahasiswa magang, dan mengisi evaluasi berkala.',
            'access_tier'  => 'Unit Akademik Jurusan (Level 3)'
        ],
        'unit_kerja' => [
            'badge_bg'     => 'rgba(16, 185, 129, 0.12)',
            'badge_text'   => '#059669',
            'badge_border' => 'rgba(16, 185, 129, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
            'icon'         => 'fas fa-building',
            'role_desc'    => 'Membantu administrasi kerjasama institusi, menjalin komunikasi dengan mitra industri, verifikasi berkas, dan penyusunan draf kerjasama.',
            'access_tier'  => 'Unit Kerja Humas / KUI (Level 3)'
        ],
        'upa' => [
            'badge_bg'     => 'rgba(6, 182, 212, 0.12)',
            'badge_text'   => '#0891b2',
            'badge_border' => 'rgba(6, 182, 212, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #0891b2 0%, #0e7490 100%)',
            'icon'         => 'fas fa-building-columns',
            'role_desc'    => 'Mengelola program kerjasama dan layanan teknis di lingkup Unit Pelaksana Akademik (Perpustakaan, Lab, Bahasa, dll).',
            'access_tier'  => 'Unit Pelaksana Akademik (Level 3)'
        ],
        'pusat' => [
            'badge_bg'     => 'rgba(168, 85, 247, 0.12)',
            'badge_text'   => '#9333ea',
            'badge_border' => 'rgba(168, 85, 247, 0.25)',
            'gradient'     => 'linear-gradient(135deg, #9333ea 0%, #7e22ce 100%)',
            'icon'         => 'fas fa-landmark',
            'role_desc'    => 'Mengelola program kerjasama strategis tingkat pusat (P3M, Pusat Karir, Pusat Penjaminan Mutu, dsb).',
            'access_tier'  => 'Unit Pusat / Lembaga Khusus (Level 3)'
        ],
        'mitra' => [
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

<main class="main-content admin-dashboard ud-detail-page">
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
                <span class="ud-title-icon" style="background: {{ $currentTheme['badge_bg'] }}; color: {{ $currentTheme['badge_text'] }};">
                    <i class="{{ $currentTheme['icon'] }}"></i>
                </span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Detail Data Pengguna</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Informasi lengkap profil, hak akses peran, struktur penempatan unit, dan riwayat kredensial.
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
                    <span class="ud-role-pill" style="background: {{ $currentTheme['badge_bg'] }}; color: {{ $currentTheme['badge_text'] }}; border-color: {{ $currentTheme['badge_border'] }};">
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

    {{-- ── Main Grid Section ──────────────────────────────────────── --}}
    <div class="ud-grid-container">

        {{-- ── Left Column: Security, Account & Role Tier (380px) ── --}}
        <div class="ud-col-side">
            
            {{-- Card 1: Informasi Kredensial & Autentikasi --}}
            <div class="card ud-card">
                <div class="ud-card-header">
                    <div class="ud-card-title">
                        <i class="fas fa-key ud-header-icon" style="color: #6366f1;"></i>
                        <span>Kredensial & Autentikasi</span>
                    </div>
                    <span class="ud-header-badge">Privat</span>
                </div>

                <div class="ud-card-body">
                    <div class="ud-info-item">
                        <div class="ud-info-label">
                            <i class="fas fa-envelope"></i>
                            <span>Email Terdaftar</span>
                        </div>
                        <div class="ud-info-value-wrap">
                            <span class="ud-info-val">{{ $user->email }}</span>
                            <button type="button" class="ud-copy-btn" onclick="copyToClipboard('{{ $user->email }}', 'Email disalin!')" title="Salin Email">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="ud-info-item">
                        <div class="ud-info-label">
                            <i class="fas fa-id-card"></i>
                            <span>Nomor Induk Kependudukan (NIK)</span>
                        </div>
                        <div class="ud-info-value-wrap">
                            <span class="ud-info-val ud-mono">{{ $user->nik ?? 'Belum diisi' }}</span>
                            @if($user->nik)
                                <button type="button" class="ud-copy-btn" onclick="copyToClipboard('{{ $user->nik }}', 'NIK disalin!')" title="Salin NIK">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="ud-info-item">
                        <div class="ud-info-label">
                            <i class="fas fa-lock"></i>
                            <span>Keamanan Password</span>
                        </div>
                        <div class="ud-pass-status-box">
                            <div class="ud-pass-dots">
                                <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                            </div>
                            <span class="ud-pass-meta"><i class="fas fa-shield-check"></i> Terenkripsi Bcrypt (60-char)</span>
                        </div>
                    </div>

                    <div class="ud-info-item">
                        <div class="ud-info-label">
                            <i class="fas fa-circle-check"></i>
                            <span>Status Verifikasi Akun</span>
                        </div>
                        <div class="ud-verification-status">
                            @if($user->email_verified_at)
                                <div class="ud-status-tag verified">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Terverifikasi pada {{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @else
                                <div class="ud-status-tag unverified">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <span>Belum Diverifikasi</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Tingkat Akses & Otoritas --}}
            <div class="card ud-card">
                <div class="ud-card-header">
                    <div class="ud-card-title">
                        <i class="fas fa-user-shield ud-header-icon" style="color: {{ $currentTheme['badge_text'] }};"></i>
                        <span>Otoritas & Hak Akses</span>
                    </div>
                </div>
                <div class="ud-card-body">
                    <div class="ud-role-banner" style="background: {{ $currentTheme['badge_bg'] }}; border: 1px solid {{ $currentTheme['badge_border'] }};">
                        <div class="ud-role-icon-box" style="color: {{ $currentTheme['badge_text'] }};">
                            <i class="{{ $currentTheme['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="ud-role-banner-title" style="color: {{ $currentTheme['badge_text'] }};">
                                {{ $roleLabels[$user->role?->role_name] ?? $user->role?->role_name ?? 'Default User' }}
                            </div>
                            <div class="ud-role-tier">{{ $currentTheme['access_tier'] }}</div>
                        </div>
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
                    <div class="ud-card-title" style="color: #ef4444;">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span>Zona Tindakan Kritis</span>
                    </div>
                </div>
                <div class="ud-card-body">
                    <p class="ud-danger-desc">
                        Menghapus pengguna ini akan mencabut seluruh hak akses login ke sistem WD4 secara permanen.
                    </p>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengguna {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ud-btn-danger">
                            <i class="fas fa-trash-can"></i>
                            <span>Hapus Akun Pengguna</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- ── Right Column: Organization Structure & Timeline (1fr) ── --}}
        <div class="ud-col-main">

            {{-- Card 4: Struktur Penempatan Unit Kerja Kampus --}}
            <div class="card ud-card">
                <div class="ud-card-header">
                    <div class="ud-card-title">
                        <i class="fas fa-sitemap ud-header-icon" style="color: #0284c7;"></i>
                        <span>Penempatan Unit & Struktur Organisasi</span>
                    </div>
                    <span class="ud-unit-type-badge">
                        <i class="{{ $unitIcon }}"></i> {{ $unitType }}
                    </span>
                </div>

                <div class="ud-card-body">
                    <div class="ud-structure-hero">
                        <div class="ud-structure-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                            <i class="{{ $unitIcon }}"></i>
                        </div>
                        <div class="ud-structure-text">
                            <span class="ud-structure-eyebrow">Unit Kerja Terdaftar</span>
                            <h3 class="ud-structure-name">{{ $unitName }}</h3>
                            <span class="ud-structure-sub">
                                Politeknik Negeri Manado — Sistem Informasi Kerjasama WD4
                            </span>
                        </div>
                    </div>

                    <div class="ud-profile-detail-grid">
                        <div class="ud-profile-box">
                            <span class="ud-pbox-icon"><i class="fas fa-briefcase"></i></span>
                            <div class="ud-pbox-content">
                                <span class="ud-pbox-label">Jabatan dalam Unit</span>
                                <span class="ud-pbox-val">{{ $user->profile?->jabatan ?: '— (Belum Ditentukan)' }}</span>
                            </div>
                        </div>

                        <div class="ud-profile-box">
                            <span class="ud-pbox-icon"><i class="fas fa-building-user"></i></span>
                            <div class="ud-pbox-content">
                                <span class="ud-pbox-label">Tipe Unit Institusi</span>
                                <span class="ud-pbox-val">{{ $unitType }}</span>
                            </div>
                        </div>

                        @if($user->profile?->jurusan)
                            <div class="ud-profile-box">
                                <span class="ud-pbox-icon"><i class="fas fa-code-branch"></i></span>
                                <div class="ud-pbox-content">
                                    <span class="ud-pbox-label">Kode Jurusan</span>
                                    <span class="ud-pbox-val ud-mono">{{ $user->profile->jurusan->kode_jurusan ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="ud-profile-box">
                                <span class="ud-pbox-icon"><i class="fas fa-graduation-cap"></i></span>
                                <div class="ud-pbox-content">
                                    <span class="ud-pbox-label">Total Program Studi</span>
                                    <span class="ud-pbox-val">{{ $user->profile->jurusan->prodis?->count() ?? 0 }} Prodi Terdaftar</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card 5: Timeline & Audit Trail --}}
            <div class="card ud-card">
                <div class="ud-card-header">
                    <div class="ud-card-title">
                        <i class="fas fa-clock-rotate-left ud-header-icon" style="color: #10b981;"></i>
                        <span>Riwayat Aktivitas &amp; Audit Trail</span>
                    </div>
                </div>

                <div class="ud-card-body">
                    <div class="ud-timeline">
                        <div class="ud-timeline-item">
                            <div class="ud-timeline-bullet created">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="ud-timeline-content">
                                <div class="ud-tl-top">
                                    <h4 class="ud-tl-title">Akun Didaftarkan ke Sistem</h4>
                                    <span class="ud-tl-time">{{ $user->created_at ? $user->created_at->format('d M Y - H:i:s') : '—' }}</span>
                                </div>
                                <p class="ud-tl-desc">
                                    Pengguna resmi ditambahkan ke basis data dengan ID pengguna <strong>#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</strong> dan role awal <strong>{{ $roleLabels[$user->role?->role_name] ?? $user->role?->role_name ?? 'Staf' }}</strong>.
                                </p>
                            </div>
                        </div>

                        <div class="ud-timeline-item">
                            <div class="ud-timeline-bullet updated">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="ud-timeline-content">
                                <div class="ud-tl-top">
                                    <h4 class="ud-tl-title">Pembaruan Terakhir</h4>
                                    <span class="ud-tl-time">{{ $user->updated_at ? $user->updated_at->format('d M Y - H:i:s') : '—' }}</span>
                                </div>
                                <p class="ud-tl-desc">
                                    Catatan profil atau data kredensial terakhir disinkronisasi ({{ $user->updated_at ? $user->updated_at->diffForHumans() : '—' }}).
                                </p>
                            </div>
                        </div>

                        <div class="ud-timeline-item">
                            <div class="ud-timeline-bullet verified">
                                <i class="fas fa-shield-check"></i>
                            </div>
                            <div class="ud-timeline-content">
                                <div class="ud-tl-top">
                                    <h4 class="ud-tl-title">Verifikasi Keamanan Email</h4>
                                    <span class="ud-tl-time">
                                        {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y - H:i:s') : 'Status: Belum Verifikasi' }}
                                    </span>
                                </div>
                                <p class="ud-tl-desc">
                                    @if($user->email_verified_at)
                                        Alamat email telah dinyatakan sah dan aktif menerima notifikasi kegiatan kerja sama kampus.
                                    @else
                                        Akun belum menyelesaikan proses verifikasi email resmi.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 6: Tindakan Cepat (Quick Action Bar) --}}
            <div class="card ud-card ud-action-card">
                <div class="ud-action-card-inner">
                    <div class="ud-action-info">
                        <span class="ud-action-badge"><i class="fas fa-bolt"></i> Shortcut Tindakan</span>
                        <h4 class="ud-action-title">Perlu Mengubah Data Pengguna Ini?</h4>
                        <p class="ud-action-desc">
                            Anda dapat memperbarui nama, NIK, jabatan, unit penempatan, atau mengatur ulang kata sandi melalui form edit.
                        </p>
                    </div>
                    <div class="ud-action-buttons">
                        <a href="mailto:{{ $user->email }}" class="ud-btn-contact" title="Kirim Pesan Email">
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim Email</span>
                        </a>
                        <a href="{{ route('users.edit', $user->id) }}" class="ud-btn-edit-primary">
                            <i class="fas fa-pen-to-square"></i>
                            <span>Edit Data Lengkap</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

{{-- ── Toast Notification for Copy Action ──────────────────────── --}}
<div id="udToast" class="ud-toast">
    <i class="fas fa-circle-check"></i>
    <span id="udToastMsg">Teks berhasil disalin ke clipboard</span>
</div>

@endsection

@section('styles')
<style>
/* ─── SCOPED DETAIL USER STYLES (WD4 THEME INTEGRATED) ─────────────────── */
.ud-detail-page {
    padding-bottom: 60px;
}

/* Breadcrumb & Topbar */
.ud-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.ud-topbar-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.ud-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    border-radius: 11px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-sub, #64748b);
    background: var(--bg-card, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    text-decoration: none;
    transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.ud-btn-back:hover {
    color: var(--text-main, #0f172a);
    background: var(--bg-body, #f8fafc);
    border-color: #cbd5e1;
    transform: translateX(-2px);
}
[data-theme="dark"] .ud-btn-back:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.15);
}
.ud-btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 11px;
    font-size: 13.5px;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    text-decoration: none;
    border: none;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
    transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
}
.ud-btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.38);
    color: #ffffff;
}

/* ─── Hero Spotlight Card ─────────────────────────────────────────────── */
.ud-hero-card {
    position: relative;
    background: var(--bg-card, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
}
.ud-hero-glow {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(59, 130, 246, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.ud-hero-pattern {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(148, 163, 184, 0.15) 1px, transparent 1px);
    background-size: 16px 16px;
    mask-image: linear-gradient(to right, transparent 60%, black 100%);
    -webkit-mask-image: linear-gradient(to right, transparent 60%, black 100%);
    pointer-events: none;
}
[data-theme="dark"] .ud-hero-card {
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.3);
}
.ud-hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 28px;
    flex-wrap: wrap;
}
.ud-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}
.ud-avatar {
    width: 88px;
    height: 88px;
    border-radius: 24px;
    color: #ffffff;
    font-size: 32px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    letter-spacing: -0.02em;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
}
.ud-status-pulse {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    background: #10b981;
    border: 3px solid var(--bg-card, #ffffff);
    border-radius: 50%;
}
.ud-user-meta {
    flex: 1;
    min-width: 280px;
}
.ud-user-top {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.ud-user-name {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--text-main, #0f172a);
    margin: 0;
    letter-spacing: -0.02em;
}
.ud-role-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 700;
    border: 1px solid transparent;
}
.ud-verified-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}
.ud-unverified-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
}
.ud-quick-badges {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.ud-quick-item {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    border-radius: 10px;
    background: var(--bg-body, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    font-size: 13px;
    color: var(--text-sub, #475569);
    cursor: pointer;
    transition: all .15s ease;
}
.ud-quick-item:hover {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #0f172a);
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
[data-theme="dark"] .ud-quick-item {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .ud-quick-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
}
.ud-copy-icon {
    font-size: 11px;
    opacity: 0.6;
    margin-left: 2px;
}
.ud-mono {
    font-family: 'DM Mono', monospace;
    font-weight: 500;
}

.ud-hero-stats {
    display: flex;
    align-items: center;
    gap: 20px;
    padding-left: 20px;
    border-left: 1px solid var(--border, #e2e8f0);
}
[data-theme="dark"] .ud-hero-stats {
    border-left-color: rgba(255, 255, 255, 0.1);
}
.ud-stat-box {
    display: flex;
    flex-direction: column;
}
.ud-stat-label {
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-sub, #64748b);
    margin-bottom: 3px;
}
.ud-stat-value {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--text-main, #0f172a);
}
.ud-stat-sub {
    font-size: 11.5px;
    color: var(--text-sub, #94a3b8);
}
.ud-stat-divider {
    width: 1px;
    height: 36px;
    background: var(--border, #e2e8f0);
}
[data-theme="dark"] .ud-stat-divider {
    background: rgba(255, 255, 255, 0.08);
}

/* ─── Layout Grid (380px / 1fr) ─────────────────────────────────────── */
.ud-grid-container {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 24px;
    align-items: start;
}
@media (max-width: 1080px) {
    .ud-grid-container {
        grid-template-columns: 1fr;
    }
    .ud-hero-stats {
        border-left: none;
        padding-left: 0;
        width: 100%;
        margin-top: 10px;
    }
}

/* ─── Card Design System ─────────────────────────────────────────────── */
.ud-card {
    background: var(--bg-card, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 18px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    transition: box-shadow .2s ease;
}
[data-theme="dark"] .ud-card {
    border-color: rgba(255, 255, 255, 0.07);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}
.ud-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border, #f1f5f9);
    background: var(--bg-card, #ffffff);
}
[data-theme="dark"] .ud-card-header {
    border-bottom-color: rgba(255, 255, 255, 0.06);
}
.ud-card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    color: var(--text-main, #0f172a);
}
.ud-header-icon {
    font-size: 16px;
}
.ud-header-badge {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 3px 8px;
    border-radius: 6px;
    background: var(--bg-body, #f1f5f9);
    color: var(--text-sub, #64748b);
}
[data-theme="dark"] .ud-header-badge {
    background: rgba(255, 255, 255, 0.08);
}
.ud-card-body {
    padding: 24px;
}

/* Info Item Rows */
.ud-info-item {
    margin-bottom: 18px;
}
.ud-info-item:last-child {
    margin-bottom: 0;
}
.ud-info-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-sub, #64748b);
    margin-bottom: 6px;
}
.ud-info-value-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: var(--bg-body, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 12px;
}
[data-theme="dark"] .ud-info-value-wrap {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.07);
}
.ud-info-val {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-main, #0f172a);
    word-break: break-all;
}
.ud-copy-btn {
    background: transparent;
    border: none;
    color: var(--text-sub, #94a3b8);
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    font-size: 13px;
    transition: all .15s ease;
}
.ud-copy-btn:hover {
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
}

/* Password Status Box */
.ud-pass-status-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: var(--bg-body, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 12px;
}
[data-theme="dark"] .ud-pass-status-box {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.07);
}
.ud-pass-dots {
    display: flex;
    gap: 4px;
}
.ud-pass-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #64748b;
}
.ud-pass-meta {
    font-size: 11.5px;
    font-weight: 600;
    color: #10b981;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Verification Status Tag */
.ud-status-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    width: 100%;
}
.ud-status-tag.verified {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.2);
}
[data-theme="dark"] .ud-status-tag.verified {
    color: #34d399;
}
.ud-status-tag.unverified {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

/* Role Authority Card */
.ud-role-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    border-radius: 14px;
    margin-bottom: 14px;
}
.ud-role-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--bg-card, #ffffff);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}
[data-theme="dark"] .ud-role-icon-box {
    background: rgba(0, 0, 0, 0.25);
}
.ud-role-banner-title {
    font-size: 15px;
    font-weight: 800;
}
.ud-role-tier {
    font-size: 12px;
    color: var(--text-sub, #64748b);
    font-weight: 500;
}
.ud-role-description {
    font-size: 13.5px;
    line-height: 1.6;
    color: var(--text-sub, #475569);
    margin: 0 0 16px 0;
}
.ud-role-meta-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-top: 1px dashed var(--border, #e2e8f0);
    padding-top: 14px;
}
[data-theme="dark"] .ud-role-meta-list {
    border-top-color: rgba(255, 255, 255, 0.1);
}
.ud-role-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-main, #334155);
}

/* Danger Card */
.ud-card-danger {
    border-color: rgba(239, 68, 68, 0.25);
}
.ud-danger-desc {
    font-size: 13px;
    color: var(--text-sub, #64748b);
    line-height: 1.5;
    margin: 0 0 16px 0;
}
.ud-btn-danger {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 11px;
    font-size: 13.5px;
    font-weight: 600;
    color: #ef4444;
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.2);
    cursor: pointer;
    transition: all .2s ease;
}
.ud-btn-danger:hover {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Structure Card & Hero */
.ud-unit-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    background: rgba(2, 132, 199, 0.1);
    color: #0284c7;
}
.ud-structure-hero {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 18px;
    background: var(--bg-body, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 16px;
    margin-bottom: 20px;
}
[data-theme="dark"] .ud-structure-hero {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.08);
}
.ud-structure-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.ud-structure-text {
    flex: 1;
}
.ud-structure-eyebrow {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-sub, #64748b);
    display: block;
    margin-bottom: 2px;
}
.ud-structure-name {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-main, #0f172a);
    margin: 0 0 3px 0;
}
.ud-structure-sub {
    font-size: 12.5px;
    color: var(--text-sub, #64748b);
}

/* Profile Detail Grid */
.ud-profile-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 14px;
}
.ud-profile-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: var(--bg-card, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 14px;
    transition: all .2s ease;
}
.ud-profile-box:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
[data-theme="dark"] .ud-profile-box {
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.06);
}
.ud-pbox-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--bg-body, #f1f5f9);
    color: var(--text-sub, #475569);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
[data-theme="dark"] .ud-pbox-icon {
    background: rgba(255, 255, 255, 0.06);
    color: #cbd5e1;
}
.ud-pbox-content {
    display: flex;
    flex-direction: column;
}
.ud-pbox-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-sub, #64748b);
    margin-bottom: 2px;
}
.ud-pbox-val {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-main, #0f172a);
}

/* ─── Timeline Audit Trail ───────────────────────────────────────────── */
.ud-timeline {
    position: relative;
    padding-left: 28px;
}
.ud-timeline::before {
    content: '';
    position: absolute;
    left: 11px;
    top: 6px;
    bottom: 6px;
    width: 2px;
    background: var(--border, #e2e8f0);
}
[data-theme="dark"] .ud-timeline::before {
    background: rgba(255, 255, 255, 0.1);
}
.ud-timeline-item {
    position: relative;
    margin-bottom: 22px;
}
.ud-timeline-item:last-child {
    margin-bottom: 0;
}
.ud-timeline-bullet {
    position: absolute;
    left: -28px;
    top: 2px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    background: var(--bg-card, #ffffff);
    border: 2px solid;
    z-index: 1;
}
.ud-timeline-bullet.created {
    color: #3b82f6;
    border-color: #3b82f6;
    background: #eff6ff;
}
[data-theme="dark"] .ud-timeline-bullet.created {
    background: #1e293b;
}
.ud-timeline-bullet.updated {
    color: #0284c7;
    border-color: #0284c7;
    background: #f0f9ff;
}
[data-theme="dark"] .ud-timeline-bullet.updated {
    background: #1e293b;
}
.ud-timeline-bullet.verified {
    color: #10b981;
    border-color: #10b981;
    background: #ecfdf5;
}
[data-theme="dark"] .ud-timeline-bullet.verified {
    background: #1e293b;
}
.ud-timeline-content {
    background: var(--bg-body, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 12px;
    padding: 12px 16px;
}
[data-theme="dark"] .ud-timeline-content {
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.06);
}
.ud-tl-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
.ud-tl-title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-main, #0f172a);
    margin: 0;
}
.ud-tl-time {
    font-size: 11.5px;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
    color: var(--text-sub, #64748b);
}
.ud-tl-desc {
    font-size: 13px;
    line-height: 1.5;
    color: var(--text-sub, #475569);
    margin: 0;
}

/* ─── Shortcut Action Banner Card ────────────────────────────────────── */
.ud-action-card {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.06) 0%, rgba(79, 70, 229, 0.04) 100%);
    border: 1px solid rgba(59, 130, 246, 0.2);
}
[data-theme="dark"] .ud-action-card {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, rgba(79, 70, 229, 0.08) 100%);
    border-color: rgba(59, 130, 246, 0.25);
}
.ud-action-card-inner {
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}
.ud-action-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #2563eb;
    margin-bottom: 6px;
}
[data-theme="dark"] .ud-action-badge {
    color: #60a5fa;
}
.ud-action-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--text-main, #0f172a);
    margin: 0 0 4px 0;
}
.ud-action-desc {
    font-size: 13px;
    color: var(--text-sub, #475569);
    margin: 0;
    max-width: 520px;
}
.ud-action-buttons {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.ud-btn-contact {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 11px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-main, #0f172a);
    background: var(--bg-card, #ffffff);
    border: 1px solid var(--border, #cbd5e1);
    text-decoration: none;
    transition: all .2s ease;
}
.ud-btn-contact:hover {
    background: var(--bg-body, #f8fafc);
    border-color: #94a3b8;
    transform: translateY(-2px);
}
[data-theme="dark"] .ud-btn-contact {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.12);
}
.ud-btn-edit-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 11px;
    font-size: 13.5px;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    text-decoration: none;
    border: none;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    transition: all .2s ease;
}
.ud-btn-edit-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
    color: #ffffff;
}

/* ─── Floating Toast Notification ───────────────────────────────────── */
.ud-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #0f172a;
    color: #f8fafc;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    transform: translateY(100px);
    opacity: 0;
    visibility: hidden;
    transition: all .3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    z-index: 99999;
}
.ud-toast.active {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
}
.ud-toast i {
    color: #10b981;
    font-size: 16px;
}
[data-theme="dark"] .ud-toast {
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>

<script>
function copyToClipboard(text, message) {
    if (!text || text === '-') return;
    navigator.clipboard.writeText(text).then(function() {
        showToast(message || 'Berhasil disalin ke clipboard');
    }).catch(function(err) {
        console.error('Gagal menyalin:', err);
    });
}

function showToast(message) {
    const toast = document.getElementById('udToast');
    const msgEl = document.getElementById('udToastMsg');
    if (!toast || !msgEl) return;
    
    msgEl.textContent = message;
    toast.classList.add('active');
    
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(function() {
        toast.classList.remove('active');
    }, 2800);
}
</script>
@endsection

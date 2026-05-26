@php
    $totalKegiatan = $mitra->cooperations->count();
    $aktifKegiatan = $mitra->cooperations->where('status', 'aktif')->count();
    $kategoriClass = $mitra->kategori == 'nasional' ? 'dk-status-active' : 'dk-status-warning';
    $kategoriIcon = $mitra->kategori == 'nasional' ? 'fa-building' : 'fa-earth-americas';
@endphp

<main id="mainContent" class="dk-page">
    {{-- ═══ HERO SECTION ═══ --}}
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('upa.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('upa.mitra') }}" style="text-decoration: none; color: inherit;">
                    <span>Daftar Mitra</span>
                </a>
                <span class="sep">/</span>
                <span class="current">Detail Mitra</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon">
                    <i class="fas fa-building-circle-check"></i>
                </div>
                <div style="flex: 1;">
                    <span class="dk-eyebrow">Profil Mitra Kerjasama</span>
                    <h2 id="pageTitle" style="margin-bottom: 12px;">{{ $mitra->nama_mitra }}</h2>
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <span class="dk-status {{ $kategoriClass }}">
                            <i class="fas {{ $kategoriIcon }}"></i>
                            {{ ucfirst($mitra->kategori) }}
                        </span>
                        @if($mitra->klasifikasi)
                            <span style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 8px; font-size: 12px; color: white; font-weight: 600;">
                                <i class="fas fa-tag" style="margin-right: 6px; opacity: 0.8;"></i>
                                {{ $mitra->klasifikasi->nama }}
                            </span>
                        @endif
                        <span style="color: rgba(255,255,255,0.7); font-size: 13px;">
                            <i class="fas fa-location-dot" style="margin-right: 6px;"></i>
                            {{ $mitra->negara ?: 'Indonesia' }}
                        </span>
                    </div>
                </div>
                <div class="dk-hero-action">
                    <a href="javascript:void(0)" onclick="openMitraEditModal('{{ $mitra->id }}', '{{ addslashes($mitra->nama_mitra) }}', '{{ $mitra->id_klasifikasi }}', '{{ $mitra->kategori }}', '{{ addslashes($mitra->negara) }}', '{{ addslashes($mitra->alamat) }}', '{{ addslashes($mitra->telp) }}', '{{ addslashes($mitra->website) }}')" class="dk-primary-btn" style="background: linear-gradient(135deg, #4f46e5, #6366f1); color: white; min-height: 40px; padding: 0 18px; border-radius: 10px; font-size: 13px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); border: none;">
                        <i class="fas fa-pen-to-square"></i>
                        <span>Edit Profil</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ STATS GRID ═══ --}}
    <section class="dk-stats-grid" style="margin-top: -35px; position: relative; z-index: 10; padding: 0 20px;">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-file-signature"></i></div>
            <div>
                <span class="dk-stat-label">Total Kerjasama</span>
                <div>{{ $totalKegiatan }} Dokumen</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Kerjasama Aktif</span>
                <div>{{ $aktifKegiatan }} Dokumen</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-clock-rotate-left"></i></div>
            <div>
                <span class="dk-stat-label">Terakhir Update</span>
                <div>{{ $mitra->updated_at ? $mitra->updated_at->diffForHumans() : '-' }}</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger">
            <div class="dk-stat-icon"><i class="fas fa-earth-asia"></i></div>
            <div>
                <span class="dk-stat-label">Negara Asal</span>
                <div>{{ $mitra->negara ?: 'Indonesia' }}</div>
            </div>
        </div>
    </section>

    <div style="display: grid; grid-template-columns: 380px 1fr; gap: 24px; max-width: 1400px; margin: 24px auto; padding: 0 20px 40px;">
        {{-- Left Column: Mitra Info --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card dk-card">
                <div class="card-header dk-card-header">
                    <div class="dk-card-title">
                        <span class="dk-title-icon"><i class="fas fa-id-card"></i></span>
                        <span><strong>Informasi Kontak</strong></span>
                    </div>
                </div>
                <div class="card-body dk-card-body" style="padding: 24px;">
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div class="dk-entity" style="padding: 12px; background: var(--surface2); border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dk-entity-icon dk-entity-indigo"><i class="fas fa-map-location-dot"></i></span>
                            <div class="dk-entity-text">
                                <small style="display: block; font-size: 10px; color: var(--text-sub); text-transform: uppercase; font-weight: 700;">Alamat Kantor</small>
                                <span style="font-size: 13px; font-weight: 600;">{{ $mitra->alamat ?: '-' }}</span>
                            </div>
                        </div>

                        <div class="dk-entity" style="padding: 12px; background: var(--surface2); border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dk-entity-icon dk-entity-cyan"><i class="fas fa-phone-volume"></i></span>
                            <div class="dk-entity-text">
                                <small style="display: block; font-size: 10px; color: var(--text-sub); text-transform: uppercase; font-weight: 700;">Telepon / Fax</small>
                                <span style="font-size: 14px; font-weight: 700;">{{ $mitra->telp ?: '-' }}</span>
                            </div>
                        </div>

                        <div class="dk-entity" style="padding: 12px; background: var(--surface2); border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dk-entity-icon dk-entity-violet"><i class="fas fa-globe"></i></span>
                            <div class="dk-entity-text">
                                <small style="display: block; font-size: 10px; color: var(--text-sub); text-transform: uppercase; font-weight: 700;">Website Resmi</small>
                                @if($mitra->website)
                                    <a href="{{ $mitra->website }}" target="_blank" style="font-size: 13px; font-weight: 700; color: var(--accent); text-decoration: none; word-break: break-all;">
                                        {{ str_replace(['http://', 'https://'], '', $mitra->website) }}
                                    </a>
                                @else
                                    <span style="font-size: 13px; font-weight: 600;">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="{{ route('upa.mitra') }}" class="dk-secondary-btn" style="width: 100%; justify-content: center; height: 48px; border-radius: 12px; background: var(--surface2); color: var(--text); border: 1px solid var(--border);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        {{-- Right Column: Activities List --}}
        <div class="card dk-card">
            <div class="card-header dk-card-header">
                <div class="dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-clock-rotate-left"></i></span>
                    <span>
                        <strong>Riwayat Kerjasama</strong>
                        <small>Daftar seluruh dokumen kerjasama dengan mitra ini</small>
                    </span>
                </div>
            </div>
            <div class="card-body dk-card-body" style="padding: 0;">
                <div class="table-wrap dk-table-wrap">
                    <table class="dk-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Informasi Dokumen</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mitra->cooperations as $index => $kegiatan)
                                @php
                                    $status = strtolower($kegiatan->status ?? '');
                                    $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
                                    $isExtended = str_contains($status, 'perpanjangan');
                                    
                                    $statusClass = match (true) {
                                        $status === 'aktif' => 'dk-status-active',
                                        $isExtended => 'dk-status-warning',
                                        $isExpired => 'dk-status-danger',
                                        $status === 'tidak aktif' => 'dk-status-muted',
                                        default => 'dk-status-neutral',
                                    };
                                    $statusIcon = match (true) {
                                        $status === 'aktif' => 'fa-circle-check',
                                        $isExtended => 'fa-clock',
                                        $isExpired => 'fa-circle-xmark',
                                        $status === 'tidak aktif' => 'fa-circle-minus',
                                        default => 'fa-circle-info',
                                    };
                                    $statusLabel = match (true) {
                                        $status === 'aktif' => 'Aktif',
                                        $isExtended => 'Perpanjangan',
                                        $isExpired => 'Kadarluarsa',
                                        $status === 'tidak aktif' => 'Tidak Aktif',
                                        $status !== '' => ucwords($status),
                                        default => 'Belum Diatur',
                                    };
                                @endphp
                                <tr>
                                    <td><span class="dk-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span></td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 4px;">{{ $kegiatan->title }}</div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="tag tag-gray" style="font-size: 10px;">{{ $kegiatan->jenis }}</span>
                                            <span style="font-family: 'DM Mono', monospace; font-size: 11px; color: var(--text-sub);">#{{ $kegiatan->doc_number ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 12px; color: var(--text); font-weight: 600;">
                                            {{ $kegiatan->start_date ? $kegiatan->start_date->format('d M Y') : '-' }}
                                        </div>
                                        <div style="font-size: 10px; color: var(--text-sub);">s/d {{ $kegiatan->end_date ? $kegiatan->end_date->format('d M Y') : 'Selesai' }}</div>
                                    </td>
                                    <td>
                                        <span class="dk-status {{ $statusClass }}" style="font-size: 11px; padding: 4px 10px;">
                                            <i class="fas {{ $statusIcon }}"></i>
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="um-actions dk-actions-compact">
                                            <a href="{{ route('upa.kerjasama.show', $kegiatan->id) }}" class="dk-action-btn view" title="Detail Dokumen">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 50px; color: var(--text-sub);">
                                        <i class="fas fa-inbox" style="font-size: 32px; opacity: 0.2; margin-bottom: 12px; display: block;"></i>
                                        <span style="font-weight: 500;">Belum ada riwayat kerjasama terdaftar.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@include('auth.layout.upa.mitra._modal_edit')

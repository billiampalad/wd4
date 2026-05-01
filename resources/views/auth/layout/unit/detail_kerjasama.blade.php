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

    $pelaksanaIcon = 'fa-building';
    $pelaksanaClass = 'dk-entity-indigo';
    $pelaksanaName = '-';
    $pelaksanaType = '';
    if ($kegiatan->tipe_pelaksana === 'jurusan') {
        $pelaksanaIcon = 'fa-microchip';
        $pelaksanaClass = 'dk-entity-indigo';
        $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
        $pelaksanaType = 'Jurusan';
    } elseif ($kegiatan->tipe_pelaksana === 'upa') {
        $pelaksanaIcon = 'fa-building-columns';
        $pelaksanaClass = 'dk-entity-cyan';
        $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
        $pelaksanaType = 'UPA';
    } elseif ($kegiatan->tipe_pelaksana === 'pusat') {
        $pelaksanaIcon = 'fa-landmark';
        $pelaksanaClass = 'dk-entity-violet';
        $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
        $pelaksanaType = 'Pusat';
    }

    $totalNilai = $kegiatan->details->sum('nilai_kontrak');
    
    $timeRemainingLabel = '-';
    $timeRemainingColor = 'var(--text)';
    if ($kegiatan->end_date) {
        $now = now();
        $end = \Carbon\Carbon::parse($kegiatan->end_date);
        $diff = $now->diff($end);
        $isPast = $now->greaterThan($end);
        
        if ($isPast) {
            $timeRemainingLabel = 'Kadarluarsa';
            $timeRemainingColor = '#ef4444';
        } else {
            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;
            
            $parts = [];
            if ($years > 0) $parts[] = $years . ' Thn';
            if ($months > 0) $parts[] = $months . ' Bln';
            if ($days > 0 || empty($parts)) $parts[] = $days . ' Hari';
            
            $timeRemainingLabel = implode(', ', array_slice($parts, 0, 2));
            
            // Color logic
            $totalDays = $now->diffInDays($end);
            if ($totalDays < 30) {
                $timeRemainingColor = '#ef4444'; // Red for < 1 month
            } elseif ($totalDays < 90) {
                $timeRemainingColor = '#f59e0b'; // Orange for < 3 months
            } else {
                $timeRemainingColor = '#10b981'; // Green for safe
            }
        }
    }
@endphp

<main id="mainContent" class="dk-page">
    {{-- ═══ HERO SECTION ═══ --}}
    <section class="dk-hero" style="padding: 32px; min-height: 220px;">
        <div class="dk-hero-content" style="width: 100%;">
            <div class="breadcrumb dk-breadcrumb" style="margin-bottom: 20px;">
                <a href="{{ route('unit.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px; opacity: 0.8; transition: 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep" style="opacity: 0.5;">/</span>
                <a href="{{ route('unit.dkerjasama') }}" style="text-decoration: none; color: inherit; opacity: 0.8; transition: 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    <span>Data Kerjasama</span>
                </a>
                <span class="sep" style="opacity: 0.5;">/</span>
                <span class="current" style="font-weight: 700;">Detail Dokumen</span>
            </div>

            <div class="dk-hero-main" style="align-items: flex-start; gap: 24px;">
                <div class="dk-hero-icon" style="width: 64px; height: 64px; font-size: 26px; flex-shrink: 0;">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <span class="dk-eyebrow" style="margin-bottom: 8px;">Repositori Unit Pelaksana</span>
                    <h2 id="pageTitle" style="margin-bottom: 14px; font-size: 20px; letter-spacing: -0.01em; line-height: 1.2;">{{ $kegiatan->title }}</h2>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <span class="dk-status {{ $statusClass }}" style="padding: 6px 14px; font-size: 13px; font-weight: 700;">
                            <i class="fas {{ $statusIcon }}" style="margin-right: 8px;"></i>
                            {{ $statusLabel }}
                        </span>
                        <div class="dk-hero-date-box" style="display: flex; align-items: center; background: var(--surface2); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); opacity: 0.95;">
                            <div style="padding: 6px 14px; border-right: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-calendar-check" style="color: #10b981; font-size: 12px;"></i>
                                <span style="color: var(--text); font-size: 12px; font-weight: 700;">{{ $kegiatan->start_date?->format('d M Y') ?? '-' }}</span>
                            </div>
                            <div style="padding: 6px 14px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-calendar-xmark" style="color: #ef4444; font-size: 12px;"></i>
                                <span style="color: var(--text); font-size: 12px; font-weight: 700;">{{ $kegiatan->end_date?->format('d M Y') ?? 'Selesai' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dk-hero-action" style="align-self: center;">
                    <a href="{{ route('unit.kerjasama.edit', $kegiatan->id) }}" class="dk-primary-btn" style="background: linear-gradient(135deg, #4f46e5, #6366f1); color: white; min-height: 40px; padding: 0 18px; border-radius: 10px; font-size: 13px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); border: none;">
                        <i class="fas fa-pen-to-square" style="font-size: 12px;"></i>
                        <span>Edit Data</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ STATS GRID ═══ --}}
    <section class="dk-stats-grid" style="margin-top: -35px; position: relative; z-index: 10; padding: 0 24px; gap: 14px;">
        <div class="dk-stat-card dk-stat-total" style="min-height: 80px; padding: 14px 18px;">
            <div class="dk-stat-icon" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <span class="dk-stat-label" style="font-size: 11px; margin-bottom: 2px;">Nilai Kontrak</span>
                <strong style="font-size: 15px;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active" style="min-height: 80px; padding: 14px 18px;">
            <div class="dk-stat-icon" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-handshake"></i></div>
            <div>
                <span class="dk-stat-label" style="font-size: 11px; margin-bottom: 2px;">Ruang Lingkup</span>
                <strong style="font-size: 15px;">{{ $kegiatan->details->count() }} Kegiatan</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning" style="min-height: 80px; padding: 14px 18px;">
            <div class="dk-stat-icon" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <span class="dk-stat-label" style="font-size: 11px; margin-bottom: 2px;">Sisa Waktu</span>
                <strong style="font-size: 15px; color: {{ $timeRemainingColor }};">
                    {{ $timeRemainingLabel }}
                </strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger" style="min-height: 80px; padding: 14px 18px;">
            <div class="dk-stat-icon" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-building-user"></i></div>
            <div>
                <span class="dk-stat-label" style="font-size: 11px; margin-bottom: 2px;">Tipe Pelaksana</span>
                <strong style="font-size: 15px;">{{ $pelaksanaType ?: '-' }}</strong>
            </div>
        </div>
    </section>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 350px; gap: 28px; max-width: 1400px; margin: 32px auto; padding: 0 24px 60px;">
        
        {{-- Left Column --}}
        <div style="display: flex; flex-direction: column; gap: 28px; min-width: 0;">
            
            {{-- Card: Ringkasan --}}
            <div class="card dk-card">
                <div class="card-header dk-card-header">
                    <div class="dk-card-title">
                        <span class="dk-title-icon"><i class="fas fa-circle-info"></i></span>
                        <span>
                            <strong>Ringkasan Dokumen</strong>
                            <small>Informasi mendasar naskah kerjasama</small>
                        </span>
                    </div>
                </div>
                <div class="card-body dk-card-body" style="padding: 28px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 28px;">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Nomor Dokumen Utama</label>
                            <div style="font-family: 'DM Mono', monospace; font-size: 14px; color: var(--text); padding: 10px 14px; background: var(--surface2); border-radius: 10px; border: 1px solid var(--border); word-break: break-all;">
                                {{ $kegiatan->doc_number ?: '-' }}
                            </div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Nomor PKS</label>
                            <div style="font-family: 'DM Mono', monospace; font-size: 14px; color: var(--text); padding: 10px 14px; background: var(--surface2); border-radius: 10px; border: 1px solid var(--border); word-break: break-all;">
                                {{ $kegiatan->pks_number ?: '-' }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Deskripsi Kegiatan</label>
                        <div style="font-size: 15px; color: var(--text); line-height: 1.8; text-align: justify; white-space: pre-line;">
                            {{ $kegiatan->description ?: 'Tidak ada deskripsi tambahan.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Pihak Terlibat --}}
            <div class="card dk-card">
                <div class="card-header dk-card-header">
                    <div class="dk-card-title">
                        <span class="dk-title-icon"><i class="fas fa-users-rectangle"></i></span>
                        <span>
                            <strong>Pihak Terlibat</strong>
                            <small>Pejabat penandatangan & penanggung jawab</small>
                        </span>
                    </div>
                </div>
                <div class="card-body dk-card-body" style="padding: 28px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
                        {{-- Pihak 1 --}}
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                    <i class="fas fa-university"></i>
                                </div>
                                <span style="font-weight: 800; font-size: 15px; color: var(--text);">Politeknik Negeri Manado</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                <div class="dk-entity" style="padding: 14px; background: var(--surface2); border-radius: 14px; border: 1px solid var(--border);">
                                    <span class="dk-entity-icon dk-entity-indigo" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-pen-nib"></i></span>
                                    <div class="dk-entity-text">
                                        <small style="display: block; font-size: 10px; color: #4f46e5; text-transform: uppercase; font-weight: 800; margin-bottom: 4px;">Penandatangan</small>
                                        <strong style="display: block; font-size: 14px; margin-bottom: 2px;">{{ $kegiatan->penandatanganInternal?->nama ?: '-' }}</strong>
                                        <span style="font-size: 12px; color: var(--text-sub); line-height: 1.4;">{{ $kegiatan->penandatanganInternal?->jabatan ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="dk-entity" style="padding: 14px; background: var(--surface2); border-radius: 14px; border: 1px solid var(--border);">
                                    <span class="dk-entity-icon dk-entity-indigo" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-user-tie"></i></span>
                                    <div class="dk-entity-text">
                                        <small style="display: block; font-size: 10px; color: #4f46e5; text-transform: uppercase; font-weight: 800; margin-bottom: 4px;">Penanggung Jawab</small>
                                        <strong style="display: block; font-size: 14px; margin-bottom: 2px;">{{ $kegiatan->pjInternal?->nama ?: '-' }}</strong>
                                        <span style="font-size: 12px; color: var(--text-sub); line-height: 1.4;">{{ $kegiatan->pjInternal?->jabatan ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Pihak 2 --}}
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                    <i class="fas fa-building"></i>
                                </div>
                                <span style="font-weight: 800; font-size: 15px; color: var(--text);">{{ $kegiatan->mitra?->nama_mitra ?: 'Pihak Mitra' }}</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                <div class="dk-entity" style="padding: 14px; background: var(--surface2); border-radius: 14px; border: 1px solid var(--border);">
                                    <span class="dk-entity-icon dk-entity-emerald" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-pen-nib"></i></span>
                                    <div class="dk-entity-text">
                                        <small style="display: block; font-size: 10px; color: #059669; text-transform: uppercase; font-weight: 800; margin-bottom: 4px;">Penandatangan</small>
                                        <strong style="display: block; font-size: 14px; margin-bottom: 2px;">{{ $kegiatan->penandatanganMitra?->nama ?: '-' }}</strong>
                                        <span style="font-size: 12px; color: var(--text-sub); line-height: 1.4;">{{ $kegiatan->penandatanganMitra?->jabatan ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="dk-entity" style="padding: 14px; background: var(--surface2); border-radius: 14px; border: 1px solid var(--border);">
                                    <span class="dk-entity-icon dk-entity-emerald" style="width: 38px; height: 38px; font-size: 14px;"><i class="fas fa-user-tie"></i></span>
                                    <div class="dk-entity-text">
                                        <small style="display: block; font-size: 10px; color: #059669; text-transform: uppercase; font-weight: 800; margin-bottom: 4px;">Penanggung Jawab</small>
                                        <strong style="display: block; font-size: 14px; margin-bottom: 2px;">{{ $kegiatan->pjMitra?->nama ?: '-' }}</strong>
                                        <span style="font-size: 12px; color: var(--text-sub); line-height: 1.4;">{{ $kegiatan->pjMitra?->jabatan ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Ruang Lingkup --}}
            <div class="card dk-card">
                <div class="card-header dk-card-header">
                    <div class="dk-card-title">
                        <span class="dk-title-icon"><i class="fas fa-list-check"></i></span>
                        <span>
                            <strong>Ruang Lingkup Kegiatan</strong>
                            <small>Detail implementasi kerjasama</small>
                        </span>
                    </div>
                </div>
                <div class="card-body dk-card-body" style="padding: 0;">
                    <div class="table-wrap dk-table-wrap" style="overflow-x: auto;">
                        <table class="dk-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Bentuk Kegiatan</th>
                                    <th>Sasaran</th>
                                    <th style="text-align: right;">Nilai Kontrak</th>
                                    <th>Luaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kegiatan->details as $idx => $item)
                                    <tr>
                                        <td><span class="dk-num">{{ $idx + 1 }}</span></td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text); font-size: 14px;">{{ $item->jenisKerjasama?->nama_kerjasama ?? '-' }}</div>
                                            @if($item->keterangan)
                                                <div style="font-size: 11px; color: var(--text-sub); margin-top: 5px; line-height: 1.4;">{{ $item->keterangan }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="font-size: 13px; color: var(--text);">{{ $item->sasaran?->deskripsi ?? '-' }}</span>
                                        </td>
                                        <td style="text-align: right;">
                                            @if($item->nilai_kontrak > 0)
                                                <div style="font-weight: 800; color: #059669; font-size: 14px;">Rp {{ number_format($item->nilai_kontrak, 0, ',', '.') }}</div>
                                                <span class="tag {{ $item->income === 'ya' ? 'tag-blue' : 'tag-gray' }}" style="font-size: 10px; margin-top: 6px; padding: 2px 8px;">{{ $item->income === 'ya' ? 'Income' : 'Non-Income' }}</span>
                                            @else
                                                <span style="color: var(--text-sub); font-size: 13px; font-weight: 600;">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->volume_luaran)
                                                <div style="font-weight: 700; font-size: 13px; color: var(--text);">{{ $item->volume_luaran }} <span style="font-weight: 500; color: var(--text-sub);">{{ $item->satuan_luaran }}</span></div>
                                            @else
                                                <span style="color: var(--text-sub);">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 50px; color: var(--text-sub);">
                                            <i class="fas fa-inbox" style="font-size: 32px; opacity: 0.2; margin-bottom: 12px; display: block;"></i>
                                            <span style="font-weight: 500;">Belum ada detail kegiatan terdaftar.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div style="display: flex; flex-direction: column; gap: 28px;">
            
            {{-- Card: Pelaksana --}}
            <div class="card dk-card">
                <div class="card-header dk-card-header">
                    <div class="dk-card-title">
                        <span class="dk-title-icon"><i class="fas fa-users-gear"></i></span>
                        <span><strong>Unit Pelaksana</strong></span>
                    </div>
                </div>
                <div class="card-body dk-card-body" style="padding: 24px;">
                    <div class="dk-entity" style="margin-bottom: 20px;">
                        <span class="dk-entity-icon {{ $pelaksanaClass }}" style="width: 44px; height: 44px; font-size: 16px;">
                            <i class="fas {{ $pelaksanaIcon }}"></i>
                        </span>
                        <div class="dk-entity-text">
                            <small style="display: block; font-size: 10px; color: var(--text-sub); font-weight: 800; text-transform: uppercase; margin-bottom: 3px;">{{ $pelaksanaType ?: 'Unit' }}</small>
                            <strong style="display: block; font-size: 14px; line-height: 1.3;">{{ $pelaksanaName }}</strong>
                        </div>
                    </div>

                    @if($kegiatan->prodis->count() > 0)
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px dashed var(--border);">
                            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.02em;">Program Studi Terkait</label>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach($kegiatan->prodis as $prodi)
                                    <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text); padding: 10px 14px; background: var(--surface2); border-radius: 10px; border-left: 4px solid #059669; font-weight: 600;">
                                        <i class="fas fa-graduation-cap" style="font-size: 12px; color: #059669; opacity: 0.8;"></i>
                                        <span>{{ $prodi->nama_prodi }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card: Profil Mitra --}}
            <div class="card dk-card">
                <div class="card-header dk-card-header">
                    <div class="dk-card-title">
                        <span class="dk-title-icon"><i class="fas fa-building-circle-check"></i></span>
                        <span><strong>Profil Mitra</strong></span>
                    </div>
                </div>
                <div class="card-body dk-card-body" style="padding: 24px;">
                    @if($kegiatan->mitra)
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <div style="text-align: center; padding: 12px 0;">
                                <div style="width: 68px; height: 68px; border-radius: 18px; background: var(--surface2); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px; border: 1px solid var(--border);">
                                    <i class="fas fa-building"></i>
                                </div>
                                <strong style="display: block; font-size: 15px; color: var(--text); margin-bottom: 8px; line-height: 1.4;">{{ $kegiatan->mitra->nama_mitra }}</strong>
                                <span class="tag tag-blue" style="font-size: 11px; padding: 4px 12px;">{{ ucfirst($kegiatan->mitra->kategori ?? 'Nasional') }}</span>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px;">
                                <div>
                                    <small style="display: block; font-size: 10px; color: var(--text-sub); font-weight: 800; text-transform: uppercase; margin-bottom: 6px;">Alamat</small>
                                    <div style="font-size: 13px; color: var(--text); line-height: 1.6; display: flex; gap: 10px;">
                                        <i class="fas fa-map-marker-alt" style="margin-top: 4px; font-size: 12px; color: #ef4444; flex-shrink: 0;"></i>
                                        <span>{{ $kegiatan->mitra->alamat ?: '-' }}</span>
                                    </div>
                                </div>
                                @if($kegiatan->mitra->website)
                                    <div>
                                        <small style="display: block; font-size: 10px; color: var(--text-sub); font-weight: 800; text-transform: uppercase; margin-bottom: 6px;">Website Resmi</small>
                                        <a href="{{ $kegiatan->mitra->website }}" target="_blank" style="font-size: 13px; color: var(--accent); text-decoration: none; display: flex; align-items: center; gap: 10px; font-weight: 600;">
                                            <i class="fas fa-globe" style="font-size: 12px;"></i>
                                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ str_replace(['http://', 'https://'], '', $kegiatan->mitra->website) }}</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; color: var(--text-sub); font-size: 13px; padding: 30px;">
                            <i class="fas fa-building-slash" style="font-size: 24px; opacity: 0.2; margin-bottom: 10px; display: block;"></i>
                            Data mitra tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @if($kegiatan->document_link)
                    <a href="{{ $kegiatan->document_link }}" target="_blank" class="dk-primary-btn" style="width: 100%; min-height: 44px; border-radius: 12px; font-size: 13px; gap: 10px; padding: 0 16px;">
                        <i class="fas fa-file-pdf" style="font-size: 14px;"></i>
                        <span>Lihat Dokumen Asli</span>
                    </a>
                @endif
                <a href="{{ route('unit.dkerjasama') }}" class="dk-secondary-btn" style="width: 100%; min-height: 44px; border-radius: 12px; font-size: 13px; gap: 10px; background: var(--surface2); color: var(--text); border: 1px solid var(--border); padding: 0 16px;">
                    <i class="fas fa-arrow-left" style="font-size: 12px;"></i>
                    <span>Kembali ke Repositori</span>
                </a>
            </div>
        </div>
    </div>
</main>

@php
    $status = strtolower($kegiatan->status ?? '');
    $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
    $isExtended = str_contains($status, 'perpanjangan');

    $statusColor = match (true) {
        $status === 'aktif' => '#10b981',
        $isExtended => '#f59e0b',
        $isExpired => '#ef4444',
        $status === 'tidak aktif' => '#6b7280',
        default => '#3b82f6',
    };
    $statusBg = match (true) {
        $status === 'aktif' => 'rgba(16,185,129,0.1)',
        $isExtended => 'rgba(245,158,11,0.1)',
        $isExpired => 'rgba(239,68,68,0.1)',
        $status === 'tidak aktif' => 'rgba(107,114,128,0.1)',
        default => 'rgba(59,130,246,0.1)',
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
        $isExpired => 'Kadaluarsa',
        $status === 'tidak aktif' => 'Tidak Aktif',
        $status !== '' => ucwords($status),
        default => 'Belum Diatur',
    };

    $pelaksanaName = '-';
    $pelaksanaType = '-';
    $pelaksanaIcon = 'fa-building';

    if ($kegiatan->tipe_pelaksana === 'jurusan') {
        $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
        $pelaksanaType = 'Jurusan';
        $pelaksanaIcon = 'fa-microchip';
    } elseif ($kegiatan->tipe_pelaksana === 'upa') {
        $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
        $pelaksanaType = 'UPA';
        $pelaksanaIcon = 'fa-building-columns';
    } elseif ($kegiatan->tipe_pelaksana === 'pusat') {
        $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
        $pelaksanaType = 'Pusat';
        $pelaksanaIcon = 'fa-landmark';
    }

    $totalNilai = $kegiatan->details->sum('nilai_kontrak');

    $timeRemainingLabel = '-';
    $timeRemainingColor = 'var(--text)';
    $timeRemainingBg = 'var(--surface2)';
    $timeRemainingIcon = 'fa-hourglass-half';

    if ($kegiatan->end_date) {
        $now = now();
        $end = \Carbon\Carbon::parse($kegiatan->end_date);
        $diff = $now->diff($end);
        $isPast = $now->greaterThan($end);

        if ($isPast) {
            $timeRemainingLabel = 'Telah Kadaluarsa';
            $timeRemainingColor = '#ef4444';
            $timeRemainingBg = 'rgba(239,68,68,0.1)';
            $timeRemainingIcon = 'fa-calendar-times';
        } else {
            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;

            $parts = [];
            if ($years > 0)
                $parts[] = $years . ' Thn';
            if ($months > 0)
                $parts[] = $months . ' Bln';
            if ($days > 0 || empty($parts))
                $parts[] = $days . ' Hari';

            $timeRemainingLabel = implode(', ', array_slice($parts, 0, 2)) . ' Lagi';

            $totalDays = $now->diffInDays($end);
            if ($totalDays < 30) {
                $timeRemainingColor = '#ef4444';
                $timeRemainingBg = 'rgba(239,68,68,0.1)';
                $timeRemainingIcon = 'fa-fire';
            } elseif ($totalDays < 90) {
                $timeRemainingColor = '#f59e0b';
                $timeRemainingBg = 'rgba(245,158,11,0.1)';
                $timeRemainingIcon = 'fa-clock';
            } else {
                $timeRemainingColor = '#10b981';
                $timeRemainingBg = 'rgba(16,185,129,0.1)';
                $timeRemainingIcon = 'fa-calendar-check';
            }
        }
    }
@endphp

<main id="mainContent" class="dk-page">

    <style>
        .dm-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .dm-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .dm-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dm-card-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text);
            margin: 0;
        }

        .dm-card-body {
            padding: 24px;
        }

        .dm-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-sub);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }

        .dm-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.5;
        }

        .dm-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .dm-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .dm-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .dm-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .dm-stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s;
        }

        .dm-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.04);
        }

        .dm-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            background: var(--surface2);
            color: var(--text-sub);
            border: 2px solid var(--border);
        }

        .dm-person-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
        }

        .dm-doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .dm-doc-item:hover {
            background: var(--hover);
        }

        .dm-detail-row {
            display: flex;
            padding: 16px 0;
            border-bottom: 1px dashed var(--border);
        }

        .dm-detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .dm-main-layout,
        .dm-main-column,
        .dm-sidebar-column {
            min-width: 0;
        }

        .dm-doc-content {
            min-width: 0;
        }

        .dm-activity-content,
        .dm-activity-main,
        .dm-activity-meta-item {
            min-width: 0;
        }

        .dm-activity-target,
        .dm-activity-title,
        .dm-activity-meta-item .dm-value {
            overflow-wrap: anywhere;
            word-break: normal;
        }

        @media only screen and (max-width: 767px) {
            .dk-page {
                padding-inline: 14px;
            }

            .dm-main-layout {
                grid-template-columns: 1fr !important;
                gap: 18px !important;
            }

            .dm-main-column,
            .dm-sidebar-column {
                width: 100%;
                gap: 18px !important;
            }

            .dm-card {
                border-radius: 14px;
                margin-bottom: 18px;
            }

            .dm-card-header {
                padding: 15px 16px;
                gap: 10px;
            }

            .dm-card-title {
                font-size: 14px;
                line-height: 1.35;
            }

            .dm-card-body {
                padding: 16px;
            }

            .dm-grid-2,
            .dm-grid-3,
            .dm-score-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            .dm-score-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .dm-person-card,
            .dm-doc-item,
            .dm-stat-card {
                align-items: flex-start;
                gap: 12px;
                padding: 14px;
            }

            .dm-doc-item {
                width: 100%;
            }

            .dm-doc-content {
                flex: 1;
                min-width: 0;
            }

            .dm-doc-title {
                max-width: min(58vw, 260px) !important;
            }

            .dm-detail-row {
                padding: 18px 0 !important;
            }

            .dm-activity-row {
                position: relative;
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 18px 16px !important;
            }

            .dm-activity-num {
                width: 30px !important;
                height: 30px !important;
                margin-right: 0 !important;
                font-size: 11px !important;
            }

            .dm-activity-content {
                width: 100%;
            }

            .dm-activity-head {
                flex-direction: column;
                gap: 12px;
                margin-bottom: 12px !important;
            }

            .dm-activity-main {
                width: 100%;
            }

            .dm-activity-title {
                font-size: 14px !important;
                line-height: 1.45;
            }

            .dm-activity-target {
                display: block;
                font-size: 12px !important;
                line-height: 1.55;
            }

            .dm-activity-value {
                width: 100%;
                text-align: left !important;
                padding: 12px;
                border: 1px solid rgba(16, 185, 129, .16);
                border-radius: 12px;
                background: rgba(16, 185, 129, .07);
            }

            .dm-activity-meta-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            .dm-activity-meta-item {
                padding-bottom: 12px;
                border-bottom: 1px dashed var(--border);
            }

            .dm-activity-meta-item:last-child {
                padding-bottom: 0;
                border-bottom: 0;
            }
        }

        @media only screen and (min-width: 768px) and (max-width: 1023px) {
            .dm-main-layout {
                grid-template-columns: 1fr !important;
                gap: 22px !important;
            }

            .dm-sidebar-column {
                width: 100%;
            }

            .dm-score-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }

            .dm-doc-title {
                max-width: 520px !important;
            }

            .dm-activity-row {
                padding: 20px !important;
            }

            .dm-activity-head {
                gap: 16px;
            }

            .dm-activity-meta-grid {
                gap: 16px !important;
            }
        }
    </style>

    {{-- Hero Section --}}
    <div
        style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 20px; padding: 32px; margin-bottom: 24px; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); color: white; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
        <div
            style="position: absolute; right: -50px; top: -50px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(56,189,248,0.15) 0%, transparent 70%); border-radius: 50%;">
        </div>
        <div
            style="position: absolute; left: 20%; bottom: -100px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(167,139,250,0.1) 0%, transparent 70%); border-radius: 50%;">
        </div>

        <div style="position: relative; z-index: 1;">
            <div
                style="font-size: 13px; color: #94a3b8; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('pimpinan.dashboard') }}"
                    style="color: #94a3b8; text-decoration: none; transition: color 0.2s;"
                    onmouseover="this.style.color='white'" onmouseout="this.style.color='#94a3b8'"><i
                        class="fas fa-home"></i></a>
                <span>/</span>
                <a href="{{ route('pimpinan.monitoring') }}"
                    style="color: #94a3b8; text-decoration: none; transition: color 0.2s;"
                    onmouseover="this.style.color='white'" onmouseout="this.style.color='#94a3b8'">Monitoring Data</a>
                <span>/</span>
                <span style="color: white; font-weight: 600;">Detail Kerjasama</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div
                    style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 20px;">
                    <div style="flex: 1; min-width: 300px;">
                        <div
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(255,255,255,0.1); border-radius: 20px; font-size: 12px; font-weight: 700; margin-bottom: 16px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(4px);">
                            <i class="fas {{ $statusIcon }}" style="color: {{ $statusColor }}"></i>
                            <span style="color: white;">{{ $statusLabel }}</span>
                        </div>
                        <h1
                            style="font-size: 32px; font-weight: 800; margin: 0 0 16px 0; line-height: 1.3; letter-spacing: -0.5px;">
                            {{ $kegiatan->title }}</h1>

                        <div
                            style="display: flex; flex-wrap: wrap; gap: 20px; font-size: 14px; color: #cbd5e1; font-weight: 500;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-building" style="color: #94a3b8;"></i>
                                {{ $kegiatan->mitra?->nama_mitra ?? '-' }}
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-tag" style="color: #94a3b8;"></i>
                                {{ $kegiatan->jenis ?? '-' }}
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-calendar" style="color: #94a3b8;"></i>
                                {{ $kegiatan->start_date?->format('d M Y') ?? '-' }} &mdash;
                                {{ $kegiatan->end_date?->format('d M Y') ?? 'Selesai' }}
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <a href="{{ route('pimpinan.monitoring') }}"
                            style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: rgba(255,255,255,0.1); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.15)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('pimpinan.evaluasi') }}"
                            style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: #3b82f6; color: white; border-radius: 12px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 12px rgba(59,130,246,0.3); transition: all 0.2s;"
                            onmouseover="this.style.background='#2563eb'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.background='#3b82f6'; this.style.transform='translateY(0)'">
                            <i class="fas fa-clipboard-check"></i> Cek Evaluasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: rgba(16,185,129,0.1); color: #10b981;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Total Nilai
                    Kontrak</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--text);">Rp
                    {{ number_format($totalNilai, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Ruang
                    Lingkup</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--text);">{{ $kegiatan->details->count() }}
                    Kegiatan</div>
            </div>
        </div>

        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: {{ $timeRemainingBg }}; color: {{ $timeRemainingColor }};">
                <i class="fas {{ $timeRemainingIcon }}"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Sisa Waktu
                </div>
                <div style="font-size: 16px; font-weight: 800; color: {{ $timeRemainingColor }};">
                    {{ $timeRemainingLabel }}</div>
            </div>
        </div>

        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                <i class="fas {{ $pelaksanaIcon }}"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Pelaksana
                    ({{ $pelaksanaType }})</div>
                <div style="font-size: 16px; font-weight: 800; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;"
                    title="{{ $pelaksanaName }}">{{ $pelaksanaName }}</div>
            </div>
        </div>
    </div>

    <div class="dm-main-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 28px; align-items: start;">

        {{-- Main Column --}}
        <div class="dm-main-column" style="display: flex; flex-direction: column; gap: 28px;">

            {{-- Ringkasan Dokumen --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-icon-box"
                        style="width:32px; height:32px; background:rgba(99,102,241,0.1); color:#6366f1; font-size:14px;">
                        <i class="fas fa-file-lines"></i></div>
                    <h3 class="dm-card-title">Informasi Dokumen</h3>
                </div>
                <div class="dm-card-body">
                    <div class="dm-grid-2" style="margin-bottom: 24px;">
                        <div
                            style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dm-label">Nomor Dokumen Mitra</span>
                            <span class="dm-value"
                                style="font-family: 'DM Mono', monospace;">{{ $kegiatan->doc_number ?: 'Tidak ada nomor' }}</span>
                        </div>
                        <div
                            style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dm-label">Nomor Surat Polimdo (PKS)</span>
                            <span class="dm-value"
                                style="font-family: 'DM Mono', monospace;">
                                @forelse($kegiatan->pksNumbers as $pksNumber)
                                    <span style="display: block;">{{ $pksNumber->number }}</span>
                                @empty
                                    Tidak ada nomor
                                @endforelse
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="dm-label">Deskripsi / Ruang Lingkup Umum</span>
                        <div
                            style="font-size: 14px; color: var(--text); line-height: 1.7; text-align: justify; padding: 20px; background: var(--bg); border-radius: 12px; border: 1px solid var(--border);">
                            {{ $kegiatan->description ?: 'Tidak ada deskripsi yang dicantumkan.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Implementasi --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-icon-box"
                        style="width:32px; height:32px; background:rgba(16,185,129,0.1); color:#10b981; font-size:14px;">
                        <i class="fas fa-list-check"></i></div>
                    <h3 class="dm-card-title">Detail Kegiatan & Sasaran</h3>
                </div>
                <div class="dm-card-body" style="padding: 0;">
                    @forelse($kegiatan->details as $idx => $det)
                        <div class="dm-detail-row dm-activity-row" style="padding: 24px;">
                            <div class="dm-activity-num"
                                style="width: 32px; height: 32px; border-radius: 50%; background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; color: var(--text-sub); margin-right: 16px;">
                                {{ $idx + 1 }}</div>
                            <div class="dm-activity-content" style="flex: 1;">
                                <div class="dm-activity-head"
                                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                    <div class="dm-activity-main">
                                        <div class="dm-activity-title"
                                            style="font-weight: 700; font-size: 15px; color: var(--text); margin-bottom: 4px;">
                                            {{ $det->jenisKerjasama->nama_kerjasama ?? 'Bentuk Kegiatan Tidak Spesifik' }}
                                        </div>
                                        <div class="dm-activity-target" style="font-size: 12px; color: var(--text-sub);"><i class="fas fa-bullseye"
                                                style="color:#8b5cf6; margin-right:4px;"></i> Sasaran:
                                            {{ $det->sasaran->deskripsi ?? '-' }}</div>
                                    </div>
                                    <div class="dm-activity-value" style="text-align: right;">
                                        <div
                                            style="font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase;">
                                            Nilai Kontrak</div>
                                        <div style="font-size: 15px; font-weight: 800; color: #10b981;">Rp
                                            {{ number_format($det->nilai_kontrak, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div
                                    style="background: var(--bg); border: 1px solid var(--border); border-radius: 10px; padding: 16px;">
                                    <div class="dm-grid-2 dm-activity-meta-grid">
                                        <div class="dm-activity-meta-item">
                                            <span class="dm-label" style="font-size: 10px;">Indikator Luaran</span>
                                            <span class="dm-value" style="font-size: 13px;">{{ $det->volume_luaran ?? 0 }}
                                                {{ $det->satuan_luaran ?? '-' }}</span>
                                        </div>
                                        <div class="dm-activity-meta-item">
                                            <span class="dm-label" style="font-size: 10px;">Keterangan Tambahan</span>
                                            <span class="dm-value"
                                                style="font-size: 13px;">{{ $det->keterangan ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 40px 20px;">
                            <div
                                style="width: 64px; height: 64px; border-radius: 50%; background: var(--surface2); color: var(--text-sub); font-size: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <p style="font-weight: 600; color: var(--text); margin: 0 0 4px 0;">Belum Ada Rincian Kegiatan
                            </p>
                            <p style="font-size: 13px; color: var(--text-sub); margin: 0;">Detail implementasi (IA) belum
                                ditambahkan ke dokumen ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pihak Terlibat --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-icon-box"
                        style="width:32px; height:32px; background:rgba(245,158,11,0.1); color:#f59e0b; font-size:14px;">
                        <i class="fas fa-users-viewfinder"></i></div>
                    <h3 class="dm-card-title">Pihak Terlibat</h3>
                </div>
                <div class="dm-card-body">
                    <div class="dm-grid-2">
                        {{-- Internal --}}
                        <div>
                            <div
                                style="font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid var(--border); display:flex; align-items:center; gap:8px;">
                                <i class="fas fa-university" style="color: #3b82f6;"></i> Pihak Internal (Polimdo)
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-pen-nib"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penandatangan</span>
                                        <div class="dm-value">
                                            {{ $kegiatan->penandatanganInternal?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">
                                            {{ $kegiatan->penandatanganInternal?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-user-shield"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penanggung Jawab</span>
                                        <div class="dm-value">{{ $kegiatan->pjInternal?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">
                                            {{ $kegiatan->pjInternal?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Mitra --}}
                        <div>
                            <div
                                style="font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid var(--border); display:flex; align-items:center; gap:8px;">
                                <i class="fas fa-building" style="color: #10b981;"></i> Pihak Eksternal (Mitra)
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-pen-nib"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penandatangan</span>
                                        <div class="dm-value">
                                            {{ $kegiatan->penandatanganMitra?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">
                                            {{ $kegiatan->penandatanganMitra?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-user-shield"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penanggung Jawab</span>
                                        <div class="dm-value">{{ $kegiatan->pjMitra?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">
                                            {{ $kegiatan->pjMitra?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Column --}}
        <div class="dm-sidebar-column" style="display: flex; flex-direction: column; gap: 28px;">

            {{-- Evaluasi --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-icon-box"
                        style="width:32px; height:32px; background:rgba(236,72,153,0.1); color:#ec4899; font-size:14px;">
                        <i class="fas fa-star"></i></div>
                    <h3 class="dm-card-title">Skor Evaluasi</h3>
                </div>
                <div class="dm-card-body">
                    @php $e = $kegiatan->evaluasis->first(); @endphp
                    @if($e)
                        <div class="dm-score-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div
                                style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 10px;">Kesesuaian</span>
                                <div style="font-size: 24px; font-weight: 800; color: #ec4899; line-height: 1;">
                                    {{ $e->sesuai_rencana ?? 0 }}<span
                                        style="font-size:14px;color:var(--text-sub)">/5</span></div>
                            </div>
                            <div
                                style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 10px;">Kualitas</span>
                                <div style="font-size: 24px; font-weight: 800; color: #8b5cf6; line-height: 1;">
                                    {{ $e->kualitas ?? 0 }}<span style="font-size:14px;color:var(--text-sub)">/5</span>
                                </div>
                            </div>
                            <div
                                style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 10px;">Keterlibatan</span>
                                <div style="font-size: 24px; font-weight: 800; color: #3b82f6; line-height: 1;">
                                    {{ $e->keterlibatan ?? 0 }}<span style="font-size:14px;color:var(--text-sub)">/5</span>
                                </div>
                            </div>
                            <div
                                style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 10px;">Efisiensi</span>
                                <div style="font-size: 24px; font-weight: 800; color: #10b981; line-height: 1;">
                                    {{ $e->efisiensi ?? 0 }}<span style="font-size:14px;color:var(--text-sub)">/5</span>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
                            <span class="dm-label" style="font-size:10px;">Catatan Evaluasi</span>
                            <div style="font-size: 13px; color: var(--text); font-style: italic;">
                                "{{ $e->catatan ?? 'Tidak ada catatan khusus.' }}"</div>
                        </div>
                    @else
                        <div
                            style="text-align: center; padding: 24px 10px; background: var(--bg); border-radius: 12px; border: 1px dashed var(--border);">
                            <div
                                style="width: 48px; height: 48px; border-radius: 50%; background: var(--surface2); color: var(--text-sub); display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 12px auto;">
                                <i class="fas fa-clipboard-question"></i>
                            </div>
                            <div style="font-weight: 600; font-size: 13px; color: var(--text);">Belum Dievaluasi</div>
                            <div style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Pimpinan belum memberikan
                                penilaian untuk kerjasama ini.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dokumen Lampiran --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-icon-box"
                        style="width:32px; height:32px; background:rgba(14,165,233,0.1); color:#0ea5e9; font-size:14px;">
                        <i class="fas fa-folder-open"></i></div>
                    <h3 class="dm-card-title">Lampiran & File</h3>
                </div>
                <div class="dm-card-body" style="padding: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @if($kegiatan->document_link)
                            <a href="{{ $kegiatan->document_link }}" target="_blank" class="dm-doc-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 10px; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="dm-doc-content">
                                        <div style="font-weight: 700; font-size: 13px; margin-bottom: 2px;">Naskah Kerjasama
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-sub);">Dokumen legal (MoU/MoA/IA)
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-external-link-alt" style="color: var(--text-sub); font-size: 12px;"></i>
                            </a>
                        @endif

                        @forelse($kegiatan->laporanFiles as $file)
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="dm-doc-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59,130,246,0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                        <i class="fas fa-file-image"></i>
                                    </div>
                                    <div class="dm-doc-content">
                                        <div class="dm-doc-title" style="font-weight: 700; font-size: 13px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;"
                                            title="{{ $file->nama_file ?: 'Lampiran Laporan' }}">
                                            {{ $file->nama_file ?: 'Lampiran Laporan' }}</div>
                                        <div style="font-size: 11px; color: var(--text-sub);">
                                            {{ $file->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                                <i class="fas fa-download" style="color: var(--text-sub); font-size: 12px;"></i>
                            </a>
                        @empty
                            @if(!$kegiatan->document_link)
                                <div style="text-align: center; padding: 20px; color: var(--text-sub); font-size: 12px;">
                                    <i class="fas fa-folder-minus"
                                        style="font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                                    Tidak ada dokumen yang dilampirkan.
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

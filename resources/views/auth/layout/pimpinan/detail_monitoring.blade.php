@php
    $status = strtolower($kegiatan->status_berlaku ?? $kegiatan->status ?? '');
    $statusDokumen = $kegiatan->status_dokumen ?? 'Draft';
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
        $isExtended => 'fa-clock-rotate-left',
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

    $pelaksanaGroups = collect();

    $jurusanNames = $kegiatan->jurusans->pluck('nama_jurusan')->filter()->values();
    if ($jurusanNames->isEmpty() && $kegiatan->jurusan?->nama_jurusan) {
        $jurusanNames = collect([$kegiatan->jurusan->nama_jurusan]);
    }
    if ($jurusanNames->isNotEmpty()) {
        $pelaksanaGroups->push([
            'type' => 'Jurusan',
            'icon' => 'fa-microchip',
            'class' => 'dk-entity-indigo',
            'label_class' => 'indigo',
            'names' => $jurusanNames,
        ]);
    }

    $upaNames = $kegiatan->upas->pluck('nama_upa')->filter()->values();
    if ($upaNames->isEmpty() && $kegiatan->upa?->nama_upa) {
        $upaNames = collect([$kegiatan->upa->nama_upa]);
    }
    if ($upaNames->isNotEmpty()) {
        $pelaksanaGroups->push([
            'type' => 'UPA',
            'icon' => 'fa-building-columns',
            'class' => 'dk-entity-cyan',
            'label_class' => 'cyan',
            'names' => $upaNames,
        ]);
    }

    $pusatNames = $kegiatan->pusats->pluck('nama_pusat')->filter()->values();
    if ($pusatNames->isEmpty() && $kegiatan->pusat?->nama_pusat) {
        $pusatNames = collect([$kegiatan->pusat->nama_pusat]);
    }
    if ($pusatNames->isNotEmpty()) {
        $pelaksanaGroups->push([
            'type' => 'Pusat',
            'icon' => 'fa-landmark',
            'class' => 'dk-entity-violet',
            'label_class' => 'violet',
            'names' => $pusatNames,
        ]);
    }

    $hasPelaksanaData = $pelaksanaGroups->isNotEmpty();
    $pelaksanaName = $pelaksanaGroups->flatMap(fn ($group) => $group['names'])->implode(', ') ?: 'Instansi';
    $pelaksanaType = $pelaksanaGroups->pluck('type')->filter()->implode(', ') ?: 'Instansi';
    $primaryPelaksanaType = $pelaksanaGroups->first()['type'] ?? null;
    $pelaksanaIcon = match (true) {
        $pelaksanaGroups->count() > 1 => 'fa-users-gear',
        $primaryPelaksanaType === 'Jurusan' => 'fa-microchip',
        $primaryPelaksanaType === 'UPA' => 'fa-building-columns',
        $primaryPelaksanaType === 'Pusat' => 'fa-landmark',
        default => 'fa-building',
    };

    $totalNilai = $kegiatan->details->sum('nilai_kontrak');

    $timeRemainingLabel = '-';
    $timeRemainingColor = 'var(--text)';
    $timeRemainingBg = 'var(--surface2)';
    $timeRemainingIcon = 'fa-hourglass-half';

    if ($kegiatan->end_date) {
        $today = now()->startOfDay();
        $threeMonthsFromToday = $today->copy()->addMonthsNoOverflow(3)->endOfDay();
        $end = \Carbon\Carbon::parse($kegiatan->end_date)->startOfDay();
        $daysUntilEnd = (int) $today->diffInDays($end, false);
        $isPast = $daysUntilEnd < 0;
        $isNearExpiry = !$isPast && $end->lte($threeMonthsFromToday);

        if ($isPast) {
            $timeRemainingLabel = 'Telah Kadaluarsa';
            $timeRemainingColor = '#ef4444';
            $timeRemainingBg = 'rgba(239,68,68,0.1)';
            $timeRemainingIcon = 'fa-calendar-times';
        } elseif ($daysUntilEnd === 0) {
            $timeRemainingLabel = 'Berakhir Hari Ini';
            $timeRemainingColor = '#ef4444';
            $timeRemainingBg = 'rgba(239,68,68,0.1)';
            $timeRemainingIcon = 'fa-fire';
        } else {
            $diff = $today->diff($end);
            $parts = [];

            if ($diff->y > 0) {
                $parts[] = $diff->y . ' Thn';
            }
            if ($diff->m > 0) {
                $parts[] = $diff->m . ' Bln';
            }
            if ($diff->d > 0 || empty($parts)) {
                $parts[] = $diff->d . ' Hari';
            }

            $timeRemainingLabel = implode(', ', array_slice($parts, 0, 2)) . ' Lagi';

            if ($daysUntilEnd <= 30) {
                $timeRemainingColor = '#ef4444';
                $timeRemainingBg = 'rgba(239,68,68,0.1)';
                $timeRemainingIcon = 'fa-fire';
            } elseif ($isNearExpiry) {
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

    $evaluasi = $kegiatan->evaluasis->first();
    $perpanjanganAsal = $kegiatan->perpanjanganDari;
    $perpanjangans = $kegiatan->perpanjangans;
    $judulUtama = $kegiatan->judul ?: ($kegiatan->title ?: 'Dokumen Kerja Sama');
    $deskripsiUtama = $kegiatan->ruang_lingkup ?: ($kegiatan->description ?: 'Tidak ada deskripsi yang dicantumkan.');
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
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .dm-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .dm-card-header-left {
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

        .dm-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .dm-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
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
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .dm-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.04);
        }

        .dm-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            background: var(--surface2);
            color: var(--text-sub);
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .dm-person-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            transition: border-color 0.2s, background 0.2s;
        }

        .dm-person-card:hover {
            border-color: rgba(79, 70, 229, 0.3);
            background: var(--surface2);
        }

        .dm-doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .dm-doc-item:hover {
            background: var(--surface2);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-1px);
        }

        .dm-table-wrap {
            overflow-x: auto;
            border-radius: 0 0 16px 16px;
        }

        .dm-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .dm-table th {
            background: var(--surface2);
            padding: 14px 18px;
            font-weight: 700;
            color: var(--text-sub);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        .dm-table td {
            padding: 18px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
            color: var(--text);
        }

        .dm-table tr:last-child td {
            border-bottom: none;
        }

        .dm-table tr:hover td {
            background: rgba(248, 250, 252, 0.5);
        }

        .tag-income {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .tag-income-yes {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .tag-income-no {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, 0.2);
        }

        .dm-main-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .dm-main-column,
        .dm-sidebar-column {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        @media only screen and (max-width: 991px) {
            .dm-main-layout {
                grid-template-columns: 1fr !important;
            }
            .dm-grid-2, .dm-grid-3 {
                grid-template-columns: 1fr !important;
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
            {{-- Breadcrumbs --}}
            <div
                style="font-size: 13px; color: #94a3b8; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
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
                        {{-- Badges --}}
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;">
                            <div
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(255,255,255,0.1); border-radius: 20px; font-size: 12px; font-weight: 700; border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                                <i class="fas {{ $statusIcon }}" style="color: {{ $statusColor }}"></i>
                                <span style="color: white;">Status: {{ $statusLabel }}</span>
                            </div>

                            <div
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(59,130,246,0.15); border-radius: 20px; font-size: 12px; font-weight: 700; border: 1px solid rgba(59,130,246,0.3); color: #93c5fd;">
                                <i class="fas fa-file-shield"></i>
                                <span>Dokumen: {{ $statusDokumen }}</span>
                            </div>

                            @if($kegiatan->tingkat)
                                <div
                                    style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(168,85,247,0.15); border-radius: 20px; font-size: 12px; font-weight: 700; border: 1px solid rgba(168,85,247,0.3); color: #d8b4fe;">
                                    <i class="fas fa-globe"></i>
                                    <span>Tingkat: {{ ucfirst($kegiatan->tingkat) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Judul Utama --}}
                        <h1
                            style="font-size: 28px; font-weight: 800; margin: 0 0 16px 0; line-height: 1.35; letter-spacing: -0.5px;">
                            {{ $judulUtama }}
                        </h1>

                        {{-- Info Metadata Bar --}}
                        <div
                            style="display: flex; flex-wrap: wrap; gap: 20px; font-size: 14px; color: #cbd5e1; font-weight: 500;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-building" style="color: #38bdf8;"></i>
                                <span>{{ $kegiatan->mitra?->nama_mitra ?? '-' }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-file-contract" style="color: #a78bfa;"></i>
                                <span>{{ $kegiatan->jenis ?? '-' }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-calendar-days" style="color: #4ade80;"></i>
                                <span>{{ $kegiatan->start_date?->format('d M Y') ?? '-' }} &mdash; {{ $kegiatan->end_date?->format('d M Y') ?? 'Selesai' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="{{ route('pimpinan.monitoring') }}"
                            style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: rgba(255,255,255,0.1); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; border: 1px solid rgba(255,255,255,0.15); transition: all 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'"
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
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: rgba(16,185,129,0.1); color: #10b981;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Total Nilai Kontrak</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--text);">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Rincian Kegiatan</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--text);">{{ $kegiatan->details->count() }} Kegiatan</div>
            </div>
        </div>

        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: {{ $timeRemainingBg }}; color: {{ $timeRemainingColor }};">
                <i class="fas {{ $timeRemainingIcon }}"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Sisa Masa Berlaku</div>
                <div style="font-size: 16px; font-weight: 800; color: {{ $timeRemainingColor }};">{{ $timeRemainingLabel }}</div>
            </div>
        </div>

        <div class="dm-stat-card">
            <div class="dm-icon-box" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                <i class="fas {{ $pelaksanaIcon }}"></i>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px;">Unit Pelaksana</div>
                <div style="font-size: 15px; font-weight: 800; color: var(--text); line-height: 1.35;" title="{{ $pelaksanaName }}">
                    {{ $pelaksanaType }}
                </div>
            </div>
        </div>
    </div>

    {{-- Main Layout --}}
    <div class="dm-main-layout">

        {{-- Main Column (Left) --}}
        <div class="dm-main-column">

            {{-- 1. Card Informasi Dokumen & Legalitas --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-card-header-left">
                        <div class="dm-icon-box" style="width:32px; height:32px; background:rgba(99,102,241,0.1); color:#6366f1; font-size:14px;">
                            <i class="fas fa-file-lines"></i>
                        </div>
                        <h3 class="dm-card-title">Informasi Dokumen & Legalitas</h3>
                    </div>
                </div>
                <div class="dm-card-body">
                    <div class="dm-grid-3" style="margin-bottom: 20px;">
                        <div style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dm-label">Nomor Dokumen Mitra</span>
                            <span class="dm-value" style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                {{ $kegiatan->doc_number ?: 'Tidak ada nomor' }}
                            </span>
                        </div>
                        <div style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dm-label">Nomor Surat PKS (Polimdo)</span>
                            <span class="dm-value" style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                @forelse($kegiatan->pksNumbers as $pksNumber)
                                    <span style="display: block;">{{ $pksNumber->number }}</span>
                                @empty
                                    <span style="color: var(--text-sub);">Tidak ada nomor</span>
                                @endforelse
                            </span>
                        </div>
                        <div style="background: var(--surface2); padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <span class="dm-label">Tingkat & Kategori</span>
                            <span class="dm-value">
                                {{ ucfirst($kegiatan->tingkat ?? 'Institusi') }} / {{ $kegiatan->jenis ?? 'MoU' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="dm-label">Deskripsi / Ruang Lingkup Kerja Sama</span>
                        <div
                            style="font-size: 14px; color: var(--text); line-height: 1.7; text-align: justify; padding: 20px; background: var(--bg); border-radius: 12px; border: 1px solid var(--border); white-space: pre-line;">
                            {{ $deskripsiUtama }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Card Unit Pelaksana & Prodi --}}
            @if ($hasPelaksanaData || $kegiatan->prodis->count() > 0)
                <div class="dm-card dk-card">
                    <div class="card-header dk-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border);">
                        <div class="dk-card-title" style="display: flex; align-items: center; gap: 10px;">
                            <span class="dk-title-icon" style="color: #4f46e5;"><i class="fas fa-users-gear"></i></span>
                            <h3 class="dm-card-title">Unit Pelaksana & Program Studi</h3>
                        </div>
                    </div>
                    <div class="card-body dk-card-body dk-detail-card-body" style="padding: 24px;">
                        @if($hasPelaksanaData)
                            <div class="dk-entity-grid" style="display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
                                @foreach ($pelaksanaGroups as $group)
                                    <div class="dk-entity-card" style="display: flex; align-items: center; gap: 12px; padding: 12px 18px; border: 1px solid var(--border); border-radius: 12px; background: var(--surface2);">
                                        <span class="dk-entity-icon {{ $group['class'] }}" style="font-size: 18px;">
                                            <i class="fas {{ $group['icon'] }}"></i>
                                        </span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label {{ $group['label_class'] }}" style="font-size: 10px; font-weight: 700; text-transform: uppercase; display: block;">{{ $group['type'] }}</small>
                                            <strong style="font-size: 13px; color: var(--text);">{{ $group['names']->implode(', ') }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($kegiatan->prodis->count() > 0)
                            <div>
                                <label class="dm-label" style="margin-bottom: 10px;">Program Studi Terkait</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    @foreach ($kegiatan->prodis as $prodi)
                                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(79,70,229,0.08); color: #4f46e5; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid rgba(79,70,229,0.15);">
                                            <i class="fas fa-graduation-cap"></i>
                                            <span>{{ $prodi->nama_prodi }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 3. Card Ruang Lingkup & Rincian Kegiatan (Table) --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-card-header-left">
                        <div class="dm-icon-box" style="width:32px; height:32px; background:rgba(16,185,129,0.1); color:#10b981; font-size:14px;">
                            <i class="fas fa-list-check"></i>
                        </div>
                        <h3 class="dm-card-title">Ruang Lingkup & Rincian Implementasi</h3>
                    </div>
                    <span style="font-size: 12px; color: var(--text-sub); font-weight: 600;">{{ $kegiatan->details->count() }} Kegiatan Terdaftar</span>
                </div>
                <div class="dm-table-wrap">
                    <table class="dm-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th style="min-width: 180px;">Bentuk Kegiatan</th>
                                <th style="min-width: 180px;">Sasaran & Indikator</th>
                                <th style="text-align: right; min-width: 140px;">Nilai Kontrak</th>
                                <th style="min-width: 150px;">Output & Outcome</th>
                                <th style="min-width: 120px;">Volume Luaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kegiatan->details as $idx => $item)
                                <tr>
                                    <td style="text-align: center; font-weight: 700; color: var(--text-sub);">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; font-size: 14px; color: var(--text); margin-bottom: 4px;">
                                            {{ $item->jenisKerjasama?->nama_kerjasama ?? 'Kegiatan Kerjasama' }}
                                        </div>
                                        @if($item->keterangan_luaran || $item->keterangan)
                                            <div style="font-size: 12px; color: var(--text-sub); line-height: 1.4;">
                                                {{ $item->keterangan_luaran ?: $item->keterangan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color: var(--text); margin-bottom: 6px;">
                                            <i class="fas fa-bullseye" style="color: #8b5cf6; margin-right: 4px;"></i>
                                            <strong>Sasaran:</strong> {{ $item->sasaran?->deskripsi ?? '-' }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-sub);">
                                            <i class="fas fa-chart-line" style="color: #0ea5e9; margin-right: 4px;"></i>
                                            <strong>Indikator:</strong> {{ $item->indikator?->nama_indikator ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        @if($item->nilai_kontrak > 0)
                                            <div style="font-size: 14px; font-weight: 800; color: #10b981; margin-bottom: 4px;">
                                                Rp {{ number_format($item->nilai_kontrak, 0, ',', '.') }}
                                            </div>
                                            <span class="tag-income {{ $item->income === 'ya' ? 'tag-income-yes' : 'tag-income-no' }}">
                                                {{ $item->income === 'ya' ? 'Income' : 'Non-Income' }}
                                            </span>
                                        @else
                                            <span style="color: var(--text-sub); font-size: 13px;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-size: 12px; margin-bottom: 6px;">
                                            <span style="font-weight: 700; color: var(--text-sub);">Output:</span>
                                            <span style="color: var(--text);">{{ $item->output ?: '-' }}</span>
                                        </div>
                                        <div style="font-size: 12px;">
                                            <span style="font-weight: 700; color: var(--text-sub);">Outcome:</span>
                                            <span style="color: var(--text);">{{ $item->outcome ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->volume_luaran)
                                            <div style="font-weight: 700; font-size: 13px; color: var(--text);">
                                                {{ $item->volume_luaran }} <span style="font-size: 11px; color: var(--text-sub);">{{ $item->satuan_luaran }}</span>
                                            </div>
                                        @else
                                            <span style="color: var(--text-sub); font-size: 13px;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px 20px;">
                                        <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--surface2); color: var(--text-sub); font-size: 22px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <div style="font-weight: 700; color: var(--text); margin-bottom: 4px;">Belum Ada Rincian Kegiatan</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">Detail kegiatan implementasi (IA) belum ditambahkan ke dokumen ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. Card Pihak Terlibat --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-card-header-left">
                        <div class="dm-icon-box" style="width:32px; height:32px; background:rgba(245,158,11,0.1); color:#f59e0b; font-size:14px;">
                            <i class="fas fa-users-viewfinder"></i>
                        </div>
                        <h3 class="dm-card-title">Pejabat & Pihak Terlibat</h3>
                    </div>
                </div>
                <div class="dm-card-body">
                    <div class="dm-grid-2">
                        {{-- Pihak Internal --}}
                        <div>
                            <div style="font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid var(--border); display:flex; align-items:center; gap:8px;">
                                <i class="fas fa-university" style="color: #3b82f6;"></i> Pihak Internal (Polimdo)
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-pen-nib"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penandatangan</span>
                                        <div class="dm-value">{{ $kegiatan->penandatanganInternal?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">{{ $kegiatan->penandatanganInternal?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-user-shield"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penanggung Jawab</span>
                                        <div class="dm-value">{{ $kegiatan->pjInternal?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">{{ $kegiatan->pjInternal?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pihak Mitra --}}
                        <div>
                            <div style="font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid var(--border); display:flex; align-items:center; gap:8px;">
                                <i class="fas fa-building" style="color: #10b981;"></i> Pihak Eksternal (Mitra)
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-pen-nib"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penandatangan</span>
                                        <div class="dm-value">{{ $kegiatan->penandatanganMitra?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">{{ $kegiatan->penandatanganMitra?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="dm-person-card">
                                    <div class="dm-avatar"><i class="fas fa-user-shield"></i></div>
                                    <div>
                                        <span class="dm-label" style="margin-bottom:2px;">Penanggung Jawab</span>
                                        <div class="dm-value">{{ $kegiatan->pjMitra?->nama ?: 'Belum diatur' }}</div>
                                        <div style="font-size: 12px; color: var(--text-sub);">{{ $kegiatan->pjMitra?->jabatan ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar Column (Right) --}}
        <div class="dm-sidebar-column">

            {{-- 1. Profil Lengkap Mitra --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-card-header-left">
                        <div class="dm-icon-box" style="width:32px; height:32px; background:rgba(59,130,246,0.1); color:#3b82f6; font-size:14px;">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="dm-card-title">Profil Mitra</h3>
                    </div>
                </div>
                <div class="dm-card-body">
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div>
                            <span class="dm-label">Nama Instansi / Perusahaan</span>
                            <div style="font-size: 15px; font-weight: 800; color: var(--text);">
                                {{ $kegiatan->mitra?->nama_mitra ?? '-' }}
                            </div>
                        </div>

                        <div class="dm-grid-2">
                            <div>
                                <span class="dm-label">Klasifikasi</span>
                                <div class="dm-value" style="font-size: 13px;">
                                    {{ $kegiatan->mitra?->klasifikasi?->nama ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="dm-label">Kategori / Negara</span>
                                <div class="dm-value" style="font-size: 13px;">
                                    {{ ucfirst($kegiatan->mitra?->kategori ?? 'Nasional') }} ({{ $kegiatan->mitra?->negara ?? 'Indonesia' }})
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="dm-label">Alamat Kantor</span>
                            <div style="font-size: 13px; color: var(--text); line-height: 1.5;">
                                {{ $kegiatan->mitra?->alamat ?? 'Alamat tidak dicantumkan' }}
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 8px; padding-top: 12px; border-top: 1px dashed var(--border); font-size: 12px;">
                            @if($kegiatan->mitra?->telp)
                                <div style="display: flex; align-items: center; gap: 8px; color: var(--text);">
                                    <i class="fas fa-phone" style="color: #6366f1; width: 14px;"></i>
                                    <span>{{ $kegiatan->mitra->telp }}</span>
                                </div>
                            @endif
                            @if($kegiatan->mitra?->email)
                                <div style="display: flex; align-items: center; gap: 8px; color: var(--text);">
                                    <i class="fas fa-envelope" style="color: #0ea5e9; width: 14px;"></i>
                                    <span>{{ $kegiatan->mitra->email }}</span>
                                </div>
                            @endif
                            @if($kegiatan->mitra?->website)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-globe" style="color: #10b981; width: 14px;"></i>
                                    <a href="{{ $kegiatan->mitra->website }}" target="_blank" style="color: #3b82f6; text-decoration: none; word-break: break-all;">
                                        {{ $kegiatan->mitra->website }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Skor & Riwayat Evaluasi Pimpinan --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-card-header-left">
                        <div class="dm-icon-box" style="width:32px; height:32px; background:rgba(236,72,153,0.1); color:#ec4899; font-size:14px;">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="dm-card-title">Evaluasi Pimpinan</h3>
                    </div>
                    @if($evaluasi)
                        <span class="dm-badge" style="background: rgba(16,185,129,0.1); color: #10b981;">
                            <i class="fas fa-check-circle"></i> {{ $evaluasi->status_validasi ?? 'Divalidasi' }}
                        </span>
                    @endif
                </div>
                <div class="dm-card-body">
                    @if($evaluasi)
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                            <div style="background: var(--surface2); padding: 12px; border-radius: 10px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 9px;">Kesesuaian</span>
                                <div style="font-size: 20px; font-weight: 800; color: #ec4899; line-height: 1;">
                                    {{ $evaluasi->sesuai_rencana ?? 0 }}<span style="font-size:12px;color:var(--text-sub)">/5</span>
                                </div>
                            </div>
                            <div style="background: var(--surface2); padding: 12px; border-radius: 10px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 9px;">Kualitas</span>
                                <div style="font-size: 20px; font-weight: 800; color: #8b5cf6; line-height: 1;">
                                    {{ $evaluasi->kualitas ?? 0 }}<span style="font-size:12px;color:var(--text-sub)">/5</span>
                                </div>
                            </div>
                            <div style="background: var(--surface2); padding: 12px; border-radius: 10px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 9px;">Keterlibatan</span>
                                <div style="font-size: 20px; font-weight: 800; color: #3b82f6; line-height: 1;">
                                    {{ $evaluasi->keterlibatan ?? 0 }}<span style="font-size:12px;color:var(--text-sub)">/5</span>
                                </div>
                            </div>
                            <div style="background: var(--surface2); padding: 12px; border-radius: 10px; border: 1px solid var(--border); text-align: center;">
                                <span class="dm-label" style="font-size: 9px;">Efisiensi</span>
                                <div style="font-size: 20px; font-weight: 800; color: #10b981; line-height: 1;">
                                    {{ $evaluasi->efisiensi ?? 0 }}<span style="font-size:12px;color:var(--text-sub)">/5</span>
                                </div>
                            </div>
                        </div>

                        @if($evaluasi->ringkasan)
                            <div style="margin-bottom: 12px;">
                                <span class="dm-label" style="font-size:10px;">Ringkasan Evaluasi</span>
                                <div style="font-size: 13px; color: var(--text);">{{ $evaluasi->ringkasan }}</div>
                            </div>
                        @endif

                        @if($evaluasi->saran || $evaluasi->rekomendasi)
                            <div style="margin-bottom: 12px;">
                                <span class="dm-label" style="font-size:10px;">Saran / Rekomendasi</span>
                                <div style="font-size: 13px; color: var(--text);">{{ $evaluasi->saran ?: $evaluasi->rekomendasi }}</div>
                            </div>
                        @endif

                        @if($evaluasi->tindak_lanjut)
                            <div style="margin-bottom: 12px;">
                                <span class="dm-label" style="font-size:10px;">Tindak Lanjut</span>
                                <div style="font-size: 13px; color: var(--text);">{{ $evaluasi->tindak_lanjut }}</div>
                            </div>
                        @endif

                        <div style="padding-top: 12px; border-top: 1px dashed var(--border); display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: var(--text-sub);">
                            <span>Evaluator: {{ $evaluasi->evaluator?->name ?? 'Pimpinan' }}</span>
                            <span>{{ $evaluasi->updated_at?->format('d M Y') ?? '-' }}</span>
                        </div>
                    @else
                        <div style="text-align: center; padding: 24px 10px; background: var(--bg); border-radius: 12px; border: 1px dashed var(--border);">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--surface2); color: var(--text-sub); display: flex; align-items: center; justify-content: center; font-size: 18px; margin: 0 auto 10px auto;">
                                <i class="fas fa-clipboard-question"></i>
                            </div>
                            <div style="font-weight: 700; font-size: 13px; color: var(--text);">Belum Dievaluasi</div>
                            <div style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Pimpinan belum melakukan evaluasi atau validasi untuk dokumen ini.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. Riwayat Perpanjangan --}}
            @if($perpanjanganAsal || ($perpanjangans && $perpanjangans->count() > 0))
                <div class="dm-card">
                    <div class="dm-card-header">
                        <div class="dm-card-header-left">
                            <div class="dm-icon-box" style="width:32px; height:32px; background:rgba(245,158,11,0.1); color:#f59e0b; font-size:14px;">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <h3 class="dm-card-title">Riwayat Perpanjangan</h3>
                        </div>
                    </div>
                    <div class="dm-card-body">
                        @if($perpanjanganAsal)
                            <div style="margin-bottom: 14px;">
                                <span class="dm-label">Perpanjangan Dari Dokumen</span>
                                <a href="{{ route('pimpinan.monitoring.detail', $perpanjanganAsal->id) }}" class="dm-doc-item">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-link" style="color: #f59e0b;"></i>
                                        <div>
                                            <div style="font-weight: 700; font-size: 12px;">{{ $perpanjanganAsal->judul ?: $perpanjanganAsal->title }}</div>
                                            <div style="font-size: 11px; color: var(--text-sub);">#{{ $perpanjanganAsal->doc_number ?: 'Tanpa nomor' }}</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right" style="font-size: 11px; color: var(--text-sub);"></i>
                                </a>
                            </div>
                        @endif

                        @if($perpanjangans && $perpanjangans->count() > 0)
                            <div>
                                <span class="dm-label">Dokumen Perpanjangan Lanjutan</span>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    @foreach($perpanjangans as $perp)
                                        <a href="{{ route('pimpinan.monitoring.detail', $perp->id) }}" class="dm-doc-item">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <i class="fas fa-file-signature" style="color: #10b981;"></i>
                                                <div>
                                                    <div style="font-weight: 700; font-size: 12px;">{{ $perp->judul ?: $perp->title }}</div>
                                                    <div style="font-size: 11px; color: var(--text-sub);">#{{ $perp->doc_number ?: 'Tanpa nomor' }}</div>
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-right" style="font-size: 11px; color: var(--text-sub);"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 4. Lampiran Dokumen Legal & Laporan --}}
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-card-header-left">
                        <div class="dm-icon-box" style="width:32px; height:32px; background:rgba(14,165,233,0.1); color:#0ea5e9; font-size:14px;">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h3 class="dm-card-title">Lampiran & Berkas</h3>
                    </div>
                </div>
                <div class="dm-card-body" style="padding: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @if($kegiatan->document_link)
                            <a href="{{ $kegiatan->document_link }}" target="_blank" class="dm-doc-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 10px; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 13px; margin-bottom: 2px;">Naskah Legal Kerjasama</div>
                                        <div style="font-size: 11px; color: var(--text-sub);">Dokumen resmi MoU/MoA/IA</div>
                                    </div>
                                </div>
                                <i class="fas fa-external-link-alt" style="color: var(--text-sub); font-size: 12px;"></i>
                            </a>
                        @endif

                        @forelse($kegiatan->laporanFiles as $file)
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="dm-doc-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59,130,246,0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                        <i class="fas fa-file-arrow-down"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 13px; margin-bottom: 2px; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                            title="{{ $file->nama_file ?: 'Berkas Laporan' }}">
                                            {{ $file->nama_file ?: 'Berkas Laporan' }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-sub);">
                                            {{ $file->created_at?->format('d M Y') ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-download" style="color: var(--text-sub); font-size: 12px;"></i>
                            </a>
                        @empty
                            @if(!$kegiatan->document_link)
                                <div style="text-align: center; padding: 20px; color: var(--text-sub); font-size: 12px;">
                                    <i class="fas fa-folder-minus" style="font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                                    Tidak ada dokumen lampiran yang tersedia.
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

</main>

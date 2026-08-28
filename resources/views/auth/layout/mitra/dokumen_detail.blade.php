@php
    $kegiatan = $kegiatan ?? $cooperation ?? null;
    $status = strtolower($kegiatan->status ?? '');
    $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
    $isExtended = str_contains($status, 'perpanjangan');
    $statusDokumen = $kegiatan->status_dokumen ?? 'Draft';
    $canSubmitToPimpinan = in_array($statusDokumen, ['Draft', 'Revisi'], true);

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
    $pelaksanaTypeLabel = $pelaksanaGroups->pluck('type')->filter()->implode(', ') ?: 'Instansi';

    $totalNilai = $kegiatan->details->sum('nilai_kontrak');

    $timeRemainingLabel = '-';
    $timeRemainingColor = 'var(--text)';
    $isPastDate = false;
    $isNearExpiry = false;
    $daysUntilEnd = null;
    if ($kegiatan->end_date) {
        $today = now()->startOfDay();
        $threeMonthsFromToday = $today->copy()->addMonthsNoOverflow(3)->endOfDay();
        $end = \Carbon\Carbon::parse($kegiatan->end_date)->startOfDay();
        $daysUntilEnd = (int) $today->diffInDays($end, false);
        $isPastDate = $daysUntilEnd < 0;
        $isNearExpiry = !$isPastDate && $end->lte($threeMonthsFromToday);

        if ($isPastDate) {
            $timeRemainingLabel = 'Kadarluarsa';
            $timeRemainingColor = '#ef4444';
            $daysUntilEnd = 0;
        } elseif ($daysUntilEnd === 0) {
            $timeRemainingLabel = 'Berakhir Hari Ini';
            $timeRemainingColor = '#ef4444';
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
            } elseif ($isNearExpiry) {
                $timeRemainingColor = '#f59e0b';
            } else {
                $timeRemainingColor = '#10b981';
            }
        }
    }
    $canAjukanPerpanjangan =
        $statusDokumen === 'Disahkan' && !$isExtended && ($isExpired || $isPastDate || $isNearExpiry);
@endphp

<style>
    .dk-warning-btn {
        background: linear-gradient(135deg, #ff9a00 0%, #ff5a00 100%);
        color: #fff !important;
        box-shadow: 0 10px 20px rgba(255, 90, 0, 0.2);
        text-decoration: none;
    }

    .dk-warning-btn:hover {
        background: linear-gradient(135deg, #ffb347 0%, #ff9a00 100%);
        box-shadow: 0 12px 24px rgba(255, 90, 0, 0.3);
        transform: translateY(-2px);
    }

    .dk-info-btn {
        background: linear-gradient(135deg, #e90606ff 0%, #e90606ff 100%);
        color: #fff !important;
        box-shadow: 0 10px 20px rgba(0, 114, 255, 0.2);
        text-decoration: none;
    }

    .dk-info-btn:hover {
        background: linear-gradient(135deg, #ff6969ff 0%, #e90606ff 100%);
        box-shadow: 0 12px 24px rgba(0, 114, 255, 0.3);
        transform: translateY(-2px);
    }
</style>

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">

<main id="mainContent" class="dk-page">
    {{-- ═══ HERO SECTION ═══ --}}
    <section class="ud-topbar ud-detail-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span class="sep">/</span>
                <a href="{{ route('mitra.dokumen.index') }}">Daftar Dokumen</a>
                <span class="sep">/</span>
                <span>Detail Dokumen</span>
            </div>

            <div class="ud-title-row ud-detail-title-row">
                <span class="ud-title-icon">
                    <i class="fas fa-file-contract"></i>
                </span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">{{ $kegiatan->judul ?? $kegiatan->title }}</h2>
                    <div class="dk-hero-meta ud-detail-meta">
                        <span class="dk-status {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }}"></i>
                            {{ $statusLabel }}
                        </span>
                        <div class="dk-hero-date-box">
                            <div class="date-item start">
                                <i class="fas fa-calendar-check"></i>
                                <span>{{ $kegiatan->start_date?->format('d M Y') ?? '-' }}</span>
                            </div>
                            <div class="date-item end">
                                <i class="fas fa-calendar-xmark"></i>
                                <span>{{ $kegiatan->end_date?->format('d M Y') ?? 'Selesai' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ud-detail-actions">
                    @if ($kegiatan->document_link)
                        <a href="{{ $kegiatan->document_link }}" target="_blank" class="dk-primary-btn">
                            <i class="fas fa-file-pdf"></i>
                            <span>Buka Berkas PDF</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ STATS GRID ═══ --}}
    <section>
        <div class="dk-stats-grid">
            <div class="dk-stat-card dk-stat-total">
                <div class="dk-stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="dk-stat-info">
                    <span class="dk-stat-label">Nilai Kontrak</span>
                    <strong>Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="dk-stat-card dk-stat-active">
                <div class="dk-stat-icon"><i class="fas fa-handshake"></i></div>
                <div class="dk-stat-info">
                    <span class="dk-stat-label">Ruang Lingkup</span>
                    <strong>{{ $kegiatan->details->count() }} Kegiatan</strong>
                </div>
            </div>
            <div class="dk-stat-card dk-stat-warning">
                <div class="dk-stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="dk-stat-info">
                    <span class="dk-stat-label">Sisa Waktu</span>
                    <strong style="color: {{ $timeRemainingColor }};">
                        {{ $timeRemainingLabel }}
                    </strong>
                </div>
            </div>
            <div class="dk-stat-card dk-stat-danger">
                <div class="dk-stat-icon"><i class="fas fa-building-user"></i></div>
                <div class="dk-stat-info">
                    <span class="dk-stat-label">Unit Pelaksana</span>
                    <strong>{{ $pelaksanaTypeLabel }}</strong>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div>
        <div class="dk-grid-layout">

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
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 28px;">
                            <div>
                                <label
                                    style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Nomor
                                    Dokumen Utama</label>
                                <div
                                    style="font-family: 'DM Mono', monospace; font-size: 14px; color: var(--text); padding: 10px 14px; background: var(--surface2); border-radius: 10px; border: 1px solid var(--border); word-break: break-all;">
                                    {{ $kegiatan->doc_number ?: '-' }}
                                </div>
                            </div>
                            <div>
                                <label
                                    style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Nomor
                                    PKS</label>
                                <div
                                    style="font-family: 'DM Mono', monospace; font-size: 14px; color: var(--text); padding: 10px 14px; background: var(--surface2); border-radius: 10px; border: 1px solid var(--border); word-break: break-all;">
                                    @forelse($kegiatan->pksNumbers as $pksNumber)
                                        <div>{{ $pksNumber->number }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Deskripsi
                                Kegiatan</label>
                            <div
                                style="font-size: 15px; color: var(--text); line-height: 1.8; text-align: justify; white-space: pre-line;">
                                {{ $kegiatan->description ?: 'Tidak ada deskripsi tambahan.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Catatan Pimpinan (hanya tampil jika sudah dievaluasi) --}}
                @php $evaluasi = $kegiatan->evaluasis->first(); @endphp
                @if ($evaluasi)
                    <div class="card dk-card">
                        <div class="card-header dk-card-header">
                            <div class="dk-card-title">
                                <span class="dk-title-icon"><i class="fas fa-clipboard-check"></i></span>
                                <span>
                                    <strong>Catatan Pimpinan</strong>
                                    <small>Hasil evaluasi dan arahan dari pimpinan</small>
                                </span>
                            </div>
                            <div>
                                @php
                                    $dokStatus = $kegiatan->status_dokumen ?? '-';
                                    $dokBadgeClass = match ($dokStatus) {
                                        'Disahkan' => 'dk-status-active',
                                        'Revisi' => 'dk-status-warning',
                                        'Menunggu Evaluasi' => 'dk-status-info',
                                        default => 'dk-status-neutral',
                                    };
                                    $dokBadgeIcon = match ($dokStatus) {
                                        'Disahkan' => 'fa-circle-check',
                                        'Revisi' => 'fa-pen-to-square',
                                        'Menunggu Evaluasi' => 'fa-clock',
                                        default => 'fa-circle-info',
                                    };
                                @endphp
                                <span class="dk-status {{ $dokBadgeClass }}">
                                    <i class="fas {{ $dokBadgeIcon }}"></i>
                                    {{ $dokStatus }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body dk-card-body" style="padding: 28px;">
                            {{-- Ringkasan / Catatan --}}
                            <div style="margin-bottom: 20px;">
                                <label
                                    style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">
                                    <i class="fas fa-quote-left" style="margin-right: 4px; opacity: 0.5;"></i> Ringkasan
                                    Evaluasi
                                </label>
                                <div
                                    style="font-size: 14px; color: var(--text); line-height: 1.8; text-align: justify; white-space: pre-line; padding: 16px 20px; background: var(--surface2); border-radius: 12px; border-left: 4px solid {{ $dokStatus === 'Disahkan' ? '#10b981' : '#f59e0b' }};">
                                    {{ $evaluasi->ringkasan ?: 'Tidak ada catatan dari pimpinan.' }}
                                </div>
                            </div>

                            @include('auth.layout.partials.evaluasi_pimpinan_nilai', ['evaluasi' => $evaluasi])

                            {{-- Penilai & Waktu --}}
                            <div
                                style="display: flex; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border);">
                                <div
                                    style="width: 36px; height: 36px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800;">
                                    {{ strtoupper(substr($evaluasi->penilai->name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 13px; color: var(--text);">
                                        {{ $evaluasi->penilai->name ?? 'Pimpinan' }}
                                    </div>
                                    <div style="font-size: 11px; color: var(--text-sub);">
                                        <i class="far fa-clock" style="margin-right: 4px;"></i>
                                        {{ $evaluasi->updated_at?->format('d M Y, H:i') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

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
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
                            {{-- Pihak 1 --}}
                            <div>
                                <div
                                    style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <span style="font-weight: 800; font-size: 15px; color: var(--text);">Politeknik
                                        Negeri Manado</span>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-indigo"><i
                                                class="fas fa-pen-nib"></i></span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label indigo">Penandatangan</small>
                                            <strong>{{ $kegiatan->penandatanganInternal?->nama ?: '-' }}</strong>
                                            <span>{{ $kegiatan->penandatanganInternal?->jabatan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-indigo"><i
                                                class="fas fa-user-tie"></i></span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label indigo">Penanggung Jawab</small>
                                            <strong>{{ $kegiatan->pjInternal?->nama ?: '-' }}</strong>
                                            <span>{{ $kegiatan->pjInternal?->jabatan ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Pihak 2 --}}
                            <div>
                                <div
                                    style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <span
                                        style="font-weight: 800; font-size: 15px; color: var(--text);">{{ $kegiatan->mitra?->nama_mitra ?: 'Pihak Mitra' }}</span>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-emerald"><i
                                                class="fas fa-pen-nib"></i></span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label emerald">Penandatangan</small>
                                            <strong>{{ $kegiatan->penandatanganMitra?->nama ?: '-' }}</strong>
                                            <span>{{ $kegiatan->penandatanganMitra?->jabatan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-emerald"><i
                                                class="fas fa-user-tie"></i></span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label emerald">Penanggung Jawab</small>
                                            <strong>{{ $kegiatan->pjMitra?->nama ?: '-' }}</strong>
                                            <span>{{ $kegiatan->pjMitra?->jabatan ?: '-' }}</span>
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
                                <small>Detail implementasi kerjasama yang terkait</small>
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
                                        <th>Output</th>
                                        <th>Outcome</th>
                                        <th>Luaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kegiatan->details as $idx => $item)
                                        <tr>
                                            <td><span class="dk-num">{{ $idx + 1 }}</span></td>
                                            <td>
                                                <div style="font-weight: 700; color: var(--text); font-size: 14px;">
                                                    {{ $item->jenisKerjasama?->nama_kerjasama ?? '-' }}
                                                </div>
                                                @if ($item->keterangan)
                                                    <div
                                                        style="font-size: 11px; color: var(--text-sub); margin-top: 5px; line-height: 1.4;">
                                                        {{ $item->keterangan }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-size: 13px; color: var(--text); line-height: 1.45;">
                                                    {{ $item->sasaran?->deskripsi ?? '-' }}
                                                </div>
                                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border);">
                                                    <div style="font-size: 10px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 3px;">
                                                        Indikator
                                                    </div>
                                                    <div style="font-size: 12px; color: var(--text); line-height: 1.45;">
                                                        {{ $item->indikator?->nama_indikator ?? '-' }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($item->nilai_kontrak > 0)
                                                    <div style="font-weight: 800; color: #059669; font-size: 14px;">Rp
                                                        {{ number_format($item->nilai_kontrak, 0, ',', '.') }}
                                                    </div>
                                                    <span
                                                        class="tag {{ $item->income === 'ya' ? 'tag-blue' : 'tag-gray' }}"
                                                        style="font-size: 10px; margin-top: 6px; padding: 2px 8px;">{{ $item->income === 'ya' ? 'Income' : 'Non-Income' }}</span>
                                                @else
                                                    <span
                                                        style="color: var(--text-sub); font-size: 13px; font-weight: 600;">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->output)
                                                    <div
                                                        style="font-size: 13px; color: var(--text); line-height: 1.5; white-space: pre-line;">
                                                        {{ $item->output }}
                                                    </div>
                                                @else
                                                    <span style="color: var(--text-sub);">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->outcome)
                                                    <div
                                                        style="font-size: 13px; color: var(--text); line-height: 1.5; white-space: pre-line;">
                                                        {{ $item->outcome }}
                                                    </div>
                                                @else
                                                    <span style="color: var(--text-sub);">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->volume_luaran)
                                                    <div
                                                        style="font-weight: 700; font-size: 13px; color: var(--text);">
                                                        {{ $item->volume_luaran }} <span
                                                            style="font-weight: 500; color: var(--text-sub);">{{ $item->satuan_luaran }}</span>
                                                    </div>
                                                @else
                                                    <span style="color: var(--text-sub);">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7"
                                                style="text-align: center; padding: 50px; color: var(--text-sub);">
                                                <i class="fas fa-inbox"
                                                    style="font-size: 32px; opacity: 0.2; margin-bottom: 12px; display: block;"></i>
                                                <span style="font-weight: 500;">Belum ada detail kegiatan
                                                    terdaftar.</span>
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
            <div style="display: flex; flex-direction: column; gap: 28px; min-width: 0;">

                {{-- ═══ Card Review Draf Mitra (UC13) ═══ --}}
                <div class="card dk-card">
                    <div class="card-header dk-card-header">
                        <div class="dk-card-title">
                            <span class="dk-title-icon"><i class="fas fa-file-signature"></i></span>
                            <span>
                                <strong>Catatan Review Draf (Mitra)</strong>
                                <small>Kirimkan masukan atau telaah terhadap draf dokumen</small>
                            </span>
                        </div>
                    </div>

                    <div class="card-body dk-card-body" style="padding: 28px;">
                        <p style="font-size: 13px; color: var(--text-sub); line-height: 1.5; margin-bottom: 16px;">
                            Jika dokumen kerja sama ini masih dalam status <strong>Draf / Menunggu Review</strong>, Anda dapat menyampaikan catatan revisi atau persetujuan kepada unit pengusul Polimdo.
                        </p>

                        <form action="{{ route('mitra.dokumen.review', $kegiatan->id) }}" method="POST">
                            @csrf
                            <div style="margin-bottom: 16px;">
                                <label for="catatan_review" style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
                                    Tulis Catatan / Masukan
                                </label>
                                <textarea name="catatan_review" id="catatan_review" rows="4" required
                                    placeholder="Tuliskan masukan pasal, klausul, atau persetujuan draf di sini..."
                                    style="width: 100%; border-radius: 12px; border: 1px solid var(--border); background: var(--surface2); color: var(--text); padding: 12px 14px; font-family: inherit; font-size: 13px; line-height: 1.6; resize: vertical; box-sizing: border-box;"></textarea>
                            </div>
                            <button type="submit" class="dk-primary-btn" style="width: 100%; justify-content: center;">
                                <i class="fas fa-paper-plane"></i>
                                <span>Kirim Catatan Review</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ═══ Enhanced Document Management Card (Berkas & Arsip) ═══ --}}
                <div class="card dk-card">
                    {{-- Card Header --}}
                    <div class="card-header dk-card-header">
                        <div class="dk-card-title">
                            <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                            <span>
                                <strong>Berkas & Dokumen Laporan</strong>
                                <small>Daftar file yang diunggah oleh kampus</small>
                            </span>
                        </div>
                    </div>

                    <div class="card-body dk-card-body" style="padding: 28px;">
                        @if ($kegiatan->laporanFiles && $kegiatan->laporanFiles->count() > 0)
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @foreach ($kegiatan->laporanFiles as $file)
                                    <div class="dk-file-item">
                                        <div style="display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1;">
                                            {{-- File Icon Badge --}}
                                            <div class="dk-file-icon">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>

                                            <div class="dk-file-info">
                                                <p class="dk-file-name">{{ $file->original_name }}</p>
                                                <div class="dk-file-meta">
                                                    <span class="dk-file-meta-item"><i class="far fa-calendar-alt"
                                                            style="margin-right: 4px;"></i>{{ $file->created_at->format('d M Y') }}</span>
                                                    <span class="dk-file-dot"></span>
                                                    <span class="dk-file-meta-item">{{ round($file->file_size / 1024 / 1024, 2) }} MB</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                class="dk-action-btn view" title="Pratinjau Dokumen">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="text-align: center; padding: 30px 20px;">
                                <div style="width: 56px; height: 56px; background: var(--surface2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; border: 1px solid var(--border);">
                                    <i class="fas fa-inbox" style="color: var(--text-sub); opacity: 0.4; font-size: 20px;"></i>
                                </div>
                                <h4 style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text);">Belum Ada Berkas Tambahan</h4>
                                <p style="margin: 4px 0 0; font-size: 11px; color: var(--text-sub);">Dokumen utama dapat dilihat melalui tautan dokumen PDF.</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($hasPelaksanaData)
                    {{-- Card: Pelaksana --}}
                    <div class="card dk-card">
                        <div class="card-header dk-card-header">
                            <div class="dk-card-title">
                                <span class="dk-title-icon"><i class="fas fa-users-gear"></i></span>
                                <span>
                                    <strong>Unit Pelaksana Kampus</strong>
                                    <small>Instansi pengelola kegiatan di Polimdo</small>
                                </span>
                            </div>
                        </div>
                        <div class="card-body dk-card-body dk-detail-card-body">
                            <div class="dk-entity-grid">
                                @foreach ($pelaksanaGroups as $group)
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon {{ $group['class'] }}">
                                            <i class="fas {{ $group['icon'] }}"></i>
                                        </span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label {{ $group['label_class'] }}">{{ $group['type'] }}</small>
                                            <strong>{{ is_array($group['names']) ? implode(', ', $group['names']) : (is_string($group['names']) ? $group['names'] : $group['names']->implode(', ')) }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($kegiatan->prodis && $kegiatan->prodis->count() > 0)
                                <div class="dk-prodi-list">
                                    <label class="dk-prodi-label">Program Studi Terkait</label>
                                    <div class="dk-prodi-container">
                                        @foreach ($kegiatan->prodis as $prodi)
                                            <div class="dk-prodi-item">
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

                {{-- Action Buttons --}}
                <div class="dk-btn-group">
                    @if ($kegiatan->document_link)
                        <a href="{{ $kegiatan->document_link }}" target="_blank" class="dk-primary-btn dk-btn-full">
                            <i class="fas fa-file-pdf"></i>
                            <span>Buka Berkas PDF Online</span>
                        </a>
                    @endif

                    @if ($canAjukanPerpanjangan)
                        <a href="{{ route('mitra.pengajuan.create') }}" class="dk-warning-btn dk-btn-full">
                            <i class="fas fa-clock-rotate-left"></i>
                            <span>Ajukan Perpanjangan</span>
                        </a>
                    @endif

                    <a href="{{ route('mitra.dokumen.index') }}" class="dk-secondary-btn dk-btn-full dk-btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Daftar Dokumen</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

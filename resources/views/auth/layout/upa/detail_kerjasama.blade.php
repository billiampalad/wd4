@php
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

    $pelaksanaGroups = collect($kegiatan->pelaksana_groups);
    $pelaksanaTypeLabel = $kegiatan->pelaksana_type_label;
    $hasPelaksanaData = $pelaksanaGroups->isNotEmpty();

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

<main id="mainContent" class="dk-page">
    {{-- ═══ HERO SECTION ═══ --}}
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('upa.dashboard') }}" class="breadcrumb-item">
                    <i class="fas fa-home"></i>
                </a>
                <a href="{{ route('upa.dashboard') }}" style="text-decoration: none; color: inherit;">
                    <span class="current">Beranda</span>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('upa.dkerjasama') }}" style="text-decoration: none; color: inherit;">
                    <span class="current">Daftar Kerjasama</span>
                </a>
                <span class="sep">/</span>
                <span class="current">Detail Dokumen</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="dk-hero-info">
                    <span class="dk-eyebrow">Repositori Unit Pelaksana</span>
                    <h2 id="pageTitle">{{ $kegiatan->title }}</h2>
                    <div class="dk-hero-meta">
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
                <div class="dk-hero-action">
                    <a href="{{ route('upa.kerjasama.edit', $kegiatan->id) }}" class="dk-primary-btn">
                        <i class="fas fa-pen-to-square"></i>
                        <span>Edit Data</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ STATS GRID ═══ --}}
    <section class="dk-stats-grid-wrapper">
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
                    <span class="dk-stat-label">Tipe Pelaksana</span>
                    <strong>{{ $pelaksanaTypeLabel ?: '-' }}</strong>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div class="dk-container">
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
                        <div class="card-body dk-card-body dk-detail-card-body">
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
                    <div class="card-body dk-card-body dk-detail-card-body">
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
                                                <span
                                                    style="font-size: 13px; color: var(--text);">{{ $item->sasaran?->deskripsi ?? '-' }}</span>
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

                {{-- ═══ Enhanced Document Management Card ═══ --}}
                <div class="card dk-card">
                    {{-- Card Header --}}
                    <div class="card-header dk-card-header">
                        <div class="dk-card-title">
                            <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                            <span>
                                <strong>Manajemen Dokumen</strong>
                                <small>Laporan & Arsip Kerjasama</small>
                            </span>
                        </div>
                    </div>

                    <div class="card-body dk-card-body" style="padding: 28px;" x-data="{ tab: 'list' }">
                        {{-- ═══ Modern Sliding Tab Navigation ═══ --}}
                        <div class="sliding-tab-container">
                            {{-- Background Pill Animation --}}
                            <div class="sliding-tab-pill"
                                :style="tab === 'list' ? 'transform: translateX(0);' : 'transform: translateX(100%);'">
                            </div>

                            <button @click="tab = 'list'" class="sliding-tab-btn"
                                :class="{ 'active': tab === 'list' }">
                                <i class="fas fa-list-ul"></i>
                                <span>Riwayat</span>
                            </button>
                            <button @click="tab = 'upload'" class="sliding-tab-btn"
                                :class="{ 'active': tab === 'upload' }">
                                <i class="fas fa-cloud-arrow-up"></i>
                                <span>Upload Baru</span>
                            </button>
                        </div>

                        {{-- ═══ Tab Content: List (File History) ═══ --}}
                        <div x-show="tab === 'list'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0">

                            @if ($kegiatan->laporanFiles->count() > 0)
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    @foreach ($kegiatan->laporanFiles as $file)
                                        <div class="dk-file-item">
                                            <div
                                                style="display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1;">
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
                                                        <span
                                                            class="dk-file-meta-item">{{ round($file->file_size / 1024 / 1024, 2) }}
                                                            MB</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                    class="dk-action-btn view" title="Pratinjau Dokumen">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>

                                                <form action="{{ route('upa.form.destroy', $file->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Hapus dokumen ini dari riwayat?')"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dk-action-btn delete"
                                                        title="Hapus Dokumen">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="text-align: center; padding: 40px 20px;">
                                    <div
                                        style="width: 64px; height: 64px; background: var(--surface2); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; border: 1px solid var(--border);">
                                        <i class="fas fa-inbox"
                                            style="color: var(--text-sub); opacity: 0.4; font-size: 24px;"></i>
                                    </div>
                                    <h4 style="margin: 0; font-size: 14px; font-weight: 700; color: var(--text);">Belum
                                        Ada
                                        Dokumen</h4>
                                    <p
                                        style="margin: 4px 0 0; font-size: 11px; color: var(--text-sub); max-width: 200px; margin-left: auto; margin-right: auto;">
                                        Klik tab 'Upload Baru' untuk menambahkan laporan kerjasama.</p>
                                </div>
                            @endif
                        </div>

                        {{-- ═══ Tab Content: Upload (Form) ═══ --}}
                        <div x-show="tab === 'upload'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" x-cloak>

                            <form action="{{ route('upa.form.store') }}" method="POST"
                                enctype="multipart/form-data" x-data="{ fileName: '' }">
                                @csrf
                                <input type="hidden" name="cooperation_id" value="{{ $kegiatan->id }}">

                                <div style="margin-bottom: 24px;">
                                    <div class="dk-upload-zone-wrapper">
                                        <input type="file" name="file_laporan" id="file_laporan" accept=".pdf"
                                            required class="dk-upload-input"
                                            @change="fileName = $event.target.files[0].name">

                                        <div class="dk-upload-zone" :class="{ 'has-file': fileName }">
                                            {{-- Visual Feedback on File Select --}}
                                            <template x-if="!fileName">
                                                <div>
                                                    <div class="dk-upload-icon">
                                                        <i class="fas fa-cloud-arrow-up"></i>
                                                    </div>
                                                    <p class="dk-upload-text-main">Tarik & Lepas File</p>
                                                    <p class="dk-upload-text-sub">atau klik untuk menelusuri berkas
                                                        (PDF)</p>
                                                </div>
                                            </template>

                                            <template x-if="fileName">
                                                <div style="animation: bounceIn 0.5s ease;">
                                                    <div class="dk-upload-icon">
                                                        <i class="fas fa-file-circle-check"></i>
                                                    </div>
                                                    <p class="dk-upload-text-main"
                                                        style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 0 20px;"
                                                        x-text="fileName"></p>
                                                    <p class="dk-upload-text-sub">Dokumen siap diunggah!</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="dk-btn-submit" :disabled="!fileName">
                                    <i class="fas fa-rocket"></i>
                                    <span>Kirim Dokumen</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if ($hasPelaksanaData)
                    {{-- Card: Pelaksana --}}
                    <div class="card dk-card">
                        <div class="card-header dk-card-header">
                            <div class="dk-card-title">
                                <span class="dk-title-icon"><i class="fas fa-users-gear"></i></span>
                                <span>
                                    <strong>Unit Pelaksana</strong>
                                    <small>Instansi pengelola kegiatan</small>
                                </span>
                            </div>
                        </div>
                        <div class="card-body dk-card-body" style="padding: 28px;">
                            <div style="display: grid; gap: 12px;">
                                @foreach ($pelaksanaGroups as $group)
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon {{ $group['class'] }}">
                                            <i class="fas {{ $group['icon'] }}"></i>
                                        </span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label {{ $group['label_class'] }}">{{ $group['type'] }}</small>
                                            <strong>{{ implode(', ', $group['names']) }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($kegiatan->prodis->count() > 0)
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

                {{-- Card: Profil Mitra --}}
                <div class="card dk-card">
                    <div class="card-header dk-card-header">
                        <div class="dk-card-title">
                            <span class="dk-title-icon"><i class="fas fa-building-circle-check"></i></span>
                            <span>
                                <strong>Profil Mitra</strong>
                                <small>Informasi detail instansi mitra</small>
                            </span>
                        </div>
                    </div>
                    <div class="card-body dk-card-body" style="padding: 28px;">
                        @if ($kegiatan->mitra)
                            <div class="dk-mitra-profile">
                                <div class="dk-mitra-header">
                                    <div class="dk-mitra-logo">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <strong class="dk-mitra-name">{{ $kegiatan->mitra->nama_mitra }}</strong>
                                    <span class="tag tag-blue dk-mitra-category">{{ ucfirst($kegiatan->mitra->kategori ?? 'Nasional') }}</span>
                                </div>

                                <div class="dk-mitra-info-list">
                                    <div class="dk-mitra-info-item">
                                        <small class="dk-mitra-info-label">Alamat Instansi</small>
                                        <div class="dk-mitra-info-value">
                                            <i class="fas fa-map-location-dot dk-mitra-map-icon"></i>
                                            <span>{{ $kegiatan->mitra->alamat ?: 'Alamat belum dilengkapi.' }}</span>
                                        </div>
                                    </div>
                                    @if ($kegiatan->mitra->website)
                                        <div class="dk-mitra-info-item">
                                            <small class="dk-mitra-info-label">Website Resmi</small>
                                            <a href="{{ $kegiatan->mitra->website }}" target="_blank"
                                                class="dk-mitra-website">
                                                <i class="fas fa-globe"></i>
                                                <span>{{ str_replace(['http://', 'https://'], '', $kegiatan->mitra->website) }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="dk-mitra-empty">
                                <i class="fas fa-building-slash"></i>
                                <span>Data mitra tidak ditemukan.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="dk-btn-group">
                    @if ($kegiatan->document_link)
                        <a href="{{ $kegiatan->document_link }}" target="_blank" class="dk-primary-btn dk-btn-full">
                            <i class="fas fa-file-pdf"></i>
                            <span>Lihat Dokumen Asli</span>
                        </a>
                    @endif

                    @if ($canSubmitToPimpinan)
                        <form action="{{ route('upa.kerjasama.submit', $kegiatan->id) }}" method="POST"
                            class="dk-btn-full" id="submitForm">
                            @csrf
                            <button type="submit" class="dk-warning-btn dk-btn-full"
                                style="border: none; cursor: pointer;"
                                onclick="return confirm('{{ $statusDokumen === 'Revisi' ? 'Kirim ulang dokumen revisi ini ke Pimpinan?' : 'Apakah Anda yakin ingin mengirim permintaan persetujuan ke Pimpinan?' }}')">
                                <i class="fas fa-paper-plane"></i>
                                <span>Minta Persetujuan Pimpinan</span>
                            </button>
                        </form>
                    @endif

                    @if ($canAjukanPerpanjangan)
                        <a href="{{ route('upa.kerjasama.create', ['perpanjangan_dari' => $kegiatan->id]) }}"
                            class="dk-info-btn dk-btn-full">
                            <i class="fas fa-clock-rotate-left"></i>
                            <span>Ajukan Perpanjangan</span>
                        </a>
                    @endif

                    <a href="{{ route('upa.dkerjasama') }}" class="dk-secondary-btn dk-btn-full dk-btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Repositori</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

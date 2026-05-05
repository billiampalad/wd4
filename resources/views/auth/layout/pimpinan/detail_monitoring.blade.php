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

$pelaksanaName = '-';
$pelaksanaType = '-';
$pelaksanaClass = 'dk-entity-indigo';
$pelaksanaIcon = 'fa-building';

if ($kegiatan->tipe_pelaksana === 'jurusan') {
    $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
    $pelaksanaType = 'Jurusan';
    $pelaksanaIcon = 'fa-microchip';
} elseif ($kegiatan->tipe_pelaksana === 'upa') {
    $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
    $pelaksanaType = 'UPA';
    $pelaksanaIcon = 'fa-building-columns';
    $pelaksanaClass = 'dk-entity-cyan';
} elseif ($kegiatan->tipe_pelaksana === 'pusat') {
    $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
    $pelaksanaType = 'Pusat';
    $pelaksanaIcon = 'fa-landmark';
    $pelaksanaClass = 'dk-entity-violet';
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

        $totalDays = $now->diffInDays($end);
        if ($totalDays < 30) {
            $timeRemainingColor = '#ef4444';
        } elseif ($totalDays < 90) {
            $timeRemainingColor = '#f59e0b';
        } else {
            $timeRemainingColor = '#10b981';
        }
    }
}
@endphp

<main id="mainContent" class="dk-page">
    {{-- ═══ HERO SECTION ═══ --}}
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('pimpinan.dashboard') }}" class="breadcrumb-item">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('pimpinan.monitoring') }}" class="breadcrumb-item">
                    <span>Monitoring</span>
                </a>
                <span class="sep">/</span>
                <span class="current">Detail Kerjasama Global</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                    <i class="fas fa-desktop"></i>
                </div>
                <div class="dk-hero-info">
                    <span class="dk-eyebrow">Detail Monitoring Pimpinan</span>
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
                    <a href="{{ route('pimpinan.evaluasi') }}" class="dk-primary-btn" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                        <i class="fas fa-file-signature"></i>
                        <span>Evaluasi Laporan</span>
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
                    <strong>{{ $pelaksanaType }}</strong>
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
                            {{-- Pihak 1: Internal --}}
                            <div>
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <span style="font-weight: 800; font-size: 15px; color: var(--text);">Politeknik Negeri Manado</span>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-indigo"><i class="fas fa-pen-nib"></i></span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label indigo">Penandatangan</small>
                                            <strong>{{ $kegiatan->penandatanganInternal?->nama ?: '-' }}</strong>
                                            <span>{{ $kegiatan->penandatanganInternal?->jabatan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-indigo"><i class="fas fa-user-tie"></i></span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label indigo">Penanggung Jawab</small>
                                            <strong>{{ $kegiatan->pjInternal?->nama ?: '-' }}</strong>
                                            <span>{{ $kegiatan->pjInternal?->jabatan ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Pihak 2: Mitra --}}
                            <div>
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <span style="font-weight: 800; font-size: 15px; color: var(--text);">{{ $kegiatan->mitra?->nama_mitra ?: 'Pihak Mitra' }}</span>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-emerald"><i class="fas fa-pen-nib"></i></span>
                                        <div class="dk-entity-text">
                                            <small class="dk-entity-label emerald">Penandatangan</small>
                                            <strong>{{ $kegiatan->penandatanganMitra?->nama ?: '-' }}</strong>
                                            <span>{{ $kegiatan->penandatanganMitra?->jabatan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="dk-entity-card">
                                        <span class="dk-entity-icon dk-entity-emerald"><i class="fas fa-user-tie"></i></span>
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

                {{-- Right Column --}}
                <div style="display: flex; flex-direction: column; gap: 28px;">
                    {{-- Card: Unit Pengusul --}}
                    <div class="card dk-card">
                        <div class="card-header dk-card-header">
                            <div class="dk-card-title">
                                <span class="dk-title-icon"><i class="fas fa-sitemap"></i></span>
                                <span>
                                    <strong>Unit Pengusul</strong>
                                    <small>Pelaksana kerjasama internal</small>
                                </span>
                            </div>
                        </div>
                        <div class="card-body dk-card-body" style="padding: 24px;">
                            <div class="dk-entity-card" style="margin-bottom: 0;">
                                <span class="dk-entity-icon {{ $pelaksanaClass }}"><i class="fas {{ $pelaksanaIcon }}"></i></span>
                                <div class="dk-entity-text">
                                    <small class="dk-entity-label {{ str_replace('dk-entity-', '', $pelaksanaClass) }}">{{ $pelaksanaType }}</small>
                                    <strong style="font-size: 15px;">{{ $pelaksanaName }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card: Evaluasi Pimpinan --}}
                    <div class="card dk-card">
                        <div class="card-header dk-card-header">
                            <div class="dk-card-title">
                                <span class="dk-title-icon" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;"><i class="fas fa-star"></i></span>
                                <span>
                                    <strong>Evaluasi Kinerja</strong>
                                    <small>Skor capaian kerjasama</small>
                                </span>
                            </div>
                        </div>
                        <div class="card-body dk-card-body" style="padding: 24px;">
                            @php $e = $kegiatan->evaluasis->first(); @endphp
                            @if($e)
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div class="dk-score-card">
                                    <span class="label">Kesesuaian</span>
                                    <span class="value">{{ $e->sesuai_rencana ?? '-' }}<small>/5</small></span>
                                </div>
                                <div class="dk-score-card">
                                    <span class="label">Kualitas</span>
                                    <span class="value">{{ $e->kualitas ?? '-' }}<small>/5</small></span>
                                </div>
                                <div class="dk-score-card">
                                    <span class="label">Keterlibatan</span>
                                    <span class="value">{{ $e->keterlibatan ?? '-' }}<small>/5</small></span>
                                </div>
                                <div class="dk-score-card">
                                    <span class="label">Efisiensi</span>
                                    <span class="value">{{ $e->efisiensi ?? '-' }}<small>/5</small></span>
                                </div>
                            </div>
                            @else
                            <div style="text-align: center; padding: 20px; background: var(--surface2); border-radius: 12px; border: 1.5px dashed var(--border);">
                                <i class="fas fa-clipboard-question" style="font-size: 24px; color: var(--text-sub); margin-bottom: 8px; display: block;"></i>
                                <span style="font-size: 12px; color: var(--text-sub); font-weight: 600;">Belum ada evaluasi</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Card: Dokumentasi --}}
                    <div class="card dk-card">
                        <div class="card-header dk-card-header">
                            <div class="dk-card-title">
                                <span class="dk-title-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"><i class="fas fa-cloud-arrow-down"></i></span>
                                <span>
                                    <strong>Lampiran Dokumen</strong>
                                    <small>Naskah & bukti pendukung</small>
                                </span>
                            </div>
                        </div>
                        <div class="card-body dk-card-body" style="padding: 24px;">
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @if($kegiatan->document_link)
                                <a href="{{ $kegiatan->document_link }}" target="_blank" class="dk-file-link">
                                    <div class="file-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="file-info">
                                        <strong>Dokumen Utama (MOU/PKS)</strong>
                                        <span>Klik untuk mengunduh naskah</span>
                                    </div>
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                                
                                @foreach($kegiatan->laporanFiles as $file)
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="dk-file-link">
                                    <div class="file-icon"><i class="fas fa-file-image"></i></div>
                                    <div class="file-info">
                                        <strong>{{ $file->nama_file ?: 'Lampiran Laporan' }}</strong>
                                        <span>{{ $file->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

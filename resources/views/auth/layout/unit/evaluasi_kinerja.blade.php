<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current">Evaluasi Kinerja</span>
        </div>
        <h2 id="pageTitle">Evaluasi Kinerja Kerjasama</h2>
        <p id="pageDesc">Kelola penilaian kinerja kerjasama untuk
            <strong>{{ auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja' }}</strong>.
        </p>
    </div>

    @if(session('success'))
    <div class="dk-alert dk-alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="dk-alert dk-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════
         1. DRAFT DOKUMEN (Status Dokumen: Draft)
    ═══════════════════════════════════════════════════ -->
    <div class="card um-card dk-card" style="margin-bottom: 24px;">
        <div class="card-header um-header dk-card-header" style="background: linear-gradient(135deg, rgba(245,158,11,0.06), rgba(217,119,6,0.03));">
            <div class="um-title dk-card-title">
                <div style="width:32px; height:32px; border-radius:8px; background:rgba(245,158,11,0.12); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:14px;">
                    <i class="fas fa-file-pen"></i>
                </div>
                <div>
                    <span style="display:block; font-size:14px; font-weight: 700;">Draft Dokumen</span>
                    <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Dokumen yang baru diajukan & butuh kelengkapan berkas</span>
                </div>
            </div>
            @if(isset($draftList) && $draftList->count() > 0)
            <span class="tag tag-orange" style="font-size:12px; padding:5px 14px;">
                {{ $draftList->count() }} Draft
            </span>
            @endif
        </div>
        <div class="card-body dk-card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Judul Kerjasama</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Kelengkapan</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($draftList ?? collect()) as $index => $kegiatan)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td">
                                <div style="font-weight:700; color:var(--text); font-size:13px;">{{ $kegiatan->title }}</div>
                                <div style="font-size:10px; color:var(--text-sub);">{{ $kegiatan->jenis }}</div>
                            </td>
                            <td class="um-td">
                                <span style="font-size:12px; font-weight:600;">{{ $kegiatan->mitra?->nama_mitra ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                @php $hasLaporan = $kegiatan->laporanFiles->count() > 0; @endphp
                                <span class="tag {{ $hasLaporan ? 'tag-green' : 'tag-gray' }}" style="font-size:10px;">
                                    <i class="fas {{ $hasLaporan ? 'fa-check' : 'fa-times' }}" style="margin-right:4px;"></i>
                                    {{ $hasLaporan ? 'Laporan Ada' : 'Belum Ada Laporan' }}
                                </span>
                            </td>
                            <td class="um-td um-td-aksi" style="text-align:center;">
                                <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="dk-primary-btn" style="padding: 6px 12px; font-size: 10px; border-radius: 6px;">
                                    <i class="fas fa-arrow-right"></i> Lengkapi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="um-empty">
                                <div class="um-empty-state">
                                    <i class="fas fa-folder-open" style="font-size: 24px; opacity: 0.2; margin-bottom: 10px;"></i>
                                    <p class="um-empty-title" style="font-size: 13px;">Tidak ada draft aktif</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         2. MENUNGGU EVALUASI (Status Dokumen: Menunggu Evaluasi)
    ═══════════════════════════════════════════════════ -->
    <div class="card um-card dk-card" style="margin-bottom: 24px;">
        <div class="card-header um-header dk-card-header" style="background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(6,182,212,0.03));">
            <div class="um-title dk-card-title">
                <div style="width:32px; height:32px; border-radius:8px; background:rgba(79,70,229,0.12); color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:14px;">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <span style="display:block; font-size:14px; font-weight: 700;">Menunggu Evaluasi</span>
                    <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Dokumen yang sudah lengkap dan siap dinilai kinerjanya</span>
                </div>
            </div>
            @if(isset($belumEvaluasi) && $belumEvaluasi->count() > 0)
            <span class="tag tag-blue" style="font-size:12px; padding:5px 14px;">
                {{ $belumEvaluasi->count() }} Kegiatan
            </span>
            @endif
        </div>
        <div class="card-body dk-card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Nama Kegiatan</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Tanggal Berakhir</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($belumEvaluasi ?? collect()) as $index => $kegiatan)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td">
                                <div style="font-weight:700; color:var(--text); font-size:13px;">{{ $kegiatan->title }}</div>
                            </td>
                            <td class="um-td" style="font-size:12px;">
                                {{ $kegiatan->mitra?->nama_mitra ?? '-' }}
                            </td>
                            <td class="um-td" style="font-size:12px; color:var(--text-sub);">
                                {{ $kegiatan->end_date ? $kegiatan->end_date->format('d M Y') : '-' }}
                            </td>
                            <td class="um-td um-td-aksi" style="text-align:center;">
                                <a href="{{ route('unit.evaluasi.form', $kegiatan->id) }}" class="dk-primary-btn" style="padding: 6px 14px; font-size:11px; border-radius:8px;">
                                    <i class="fas fa-star" style="font-size:10px;"></i> Beri Nilai
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="um-empty">
                                <div class="um-empty-state">
                                    <i class="fas fa-circle-check" style="font-size: 24px; opacity: 0.4; color: #10b981; margin-bottom: 10px;"></i>
                                    <p class="um-empty-title" style="font-size: 13px;">Semua tugas evaluasi selesai</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         3. SUDAH DIEVALUASI / DISAHKAN (Status Dokumen: Disahkan)
    ═══════════════════════════════════════════════════ -->
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header" style="background: linear-gradient(135deg, rgba(16,185,129,0.06), rgba(5,150,105,0.03));">
            <div class="um-title dk-card-title">
                <div style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:14px;">
                    <i class="fas fa-certificate"></i>
                </div>
                <div>
                    <span style="display:block; font-size:14px; font-weight: 700;">Sudah Disahkan</span>
                    <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Arsip evaluasi yang telah diselesaikan</span>
                </div>
            </div>
        </div>
        <div class="card-body dk-card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Nama Kegiatan</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Rata-rata Skor</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($evaluasiList ?? collect()) as $index => $kegiatan)
                        @php
                        $eval = $kegiatan->evaluasis?->first();
                        $avgScore = $eval
                        ? round(($eval->kualitas + $eval->keterlibatan + $eval->efisiensi + $eval->kepuasan) / 4, 1)
                        : 0;
                        @endphp
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td">
                                <div style="font-weight:700; color:var(--text); font-size:13px;">{{ $kegiatan->title }}</div>
                            </td>
                            <td class="um-td" style="font-size:12px;">
                                {{ $kegiatan->mitra?->nama_mitra ?? '-' }}
                            </td>
                            <td class="um-td">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; height: 6px; background: var(--surface2); border-radius: 10px; overflow: hidden; min-width: 60px;">
                                        <div style="width: {{ ($avgScore / 5) * 100 }}%; height: 100%; background: {{ $avgScore >= 4 ? '#10b981' : ($avgScore >= 3 ? '#f59e0b' : '#ef4444') }};"></div>
                                    </div>
                                    <span style="font-family:'DM Mono',monospace; font-size:12px; font-weight:700;">{{ $avgScore }}/5</span>
                                </div>
                            </td>
                            <td class="um-td um-td-aksi" style="text-align:center;">
                                <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="dk-action-btn view" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="um-empty">
                                <div class="um-empty-state">
                                    <p class="um-empty-title" style="font-size: 12px; color: var(--text-sub);">Belum ada riwayat pengesahan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
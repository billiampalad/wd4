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
                            <th class="um-th dk-th-title" style="width: 400px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($draftList ?? collect()) as $kegiatan)
                        @php
                        $pelaksanaIcon = 'fa-building';
                        $pelaksanaClass = 'dk-entity-indigo';
                        $pelaksanaName = '-';
                        if ($kegiatan->tipe_pelaksana === 'jurusan') {
                        $pelaksanaIcon = 'fa-microchip';
                        $pelaksanaClass = 'dk-entity-indigo';
                        $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
                        } elseif ($kegiatan->tipe_pelaksana === 'upa') {
                        $pelaksanaIcon = 'fa-building-columns';
                        $pelaksanaClass = 'dk-entity-cyan';
                        $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
                        } elseif ($kegiatan->tipe_pelaksana === 'pusat') {
                        $pelaksanaIcon = 'fa-landmark';
                        $pelaksanaClass = 'dk-entity-violet';
                        $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
                        }
                        $docNumber = $kegiatan->doc_number ?? '';
                        $title = $kegiatan->title ?? '';
                        $mitraName = $kegiatan->mitra?->nama_mitra ?? '';
                        @endphp
                        <tr class="um-row dk-row">
                            <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td dk-title-cell" style="width: 400px; vertical-align: top; padding-top: 15px;">
                                <div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">
                                    <span class="dk-doc-number">#{{ $docNumber ?: '-' }}</span>
                                    <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">{{ $title ?: '-' }}</span>
                                    <span class="dk-doc-kind">{{ $kegiatan->jenis ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon {{ $pelaksanaClass }}" style="flex-shrink: 0;">
                                        <i class="fas {{ $pelaksanaIcon }}"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $pelaksanaName }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $mitraName ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td um-td-aksi" style="text-align:center; vertical-align: top; padding-top: 15px;">
                                <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="dk-primary-btn" style="padding: 6px 12px; font-size: 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
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
                            <th class="um-th dk-th-title" style="width: 400px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($belumEvaluasi ?? collect()) as $kegiatan)
                        @php
                        $pelaksanaIcon = 'fa-building';
                        $pelaksanaClass = 'dk-entity-indigo';
                        $pelaksanaName = '-';
                        if ($kegiatan->tipe_pelaksana === 'jurusan') {
                        $pelaksanaIcon = 'fa-microchip';
                        $pelaksanaClass = 'dk-entity-indigo';
                        $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
                        } elseif ($kegiatan->tipe_pelaksana === 'upa') {
                        $pelaksanaIcon = 'fa-building-columns';
                        $pelaksanaClass = 'dk-entity-cyan';
                        $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
                        } elseif ($kegiatan->tipe_pelaksana === 'pusat') {
                        $pelaksanaIcon = 'fa-landmark';
                        $pelaksanaClass = 'dk-entity-violet';
                        $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
                        }
                        $docNumber = $kegiatan->doc_number ?? '';
                        $title = $kegiatan->title ?? '';
                        $mitraName = $kegiatan->mitra?->nama_mitra ?? '';
                        @endphp
                        <tr class="um-row dk-row">
                            <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td dk-title-cell" style="width: 400px; vertical-align: top; padding-top: 15px;">
                                <div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">
                                    <span class="dk-doc-number">#{{ $docNumber ?: '-' }}</span>
                                    <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">{{ $title ?: '-' }}</span>
                                    <span class="dk-doc-kind">{{ $kegiatan->jenis ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon {{ $pelaksanaClass }}" style="flex-shrink: 0;">
                                        <i class="fas {{ $pelaksanaIcon }}"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $pelaksanaName }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $mitraName ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td um-td-aksi" style="text-align:center; vertical-align: top; padding-top: 15px;">
                                <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="dk-primary-btn" style="padding: 6px 12px; font-size: 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-eye"></i> Lihat Detail
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
                            <th class="um-th dk-th-title" style="width: 400px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Rata-rata Skor</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($evaluasiList ?? collect()) as $kegiatan)
                        @php
                        $eval = $kegiatan->evaluasis?->first();
                        $avgScore = $eval
                        ? round(($eval->kualitas + $eval->keterlibatan + $eval->efisiensi + $eval->kepuasan) / 4, 1)
                        : 0;

                        $pelaksanaIcon = 'fa-building';
                        $pelaksanaClass = 'dk-entity-indigo';
                        $pelaksanaName = '-';
                        if ($kegiatan->tipe_pelaksana === 'jurusan') {
                        $pelaksanaIcon = 'fa-microchip';
                        $pelaksanaClass = 'dk-entity-indigo';
                        $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
                        } elseif ($kegiatan->tipe_pelaksana === 'upa') {
                        $pelaksanaIcon = 'fa-building-columns';
                        $pelaksanaClass = 'dk-entity-cyan';
                        $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
                        } elseif ($kegiatan->tipe_pelaksana === 'pusat') {
                        $pelaksanaIcon = 'fa-landmark';
                        $pelaksanaClass = 'dk-entity-violet';
                        $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
                        }
                        $docNumber = $kegiatan->doc_number ?? '';
                        $title = $kegiatan->title ?? '';
                        $mitraName = $kegiatan->mitra?->nama_mitra ?? '';
                        @endphp
                        <tr class="um-row dk-row">
                            <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td dk-title-cell" style="width: 400px; vertical-align: top; padding-top: 15px;">
                                <div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">
                                    <span class="dk-doc-number">#{{ $docNumber ?: '-' }}</span>
                                    <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">{{ $title ?: '-' }}</span>
                                    <span class="dk-doc-kind">{{ $kegiatan->jenis ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon {{ $pelaksanaClass }}" style="flex-shrink: 0;">
                                        <i class="fas {{ $pelaksanaIcon }}"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $pelaksanaName }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $mitraName ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; height: 6px; background: var(--surface2); border-radius: 10px; overflow: hidden; min-width: 60px;">
                                        <div style="width: {{ ($avgScore / 5) * 100 }}%; height: 100%; background: {{ $avgScore >= 4 ? '#10b981' : ($avgScore >= 3 ? '#f59e0b' : '#ef4444') }};"></div>
                                    </div>
                                    <span style="font-family:'DM Mono',monospace; font-size:12px; font-weight:700;">{{ $avgScore }}/5</span>
                                </div>
                            </td>
                            <td class="um-td um-td-aksi" style="text-align:center; vertical-align: top; padding-top: 15px;">
                                <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="dk-action-btn view" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="um-empty">
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
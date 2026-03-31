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
            <strong>{{ auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja' }}</strong>.</p>
    </div>

    <!-- ═══════════════════════════════════════════════════
         BELUM DIEVALUASI
    ═══════════════════════════════════════════════════ -->
    <div class="card um-card" style="margin-bottom: 24px;">
        <div class="card-header um-header"
            style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;
                   background: linear-gradient(135deg, rgba(245,158,11,0.06), rgba(217,119,6,0.03));">
            <div class="um-title"
                style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <div style="width:32px; height:32px; border-radius:8px; background:rgba(245,158,11,0.12); color:#d97706;
                            display:flex; align-items:center; justify-content:center; font-size:14px;">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <span style="display:block; font-size:14px;">Menunggu Evaluasi</span>
                    <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Kegiatan yang belum Anda evaluasi</span>
                </div>
            </div>
            @if(isset($belumEvaluasi) && $belumEvaluasi->count() > 0)
                <span class="tag tag-orange" style="font-size:12px; padding:5px 14px;">
                    <i class="fas fa-exclamation-circle" style="font-size:10px;"></i>
                    {{ $belumEvaluasi->count() }} kegiatan
                </span>
            @endif
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Nama Kegiatan</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Jenis Kerjasama</th>
                            <th class="um-th">Tanggal Mulai</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($belumEvaluasi ?? collect()) as $index => $kegiatan)
                            <tr class="um-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-name">{{ $kegiatan->nama_kegiatan ?? '-' }}</span>
                                </td>
                                <td class="um-td" style="font-size:12px;">
                                    {{ $kegiatan->mitras->pluck('nama_mitra')->join(', ') ?: '-' }}
                                </td>
                                <td class="um-td">
                                    <span class="tag tag-purple" style="font-size: 11px;">
                                        <i class="fas fa-handshake" style="font-size:9px; margin-right:4px;"></i>
                                        {{ $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}
                                    </span>
                                </td>
                                <td class="um-td" style="font-size:12px; color:var(--text-sub);">
                                    {{ $kegiatan->periode_mulai ? $kegiatan->periode_mulai->format('d M Y') : '-' }}
                                </td>
                                <td class="um-td um-td-aksi" style="text-align:center;">
                                    <a href="{{ route('unit.evaluasi.form', $kegiatan->id) }}" data-turbo="false"
                                        style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px;
                                               background:linear-gradient(135deg,var(--accent),var(--accent2));
                                               color:#fff; border-radius:8px; font-size:11px; font-weight:700;
                                               text-decoration:none; transition:all .2s;
                                               box-shadow:0 2px 8px rgba(79,70,229,.25);"
                                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(79,70,229,.35)'"
                                        onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(79,70,229,.25)'">
                                        <i class="fas fa-pen-to-square" style="font-size:10px;"></i>
                                        Beri Evaluasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state">
                                        <div class="um-empty-icon">
                                            <i class="fas fa-circle-check"
                                                style="font-size: 28px; opacity: 0.4; color: #10b981;"></i>
                                        </div>
                                        <p class="um-empty-title">Semua Tugas Selesai!</p>
                                        <p class="um-empty-sub">Tidak ada kegiatan yang perlu dievaluasi saat ini.</p>
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
         SUDAH DIEVALUASI
    ═══════════════════════════════════════════════════ -->
    <div class="card um-card">
        <div class="card-header um-header"
            style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
            <div class="um-title"
                style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <div style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10b981;
                            display:flex; align-items:center; justify-content:center; font-size:14px;">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <span style="display:block; font-size:14px;">Sudah Dievaluasi</span>
                    <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Riwayat evaluasi yang telah dilakukan</span>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Nama Kegiatan</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Status</th>
                            <th class="um-th">Rata-rata Skor</th>
                            <th class="um-th um-th-aksi" style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($evaluasiList ?? collect()) as $index => $kegiatan)
                            @php
                                $eval = $kegiatan->evaluasis->first();
                                $avgScore = $eval
                                    ? round(($eval->kualitas + $eval->keterlibatan + $eval->efisiensi + $eval->kepuasan) / 4, 1)
                                    : 0;
                            @endphp
                            <tr class="um-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-name">{{ $kegiatan->nama_kegiatan ?? '-' }}</span>
                                </td>
                                <td class="um-td" style="font-size:12px;">
                                    {{ $kegiatan->mitras->pluck('nama_mitra')->join(', ') ?: '-' }}
                                </td>
                                <td class="um-td">
                                    <span class="tag {{ $kegiatan->status_class ?? 'tag-blue' }}">
                                        <i class="fas fa-circle" style="font-size:6px;"></i>
                                        {{ $kegiatan->status_label ?? $kegiatan->status ?? '-' }}
                                    </span>
                                </td>
                                <td class="um-td">
                                    <span style="font-family:'DM Mono',monospace; font-size:13px; font-weight:700;
                                                 color: {{ $avgScore >= 4 ? '#10b981' : ($avgScore >= 3 ? '#f59e0b' : '#ef4444') }};">
                                        {{ $avgScore }}/5
                                    </span>
                                </td>
                                <td class="um-td um-td-aksi" style="text-align:center;">
                                    <a href="{{ route('unit.evaluasi.form', $kegiatan->id) }}" class="um-btn-edit" title="Detail" data-turbo="false"
                                        style="background: rgba(79,70,229,.12); color: #4f46e5;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state">
                                        <div class="um-empty-icon">
                                            <i class="fas fa-clipboard-check"
                                                style="font-size: 28px; opacity: 0.3; color: var(--text-sub);"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada evaluasi</p>
                                        <p class="um-empty-sub">Evaluasi akan muncul setelah Anda menilai kinerja kegiatan.</p>
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

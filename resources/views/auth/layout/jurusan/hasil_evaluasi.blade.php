<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current">Hasil Evaluasi</span>
        </div>
        <h2 id="pageTitle">Hasil Evaluasi Kerjasama</h2>
        <p id="pageDesc">Ringkasan evaluasi untuk <strong>{{ auth()->user()->profile?->jurusan?->nama_jurusan ?? 'Jurusan' }}</strong>.</p>
    </div>

    @if(session('success'))
        <div style="background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(5,150,105,.08)); border: 1px solid rgba(16,185,129,.3); color: #065f46; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle" style="font-size: 16px; color: #10b981;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="font-size: 16px; color: #ef4444;"></i>
            {{ session('error') }}
        </div>
    @endif

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
                    <span style="display:block; font-size:14px;">Daftar Hasil Evaluasi</span>
                    <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Hanya menampilkan kegiatan yang sudah memiliki evaluasi</span>
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
                                    <a href="{{ route('jurusan.evaluasi.form', $kegiatan->id) }}" class="um-btn-edit" title="Detail" data-turbo="false"
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
                                        <p class="um-empty-title">Belum ada hasil evaluasi</p>
                                        <p class="um-empty-sub">Hasil evaluasi akan tampil setelah Unit Kerja mengirimkan evaluasi.</p>
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
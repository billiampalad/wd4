<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current" id="breadcrumbCurrent">Dashboard Jurusan</span>
        </div>
        <h2 id="pageTitle">Selamat Datang, {{ auth()->user()->name }}</h2>
        <p id="pageDesc">Ringkasan data kerjasama untuk <strong>{{ auth()->user()->profile?->jurusan?->nama_jurusan ?? 'Jurusan' }}</strong></p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(79,70,229,0.1); color:#4f46e5;">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="stat-badge" style="background:rgba(79,70,229,0.1); color:#4f46e5; border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700;">
                    <i class="fas fa-info-circle" style="font-size:8px;"></i> Total
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value" style="font-size: 28px; font-weight: 800; color: var(--text); line-height: 1;">{{ $totalKerjasama }}</div>
                <div class="stat-label" style="font-size: 13px; color: var(--text-sub); font-weight: 600; margin-top: 6px;">Total Kerjasama</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div class="stat-badge" style="background:rgba(245,158,11,0.1); color:#f59e0b; border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700;">
                    <i class="fas fa-file-lines" style="font-size:8px;"></i> Draft
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value" style="font-size: 28px; font-weight: 800; color: var(--text); line-height: 1;">{{ $draftCount }}</div>
                <div class="stat-label" style="font-size: 13px; color: var(--text-sub); font-weight: 600; margin-top: 6px;">Draft Kerjasama</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-badge" style="background:rgba(16,185,129,0.1); color:#10b981; border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700;">
                    <i class="fas fa-check" style="font-size:8px;"></i> Selesai
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value" style="font-size: 28px; font-weight: 800; color: var(--text); line-height: 1;">{{ $sudahDievaluasi }}</div>
                <div class="stat-label" style="font-size: 13px; color: var(--text-sub); font-weight: 600; margin-top: 6px;">Total Selesai</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-badge" style="background:rgba(14,165,233,0.1); color:#0ea5e9; border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700;">
                    <i class="fas fa-spinner" style="font-size:8px;"></i> Menunggu
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value" style="font-size: 28px; font-weight: 800; color: var(--text); line-height: 1;">{{ $menungguEvaluasi }}</div>
                <div class="stat-label" style="font-size: 13px; color: var(--text-sub); font-weight: 600; margin-top: 6px;">Menunggu Evaluasi</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-pie"></i> Perbandingan Mitra</div>
            </div>
            <div class="card-body" style="height: 300px;">
                <canvas id="mitraChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-bar"></i> Sebaran Jenis Kerjasama</div>
            </div>
            <div class="card-body" style="height: 300px;">
                <canvas id="jenisChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 28px;">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-chart-line"></i> Tren Kerjasama Per Tahun</div>
        </div>
        <div class="card-body" style="height: 300px;">
            <canvas id="trenChart"></canvas>
        </div>
    </div>

    <!-- Content Row -->
    <div class="content-row">

        <!-- Tabel Kerjasama Terbaru -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-list-alt"></i> Kerjasama Terbaru</div>
                <a href="{{ route('jurusan.dkerjasama') }}" class="card-action" style="text-decoration: none; color: var(--accent); font-size: 12px; font-weight: 600;">Lihat Semua</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kegiatan</th>
                            <th>Nomor MOU</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kerjasamaTerbaru as $index => $kegiatan)
                        <tr>
                            <td><span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span></td>
                            <td>
                                <div style="font-weight:600; font-size:13px;">{{ $kegiatan->nama_kegiatan }}</div>
                                <div style="font-size:11px; color:var(--text-sub);">PJ: {{ $kegiatan->penanggung_jawab ?? '-' }}</div>
                            </td>
                            <td style="font-size: 12px;">{{ $kegiatan->nomor_mou ?? '-' }}</td>
                            <td>
                                @php
                                    $statusClass = match($kegiatan->status) {
                                        'draft' => 'tag-orange',
                                        'menunggu_evaluasi' => 'tag-blue',
                                        'revisi' => 'tag-red',
                                        'selesai' => 'tag-green',
                                        default => 'tag-gray'
                                    };
                                    $statusLabel = match($kegiatan->status) {
                                        'draft' => 'Draft',
                                        'menunggu_evaluasi' => 'Menunggu',
                                        'revisi' => 'Revisi',
                                        'selesai' => 'Selesai',
                                        default => ucfirst($kegiatan->status)
                                    };
                                @endphp
                                <span class="tag {{ $statusClass }}"><i class="fas fa-circle" style="font-size:6px;"></i> {{ $statusLabel }}</span>
                            </td>
                            <td style="font-size: 12px; color: var(--text-sub);">{{ $kegiatan->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px; color: var(--text-sub);">Belum ada data kerjasama.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right column -->
        <div style="display:flex; flex-direction:column; gap:20px;">

            <!-- Notifikasi Terbaru -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-bell"></i> Pemberitahuan</div>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="notification-list">
                        @forelse($notifikasiTerbaru as $notif)
                        <div class="notification-item" style="padding: 15px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 12px; align-items: flex-start;">
                            <div class="notif-icon" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-info-circle" style="font-size: 14px;"></i>
                            </div>
                            <div class="notif-content">
                                <div style="font-weight: 600; font-size: 13px; color: var(--text);">{{ $notif->judul }}</div>
                                <div style="font-size: 12px; color: var(--text-sub); margin-top: 2px;">{{ $notif->pesan }}</div>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 4px;">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @empty
                        <div style="padding: 30px; text-align: center; color: var(--text-sub); font-size: 13px;">
                            <i class="fas fa-bell-slash" style="display: block; font-size: 24px; margin-bottom: 10px; opacity: 0.3;"></i>
                            Tidak ada notifikasi baru
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-bolt"></i> Aksi Cepat</div>
                </div>
                <div class="card-body" style="display: flex; flex-direction: column; gap: 10px; padding: 15px;">
                    <a href="{{ route('jurusan.dkerjasama') }}" class="btn-quick" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8fafc; border-radius: 8px; text-decoration: none; color: var(--text); font-size: 13px; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-plus-circle" style="color: var(--accent);"></i>
                        Input Kerjasama Baru
                    </a>
                    <a href="#" class="btn-quick" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8fafc; border-radius: 8px; text-decoration: none; color: var(--text); font-size: 13px; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-file-export" style="color: #10b981;"></i>
                        Unduh Laporan PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

@if(isset($mitraStats))
<div id="dashboardStatsData" 
    data-mitra='{!! json_encode($mitraStats) !!}'
    data-jenis='{!! json_encode($sebaranJenis) !!}'
    data-tren='{!! json_encode($trenKerjasama) !!}'
    style="display: none;">
</div>
@endif
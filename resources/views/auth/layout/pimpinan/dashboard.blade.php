<!-- Main Content -->
<link rel="stylesheet" href="{{ asset('css/auth/pimpinan/dashboard.css') }}">

<main id="mainContent">

    <!-- Page Header -->
    <div class="pimpinan-page-header">
        <!-- decorative background -->
        <div class="pimpinan-header-bg"></div>
        <div class="pimpinan-header-content">
            <div class="pimpinan-breadcrumb">
                <i class="fas fa-home"></i>
                <span class="sep">/</span>
                <span class="current" id="breadcrumbCurrent">Beranda</span>
            </div>
            <h2 id="pageTitle" class="pimpinan-page-title">Sistem Informasi Kerjasama (Executive)</h2>
            <p id="pageDesc" class="pimpinan-page-desc">Gambaran besar aktivitas kerjasama Politeknik Negeri Manado Tahun {{ now()->year }}</p>
        </div>
        
        <!-- Global Filter & Export -->
        @php
            $latestMonitoringId = \App\Models\Cooperation::latest()->value('id');
            $detailMonitoringUrl = $latestMonitoringId
                ? route('pimpinan.monitoring.detail', $latestMonitoringId)
                : route('pimpinan.monitoring');
        @endphp
        <div class="pimpinan-header-actions">
            <a href="{{ route('pimpinan.laporan') }}" class="pimpinan-btn-filter">
                <i class="fas fa-filter"></i> Filter Global
            </a>
            <a href="{{ $detailMonitoringUrl }}" class="pimpinan-btn-download">
                <i class="fas fa-file-pdf"></i> Download Laporan
            </a>
        </div>
    </div>

    <!-- 1. KEY PERFORMANCE INDICATORS -->
    <div class="pimpinan-stats-grid">
        
        <!-- Total Kerjasama Aktif -->
        <div class="pimpinan-stat-card">
            <div class="pimpinan-stat-bg-icon success"><i class="fas fa-check-circle"></i></div>
            <div class="pimpinan-stat-header">
                <div class="pimpinan-stat-icon success">
                    <i class="fas fa-handshake"></i>
                </div>
                <span class="pimpinan-stat-tag success">Aktif</span>
            </div>
            <h3 class="pimpinan-stat-value">{{ $totalKerjasamaAktif }}</h3>
            <p class="pimpinan-stat-desc">Total Kerjasama Aktif</p>
        </div>

        <!-- Total Mitra -->
        <div class="pimpinan-stat-card">
            <div class="pimpinan-stat-bg-icon primary"><i class="fas fa-building"></i></div>
            <div class="pimpinan-stat-header">
                <div class="pimpinan-stat-icon primary">
                    <i class="fas fa-building-user"></i>
                </div>
                <span class="pimpinan-stat-tag primary">Entitas</span>
            </div>
            <h3 class="pimpinan-stat-value">{{ $totalMitra }}</h3>
            <p class="pimpinan-stat-desc">Total Mitra Terdaftar</p>
        </div>

        <!-- Total Nilai Kontrak -->
        <div class="pimpinan-stat-card">
            <div class="pimpinan-stat-bg-icon warning"><i class="fas fa-coins"></i></div>
            <div class="pimpinan-stat-header">
                <div class="pimpinan-stat-icon warning">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="pimpinan-stat-tag warning">Income</span>
            </div>
            <h3 class="pimpinan-stat-value pimpinan-stat-value-medium">Rp {{ number_format($totalNilaiKontrak, 0, ',', '.') }}</h3>
            <p class="pimpinan-stat-desc">Total Nilai Kontrak Ekonomi</p>
        </div>

        <!-- Capaian Sasaran -->
        <div class="pimpinan-stat-card">
            <div class="pimpinan-stat-bg-icon purple"><i class="fas fa-bullseye"></i></div>
            <div class="pimpinan-stat-header">
                <div class="pimpinan-stat-icon purple">
                    <i class="fas fa-bullseye"></i>
                </div>
                <span class="pimpinan-stat-tag purple">Sasaran</span>
            </div>
            <div class="pimpinan-sasaran-list">
                @forelse($capaianSasaran->take(2) as $sasaran)
                <div>
                    <div class="pimpinan-sasaran-item">
                        <span class="pimpinan-sasaran-name" title="{{ $sasaran->nama_sasaran }}">{{ $sasaran->nama_sasaran }}</span>
                        <span class="pimpinan-sasaran-val">{{ $sasaran->total }}</span>
                    </div>
                    <div class="pimpinan-sasaran-bar-bg">
                        <div class="pimpinan-sasaran-bar-fill" style="width: {{ min(100, $sasaran->total * 5) }}%;"></div>
                    </div>
                </div>
                @empty
                <p class="pimpinan-sasaran-empty">Belum ada data sasaran.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 2. VISUALISASI DATA -->
    <div class="pimpinan-grid-2-1">
        
        <!-- Tren Kerjasama Tahunan -->
        <div class="pimpinan-card">
            <div class="pimpinan-card-header">
                <div>
                    <h3 class="pimpinan-card-title">Tren Kerjasama Tahunan</h3>
                    <p class="pimpinan-card-subtitle">Produktivitas kerjasama berdasarkan tanggal mulai.</p>
                </div>
                <div class="pimpinan-card-icon primary"><i class="fas fa-chart-line"></i></div>
            </div>
            <div class="pimpinan-chart-280">
                <canvas id="trenTahunanChart" data-tren='{!! json_encode($trenTahunan) !!}'></canvas>
            </div>
        </div>

        <!-- Distribusi Jenis Kerjasama -->
        <div class="pimpinan-card">
            <div class="pimpinan-card-header">
                <div>
                    <h3 class="pimpinan-card-title">Distribusi Dokumen</h3>
                    <p class="pimpinan-card-subtitle">MoU, MoA, vs IA.</p>
                </div>
                <div class="pimpinan-card-icon pink"><i class="fas fa-chart-pie"></i></div>
            </div>
            <div class="pimpinan-chart-250">
                <canvas id="distribusiJenisChart" data-jenis='{!! json_encode($distribusiJenis) !!}'></canvas>
            </div>
        </div>
    </div>

    <!-- 3. VISUALISASI TAMBAHAN & GEOGRAFIS -->
    <div class="pimpinan-grid-3">
        <!-- Top 5 Jurusan Teraktif -->
        <div class="pimpinan-card">
            <h3 class="pimpinan-card-title pimpinan-card-title-sm"><i class="fas fa-university" style="color: #6366f1; margin-right: 8px;"></i> Top 5 Jurusan Teraktif</h3>
            <div class="pimpinan-chart-200">
                <canvas id="topJurusanChart" data-jurusan='{!! json_encode($topJurusan) !!}'></canvas>
            </div>
        </div>

        <!-- Peta Klasifikasi Mitra -->
        <div class="pimpinan-card">
            <h3 class="pimpinan-card-title pimpinan-card-title-sm"><i class="fas fa-layer-group" style="color: #f59e0b; margin-right: 8px;"></i> Klasifikasi Mitra</h3>
            <div class="pimpinan-chart-200">
                <canvas id="klasifikasiMitraChart" data-klasifikasi='{!! json_encode($klasifikasiMitra) !!}'></canvas>
            </div>
        </div>

        <!-- Nasional vs Internasional -->
        <div class="pimpinan-card pimpinan-geo-card">
            <div class="pimpinan-stat-bg-icon info" style="font-size: 120px; right: -30px; bottom: -30px; top: auto;"><i class="fas fa-globe"></i></div>
            <h3 class="pimpinan-geo-title"><i class="fas fa-map-marker-alt" style="color: #0ea5e9; margin-right: 8px;"></i> Sebaran Geografis</h3>
            
            <div class="pimpinan-geo-content">
                <div class="pimpinan-geo-item">
                    <div class="pimpinan-geo-circle nasional">{{ $nasional }}</div>
                    <span class="pimpinan-geo-label">Nasional</span>
                </div>
                <div class="pimpinan-geo-divider"></div>
                <div class="pimpinan-geo-item">
                    <div class="pimpinan-geo-circle internasional">{{ $internasional }}</div>
                    <span class="pimpinan-geo-label">Internasional</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. ACTIONABLE INSIGHTS & RINGKASAN TUGAS HARI INI -->
    <div class="pimpinan-grid-1-2">
        
        <!-- Kolom Kiri: Alerts & Logs -->
        <div class="pimpinan-alert-col">
            <!-- Critical Alerts -->
            <div class="pimpinan-card pimpinan-card-no-pad">
                <div class="pimpinan-alert-header">
                    <div class="pimpinan-alert-icon"><i class="fas fa-bell"></i></div>
                    <h3 class="pimpinan-alert-title">Perhatian Segera</h3>
                </div>
                <div style="padding: 0;">
                    <div class="pimpinan-alert-item">
                        <div class="pimpinan-alert-item-left">
                            <div class="pimpinan-alert-dot danger"></div>
                            <span class="pimpinan-alert-label">Expiring Soon (< 60 hari)</span>
                        </div>
                        <span class="pimpinan-alert-badge danger">{{ count($expiringSoon) }}</span>
                    </div>
                    <div class="pimpinan-alert-item">
                        <div class="pimpinan-alert-item-left">
                            <div class="pimpinan-alert-dot warning"></div>
                            <span class="pimpinan-alert-label">Dalam Perpanjangan</span>
                        </div>
                        <span class="pimpinan-alert-badge warning">{{ count($dalamPerpanjangan) }}</span>
                    </div>
                    <div class="pimpinan-alert-item last">
                        <div class="pimpinan-alert-item-left">
                            <div class="pimpinan-alert-dot gray"></div>
                            <span class="pimpinan-alert-label">Dokumen Tanpa Link</span>
                        </div>
                        <span class="pimpinan-alert-badge gray">{{ count($dokumenTanpaLink) }}</span>
                    </div>
                </div>
            </div>

            <!-- Monitoring Implementasi -->
            <div class="pimpinan-card pimpinan-card-sm-pad">
                <h3 class="pimpinan-card-title pimpinan-card-title-sm"><i class="fas fa-history" style="color: #10b981; margin-right: 8px;"></i> Implementasi Terbaru</h3>
                <div class="pimpinan-impl-list">
                    @forelse($implementasiTerbaru as $imp)
                    <div class="pimpinan-impl-item">
                        <div class="pimpinan-impl-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="pimpinan-impl-title">{{ $imp->cooperation->title ?? 'Kegiatan Baru' }}</p>
                            <p class="pimpinan-impl-desc">{{ $imp->cooperation->mitra->nama_mitra ?? '-' }} &bull; {{ $imp->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="pimpinan-sasaran-empty" style="text-align: center; margin: 10px 0;">Belum ada data implementasi.</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Realisasi Luaran -->
            <div class="pimpinan-card pimpinan-card-sm-pad">
                <h3 class="pimpinan-card-title pimpinan-card-title-sm"><i class="fas fa-box-open" style="color: #8b5cf6; margin-right: 8px;"></i> Realisasi Luaran</h3>
                <div class="pimpinan-luaran-list">
                    @forelse($realisasiLuaran as $luaran)
                    <div class="pimpinan-luaran-item">
                        <span class="pimpinan-luaran-val">{{ $luaran->total_volume }}</span>
                        <span class="pimpinan-luaran-satuan">{{ $luaran->satuan_luaran }}</span>
                    </div>
                    @empty
                    <p class="pimpinan-sasaran-empty" style="text-align: center; margin: 10px 0; width: 100%;">Belum ada luaran yang direalisasikan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tabel Ringkasan untuk Pimpinan -->
        <div class="pimpinan-card pimpinan-table-card">
            <div class="pimpinan-table-header">
                <h3 class="pimpinan-table-title"><i class="fas fa-table" style="color: var(--accent); margin-right: 8px;"></i> Daftar Kerjasama (Executive View)</h3>
                <a href="{{ route('pimpinan.monitoring') }}" class="pimpinan-table-link">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="pimpinan-table-wrapper">
                <table class="pimpinan-table">
                    <thead>
                        <tr>
                            <th>Judul Kerjasama</th>
                            <th>Mitra</th>
                            <th>Jenis</th>
                            <th>Tgl Berakhir</th>
                            <th>Status</th>
                            <th class="right">Nilai Kontrak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allDocs = \App\Models\Cooperation::with(['mitra', 'details'])->latest()->take(8)->get();
                        @endphp
                        @forelse($allDocs as $doc)
                        <tr>
                            <td>
                                <div class="pimpinan-td-title" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                <div class="pimpinan-td-doc">{{ $doc->doc_number ?? 'No Doc' }}</div>
                            </td>
                            <td>
                                <div class="pimpinan-td-mitra">{{ $doc->mitra->nama_mitra ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="pimpinan-td-jenis">{{ $doc->jenis ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($doc->end_date)
                                <div class="pimpinan-td-date">{{ $doc->end_date->format('d M Y') }}</div>
                                @else
                                <div class="pimpinan-td-date empty">-</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColor = '#10b981'; $statusBg = 'rgba(16,185,129,0.1)';
                                    $statusText = 'Aktif';
                                    if($doc->status === 'aktif' && $doc->end_date && $doc->end_date < now()->addDays(60) && $doc->end_date >= now()) {
                                        $statusColor = '#f59e0b'; $statusBg = 'rgba(245,158,11,0.1)';
                                    } elseif($doc->status === 'aktif' && $doc->end_date && $doc->end_date < now()) {
                                        $statusColor = '#ef4444'; $statusBg = 'rgba(239,68,68,0.1)';
                                        $statusText = 'Kadarluarsa';
                                    } elseif($doc->status === 'proses') {
                                        $statusColor = '#f59e0b'; $statusBg = 'rgba(245,158,11,0.1)';
                                        $statusText = 'Perpanjangan';
                                    } elseif($doc->status_dokumen === 'Menunggu Evaluasi' || $doc->status_dokumen === 'Menunggu Validasi') {
                                        $statusColor = '#3b82f6'; $statusBg = 'rgba(59,130,246,0.1)';
                                        $statusText = $doc->status_dokumen;
                                    }
                                @endphp
                                <span class="pimpinan-td-status" style="background: {{ $statusBg }}; color: {{ $statusColor }};">
                                    <div class="pimpinan-td-status-dot" style="background: {{ $statusColor }};"></div>
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="pimpinan-td-nilai">
                                @php
                                    $nilai = $doc->details->sum('nilai_kontrak');
                                @endphp
                                {{ $nilai > 0 ? 'Rp ' . number_format($nilai, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="pimpinan-table-empty">Belum ada data kerjasama.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</main>

{{-- ── Chart.js scripts ──────────────────────────────────────── --}}

<script src="{{ asset('js/auth/pimpinan/dashboard.js') }}"></script>

<!-- Main Content -->
<main id="mainContent">

    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current" id="breadcrumbCurrent">Dashboard Eksekutif</span>
        </div>
        <h2 id="pageTitle">Selamat Datang, {{ auth()->user()->name }}</h2>
        <p id="pageDesc">Pusat Komando Monitoring Kerjasama Polimdo — Tahun {{ now()->year }}</p>
    </div>

    <!-- ════════════════════════════════════════════════════════
         1. WIDGET KARTU STATISTIK (Quick Stats)
    ════════════════════════════════════════════════════════ -->
    <div class="stats-grid">

        {{-- Total Kerja Sama Tahun Ini --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(79,70,229,0.1); color:#4f46e5;">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-badge"
                    style="background:rgba(79,70,229,0.1); color:#4f46e5; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-calendar-check" style="font-size:8px;"></i> {{ now()->year }}
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalKerjasamaTahunIni }}</div>
                <div class="stat-label">Total Kerja Sama (Tahun Ini)</div>
            </div>
        </div>

        {{-- Menunggu Evaluasi Pimpinan --}}
        <div class="stat-card"
            style="border-color: rgba(245,158,11,0.4); --card-accent: linear-gradient(to right,#f59e0b,#d97706);">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(245,158,11,0.12); color:#f59e0b;">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-badge"
                    style="background:rgba(245,158,11,0.15); color:#d97706; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-exclamation-circle" style="font-size:8px;"></i> Antrean
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value" style="color:#d97706;">{{ $menungguEvaluasi }}</div>
                <div class="stat-label">Menunggu Evaluasi Pimpinan</div>
            </div>
        </div>

        {{-- Menunggu Validasi Akhir --}}
        <div class="stat-card"
            style="border-color: rgba(239,68,68,0.3); --card-accent: linear-gradient(to right,#ef4444,#dc2626);">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(239,68,68,0.1); color:#ef4444;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-badge"
                    style="background:rgba(239,68,68,0.12); color:#ef4444; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-clock" style="font-size:8px;"></i> Validasi
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value" style="color:#ef4444;">{{ $menungguValidasi }}</div>
                <div class="stat-label">Menunggu Validasi Akhir</div>
            </div>
        </div>

        {{-- Internasional vs Nasional --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;">
                    <i class="fas fa-globe-asia"></i>
                </div>
                <div class="stat-badge"
                    style="background:rgba(14,165,233,0.1); color:#0ea5e9; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-chart-pie" style="font-size:8px;"></i> Sebaran
                </div>
            </div>
            <div class="stat-content">
                <div style="display:flex; align-items:baseline; gap:8px;">
                    <div class="stat-value" style="font-size:22px;">{{ $internasional }}</div>
                    <span style="font-size:11px; font-weight:600; color:var(--text-sub);">Internasional</span>
                    <span style="font-size:18px; font-weight:300; color:var(--border); margin:0 2px;">|</span>
                    <div class="stat-value" style="font-size:22px;">{{ $nasional }}</div>
                    <span style="font-size:11px; font-weight:600; color:var(--text-sub);">Nasional</span>
                </div>
                <div class="stat-label">Internasional vs Nasional</div>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════
         2. VISUALISASI GRAFIK (Monitoring Kinerja)
    ════════════════════════════════════════════════════════ -->

    {{-- A. Tren Kerjasama Per Bulan + B. Sebaran Jenis --}}
    <div class="content-row" style="grid-template-columns: 1.3fr 1fr;">

        {{-- Tren Kerjasama (Line/Bar Chart) --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="gap:10px;">
                    <div
                        style="width:32px; height:32px; border-radius:8px; background:rgba(79,70,229,0.1); color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px; font-weight:700; color:var(--text);">Tren Kerja Sama
                            {{ now()->year }}</span>
                        <span
                            style="display:block; font-size:11px; font-weight:500; color:var(--text-sub); margin-top:1px;">Jumlah
                            kerjasama baru per bulan</span>
                    </div>
                </div>
                <span class="tag tag-purple" style="font-size:10px;">
                    <i class="fas fa-calendar-alt" style="font-size:8px;"></i> Tahun Berjalan
                </span>
            </div>
            <div class="card-body" style="height:320px; padding:20px;">
                <canvas id="trenBulananChart" data-tren='{!! json_encode($trenPerBulan) !!}' data-year="{{ now()->year }}"></canvas>
            </div>
        </div>

        {{-- Distribusi Sebaran Mitra (Donut Chart) --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="gap:10px;">
                    <div
                        style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px; font-weight:700; color:var(--text);">Sebaran Jenis
                            Kerjasama</span>
                        <span
                            style="display:block; font-size:11px; font-weight:500; color:var(--text-sub); margin-top:1px;">Persentase
                            jenis yang sering dilakukan</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="height:320px; padding:20px;">
                <canvas id="jenisDonutChart" data-jenis='{!! json_encode($sebaranJenis) !!}'></canvas>
            </div>
        </div>
    </div>

    {{-- C. Kinerja Jurusan & Unit Pelaksana --}}
    <div class="content-row" style="grid-template-columns: 1fr 1fr;">

        {{-- Kinerja Jurusan --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="gap:10px;">
                    <div
                        style="width:32px; height:32px; border-radius:8px; background:rgba(124,58,237,0.1); color:#7c3aed; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px; font-weight:700; color:var(--text);">Kinerja
                            Jurusan</span>
                        <span
                            style="display:block; font-size:11px; font-weight:500; color:var(--text-sub); margin-top:1px;">Ranking
                            jurusan paling aktif</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="height:300px; padding:20px;">
                <canvas id="kinerjaJurusanChart" data-jurusan='{!! json_encode($kinerjaJurusan) !!}'></canvas>
            </div>
        </div>

        {{-- Kinerja Unit Kerja --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="gap:10px;">
                    <div
                        style="width:32px; height:32px; border-radius:8px; background:rgba(14,165,233,0.1); color:#0ea5e9; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px; font-weight:700; color:var(--text);">Kinerja Unit
                            Pelaksana</span>
                        <span
                            style="display:block; font-size:11px; font-weight:500; color:var(--text-sub); margin-top:1px;">Ranking
                            unit kerja paling aktif</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="height:300px; padding:20px;">
                <canvas id="kinerjaUnitChart" data-unit='{!! json_encode($kinerjaUnit) !!}'></canvas>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════
         3. TABEL AKSI CEPAT (Actionable Table)
    ════════════════════════════════════════════════════════ -->
    <div class="content-row" style="grid-template-columns: 1fr;">
        <div class="card" style="margin-bottom: 28px;">
            <div class="card-header"
                style="background: linear-gradient(135deg, rgba(239,68,68,0.04), rgba(245,158,11,0.04));">
                <div class="card-title" style="gap:10px;">
                    <div
                        style="width:32px; height:32px; border-radius:8px; background:rgba(239,68,68,0.1); color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px; font-weight:700; color:var(--text);">Dokumen
                            Menunggu Tindakan</span>
                        <span
                            style="display:block; font-size:11px; font-weight:500; color:var(--text-sub); margin-top:1px;">Dokumen
                            yang membutuhkan aksi evaluasi atau validasi Anda</span>
                    </div>
                </div>
                @if(($menungguEvaluasi + $menungguValidasi) > 0)
                    <span class="tag tag-red" style="font-size:12px; padding:5px 14px;">
                        <i class="fas fa-exclamation-triangle" style="font-size:10px;"></i>
                        {{ $menungguEvaluasi + $menungguValidasi }} dokumen
                    </span>
                @endif
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Tanggal Masuk</th>
                            <th>Pengusul</th>
                            <th>Nama Kegiatan</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center; width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumenMenunggu as $index => $kegiatan)
                            <tr>
                                <td>
                                    <span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td style="font-size:12px; color:var(--text-sub);">
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <i class="far fa-calendar" style="font-size:11px; color:var(--accent);"></i>
                                        {{ $kegiatan->created_at->format('d M Y') }}
                                    </div>
                                    <div style="font-size:10px; color:var(--text-sub); margin-top:2px;">
                                        {{ $kegiatan->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $pengusul = $kegiatan->jurusans->pluck('nama_jurusan')->join(', ');
                                        if (!$pengusul) {
                                            $pengusul = $kegiatan->unitKerjas->pluck('nama_unit_pelaksana')->join(', ');
                                        }
                                    @endphp
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div
                                            style="width:28px; height:28px; border-radius:8px; background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0;">
                                            {{ mb_substr($pengusul ?: '?', 0, 2) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600; font-size:12px; color:var(--text);">
                                                {{ $pengusul ?: '-' }}</div>
                                            <div style="font-size:10px; color:var(--text-sub);">
                                                {{ $kegiatan->jurusans->count() > 0 ? 'Jurusan' : 'Unit Kerja' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:600; font-size:13px; color:var(--text);">
                                        {{ $kegiatan->nama_kegiatan }}</div>
                                    <div style="font-size:11px; color:var(--text-sub); margin-top:2px;">
                                        PJ: {{ $kegiatan->penanggung_jawab ?? '-' }}
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    @if($kegiatan->status === 'menunggu_evaluasi')
                                        <span class="tag tag-orange" style="font-size:10px; padding:4px 10px;">
                                            <i class="fas fa-clipboard-list" style="font-size:8px;"></i>
                                            Menunggu Evaluasi
                                        </span>
                                    @elseif($kegiatan->status === 'menunggu_validasi')
                                        <span class="tag tag-blue" style="font-size:10px; padding:4px 10px;">
                                            <i class="fas fa-check-circle" style="font-size:8px;"></i>
                                            Menunggu Validasi
                                        </span>
                                    @else
                                        <span class="tag"
                                            style="font-size:10px; padding:4px 10px;">{{ $kegiatan->status_label }}</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <a href="#"
                                        style="display:inline-flex; align-items:center; gap:6px; padding:7px 16px; background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; border-radius:8px; font-size:11px; font-weight:700; text-decoration:none; transition:all .2s; box-shadow:0 2px 8px rgba(79,70,229,.25);"
                                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 14px rgba(79,70,229,.4)'"
                                        onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(79,70,229,.25)'">
                                        <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                                        Proses Sekarang
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:50px 20px;">
                                    <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                                        <div
                                            style="width:56px; height:56px; border-radius:14px; background:rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:24px;">
                                            <i class="fas fa-circle-check"></i>
                                        </div>
                                        <span style="font-size:14px; font-weight:700; color:var(--text);">Semua Dokumen
                                            Telah Diproses!</span>
                                        <span
                                            style="font-size:12px; color:var(--text-sub); max-width:360px; line-height:1.5;">
                                            Tidak ada dokumen yang memerlukan tindakan saat ini. Sistem akan menampilkan
                                            dokumen baru ketika masuk.
                                        </span>
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

{{-- ── Chart.js scripts ──────────────────────────────────────── --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
        const textColor = isDark ? '#8b92a8' : '#6b7280';
        const surfaceColor = isDark ? '#1a1d2e' : '#ffffff';

        const namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // ═══ A. TREN KERJASAMA PER BULAN (Bar + Line hybrid) ═══
        const trenCtx = document.getElementById('trenBulananChart');
        if (trenCtx) {
            // Map data bulan ke array 12 elemen
            const rawTren = JSON.parse(trenCtx.getAttribute('data-tren') || '[]');
            const currentYear = trenCtx.getAttribute('data-year') || new Date().getFullYear();
            const trenData = new Array(12).fill(0);
            rawTren.forEach(item => { trenData[item.bulan - 1] = item.total; });

            new Chart(trenCtx, {
                type: 'bar',
                data: {
                    labels: namaBulan,
                    datasets: [{
                        label: 'Kerjasama Baru',
                        data: trenData,
                        backgroundColor: function (ctx) {
                            const chart = ctx.chart;
                            const { ctx: canvasCtx, chartArea } = chart;
                            if (!chartArea) return 'rgba(79,70,229,0.7)';
                            const gradient = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(79,70,229,0.3)');
                            gradient.addColorStop(1, 'rgba(124,58,237,0.8)');
                            return gradient;
                        },
                        borderColor: '#4f46e5',
                        borderWidth: 0,
                        borderRadius: { topLeft: 6, topRight: 6 },
                        borderSkipped: false,
                        maxBarThickness: 36,
                        hoverBackgroundColor: 'rgba(79,70,229,0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: surfaceColor,
                            titleColor: isDark ? '#e8eaf6' : '#1a1d2e',
                            bodyColor: isDark ? '#8b92a8' : '#6b7280',
                            borderColor: isDark ? '#2a2f45' : '#e4e7f0',
                            borderWidth: 1,
                            cornerRadius: 10,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                title: function (items) { return namaBulan[items[0].dataIndex] + ' ' + currentYear; },
                                label: function (ctx) { return ' ' + ctx.parsed.y + ' kerjasama baru'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: textColor, font: { size: 11, weight: 600 } },
                            grid: { color: gridColor, drawBorder: false }
                        },
                        x: {
                            ticks: { color: textColor, font: { size: 11, weight: 600 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // ═══ B. SEBARAN JENIS KERJASAMA (Donut Chart) ═══
        const jenisCtx = document.getElementById('jenisDonutChart');
        if (jenisCtx) {
            const rawJenis = JSON.parse(jenisCtx.getAttribute('data-jenis') || '[]');
            const jenisLabels = rawJenis.map(j => j.nama_kerjasama);
            const jenisData = rawJenis.map(j => j.total);
            const jenisColors = [
                '#4f46e5', '#10b981', '#f59e0b', '#ef4444',
                '#0ea5e9', '#8b5cf6', '#ec4899', '#14b8a6',
                '#f97316', '#6366f1'
            ];

            new Chart(jenisCtx, {
                type: 'doughnut',
                data: {
                    labels: jenisLabels,
                    datasets: [{
                        data: jenisData,
                        backgroundColor: jenisColors.slice(0, jenisData.length),
                        borderWidth: 3,
                        borderColor: surfaceColor,
                        hoverBorderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    animation: { animateRotate: true, duration: 1000 },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                font: { size: 11, weight: 600 },
                                padding: 14,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: surfaceColor,
                            titleColor: isDark ? '#e8eaf6' : '#1a1d2e',
                            bodyColor: isDark ? '#8b92a8' : '#6b7280',
                            borderColor: isDark ? '#2a2f45' : '#e4e7f0',
                            borderWidth: 1,
                            cornerRadius: 10,
                            padding: 12,
                            callbacks: {
                                label: function (ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                                    return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ═══ C. KINERJA JURUSAN (Horizontal Bar) ═══
        const jurusanCtx = document.getElementById('kinerjaJurusanChart');
        if (jurusanCtx) {
            const rawJurusan = JSON.parse(jurusanCtx.getAttribute('data-jurusan') || '[]');
            const jurusanLabels = rawJurusan.map(j => j.nama_jurusan);
            const jurusanData = rawJurusan.map(j => j.total);
            const jurusanColors = [
                'rgba(124,58,237,0.75)', 'rgba(79,70,229,0.7)', 'rgba(99,102,241,0.65)',
                'rgba(139,92,246,0.6)', 'rgba(167,139,250,0.55)', 'rgba(196,181,253,0.5)'
            ];

            new Chart(jurusanCtx, {
                type: 'bar',
                data: {
                    labels: jurusanLabels,
                    datasets: [{
                        label: 'Jumlah Kegiatan',
                        data: jurusanData,
                        backgroundColor: jurusanColors.slice(0, jurusanData.length),
                        borderColor: '#7c3aed',
                        borderWidth: 0,
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 28
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: surfaceColor,
                            titleColor: isDark ? '#e8eaf6' : '#1a1d2e',
                            bodyColor: isDark ? '#8b92a8' : '#6b7280',
                            borderColor: isDark ? '#2a2f45' : '#e4e7f0',
                            borderWidth: 1,
                            cornerRadius: 10,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function (ctx) { return ' ' + ctx.parsed.x + ' kegiatan kerjasama'; }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: textColor, font: { size: 11, weight: 600 } },
                            grid: { color: gridColor, drawBorder: false }
                        },
                        y: {
                            ticks: { color: textColor, font: { size: 11, weight: 600 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // ═══ C2. KINERJA UNIT KERJA (Horizontal Bar) ═══
        const unitCtx = document.getElementById('kinerjaUnitChart');
        if (unitCtx) {
            const rawUnit = JSON.parse(unitCtx.getAttribute('data-unit') || '[]');
            const unitLabels = rawUnit.map(u => u.nama_unit_pelaksana);
            const unitData = rawUnit.map(u => u.total);
            const unitColors = [
                'rgba(14,165,233,0.75)', 'rgba(6,182,212,0.7)', 'rgba(34,211,238,0.65)',
                'rgba(56,189,248,0.6)', 'rgba(125,211,252,0.55)', 'rgba(186,230,253,0.5)'
            ];

            new Chart(unitCtx, {
                type: 'bar',
                data: {
                    labels: unitLabels,
                    datasets: [{
                        label: 'Jumlah Kegiatan',
                        data: unitData,
                        backgroundColor: unitColors.slice(0, unitData.length),
                        borderColor: '#0ea5e9',
                        borderWidth: 0,
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 28
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: surfaceColor,
                            titleColor: isDark ? '#e8eaf6' : '#1a1d2e',
                            bodyColor: isDark ? '#8b92a8' : '#6b7280',
                            borderColor: isDark ? '#2a2f45' : '#e4e7f0',
                            borderWidth: 1,
                            cornerRadius: 10,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function (ctx) { return ' ' + ctx.parsed.x + ' kegiatan kerjasama'; }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: textColor, font: { size: 11, weight: 600 } },
                            grid: { color: gridColor, drawBorder: false }
                        },
                        y: {
                            ticks: { color: textColor, font: { size: 11, weight: 600 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>

<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current">Dashboard Unit Kerja</span>
        </div>
        <h2 id="pageTitle">Selamat Datang, {{ auth()->user()->name }}</h2>
        <p id="pageDesc">Ringkasan data kerjasama untuk <strong>{{ auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja' }}</strong></p>
    </div>

    <!-- ════════════════════════════════════════════════════════
         1. QUICK STATS
    ════════════════════════════════════════════════════════ -->
    <div class="stats-grid">
    
        {{-- Total Kerjasama --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(79,70,229,0.1); color:#4f46e5;">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="stat-badge" style="background:rgba(79,70,229,0.1); color:#4f46e5; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-layer-group" style="font-size:8px;"></i> Total
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalKerjasama }}</div>
                <div class="stat-label">Total Kerjasama</div>
            </div>
        </div>

        {{-- Menunggu Evaluasi Anda — WARNING color --}}
        <div class="stat-card" style="border-color: rgba(245,158,11,0.4); --card-accent: linear-gradient(to right,#f59e0b,#d97706);">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(245,158,11,0.12); color:#f59e0b;">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-badge" style="background:rgba(245,158,11,0.12); color:#d97706; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-exclamation-circle" style="font-size:8px;"></i> Aksi
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value" style="color:#d97706;">{{ $menungguEvaluasi }}</div>
                <div class="stat-label">Menunggu Evaluasi Anda</div>
            </div>
        </div>

        {{-- Menunggu Validasi Pimpinan --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-badge" style="background:rgba(14,165,233,0.1); color:#0ea5e9; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-spinner" style="font-size:8px;"></i> Proses
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $menungguValidasi }}</div>
                <div class="stat-label">Menunggu Validasi Pimpinan</div>
            </div>
        </div>

        {{-- Selesai / Tervalidasi --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-badge" style="background:rgba(16,185,129,0.1); color:#10b981; border-radius:6px; padding:2px 8px; font-size:10px; font-weight:700;">
                    <i class="fas fa-check" style="font-size:8px;"></i> Selesai
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $selesai }}</div>
                <div class="stat-label">Selesai / Tervalidasi</div>
            </div>
        </div>

    </div>

    <!-- ════════════════════════════════════════════════════════
         2. TABEL ACTION REQUIRED — "Tugas Perlu Diselesaikan"
    ════════════════════════════════════════════════════════ -->
    <div class="content-row" style="grid-template-columns: 1fr;">
        <div class="card" style="margin-bottom: 28px;">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(245,158,11,0.06), rgba(217,119,6,0.03));">
                <div class="card-title" style="gap:10px;">
                    <div style="width:32px; height:32px; border-radius:8px; background:rgba(245,158,11,0.12); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px; font-weight:700; color:var(--text);">Tugas Perlu Diselesaikan</span>
                        <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub); margin-top:1px;">Kegiatan yang belum Anda evaluasi</span>
                    </div>
                </div>
                @if($menungguEvaluasi > 0)
                    <span class="tag tag-orange" style="font-size:12px; padding:5px 14px;">
                        <i class="fas fa-exclamation-circle" style="font-size:10px;"></i>
                        {{ $menungguEvaluasi }} kegiatan
                    </span>
                @endif
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kegiatan</th>
                            <th>Mitra</th>
                            <th>Tanggal Mulai</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tugasEvaluasi as $index => $kegiatan)
                        <tr>
                            <td>
                                <span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">{{ str_pad($index+1, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600; font-size:13px;">{{ $kegiatan->nama_kegiatan }}</div>
                                <div style="font-size:11px; color:var(--text-sub);">PJ: {{ $kegiatan->penanggung_jawab ?? '-' }}</div>
                            </td>
                            <td style="font-size:12px;">
                                {{ $kegiatan->mitras->pluck('nama_mitra')->join(', ') ?: '-' }}
                            </td>
                            <td style="font-size:12px; color:var(--text-sub);">
                                {{ $kegiatan->periode_mulai ? $kegiatan->periode_mulai->format('d M Y') : '-' }}
                            </td>
                            <td style="text-align:center;">
                                <a href="#" style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; border-radius:8px; font-size:11px; font-weight:700; text-decoration:none; transition:all .2s; box-shadow:0 2px 8px rgba(79,70,229,.25);"
                                   onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(79,70,229,.35)'"
                                   onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(79,70,229,.25)'">
                                    <i class="fas fa-pen-to-square" style="font-size:10px;"></i>
                                    Beri Evaluasi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:40px 20px;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                    <div style="width:48px; height:48px; border-radius:12px; background:rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                        <i class="fas fa-circle-check"></i>
                                    </div>
                                    <span style="font-size:13px; font-weight:600; color:var(--text);">Semua Tugas Selesai!</span>
                                    <span style="font-size:12px; color:var(--text-sub);">Tidak ada kegiatan yang perlu dievaluasi saat ini.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════
         3. GRAFIK RATA-RATA EVALUASI
    ════════════════════════════════════════════════════════ -->
    <div class="content-row" style="grid-template-columns: 1fr 340px;">

        {{-- Bar Chart --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-bar"></i> Rata-rata Nilai Evaluasi</div>
                <span class="tag tag-purple" style="font-size:10px;">
                    <i class="fas fa-calendar-alt" style="font-size:8px;"></i> Keseluruhan
                </span>
            </div>
            <div class="card-body" style="height:300px; padding:20px;">
                <canvas id="evaluasiChart"></canvas>
            </div>
        </div>

        {{-- Ringkasan Skor --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-star-half-alt"></i> Ringkasan Skor</div>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:16px; padding:20px;">
                @php
                    $scores = [
                        ['label' => 'Kualitas',      'value' => $avgEvaluasi->avg_kualitas ?? 0,      'color' => '#4f46e5', 'icon' => 'fa-gem'],
                        ['label' => 'Keterlibatan',   'value' => $avgEvaluasi->avg_keterlibatan ?? 0,  'color' => '#0ea5e9', 'icon' => 'fa-users'],
                        ['label' => 'Efisiensi',      'value' => $avgEvaluasi->avg_efisiensi ?? 0,     'color' => '#10b981', 'icon' => 'fa-bolt'],
                        ['label' => 'Kepuasan',       'value' => $avgEvaluasi->avg_kepuasan ?? 0,      'color' => '#f59e0b', 'icon' => 'fa-smile'],
                    ];
                @endphp

                @foreach($scores as $s)
                <div>
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                        <span style="font-size:12px; font-weight:600; color:var(--text); display:flex; align-items:center; gap:6px;">
                            <i class="fas {{ $s['icon'] }}" style="color:{{ $s['color'] }}; font-size:11px;"></i>
                            {{ $s['label'] }}
                        </span>
                        <span style="font-family:'DM Mono',monospace; font-size:13px; font-weight:700; color:{{ $s['color'] }};">{{ $s['value'] }}/5</span>
                    </div>
                    <div style="width:100%; height:8px; background:var(--surface2); border-radius:99px; overflow:hidden;">
                        <div style="width:{{ ($s['value']/5)*100 }}%; height:100%; background:{{ $s['color'] }}; border-radius:99px; transition:width .6s cubic-bezier(.4,0,.2,1);"></div>
                    </div>
                </div>
                @endforeach

                <div style="margin-top:auto; padding-top:12px; border-top:1px solid var(--border);">
                    @php $overall = $avgEvaluasi ? round((($avgEvaluasi->avg_kualitas ?? 0)+($avgEvaluasi->avg_keterlibatan ?? 0)+($avgEvaluasi->avg_efisiensi ?? 0)+($avgEvaluasi->avg_kepuasan ?? 0))/4, 1) : 0; @endphp
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:13px; font-weight:700; color:var(--text);">Rata-rata Keseluruhan</span>
                        <span style="font-size:20px; font-weight:800; background:linear-gradient(135deg,var(--accent),var(--accent2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">{{ $overall }}/5</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

{{-- ── Chart.js script ──────────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('evaluasiChart');
    if (!ctx) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#8b92a8' : '#6b7280';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Kualitas', 'Keterlibatan', 'Efisiensi', 'Kepuasan'],
            datasets: [{
                label: 'Rata-rata Skor',
                data: [
                    {{ $avgEvaluasi->avg_kualitas ?? 0 }},
                    {{ $avgEvaluasi->avg_keterlibatan ?? 0 }},
                    {{ $avgEvaluasi->avg_efisiensi ?? 0 }},
                    {{ $avgEvaluasi->avg_kepuasan ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(79,70,229,0.75)',
                    'rgba(14,165,233,0.75)',
                    'rgba(16,185,129,0.75)',
                    'rgba(245,158,11,0.75)'
                ],
                borderColor: [
                    '#4f46e5',
                    '#0ea5e9',
                    '#10b981',
                    '#f59e0b'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 52
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1e2333' : '#fff',
                    titleColor: isDark ? '#e8eaf6' : '#1a1d2e',
                    bodyColor: isDark ? '#8b92a8' : '#6b7280',
                    borderColor: isDark ? '#2a2f45' : '#e4e7f0',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 12,
                    callbacks: {
                        label: ctx => ' Skor: ' + ctx.parsed.y + ' / 5'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
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
});
</script>
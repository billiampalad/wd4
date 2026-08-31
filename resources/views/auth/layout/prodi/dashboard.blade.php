@php
$user = auth()->user();
$prodi = $user->profile?->prodi;
$prodiName = $prodi->nama_prodi ?? 'Program Studi';

$penempatanList = $penempatans ?? collect();
if ($penempatanList->isEmpty() && $user->profile?->prodi_id) {
    $penempatanList = \App\Models\KegiatanMahasiswa::with(['mahasiswa', 'kegiatan', 'mitra'])
        ->whereHas('mahasiswa', fn($q) => $q->where('prodi_id', $user->profile->prodi_id))
        ->orderBy('created_at', 'desc')
        ->get();
}

$totalMhsAktif = $totalMahasiswaAktif ?? $penempatanList->where('status', 'Aktif')->count();
$totalSemuaPenempatan = $penempatanList->count();
$totalAlumniCount = $totalAlumni ?? \App\Models\Alumni::where('prodi_id', $user->profile?->prodi_id)->count();
$alumniBekerjaCount = $alumniBekerja ?? 0;
$persenIKU = $persentasePenyerapan ?? ($totalAlumniCount > 0 ? round(($alumniBekerjaCount / $totalAlumniCount) * 100, 1) : 0);

// Chart data
$chartStatusDistribusi = $statusDistribusi ?? [
    'Aktif' => $penempatanList->where('status', 'Aktif')->count(),
    'Selesai' => $penempatanList->where('status', 'Selesai')->count(),
    'Dibatalkan' => $penempatanList->where('status', 'Dibatalkan')->count(),
];
$chartTrendTahunan = $trendTahunan ?? ['labels' => [], 'data' => []];
$recentMhs = $recentPenempatans ?? $penempatanList->take(5);
$recentAlumni = $recentAlumniQuery ?? collect();
$jenisKegiatanData = $jenisKegiatan ?? [];
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/auth/dashboard.css') }}" data-turbo-track="reload">

<style>
/* ── PRODI DASHBOARD SPECIFIC STYLES ─────────────────────── */
.pd-grid-charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}
.pd-chart-panel {
    background: var(--surface, #fff);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04), 0 2px 4px -1px rgba(0,0,0,0.02);
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.pd-chart-panel:hover {
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.pd-chart-head {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
}
.pd-chart-icon {
    width: 44px; height: 44px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.pd-chart-icon.donut { background: rgba(99,102,241,0.1); color: #6366f1; }
.pd-chart-icon.bar   { background: rgba(16,185,129,0.1); color: #10b981; }
.pd-chart-icon.hbar  { background: rgba(245,158,11,0.1); color: #f59e0b; }
.pd-chart-head h4 {
    margin: 0; font-size: 16px; font-weight: 800;
    color: var(--text, #1e293b);
    letter-spacing: -0.01em;
}
.pd-chart-head p {
    margin: 2px 0 0; font-size: 12px; color: var(--text-sub, #64748b);
}
.pd-chart-canvas-wrap {
    position: relative; width: 100%;
}
.pd-chart-canvas-wrap canvas {
    max-height: 280px;
}

/* ── QUICK ACTION CARDS ────────────────────────────────────── */
.pd-quick-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}
.pd-quick-card {
    display: flex; align-items: center; gap: 18px;
    padding: 24px;
    background: var(--surface, #fff);
    border: 2px solid var(--border, #e2e8f0);
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.pd-quick-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px -5px rgba(0,0,0,0.1);
}
.pd-quick-card:hover .pd-quick-icon {
    transform: scale(1.1);
}
.pd-quick-icon {
    width: 54px; height: 54px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}
.pd-quick-icon.indigo  { background: rgba(79,70,229,0.1);  color: #4f46e5; }
.pd-quick-icon.emerald { background: rgba(16,185,129,0.1); color: #10b981; }
.pd-quick-icon.amber   { background: rgba(245,158,11,0.1); color: #f59e0b; }
.pd-quick-label {
    display: block; font-size: 15px; font-weight: 800;
    color: var(--text, #1e293b); margin-bottom: 3px;
}
.pd-quick-desc {
    margin: 0; font-size: 12px; color: var(--text-sub, #64748b); line-height: 1.5;
}
.pd-quick-arrow {
    margin-left: auto; color: #9ca3af; font-size: 14px; flex-shrink: 0;
}

/* ── RECENT TABLES (2 Column Grid) ───────────────────────── */
.pd-recent-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}
.pd-recent-panel {
    background: var(--surface, #fff);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04);
    transition: box-shadow 0.3s ease;
}
.pd-recent-panel:hover {
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
}
.pd-recent-head {
    padding: 22px 28px 18px;
    border-bottom: 1px solid var(--border, #e2e8f0);
    display: flex; align-items: center; justify-content: space-between;
}
.pd-recent-head-left {
    display: flex; align-items: center; gap: 12px;
}
.pd-recent-icon {
    width: 38px; height: 38px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
}
.pd-recent-icon.indigo  { background: rgba(79,70,229,0.1);  color: #4f46e5; }
.pd-recent-icon.emerald { background: rgba(16,185,129,0.1); color: #10b981; }
.pd-recent-head h4 {
    margin: 0; font-size: 15px; font-weight: 800;
    color: var(--text, #1e293b);
}
.pd-recent-head p {
    margin: 2px 0 0; font-size: 11px; color: var(--text-sub, #64748b);
}
.pd-link-all {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 700; color: #4f46e5;
    text-decoration: none; transition: gap 0.2s ease;
}
.pd-link-all:hover { gap: 10px; }
.pd-recent-table {
    width: 100%; border-collapse: collapse;
}
.pd-recent-table thead th {
    padding: 12px 20px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
    color: var(--text-sub, #64748b);
    background: var(--surface2, #f8fafc);
    text-align: left;
    border-bottom: 1px solid var(--border, #e2e8f0);
}
.pd-recent-table tbody td {
    padding: 14px 20px;
    font-size: 13px; color: var(--text, #1e293b);
    border-bottom: 1px solid var(--border, rgba(226,232,240,0.5));
    vertical-align: middle;
}
.pd-recent-table tbody tr:last-child td { border-bottom: none; }
.pd-recent-table tbody tr {
    transition: background 0.2s ease;
}
.pd-recent-table tbody tr:hover {
    background: rgba(79,70,229,0.02);
}
.pd-name { font-weight: 700; display: block; color: var(--text, #1e293b); }
.pd-sub  { font-size: 11px; color: var(--text-sub, #64748b); }
.pd-mitra-cell {
    display: flex; align-items: center; gap: 8px;
}
.pd-mitra-dot {
    width: 30px; height: 30px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; flex-shrink: 0;
}
.pd-mitra-dot.blue   { background: rgba(59,130,246,0.1); color: #3b82f6; }
.pd-mitra-dot.green  { background: rgba(16,185,129,0.1); color: #10b981; }
.pd-empty-row {
    text-align: center; padding: 40px 20px !important;
    color: var(--text-sub, #94a3b8); font-style: italic;
}

/* ── JENIS KEGIATAN mini-bars ──────────────────────────────── */
.pd-jenis-list {
    display: flex; flex-direction: column; gap: 12px;
}
.pd-jenis-item {
    display: flex; align-items: center; gap: 14px;
}
.pd-jenis-label {
    flex: 0 0 140px; font-size: 13px; font-weight: 600;
    color: var(--text, #1e293b);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.pd-jenis-bar-wrap {
    flex: 1; height: 28px; background: var(--surface2, #f1f5f9);
    border-radius: 8px; overflow: hidden; position: relative;
}
.pd-jenis-bar {
    height: 100%; border-radius: 8px;
    background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%);
    display: flex; align-items: center; justify-content: flex-end;
    padding-right: 10px; min-width: 32px;
    transition: width 0.8s cubic-bezier(0.25,0.46,0.45,0.94);
}
.pd-jenis-bar span {
    font-size: 11px; font-weight: 800; color: #fff;
}

/* ── RESPONSIVE ──────────────────────────────────────────── */
@media (max-width: 1100px) {
    .pd-grid-charts { grid-template-columns: 1fr; }
    .pd-recent-grid { grid-template-columns: 1fr; }
    .pd-quick-grid  { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .pd-quick-grid { grid-template-columns: 1fr; }
}
</style>

<!-- Main Content -->
<main id="mainContent" class="unitdash">
    {{-- ── HERO BAR & BREADCRUMB ────────────────────────────────── --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <span>Beranda</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-graduation-cap"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Dashboard Program Studi</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Ringkasan aktivitas mahasiswa, penempatan magang, dan tracking lulusan
                        <strong>{{ $prodiName }}</strong> — Tahun {{ now()->year }}.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── 4 KARTU KPI STATISTIK ────────────────────────────────── --}}
    <section class="ud-summary" aria-label="Ringkasan data program studi">
        @php
            $kpiCards = [
                [
                    'label' => 'Mahasiswa Magang Aktif',
                    'value' => number_format($totalMhsAktif),
                    'hint'  => 'Sedang menjalani kegiatan di mitra',
                    'icon'  => 'fa-user-graduate',
                    'tone'  => 'blue',
                ],
                [
                    'label' => 'Total Penempatan',
                    'value' => number_format($totalSemuaPenempatan),
                    'hint'  => 'Aktif, selesai, dan dibatalkan',
                    'icon'  => 'fa-layer-group',
                    'tone'  => 'amber',
                ],
                [
                    'label' => 'Total Alumni Terdata',
                    'value' => number_format($totalAlumniCount),
                    'hint'  => 'Lulusan tercatat di sistem',
                    'icon'  => 'fa-users',
                    'tone'  => 'indigo',
                ],
                [
                    'label' => 'Penyerapan di Mitra',
                    'value' => $persenIKU . '%',
                    'hint'  => $alumniBekerjaCount . ' dari ' . $totalAlumniCount . ' alumni',
                    'icon'  => 'fa-chart-line',
                    'tone'  => 'emerald',
                ],
            ];
        @endphp

        @foreach ($kpiCards as $card)
            <article class="ud-card ud-tone-{{ $card['tone'] }}">
                <div class="ud-card-top">
                    <div class="ud-icon"><i class="fas {{ $card['icon'] }}"></i></div>
                    <div class="ud-metric-label">{{ $card['label'] }}</div>
                </div>
                <div class="ud-metric-hint">{{ $card['hint'] }}</div>
                <div class="ud-metric-value">{{ $card['value'] }}</div>
                <div class="ud-card-accent" aria-hidden="true">
                    <i class="fas {{ $card['icon'] }}"></i>
                </div>
            </article>
        @endforeach
    </section>

    {{-- ── GRAFIK (2 KOLOM) ──────────────────────────────────────── --}}
    <section class="pd-grid-charts">
        {{-- Donut Chart: Distribusi Status Mahasiswa --}}
        <div class="pd-chart-panel">
            <div class="pd-chart-head">
                <div class="pd-chart-icon donut"><i class="fas fa-chart-pie"></i></div>
                <div>
                    <h4>Distribusi Status Mahasiswa</h4>
                    <p>Perbandingan status penempatan saat ini</p>
                </div>
            </div>
            <div class="pd-chart-canvas-wrap">
                <canvas id="chartStatusDonut"></canvas>
            </div>
        </div>

        {{-- Bar Chart: Trend Penempatan per Tahun --}}
        <div class="pd-chart-panel">
            <div class="pd-chart-head">
                <div class="pd-chart-icon bar"><i class="fas fa-chart-bar"></i></div>
                <div>
                    <h4>Trend Penempatan per Tahun</h4>
                    <p>Jumlah penempatan mahasiswa 5 tahun terakhir</p>
                </div>
            </div>
            <div class="pd-chart-canvas-wrap">
                <canvas id="chartTrendBar"></canvas>
            </div>
        </div>
    </section>

    {{-- ── DISTRIBUSI JENIS KEGIATAN (Horizontal Bars) ──────────── --}}
    @if(!empty($jenisKegiatanData))
    <section class="pd-chart-panel" style="margin-bottom: 24px;">
        <div class="pd-chart-head">
            <div class="pd-chart-icon hbar"><i class="fas fa-list-check"></i></div>
            <div>
                <h4>Distribusi Jenis Kegiatan</h4>
                <p>Persebaran mahasiswa berdasarkan jenis kegiatan kerja sama</p>
            </div>
        </div>
        <div class="pd-jenis-list">
            @php $maxJenis = max(array_values($jenisKegiatanData)) ?: 1; @endphp
            @foreach($jenisKegiatanData as $nama => $jumlah)
            <div class="pd-jenis-item">
                <span class="pd-jenis-label" title="{{ $nama }}">{{ $nama }}</span>
                <div class="pd-jenis-bar-wrap">
                    <div class="pd-jenis-bar" style="width: {{ round(($jumlah / $maxJenis) * 100) }}%;">
                        <span>{{ $jumlah }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── QUICK ACTION CARDS (3 KOLOM) ──────────────────────────── --}}
    <section class="pd-quick-grid">
        <a href="{{ route('prodi.penempatan.create') }}" class="pd-quick-card"
            onmouseover="this.style.borderColor='#4f46e5'; this.style.background='rgba(79,70,229,0.03)';"
            onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)';">
            <div class="pd-quick-icon indigo"><i class="fas fa-user-plus"></i></div>
            <div>
                <span class="pd-quick-label">Tambah Penempatan</span>
                <p class="pd-quick-desc">Daftarkan mahasiswa ke mitra industri untuk kegiatan magang atau penelitian.</p>
            </div>
            <i class="fas fa-chevron-right pd-quick-arrow"></i>
        </a>

        <a href="{{ route('prodi.alumni.create') }}" class="pd-quick-card"
            onmouseover="this.style.borderColor='#10b981'; this.style.background='rgba(16,185,129,0.03)';"
            onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)';">
            <div class="pd-quick-icon emerald"><i class="fas fa-briefcase"></i></div>
            <div>
                <span class="pd-quick-label">Tambah Data Alumni</span>
                <p class="pd-quick-desc">Catat lulusan prodi yang terserap dan bekerja di perusahaan mitra.</p>
            </div>
            <i class="fas fa-chevron-right pd-quick-arrow"></i>
        </a>

        <a href="{{ route('prodi.penempatan.index') }}" class="pd-quick-card"
            onmouseover="this.style.borderColor='#f59e0b'; this.style.background='rgba(245,158,11,0.03)';"
            onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)';">
            <div class="pd-quick-icon amber"><i class="fas fa-list-ul"></i></div>
            <div>
                <span class="pd-quick-label">Daftar Penempatan</span>
                <p class="pd-quick-desc">Lihat dan kelola seluruh data penempatan mahasiswa magang.</p>
            </div>
            <i class="fas fa-chevron-right pd-quick-arrow"></i>
        </a>
    </section>

    {{-- ── 2 TABEL RINGKASAN (2 KOLOM) ───────────────────────────── --}}
    <section class="pd-recent-grid">
        {{-- Tabel: 5 Mahasiswa Magang Terbaru --}}
        <div class="pd-recent-panel">
            <div class="pd-recent-head">
                <div class="pd-recent-head-left">
                    <div class="pd-recent-icon indigo"><i class="fas fa-user-graduate"></i></div>
                    <div>
                        <h4>Mahasiswa Magang Terbaru</h4>
                        <p>5 penempatan terakhir</p>
                    </div>
                </div>
                <a href="{{ route('prodi.penempatan.index') }}" class="pd-link-all">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <table class="pd-recent-table">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Mitra</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMhs as $item)
                    @php
                        $st = strtolower($item->status ?? 'aktif');
                        $stClass = match($st) {
                            'aktif'      => 'dk-status-active',
                            'selesai'    => 'dk-status-info',
                            'dibatalkan' => 'dk-status-danger',
                            default      => 'dk-status-neutral',
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="pd-name">{{ $item->mahasiswa?->nama ?? '-' }}</span>
                            <span class="pd-sub">NIM: {{ $item->mahasiswa?->nim ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="pd-mitra-cell">
                                <div class="pd-mitra-dot blue"><i class="fas fa-building"></i></div>
                                <span class="pd-sub" style="color: var(--text);">{{ $item->mitra?->nama_mitra ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="dk-status {{ $stClass }}" style="font-size: 11px;">
                                {{ ucfirst($st) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="pd-empty-row">
                            <i class="fas fa-inbox" style="font-size: 20px; margin-bottom: 6px; display: block;"></i>
                            Belum ada data penempatan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tabel: 5 Alumni Bekerja Terbaru --}}
        <div class="pd-recent-panel">
            <div class="pd-recent-head">
                <div class="pd-recent-head-left">
                    <div class="pd-recent-icon emerald"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <h4>Alumni Bekerja Terbaru</h4>
                        <p>5 data alumni terbaru</p>
                    </div>
                </div>
                <a href="{{ route('prodi.alumni.index') }}" class="pd-link-all" style="color: #10b981;">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <table class="pd-recent-table">
                <thead>
                    <tr>
                        <th>Alumni</th>
                        <th>Mitra / Posisi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAlumni as $am)
                    @php
                        $amStatus = strtolower($am->status ?? 'aktif');
                        $amClass = $amStatus === 'aktif' ? 'dk-status-active' : 'dk-status-neutral';
                    @endphp
                    <tr>
                        <td>
                            <span class="pd-name">{{ $am->alumni?->nama ?? '-' }}</span>
                            <span class="pd-sub">{{ $am->alumni?->tahun_lulus ? 'Lulus ' . $am->alumni->tahun_lulus : '' }}</span>
                        </td>
                        <td>
                            <div class="pd-mitra-cell">
                                <div class="pd-mitra-dot green"><i class="fas fa-building"></i></div>
                                <div>
                                    <span style="font-weight: 600; color: var(--text); display: block; font-size: 13px;">{{ $am->mitra?->nama_mitra ?? '-' }}</span>
                                    <span class="pd-sub">{{ $am->posisi ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="dk-status {{ $amClass }}" style="font-size: 11px;">
                                {{ ucfirst($amStatus) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="pd-empty-row">
                            <i class="fas fa-inbox" style="font-size: 20px; margin-bottom: 6px; display: block;"></i>
                            Belum ada data alumni bekerja
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

{{-- ── CHART.JS SCRIPTS ─────────────────────────────────────────── --}}
<script>
document.addEventListener('turbo:load', initProdiDashboardCharts);
document.addEventListener('DOMContentLoaded', initProdiDashboardCharts);

let _prodiChartsInitialized = false;

function initProdiDashboardCharts() {
    if (_prodiChartsInitialized) return;
    const donutCanvas = document.getElementById('chartStatusDonut');
    const barCanvas   = document.getElementById('chartTrendBar');
    if (!donutCanvas || !barCanvas) return;
    _prodiChartsInitialized = true;

    const rootStyle = getComputedStyle(document.documentElement);
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    // ─── Donut Chart ───
    const statusData = @json($chartStatusDistribusi);
    new Chart(donutCanvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: ['#6366f1', '#10b981', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 10,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textColor,
                        font: { family: 'Plus Jakarta Sans', size: 12, weight: 600 },
                        padding: 20,
                        usePointStyle: true,
                        pointStyleWidth: 12,
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#e2e8f0' : '#1e293b',
                    bodyColor: isDark ? '#94a3b8' : '#475569',
                    borderWidth: 1,
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                    cornerRadius: 12,
                    padding: 14,
                    titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: 700 },
                    bodyFont:  { family: 'Plus Jakarta Sans', size: 12 },
                    callbacks: {
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a,b)=> a+b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // ─── Bar Chart ───
    const trendData = @json($chartTrendTahunan);
    new Chart(barCanvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: trendData.labels,
            datasets: [{
                label: 'Penempatan',
                data: trendData.data,
                backgroundColor: 'rgba(16,185,129,0.2)',
                borderColor: '#10b981',
                borderWidth: 2,
                borderRadius: 10,
                borderSkipped: false,
                maxBarThickness: 48,
                hoverBackgroundColor: 'rgba(16,185,129,0.4)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { intersect: false, mode: 'index' },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: textColor,
                        font: { family: 'Plus Jakarta Sans', size: 12, weight: 600 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, drawBorder: false },
                    ticks: {
                        color: textColor,
                        font: { family: 'Plus Jakarta Sans', size: 12 },
                        stepSize: 1,
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#e2e8f0' : '#1e293b',
                    bodyColor: isDark ? '#94a3b8' : '#475569',
                    borderWidth: 1,
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                    cornerRadius: 12,
                    padding: 14,
                    titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: 700 },
                    bodyFont:  { family: 'Plus Jakarta Sans', size: 12 },
                }
            }
        }
    });
}
</script>

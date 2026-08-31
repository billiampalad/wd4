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

    {{-- ── QUICK ACTION CARDS (3 KOLOM KOMPAK) ─────────────────── --}}
    <section class="pd-quick-grid">
        <a href="{{ route('prodi.penempatan.create') }}" class="pd-quick-card indigo">
            <div class="pd-quick-icon indigo"><i class="fas fa-user-plus"></i></div>
            <div class="pd-quick-info">
                <span class="pd-quick-label">Tambah Penempatan</span>
                <p class="pd-quick-desc">Daftarkan mahasiswa ke mitra untuk kegiatan magang/penelitian.</p>
            </div>
            <i class="fas fa-chevron-right pd-quick-arrow"></i>
        </a>

        <a href="{{ route('prodi.alumni.create') }}" class="pd-quick-card emerald">
            <div class="pd-quick-icon emerald"><i class="fas fa-briefcase"></i></div>
            <div class="pd-quick-info">
                <span class="pd-quick-label">Tambah Data Alumni</span>
                <p class="pd-quick-desc">Catat lulusan prodi yang terserap dan bekerja di mitra.</p>
            </div>
            <i class="fas fa-chevron-right pd-quick-arrow"></i>
        </a>

        <a href="{{ route('prodi.penempatan.index') }}" class="pd-quick-card amber">
            <div class="pd-quick-icon amber"><i class="fas fa-list-ul"></i></div>
            <div class="pd-quick-info">
                <span class="pd-quick-label">Daftar Penempatan</span>
                <p class="pd-quick-desc">Lihat dan kelola seluruh data penempatan mahasiswa magang.</p>
            </div>
            <i class="fas fa-chevron-right pd-quick-arrow"></i>
        </a>
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
    <section class="pd-chart-panel" style="margin-bottom: 20px;">
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

    {{-- Data Container for Chart.js (parsed by public/js/auth/prodi/dashboard.js) --}}
    <div id="prodiDashboardData"
        data-status='@json($chartStatusDistribusi)'
        data-trend='@json($chartTrendTahunan)'
        style="display:none;"></div>
</main>

@php
    $totalPendapatan = \App\Models\DetailKegiatan::sum('nilai_kontrak') ?? 0;

    $mitraNasional = \App\Models\Mitra::where('kategori', 'Nasional')->count() ?? 0;
    $mitraInternasional = \App\Models\Mitra::where('kategori', 'Internasional')->count() ?? 0;

    $totalMoU = \App\Models\Cooperation::where('jenis', 'like', '%MoU%')->count() ?? 0;
    $totalMoA = \App\Models\Cooperation::where('jenis', 'like', '%MoA%')->count() ?? 0;
    $totalIA = \App\Models\Cooperation::where('jenis', 'like', '%IA%')->count() ?? 0;

    $jurusans = \App\Models\Jurusan::with('prodis')->get();

    // Count dari pivot table (kerjasama_jurusan)
    $jurusanCounts = \Illuminate\Support\Facades\DB::table('kerjasama_jurusan')
        ->select('jurusan_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('jurusan_id')
        ->pluck('total', 'jurusan_id')
        ->toArray();

    // Count dari pivot table (kerjasama_prodi)
    $prodiCounts = \Illuminate\Support\Facades\DB::table('kerjasama_prodi')
        ->select('prodi_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('prodi_id')
        ->pluck('total', 'prodi_id')
        ->toArray();

    $ruangLingkupKerjasama = \Illuminate\Support\Facades\DB::table('detail_kegiatans')
        ->join('jenis_kerjasamas', 'detail_kegiatans.jenis_kerjasama_id', '=', 'jenis_kerjasamas.id')
        ->select(
            'jenis_kerjasamas.nama_kerjasama',
            \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT detail_kegiatans.cooperation_id) as total_kerjasama'),
        )
        ->whereNotNull('detail_kegiatans.jenis_kerjasama_id')
        ->groupBy('jenis_kerjasamas.id', 'jenis_kerjasamas.nama_kerjasama')
        ->having('total_kerjasama', '>', 0)
        ->orderByDesc('total_kerjasama')
        ->orderBy('jenis_kerjasamas.nama_kerjasama')
        ->get();

    $chartDataJurusan = [];
    $chartDataProdi = [];

    foreach ($jurusans as $jurusan) {
        $jCount = $jurusanCounts[$jurusan->id] ?? 0;

        $chartDataJurusan[] = [
            'id' => $jurusan->id,
            'name' => $jurusan->nama_jurusan,
            'count' => $jCount,
        ];

        foreach ($jurusan->prodis as $prodi) {
            $pCount = $prodiCounts[$prodi->id] ?? 0;
            $chartDataProdi[] = [
                'id' => $prodi->id,
                'jurusan_id' => $jurusan->id,
                'name' => $prodi->nama_prodi,
                'count' => $pCount,
            ];
        }
    }

    // --- STATISTIK PERIODE KERJASAMA (TREND CHART) ---
    $now = now();

    // 1. Mingguan (7 Hari Terakhir)
    $weeklyRaw = \App\Models\Cooperation::selectRaw('DATE(created_at) as date_label, count(*) as total')
        ->where('created_at', '>=', $now->copy()->subDays(6)->startOfDay())
        ->groupBy('date_label')
        ->pluck('total', 'date_label')
        ->toArray();

    $trendWeekly = ['labels' => [], 'data' => []];
    for ($i = 6; $i >= 0; $i--) {
        $dateStr = $now->copy()->subDays($i)->format('Y-m-d');
        $display = $now->copy()->subDays($i)->format('d M');
        $trendWeekly['labels'][] = $display;
        $trendWeekly['data'][] = $weeklyRaw[$dateStr] ?? 0;
    }

    // 2. Bulanan (12 Bulan di Tahun Ini)
    $monthlyRaw = \App\Models\Cooperation::selectRaw('MONTH(created_at) as month_label, count(*) as total')
        ->whereYear('created_at', $now->year)
        ->groupBy('month_label')
        ->pluck('total', 'month_label')
        ->toArray();

    $trendMonthly = ['labels' => [], 'data' => []];
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    for ($i = 1; $i <= 12; $i++) {
        $trendMonthly['labels'][] = $months[$i - 1];
        $trendMonthly['data'][] = $monthlyRaw[$i] ?? 0;
    }

    // 3. Tahunan (5 Tahun Terakhir)
    $yearlyRaw = \App\Models\Cooperation::selectRaw('YEAR(created_at) as year_label, count(*) as total')
        ->where('created_at', '>=', $now->copy()->subYears(4)->startOfYear())
        ->groupBy('year_label')
        ->pluck('total', 'year_label')
        ->toArray();

    $trendYearly = ['labels' => [], 'data' => []];
    for ($i = 4; $i >= 0; $i--) {
        $yr = $now->copy()->subYears($i)->year;
        $trendYearly['labels'][] = (string) $yr;
        $trendYearly['data'][] = $yearlyRaw[$yr] ?? 0;
    }

    $trendData = [
        'weekly' => $trendWeekly,
        'monthly' => $trendMonthly,
        'yearly' => $trendYearly,
    ];

    $summaryCards = [
        [
            'label' => 'Jumlah Kerjasama',
            'value' => $totalKerjasama ?? 0,
            'hint' => 'Politeknik sampai saat ini',
            'icon' => 'fa-layer-group',
            'tone' => 'blue',
        ],
        [
            'label' => 'Jumlah Dokumen Kerjasama',
            'value' => $totalMoU + $totalMoA + $totalIA,
            'hint' => "MoU: $totalMoU | MoA: $totalMoA | IA: $totalIA",
            'icon' => 'fa-file-signature',
            'tone' => 'amber',
        ],
        [
            'label' => 'Jumlah Mitra',
            'value' => $mitraNasional + $mitraInternasional,
            'hint' => "Nasional: $mitraNasional | Internasional: $mitraInternasional",
            'icon' => 'fa-globe',
            'tone' => 'indigo',
        ],

        [
            'label' => 'Total Pendapatan',
            'value' => 'Rp ' . number_format($totalPendapatan, 0, ',', '.') . '.000',
            'hint' => 'Dari nilai kontrak kerjasama',
            'icon' => 'fa-wallet',
            'tone' => 'emerald',
        ],
    ];
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/dashboard.css') }}" data-turbo-track="reload">

<main id="mainContent" class="unitdash">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <span>Beranda</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake-angle"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Sistem Informasi Kerjasama</h2>
                    <p class="ud-subtitle">
                        Gambaran aktivitas kerjasama Politeknik Negeri Manado Tahun {{ now()->year }}
                    </p>
                </div>
            </div>
        </div>
        <a href="{{ route('unit.kerjasama.create', ['type' => 'baru']) }}" class="ud-create-menu">
            <span class="ud-create-icon"><i class="fas fa-file-circle-plus"></i></span>
            <span class="ud-create-copy">
                <strong>Tambah Kerjasama</strong>
                <small>Buat dokumen MoU, MoA, atau IA baru</small>
            </span>
            <span class="ud-create-arrow"><i class="fas fa-arrow-right"></i></span>
        </a>
    </section>

    <section class="ud-summary">
        @foreach ($summaryCards as $card)
            <article class="ud-card ud-tone-{{ $card['tone'] }}">
                <div class="ud-card-top">
                    <div class="ud-icon"><i class="fas {{ $card['icon'] }}"></i></div>
                    <div class="ud-metric-label">{{ $card['label'] }}</div>
                </div>
                <div class="ud-metric-hint">{{ $card['hint'] }}</div>
                <div class="ud-metric-value">
                    {{ is_numeric($card['value']) ? number_format($card['value']) : $card['value'] }}</div>
                <div class="ud-card-accent" aria-hidden="true">
                    <i class="fas {{ $card['icon'] }}"></i>
                </div>
            </article>
        @endforeach
    </section>

    <section class="ud-bento-full">
        <article class="ud-panel dashboard-cooperation-layout">
            <div class="ud-panel-head dashboard-cooperation-layout__header">
                <div>
                    <h4 class="ud-panel-title" id="dashboard-cooperation-layout-title">Ruang Lingkup Kerjasama</h4>
                    <p class="ud-panel-desc">Layout dua kolom untuk menyiapkan tampilan ringkasan data kerjasama
                        akademik.</p>
                </div>
            </div>

            <div class="dashboard-cooperation-layout__grid">
                <div class="dashboard-cooperation-layout__column">
                    <div class="dashboard-cooperation-layout__column-header">
                        <div class="dashboard-cooperation-layout__table-wrap">
                            <table class="ud-table dashboard-cooperation-layout__table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Ruang Lingkup</th>
                                        <th>Jumlah Kerjasama yang Terlibat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ruangLingkupKerjasama as $ruangLingkup)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $ruangLingkup->nama_kerjasama }}</td>
                                            <td>
                                                <span class="dashboard-cooperation-layout__count">
                                                    {{ number_format($ruangLingkup->total_kerjasama) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <div class="ud-empty">Belum ada ruang lingkup kerjasama untuk ditampilkan.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="dashboard-cooperation-layout__column">
                    <div class="dashboard-cooperation-layout__column-header">
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section class="ud-bento-full">
        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Distribusi Kerjasama Akademik</h3>
                    <p class="ud-panel-desc">Tinjauan visual distribusi kerjasama berdasarkan Jurusan dan Program Studi
                        (Prodi).</p>
                </div>
                <span class="ud-status-badge is-interactive"
                    title="Klik pada batang grafik jurusan untuk memfilter prodi">
                    <i class="fas fa-hand-pointer"></i> Interactive Filter
                </span>
            </div>

            <div class="ud-dual-chart-container">
                <div class="ud-chart-wrapper">
                    <div class="ud-chart-header">
                        <div class="ud-chart-icon" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);"><i
                                class="fas fa-building-columns"></i></div>
                        <div>
                            <h4>Grafik Jurusan</h4>
                            <span>Klik batang grafik untuk memfilter prodi.</span>
                        </div>
                    </div>
                    <div class="ud-canvas-container">
                        <canvas id="jurusanChart" data-jurusans="{{ json_encode($chartDataJurusan) }}"
                            data-prodis="{{ json_encode($chartDataProdi) }}"></canvas>
                    </div>
                </div>

                <div class="ud-chart-divider"></div>

                <div class="ud-chart-wrapper">
                    <div class="ud-chart-header">
                        <div class="ud-chart-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.1);"><i
                                class="fas fa-graduation-cap"></i></div>
                        <div>
                            <h4>Grafik Program Studi</h4>
                            <span id="prodiChartSubtitle">Menampilkan Semua Jurusan</span>
                        </div>
                    </div>
                    <div class="ud-canvas-container">
                        <canvas id="prodiChart"></canvas>
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section class="ud-bento">
        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Distribusi Jenis Dokumen Kerjasama</h3>
                    <p class="ud-panel-desc">Proporsi dokumen MoU, MoA, dan IA.</p>
                </div>
                <span class="ud-type-badge" style="background: rgba(14, 165, 233, 0.1); color: var(--accent);"><i
                        class="fas fa-chart-pie"></i> Chart</span>
            </div>

            <div class="ud-chart-layout">
                <canvas id="jenisKerjasamaChart" data-mou="{{ $totalMoU }}" data-moa="{{ $totalMoA }}"
                    data-ia="{{ $totalIA }}"></canvas>
            </div>
        </article>

        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Upcoming Deadlines</h3>
                    <p class="ud-panel-desc">Dokumen dengan masa berlaku tersisa maksimal 30 hari.</p>
                </div>
                <span class="ud-status-badge is-pending"><i class="fas fa-clock"></i> 30 hari</span>
            </div>

            <div class="ud-deadlines">
                @forelse($upcomingDeadlines ?? [] as $deadline)
                    @php
                        $daysLeft = now()
                            ->startOfDay()
                            ->diffInDays($deadline->end_date->copy()->startOfDay());
                    @endphp
                    <div class="ud-deadline-item">
                        <div class="ud-daybox">{{ $daysLeft }}</div>
                        <div style="min-width:0;">
                            <div class="ud-deadline-title">{{ $deadline->title ?? '-' }}</div>
                            <div class="ud-deadline-meta">
                                {{ $deadline->mitra?->nama_mitra ?? 'Mitra belum diisi' }} - berakhir
                                {{ $deadline->end_date?->format('d M Y') }}
                            </div>
                        </div>
                        <a class="ud-link-btn" href="{{ route('unit.kerjasama.show', $deadline->id) }}" title="Detail">
                            <i class="fas fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                @empty
                    <div class="ud-empty">Tidak ada deadline kritis dalam 30 hari.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="ud-bento-full">
        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Tren Pertumbuhan Kerjasama</h3>
                    <p class="ud-panel-desc">Statistik penambahan dokumen kerjasama baru berdasarkan rentang waktu
                        terpilih.</p>
                </div>
                <div class="ud-trend-filters">
                    <button type="button" class="ud-trend-btn" data-trend="weekly">7 Hari</button>
                    <button type="button" class="ud-trend-btn is-active" data-trend="monthly">Tahun Ini</button>
                    <button type="button" class="ud-trend-btn" data-trend="yearly">5 Tahun</button>
                </div>
            </div>

            <div class="ud-trend-chart-layout">
                <canvas id="trendChart" data-trends="{{ json_encode($trendData) }}"></canvas>
            </div>
        </article>
    </section>

    <section class="ud-panel ud-table-panel">
        <div class="ud-table-head">
            <div>
                <h3 class="ud-panel-title">Data Teknis Kerjasama</h3>
                <p class="ud-panel-desc">Filtered view, quick edit link dokumen, dan status operasional.</p>
            </div>
            <div class="ud-tabs" aria-label="Filter tipe dokumen">
                @foreach (['Semua', 'MoU', 'MoA', 'IA'] as $filter)
                    <button type="button" class="ud-tab {{ $loop->first ? 'is-active' : '' }}"
                        data-filter-tab="{{ $filter === 'Semua' ? 'all' : $filter }}">
                        {{ $filter }}
                        <span>({{ $jenisCounts[$filter] ?? 0 }})</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="ud-table-wrap">
            <table class="ud-table">
                <thead>
                    <tr>
                        <th>Judul Kegiatan</th>
                        <th>Mitra</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Link Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kerjasamaTable ?? [] as $item)
                        @php
                            $jenisLower = strtolower($item->jenis ?? '');
                            $jenisShort = str_contains($jenisLower, 'mou')
                                ? 'MoU'
                                : (str_contains($jenisLower, 'moa')
                                    ? 'MoA'
                                    : (str_contains($jenisLower, 'ia')
                                        ? 'IA'
                                        : '-'));
                            $jenisLabel = match($jenisShort) {
                                'MoU' => 'Memorandum of Understanding (MoU)',
                                'MoA' => 'Memorandum of Agreement (MoA)',
                                'IA'  => 'Implementation Arrangement (IA)',
                                default => $item->jenis ?? '-',
                            };
                            $statusRaw = strtolower(trim($item->status ?? ''));
                            $statusMap = [
                                'aktif' => ['label' => 'Aktif', 'class' => 'is-active', 'icon' => 'fa-circle-check'],
                                'dalam perpanjangan' => ['label' => 'Dalam Perpanjangan', 'class' => 'is-pending', 'icon' => 'fa-clock-rotate-left'],
                                'kadarluarsa' => ['label' => 'Kadaluarsa', 'class' => 'is-expired', 'icon' => 'fa-triangle-exclamation'],
                                'kadaluarsa' => ['label' => 'Kadaluarsa', 'class' => 'is-expired', 'icon' => 'fa-triangle-exclamation'],
                                'kedaluwarsa' => ['label' => 'Kadaluarsa', 'class' => 'is-expired', 'icon' => 'fa-triangle-exclamation'],
                                'tidak aktif' => ['label' => 'Tidak Aktif', 'class' => 'is-inactive', 'icon' => 'fa-circle-xmark'],
                                'proses' => ['label' => 'Proses', 'class' => 'is-pending', 'icon' => 'fa-spinner'],
                            ];
                            $statusInfo = $statusMap[$statusRaw] ?? ['label' => ucfirst($item->status ?? '-'), 'class' => '', 'icon' => 'fa-circle-question'];
                            $deadlineLabel = $item->end_date ? $item->end_date->format('d M Y') : '-';
                            $pjInternal = $item->pjInternal?->nama ?? '-';
                        @endphp
                        <tr data-kerjasama-row data-doc-type="{{ $jenisShort }}">
                            <td>
                                <div class="ud-small">No. {{ $item->doc_number ?: ($item->pks_number ?: '-') }}</div>
                                <div class="ud-doc-title">{{ $item->title ?? '-' }}</div>
                                <span class="ud-type-badge">{{ $jenisLabel }}</span>
                            </td>
                            <td>
                                <span class="ud-mitra">
                                    <i class="fas fa-building"></i>
                                    {{ $item->mitra?->nama_mitra ?? '-' }}
                                    <span class="ud-tooltip">PJ Internal: {{ $pjInternal }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="ud-status-badge {{ $statusInfo['class'] }}">
                                    <i class="fas {{ $statusInfo['icon'] }}"></i>
                                    {{ $statusInfo['label'] }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $deadlineLabel }}</strong>
                                <div class="ud-small">
                                    {{ $item->end_date ? 'Masa berlaku dokumen' : 'Belum ada tanggal' }}</div>
                            </td>
                            <td>
                                <div class="ud-link-editor" data-link-editor>
                                    <input class="ud-link-input" type="text" value="{{ $item->document_link }}"
                                        placeholder="Paste link Drive..." data-document-link-input>
                                    <button class="ud-save-btn" type="button" data-save-document-link
                                        data-update-url="{{ route('unit.kerjasama.document-link.update', $item->id) }}"
                                        title="Simpan link">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </div>
                                <span class="ud-save-state"
                                    data-save-state>{{ $item->document_link ? 'Link tersimpan' : 'Belum ada link' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="ud-empty">Belum ada data kerjasama untuk ditampilkan.</div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="unitDashNoResult" style="display:none;">
                        <td colspan="5">
                            <div class="ud-empty">Tidak ada dokumen pada filter ini.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/auth/dashboard.js') }}" data-turbo-track="reload"></script>

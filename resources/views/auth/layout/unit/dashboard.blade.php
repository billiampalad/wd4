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
        $trendYearly['labels'][] = (string)$yr;
        $trendYearly['data'][] = $yearlyRaw[$yr] ?? 0;
    }

    $trendData = [
        'weekly' => $trendWeekly,
        'monthly' => $trendMonthly,
        'yearly' => $trendYearly
    ];

    $summaryCards = [
        [
            'label' => 'Total Kerjasama Unit',
            'value' => $totalKerjasama ?? 0,
            'hint' => 'Dokumen yang melibatkan unit ini',
            'icon' => 'fa-layer-group',
            'tone' => 'blue',
        ],
        [
            'label' => 'Total Pendapatan',
            'value' => 'Rp ' . number_format($totalPendapatan, 0, ',', '.') . '.000',
            'hint' => 'Dari seluruh nilai kontrak',
            'icon' => 'fa-wallet',
            'tone' => 'emerald',
        ],
        [
            'label' => 'Jenis Kerjasama',
            'value' => $totalMoU + $totalMoA + $totalIA,
            'hint' => "MoU: $totalMoU | MoA: $totalMoA | IA: $totalIA",
            'icon' => 'fa-file-signature',
            'tone' => 'amber',
        ],
        [
            'label' => 'Distribusi Mitra',
            'value' => $mitraNasional + $mitraInternasional,
            'hint' => "Nasional: $mitraNasional | Internasional: $mitraInternasional",
            'icon' => 'fa-globe',
            'tone' => 'indigo',
        ],
    ];
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/dashboard.css') }}" data-turbo-track="reload">

<main id="mainContent" class="unitdash">
    <section class="ud-topbar">
        <div>
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <span>Dashboard Unit Kerja</span>
            </div>
            <h2 class="ud-title">Operasional Kerjasama</h2>
            <p class="ud-subtitle">
                {{ $unitName ?? auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja' }}
                <span style="color:#94a3b8;">/</span>
                {{ now()->format('d M Y') }}
            </p>
        </div>
        <div class="ud-live-chip">
            <span class="ud-dot"></span>
            <span>Operational Control</span>
        </div>
    </section>

    <section class="ud-summary">
        @foreach($summaryCards as $card)
            <article class="ud-card ud-tone-{{ $card['tone'] }}">
                <div class="ud-card-top">
                    <div class="ud-icon"><i class="fas {{ $card['icon'] }}"></i></div>
                </div>
                <div class="ud-metric-value">{{ is_numeric($card['value']) ? number_format($card['value']) : $card['value'] }}</div>
                <div class="ud-metric-label">{{ $card['label'] }}</div>
                <div class="ud-metric-hint">{{ $card['hint'] }}</div>
            </article>
        @endforeach
    </section>

    <section class="ud-bento">
        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Distribusi Jenis Kerjasama</h3>
                    <p class="ud-panel-desc">Proporsi dokumen MoU, MoA, dan IA.</p>
                </div>
                <span class="ud-type-badge" style="background: rgba(14, 165, 233, 0.1); color: var(--accent);"><i class="fas fa-chart-pie"></i> Chart</span>
            </div>

            <div class="ud-chart-layout">
                <canvas id="jenisKerjasamaChart" data-mou="{{ $totalMoU }}" data-moa="{{ $totalMoA }}" data-ia="{{ $totalIA }}"></canvas>
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
                        $daysLeft = now()->startOfDay()->diffInDays($deadline->end_date->copy()->startOfDay());
                    @endphp
                    <div class="ud-deadline-item">
                        <div class="ud-daybox">{{ $daysLeft }}</div>
                        <div style="min-width:0;">
                            <div class="ud-deadline-title">{{ $deadline->title ?? '-' }}</div>
                            <div class="ud-deadline-meta">
                                {{ $deadline->mitra?->nama_mitra ?? 'Mitra belum diisi' }} - berakhir {{ $deadline->end_date?->format('d M Y') }}
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
                    <h3 class="ud-panel-title">Distribusi Kerjasama Akademik</h3>
                    <p class="ud-panel-desc">Tinjauan visual distribusi kerjasama berdasarkan Jurusan dan Program Studi (Prodi).</p>
                </div>
                <span class="ud-status-badge is-interactive" title="Klik pada batang grafik jurusan untuk memfilter prodi">
                    <i class="fas fa-hand-pointer"></i> Interactive Filter
                </span>
            </div>

            <div class="ud-dual-chart-container">
                <div class="ud-chart-wrapper">
                    <div class="ud-chart-header">
                        <div class="ud-chart-icon" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);"><i class="fas fa-building-columns"></i></div>
                        <div>
                            <h4>Grafik Jurusan</h4>
                            <span>Klik batang grafik untuk memfilter prodi.</span>
                        </div>
                    </div>
                    <div class="ud-canvas-container">
                        <canvas id="jurusanChart" data-jurusans="{{ json_encode($chartDataJurusan) }}" data-prodis="{{ json_encode($chartDataProdi) }}"></canvas>
                    </div>
                </div>

                <div class="ud-chart-divider"></div>

                <div class="ud-chart-wrapper">
                    <div class="ud-chart-header">
                        <div class="ud-chart-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.1);"><i class="fas fa-graduation-cap"></i></div>
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

    <section class="ud-bento-full">
        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Tren Pertumbuhan Kerjasama</h3>
                    <p class="ud-panel-desc">Statistik penambahan dokumen kerjasama baru berdasarkan rentang waktu terpilih.</p>
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
                @foreach(['Semua', 'MoU', 'MoA', 'IA'] as $filter)
                    <button type="button" class="ud-tab {{ $loop->first ? 'is-active' : '' }}" data-filter-tab="{{ $filter === 'Semua' ? 'all' : $filter }}">
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
                        <th>Dokumen</th>
                        <th>Tipe</th>
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
                            $jenisShort = str_contains($jenisLower, 'mou') ? 'MoU' : (str_contains($jenisLower, 'moa') ? 'MoA' : (str_contains($jenisLower, 'ia') ? 'IA' : '-'));
                            $statusRaw = strtolower($item->status ?? '');
                            $isExpired = in_array($statusRaw, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true)
                                || ($item->end_date && now()->startOfDay()->greaterThan($item->end_date->copy()->startOfDay()));
                            $isPending = ($item->status_dokumen ?? '') === 'Menunggu Evaluasi';
                            $statusClass = $isExpired ? 'is-expired' : ($isPending ? 'is-pending' : '');
                            $statusLabel = $isExpired ? 'Kadaluarsa' : ($item->status_dokumen ?? ucfirst($item->status ?? 'Draft'));
                            $deadlineLabel = $item->end_date ? $item->end_date->format('d M Y') : '-';
                            $pjInternal = $item->pjInternal?->nama ?? '-';
                        @endphp
                        <tr data-kerjasama-row data-doc-type="{{ $jenisShort }}">
                            <td>
                                <div class="ud-doc-title">{{ $item->title ?? '-' }}</div>
                                <div class="ud-small">No. {{ $item->doc_number ?: ($item->pks_number ?: '-') }}</div>
                            </td>
                            <td>
                                <span class="ud-type-badge">{{ $jenisShort }}</span>
                            </td>
                            <td>
                                <span class="ud-mitra">
                                    <i class="fas fa-building"></i>
                                    {{ $item->mitra?->nama_mitra ?? '-' }}
                                    <span class="ud-tooltip">PJ Internal: {{ $pjInternal }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="ud-status-badge {{ $statusClass }}">
                                    <i class="fas {{ $isExpired ? 'fa-triangle-exclamation' : ($isPending ? 'fa-clock' : 'fa-circle-check') }}"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $deadlineLabel }}</strong>
                                <div class="ud-small">{{ $item->end_date ? 'Masa berlaku dokumen' : 'Belum ada tanggal' }}</div>
                            </td>
                            <td>
                                <div class="ud-link-editor" data-link-editor>
                                    <input class="ud-link-input" type="text" value="{{ $item->document_link }}" placeholder="Paste link Drive..." data-document-link-input>
                                    <button class="ud-save-btn" type="button" data-save-document-link data-update-url="{{ route('unit.kerjasama.document-link.update', $item->id) }}" title="Simpan link">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </div>
                                <span class="ud-save-state" data-save-state>{{ $item->document_link ? 'Link tersimpan' : 'Belum ada link' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="ud-empty">Belum ada data kerjasama untuk ditampilkan.</div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="unitDashNoResult" style="display:none;">
                        <td colspan="6">
                            <div class="ud-empty">Tidak ada dokumen pada filter ini.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script src="{{ asset('js/auth/dashboard.js') }}" data-turbo-track="reload"></script>

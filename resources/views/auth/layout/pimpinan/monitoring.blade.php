@php
    $kerjasamaList = $dataKerjasama ?? collect();
    $funnel = $funnelData ?? collect();
    $sasarans = $sasaranData ?? collect();
    $totalSasaran = $totalSasaranEntries ?? 0;
    $finance = $financialTrend ?? collect();
    $kontrakAktif = $totalNilaiKontrakAktif ?? 0;
    $ranking = $unitRanking ?? collect();
    $critical = $criticalExpiry ?? collect();
    $warning = $warningExpiry ?? collect();
    $idle = $idleCooperations ?? collect();
    $compliance = $complianceAlerts ?? collect();
    $total = $totalKerjasama ?? 0;
    $aktif = $aktifCount ?? 0;
    $expired = $expiredCount ?? 0;
    $mouCount = $funnel['MoU'] ?? 0;
    $moaCount = $funnel['MoA'] ?? 0;
    $iaCount = $funnel['IA'] ?? 0;
    $bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
@endphp

<main id="mainContent" class="dk-page">

    {{-- â•â•â• HERO â•â•â• --}}
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('pimpinan.dashboard') }}"
                    style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px"><i
                        class="fas fa-home"></i></a>
                <span class="sep">/</span>
                <span class="current">Monitoring & Mitigasi Risiko</span>
            </div>
            <div class="dk-hero-main">
                <div class="dk-hero-icon"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <span class="dk-eyebrow">Pusat Kendali Pimpinan</span>
                    <h2 id="pageTitle">Monitoring & Early Warning</h2>
                    <p id="pageDesc">Pengambilan keputusan berbasis data: performa instansi, peringatan dini, dan
                        mitigasi risiko.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- â•â•â• EXECUTIVE SUMMARY BANNER â•â•â• --}}
    <div
        style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#334155 100%);border-radius:16px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.05)">
        <div
            style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(99,102,241,0.15) 0%,transparent 70%);border-radius:50%">
        </div>
        <div
            style="position:absolute;left:30%;bottom:-60px;width:160px;height:160px;background:radial-gradient(circle,rgba(16,185,129,0.1) 0%,transparent 70%);border-radius:50%">
        </div>
        <div style="position:relative;z-index:1">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:8px;height:8px;border-radius:50%;background:#10b981;animation:pulse-dot 2s infinite">
                </div>
                <span
                    style="color:#94a3b8;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:1px">Status
                    Monitoring Hari Ini {{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px">
                <div style="text-align:center">
                    <div style="font-size:32px;font-weight:800;color:#fff;line-height:1">{{ $critical->count() }}</div>
                    <div style="font-size:12px;color:#f87171;font-weight:600;margin-top:4px"><i
                            class="fas fa-exclamation-triangle"></i> Kritis (< 30 hari)</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-size:32px;font-weight:800;color:#fff;line-height:1">{{ $warning->count() }}
                        </div>
                        <div style="font-size:12px;color:#fbbf24;font-weight:600;margin-top:4px"><i
                                class="fas fa-clock"></i> Peringatan (31-90 hari)</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-size:32px;font-weight:800;color:#fff;line-height:1">{{ $idle->count() }}</div>
                        <div style="font-size:12px;color:#fb923c;font-weight:600;margin-top:4px"><i
                                class="fas fa-pause-circle"></i> Kerjasama Pasif</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-size:32px;font-weight:800;color:#fff;line-height:1">Rp
                            {{ number_format($kontrakAktif, 0, ',', '.') }}
                        </div>
                        <div style="font-size:12px;color:#34d399;font-weight:600;margin-top:4px"><i
                                class="fas fa-wallet"></i> Potensi Pendapatan Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes pulse-dot {

                0%,
                100% {
                    opacity: 1
                }

                50% {
                    opacity: .3
                }
            }

            .mn-card {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 16px;
                overflow: hidden;
                transition: transform .3s, box-shadow .3s
            }

            .mn-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(0, 0, 0, .06)
            }

            .mn-card-head {
                padding: 18px 22px;
                border-bottom: 1px solid var(--border);
                display: flex;
                justify-content: space-between;
                align-items: center
            }

            .mn-card-head h3 {
                font-size: 15px;
                font-weight: 700;
                color: var(--text);
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px
            }

            .mn-card-body {
                padding: 20px 22px
            }

            .mn-icon {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                flex-shrink: 0
            }

            .mn-rag-red {
                background: rgba(239, 68, 68, .1);
                color: #ef4444
            }

            .mn-rag-amber {
                background: rgba(245, 158, 11, .1);
                color: #f59e0b
            }

            .mn-rag-green {
                background: rgba(16, 185, 129, .1);
                color: #10b981
            }

            .mn-rag-blue {
                background: rgba(59, 130, 246, .1);
                color: #3b82f6
            }

            .mn-rag-purple {
                background: rgba(139, 92, 246, .1);
                color: #8b5cf6
            }

            .mn-tag {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                white-space: nowrap
            }

            .mn-alert-row {
                padding: 14px 22px;
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                gap: 14px;
                transition: background .2s;
                cursor: pointer
            }

            .mn-alert-row:hover {
                background: var(--hover)
            }

            .mn-alert-row:last-child {
                border-bottom: none
            }

            .mn-progress {
                width: 100%;
                background: var(--border);
                height: 6px;
                border-radius: 3px;
                overflow: hidden
            }

            .mn-progress-fill {
                height: 100%;
                border-radius: 3px;
                transition: width .8s ease
            }

            .mn-funnel-bar {
                height: 42px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-weight: 700;
                font-size: 14px;
                transition: width .8s ease;
                min-width: 60px
            }

            .mn-table-wrap {
                overflow-x: auto
            }

            .mn-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left
            }

            .mn-table thead tr {
                background: var(--bg);
                border-bottom: 2px solid var(--border)
            }

            .mn-table th {
                padding: 12px 18px;
                font-size: 11px;
                font-weight: 600;
                color: var(--text-sub);
                text-transform: uppercase;
                letter-spacing: .5px
            }

            .mn-table td {
                padding: 14px 18px;
                border-bottom: 1px solid var(--border);
                font-size: 13px;
                color: var(--text)
            }

            .mn-table tbody tr {
                transition: background .2s
            }

            .mn-table tbody tr:hover {
                background: var(--hover)
            }

            .mn-countdown {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-weight: 700;
                font-size: 12px;
                padding: 4px 10px;
                border-radius: 8px
            }

            .mn-section-title {
                font-size: 18px;
                font-weight: 800;
                color: var(--text);
                margin: 0 0 20px 0;
                display: flex;
                align-items: center;
                gap: 10px
            }

            .mn-section-title i {
                font-size: 20px
            }

            .mn-filter-bar {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                margin-bottom: 20px
            }

            .mn-filter-bar select {
                padding: 8px 14px;
                border-radius: 10px;
                border: 1px solid var(--border);
                background: var(--surface);
                color: var(--text);
                font-size: 13px;
                font-weight: 500;
                cursor: pointer
            }
        </style>
        {{-- â•â•â• I. GRAFIK PERFORMA INSTANSI â•â•â• --}}
        <h3 class="mn-section-title"><i class="fas fa-chart-line" style="color:#6366f1"></i> Grafik Performa Instansi
        </h3>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px">

            {{-- 1. Funnel MoU - MoA - IA --}}
            <div class="mn-card">
                <div class="mn-card-head">
                    <h3>
                        <div class="mn-icon mn-rag-purple"><i class="fas fa-filter"></i></div> Rasio Efektivitas
                        Kerjasama
                    </h3>
                    <span style="font-size:11px;color:var(--text-sub)">MoU - MoA - IA Conversion</span>
                </div>
                <div class="mn-card-body">
                    @php $maxFunnel = max($mouCount, $moaCount, $iaCount, 1); @endphp
                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px"><span
                                    style="font-weight:600;font-size:13px;color:var(--text)">MoU (Nota
                                    Kesepahaman)</span><span
                                    style="font-weight:800;color:#8b5cf6">{{ $mouCount }}</span></div>
                            <div class="mn-funnel-bar"
                                style="width:{{ $maxFunnel > 0 ? max(($mouCount / $maxFunnel) * 100, 15) : 15 }}%;background:linear-gradient(90deg,#8b5cf6,#a78bfa)">
                                {{ $mouCount }}
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px"><span
                                    style="font-weight:600;font-size:13px;color:var(--text)">MoA (Nota
                                    Kesepakatan)</span><span
                                    style="font-weight:800;color:#6366f1">{{ $moaCount }}</span></div>
                            <div class="mn-funnel-bar"
                                style="width:{{ $maxFunnel > 0 ? max(($moaCount / $maxFunnel) * 100, 15) : 15 }}%;background:linear-gradient(90deg,#6366f1,#818cf8)">
                                {{ $moaCount }}
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px"><span
                                    style="font-weight:600;font-size:13px;color:var(--text)">IA (Perjanjian
                                    Implementasi)</span><span
                                    style="font-weight:800;color:#4f46e5">{{ $iaCount }}</span></div>
                            <div class="mn-funnel-bar"
                                style="width:{{ $maxFunnel > 0 ? max(($iaCount / $maxFunnel) * 100, 15) : 15 }}%;background:linear-gradient(90deg,#4f46e5,#6366f1)">
                                {{ $iaCount }}
                            </div>
                        </div>
                    </div>
                    @php
                        // Stage 1: MoU → MoA
                        $rateMouToMoa = $mouCount > 0 ? round(($moaCount / $mouCount) * 100, 1) : 0;
                        // Stage 2: MoA → IA
                        $rateMoaToIa  = $moaCount > 0 ? round(($iaCount  / $moaCount) * 100, 1) : 0;
                        // Rata-rata konversi kedua tahap
                        $avgConversion = $mouCount > 0
                            ? round(($rateMouToMoa + $rateMoaToIa) / 2, 1)
                            : 0;
                    @endphp
                    <div style="margin-top:16px;padding:12px 16px;background:var(--bg);border-radius:10px;border:1px solid var(--border)">
                        <span style="font-size:12px;color:var(--text-sub)">Conversion Rate MoU-MoA-IA:</span>
                        <span style="font-weight:800;color:#4f46e5;margin-left:6px">{{ $avgConversion }}%</span>
                        <div style="margin-top:6px;display:flex;gap:12px">
                            <span style="font-size:11px;color:var(--text-sub)">MoU→MoA: <strong style="color:#6366f1">{{ $rateMouToMoa }}%</strong></span>
                            <span style="font-size:11px;color:var(--text-sub)">MoA→IA: <strong style="color:#4f46e5">{{ $rateMoaToIa }}%</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Capaian IKU / Sasaran --}}
            <div class="mn-card">
                <div class="mn-card-head">
                    <h3>
                        <div class="mn-icon mn-rag-green"><i class="fas fa-bullseye"></i></div> Capaian Indikator
                        Kinerja (IKU)
                    </h3>
                </div>
                <div class="mn-card-body">
                    @forelse($sasarans as $s)
                        @php $pct = $totalSasaran > 0 ? round(($s->total / $totalSasaran) * 100, 1) : 0; @endphp
                        <div style="margin-bottom:14px">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                                <span
                                    style="font-size:12px;font-weight:600;color:var(--text);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                    title="{{ $s->nama_sasaran }}">{{ $s->nama_sasaran }}</span>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span style="font-size:11px;color:var(--text-sub)">{{ $s->total }} kerjasama</span>
                                    <span class="mn-tag"
                                        style="background:{{ $pct >= 30 ? 'rgba(16,185,129,.1)' : ($pct >= 15 ? 'rgba(245,158,11,.1)' : 'rgba(239,68,68,.1)') }};color:{{ $pct >= 30 ? '#10b981' : ($pct >= 15 ? '#f59e0b' : '#ef4444') }}">{{ $pct }}%</span>
                                </div>
                            </div>
                            <div class="mn-progress">
                                <div class="mn-progress-fill"
                                    style="width:{{ $pct }}%;background:{{ $pct >= 30 ? '#10b981' : ($pct >= 15 ? '#f59e0b' : '#ef4444') }}">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:30px;color:var(--text-sub)"><i class="fas fa-bullseye"
                                style="font-size:28px;margin-bottom:8px;display:block;opacity:.3"></i>
                            <p style="font-size:13px;margin:0">Belum ada data sasaran terdaftar.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px">
            {{-- 3. Revenue / Financial Trend --}}
            <div class="mn-card">
                <div class="mn-card-head">
                    <h3>
                        <div class="mn-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i
                                class="fas fa-chart-area"></i></div> Kontribusi Finansial
                    </h3>
                    <span style="font-size:11px;color:var(--text-sub)">Trend Nilai Kontrak</span>
                </div>
                <div class="mn-card-body">
                    <div style="height:260px"><canvas id="financialTrendChart"
                            data-trend='{!! json_encode($finance) !!}'></canvas></div>
                </div>
            </div>

            {{-- 4. Ranking Unit Pelaksana --}}
            <div class="mn-card">
                <div class="mn-card-head">
                    <h3>
                        <div class="mn-icon mn-rag-blue"><i class="fas fa-ranking-star"></i></div> Ranking Unit
                        Pelaksana
                    </h3>
                </div>
                <div class="mn-card-body" style="padding:0">
                    @forelse($ranking->take(8) as $idx => $unit)
                        <div
                            style="display:flex;align-items:center;gap:14px;padding:12px 22px;border-bottom:1px solid var(--border)">
                            <div
                                style="width:28px;height:28px;border-radius:50%;background:{{ $idx < 3 ? 'linear-gradient(135deg,#f59e0b,#fbbf24)' : 'var(--bg)' }};color:{{ $idx < 3 ? '#fff' : 'var(--text-sub)' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0">
                                {{ $idx + 1 }}
                            </div>
                            <div style="flex:1;min-width:0">
                                <div
                                    style="font-weight:700;font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $unit->nama }}
                                </div>
                                <div style="font-size:11px;color:var(--text-sub)">{{ $unit->tipe }}</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="width:80px">
                                    <div class="mn-progress">
                                        <div class="mn-progress-fill"
                                            style="width:{{ $ranking->max('total') > 0 ? ($unit->total / $ranking->max('total')) * 100 : 0 }}%;background:#6366f1">
                                        </div>
                                    </div>
                                </div>
                                <span
                                    style="font-weight:800;font-size:14px;color:var(--accent);min-width:24px;text-align:right">{{ $unit->total }}</span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:30px;color:var(--text-sub)">
                            <p style="font-size:13px;margin:0">Belum ada data unit pelaksana.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        {{-- â•â•â• II. SISTEM PERINGATAN DINI â•â•â• --}}
        <h3 class="mn-section-title"><i class="fas fa-triangle-exclamation" style="color:#ef4444"></i> Sistem Peringatan
            Dini (Early Warning)</h3>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:28px">

            {{-- 1. Critical Expiry (< 30 hari) --}} <div class="mn-card" style="border-left:4px solid #ef4444">
                <div class="mn-card-head" style="background:rgba(239,68,68,.04)">
                    <h3>
                        <div class="mn-icon mn-rag-red"><i class="fas fa-fire"></i></div> Kritis (< 30 Hari)</h3>
                            <span class="mn-tag mn-rag-red" style="font-size:13px">{{ $critical->count() }}</span>
                </div>
                <div style="max-height:320px;overflow-y:auto">
                    @forelse($critical as $c)
                        @php $sisa = now()->diffInDays($c->end_date, false); @endphp
                        <div class="mn-alert-row">
                            <div style="flex:1;min-width:0">
                                <div style="font-weight:700;font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                    title="{{ $c->title }}">{{ $c->title }}</div>
                                <div style="font-size:11px;color:var(--text-sub);margin-top:2px"><i
                                        class="fas fa-building"></i> {{ $c->mitra->nama_mitra ?? '-' }}</div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                                <span class="mn-countdown" style="background:rgba(239,68,68,.1);color:#ef4444"><i
                                        class="fas fa-hourglass-half"></i> {{ max(0, $sisa) }} hari</span>
                                @if($c->pjInternal)
                                    <a href="mailto:" style="font-size:10px;color:#3b82f6;text-decoration:none;font-weight:600"
                                        title="PJ: {{ $c->pjInternal->nama }}"><i class="fas fa-envelope"></i> Hubungi PJ</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:30px;color:var(--text-sub)"><i class="fas fa-check-circle"
                                style="font-size:24px;color:#10b981;margin-bottom:8px;display:block"></i>
                            <p style="margin:0;font-size:12px">Tidak ada kerjasama kritis.</p>
                        </div>
                    @endforelse
                </div>
        </div>

        {{-- 2. Warning Expiry (31-90 hari) --}}
        <div class="mn-card" style="border-left:4px solid #f59e0b">
            <div class="mn-card-head" style="background:rgba(245,158,11,.04)">
                <h3>
                    <div class="mn-icon mn-rag-amber"><i class="fas fa-clock"></i></div> Peringatan (31-90 Hari)
                </h3>
                <span class="mn-tag mn-rag-amber" style="font-size:13px">{{ $warning->count() }}</span>
            </div>
            <div style="max-height:320px;overflow-y:auto">
                @forelse($warning as $w)
                    @php $sisa = now()->diffInDays($w->end_date, false); @endphp
                    <div class="mn-alert-row">
                        <div style="flex:1;min-width:0">
                            <div style="font-weight:700;font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                title="{{ $w->title }}">{{ $w->title }}</div>
                            <div style="font-size:11px;color:var(--text-sub);margin-top:2px"><i class="fas fa-building"></i>
                                {{ $w->mitra->nama_mitra ?? '-' }}</div>
                        </div>
                        <span class="mn-countdown" style="background:rgba(245,158,11,.1);color:#f59e0b"><i
                                class="fas fa-hourglass-half"></i> {{ $sisa }} hari</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:30px;color:var(--text-sub)"><i class="fas fa-check-circle"
                            style="font-size:24px;color:#10b981;margin-bottom:8px;display:block"></i>
                        <p style="margin:0;font-size:12px">Tidak ada peringatan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 3. Combined: Idle + Compliance --}}
        <div style="display:flex;flex-direction:column;gap:20px">
            {{-- Idle Cooperations --}}
            <div class="mn-card" style="border-left:4px solid #fb923c;flex:1">
                <div class="mn-card-head" style="background:rgba(251,146,60,.04)">
                    <h3 style="font-size:13px">
                        <div class="mn-icon"
                            style="background:rgba(251,146,60,.1);color:#fb923c;width:30px;height:30px;font-size:13px">
                            <i class="fas fa-pause"></i>
                        </div> Kerjasama Pasif
                    </h3>
                    <span class="mn-tag"
                        style="background:rgba(251,146,60,.1);color:#fb923c;font-size:13px">{{ $idle->count() }}</span>
                </div>
                <div style="max-height:130px;overflow-y:auto">
                    @forelse($idle->take(3) as $i)
                        <div class="mn-alert-row" style="padding:10px 18px">
                            <div style="width:6px;height:6px;border-radius:50%;background:#fb923c;flex-shrink:0"></div>
                            <div style="flex:1;min-width:0">
                                <div
                                    style="font-size:12px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $i->title }}
                                </div>
                                <div style="font-size:10px;color:var(--text-sub)">{{ $i->mitra->nama_mitra ?? '-' }}</div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:16px;color:var(--text-sub);font-size:11px">Semua kerjasama
                            aktif memiliki kegiatan.</div>
                    @endforelse
                </div>
            </div>

            {{-- Compliance Alert --}}
            <div class="mn-card" style="border-left:4px solid #6b7280;flex:1">
                <div class="mn-card-head" style="background:rgba(107,114,128,.04)">
                    <h3 style="font-size:13px">
                        <div class="mn-icon"
                            style="background:rgba(107,114,128,.1);color:#6b7280;width:30px;height:30px;font-size:13px">
                            <i class="fas fa-file-circle-exclamation"></i>
                        </div> Dokumen Tidak Lengkap
                    </h3>
                    <span class="mn-tag"
                        style="background:rgba(107,114,128,.1);color:#6b7280;font-size:13px">{{ $compliance->count() }}</span>
                </div>
                <div style="max-height:130px;overflow-y:auto">
                    @forelse($compliance->take(3) as $doc)
                        <div class="mn-alert-row" style="padding:10px 18px">
                            <div style="width:6px;height:6px;border-radius:50%;background:#6b7280;flex-shrink:0"></div>
                            <div style="flex:1;min-width:0">
                                <div
                                    style="font-size:12px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $doc->title }}
                                </div>
                                <div style="font-size:10px;color:var(--text-sub)">
                                    @if(!$doc->document_link) <span style="color:#ef4444">âš  Tanpa Dokumen</span> @endif
                                    @if(!$doc->pks_number) <span style="color:#f59e0b">âš  Tanpa PKS</span> @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:16px;color:var(--text-sub);font-size:11px">Semua dokumen
                            lengkap.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    {{-- â•â•â• III. TABEL MONITORING DETAIL â•â•â• --}}
    <div class="mn-card" style="margin-bottom:28px">
        <div class="mn-card-head">
            <h3>
                <div class="mn-icon mn-rag-blue"><i class="fas fa-table-list"></i></div> Tabel Monitoring Detail (Deep
                Dive)
            </h3>
            <span style="font-size:12px;color:var(--text-sub)">{{ $kerjasamaList->count() }} data ditemukan</span>
        </div>
        <div class="mn-card-body" x-data="{
        search: '',
        filterTahun: '',
        filterKategori: '',
        filterJenis: '',
        currentPage: 1,
        perPage: 10,
        get filtered() {
            return this.$refs.rows ? Array.from(this.$refs.rows.querySelectorAll('tr[data-row]')) : [];
        }
    }">
            {{-- Filter Bar --}}
            <div class="mn-filter-bar">
                <div style="position:relative;flex:1;max-width:280px">
                    <i class="fas fa-search"
                        style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-sub);font-size:12px"></i>
                    <input type="text" x-model="search" placeholder="Cari mitra, judul..."
                        style="width:100%;padding:8px 14px 8px 34px;border-radius:10px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:13px">
                </div>
                <select x-model="filterTahun">
                    <option value="">Semua Tahun</option>
                    @foreach($kerjasamaList->pluck('start_date')->filter()->map(fn($d) => $d->year)->unique()->sortDesc() as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
                <select x-model="filterKategori">
                    <option value="">Semua Kategori</option>
                    <option value="nasional">Nasional</option>
                    <option value="internasional">Internasional</option>
                </select>
                <select x-model="filterJenis">
                    <option value="">Semua Jenis</option>
                    <option value="MoU">MoU</option>
                    <option value="MoA">MoA</option>
                    <option value="IA">IA</option>
                </select>
            </div>

            <div class="mn-table-wrap">
                <table class="mn-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Nama Mitra</th>
                            <th>Klasifikasi</th>
                            <th>Jenis</th>
                            <th>Implementasi Luaran</th>
                            <th>Sisa Waktu</th>
                            <th>Status</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody x-ref="rows">
                        @forelse($kerjasamaList as $k)
                            @php
                                $endDate = $k->end_date;
                                $sisaHari = $endDate ? (int) now()->diffInDays($endDate, false) : null;
                                $statusKerjasama = strtolower($k->status ?? '');
                                $isExpired = in_array($statusKerjasama, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa']);
                                $isAktif = $statusKerjasama === 'aktif';
                                $kategoriMitra = strtolower($k->mitra->kategori ?? '');
                                $tahunMulai = $k->start_date ? $k->start_date->year : '';
                                $luaran = $k->details->map(fn($d) => ($d->volume_luaran ? $d->volume_luaran . ' ' . ($d->satuan_luaran ?? '') : null))->filter()->implode(', ');
                            @endphp
                            <tr data-row
                                data-search="{{ strtolower($k->mitra->nama_mitra ?? '') }} {{ strtolower($k->title ?? '') }}"
                                data-tahun="{{ $tahunMulai }}" data-kategori="{{ $kategoriMitra }}"
                                data-jenis="{{ $k->jenis ?? '' }}" x-show="
                                                    (search === '' || $el.dataset.search.includes(search.toLowerCase())) &&
                                                    (filterTahun === '' || $el.dataset.tahun === filterTahun) &&
                                                    (filterKategori === '' || $el.dataset.kategori === filterKategori) &&
                                                    (filterJenis === '' || $el.dataset.jenis === filterJenis)
                                                ">
                                <td><span
                                        style="font-weight:600;color:var(--text-sub);font-size:12px">{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div style="font-weight:700;font-size:13px;color:var(--text)">
                                        {{ $k->mitra->nama_mitra ?? '-' }}
                                    </div>
                                    <div style="font-size:11px;color:var(--text-sub);margin-top:2px;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                        title="{{ $k->title }}">{{ $k->title }}</div>
                                </td>
                                <td><span class="mn-tag"
                                        style="background:var(--bg);color:var(--text-sub);border:1px solid var(--border)">{{ $k->mitra->klasifikasi->nama ?? '-' }}</span>
                                </td>
                                <td><span class="mn-tag mn-rag-purple">{{ $k->jenis ?? '-' }}</span></td>
                                <td style="font-size:12px">{{ $luaran ?: '-' }}</td>
                                <td>
                                    @if($sisaHari !== null)
                                        @if($sisaHari < 0)
                                            <span class="mn-countdown" style="background:rgba(239,68,68,.1);color:#ef4444"><i
                                                    class="fas fa-times-circle"></i> Lewat {{ abs($sisaHari) }}hr</span>
                                        @elseif($sisaHari <= 30)
                                            <span class="mn-countdown" style="background:rgba(239,68,68,.1);color:#ef4444"><i
                                                    class="fas fa-fire"></i> {{ $sisaHari }} hari</span>
                                        @elseif($sisaHari <= 90)
                                            <span class="mn-countdown" style="background:rgba(245,158,11,.1);color:#f59e0b"><i
                                                    class="fas fa-clock"></i> {{ $sisaHari }} hari</span>
                                        @else
                                            <span class="mn-countdown" style="background:rgba(16,185,129,.1);color:#10b981"><i
                                                    class="fas fa-check-circle"></i> {{ $sisaHari }} hari</span>
                                        @endif
                                    @else
                                        <span style="font-size:12px;color:var(--text-sub)">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isAktif)
                                        <span class="mn-tag mn-rag-green"><i class="fas fa-circle" style="font-size:6px"></i>
                                            Aktif</span>
                                    @elseif($isExpired)
                                        <span class="mn-tag mn-rag-red"><i class="fas fa-circle" style="font-size:6px"></i>
                                            Kadaluarsa</span>
                                    @else
                                        <span class="mn-tag" style="background:var(--bg);color:var(--text-sub)"><i
                                                class="fas fa-circle" style="font-size:6px"></i>
                                            {{ ucwords($statusKerjasama ?: 'N/A') }}</span>
                                    @endif
                                </td>
                                <td style="text-align:center">
                                    <a href="{{ route('pimpinan.monitoring.detail', $k->id) }}"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:rgba(79,70,229,.1);color:#4f46e5;text-decoration:none;transition:all .2s"
                                        title="Detail" onmouseover="this.style.background='rgba(79,70,229,.2)'"
                                        onmouseout="this.style.background='rgba(79,70,229,.1)'"><i class="fas fa-eye"
                                            style="font-size:13px"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:60px 20px;color:var(--text-sub)"><i
                                        class="fas fa-folder-open"
                                        style="font-size:32px;margin-bottom:12px;display:block;opacity:.3"></i>Belum ada
                                    data kerjasama.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- â•â•â• CHART.JS SCRIPTS â•â•â• --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
        const textColor = isDark ? '#8b92a8' : '#6b7280';

        // Financial Trend Chart
        const finCtx = document.getElementById('financialTrendChart');
        if (finCtx) {
            const raw = JSON.parse(finCtx.dataset.trend || '[]');
            const bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const labels = raw.map(i => bulan[i.bulan] + ' ' + i.tahun);
            const data = raw.map(i => i.total_kontrak);

            new Chart(finCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai Kontrak (Rp)',
                        data: data,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#f59e0b',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v)
                            }
                        },
                        x: { grid: { display: false }, ticks: { color: textColor, maxRotation: 45 } }
                    }
                }
            });
        }
    });
</script>
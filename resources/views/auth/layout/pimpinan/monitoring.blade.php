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

<link rel="stylesheet" href="{{ asset('css/auth/pimpinan/monitoring.css') }}">

<main id="mainContent" class="dk-page">

    {{-- ═══ HERO ═══ --}}
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('pimpinan.dashboard') }}" class="mn-breadcrumb-link"><i class="fas fa-home"></i></a>
                <span class="sep">/</span>
                <span class="current">Monitoring & Mitigasi Risiko</span>
            </div>
            <div class="dk-hero-main">
                <div class="dk-hero-icon"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <span class="dk-eyebrow">Pusat Kendali Pimpinan</span>
                    <h2 id="pageTitle">Monitoring & Early Warning</h2>
                    <p id="pageDesc">Pengambilan keputusan berbasis data: performa instansi, peringatan dini, dan mitigasi risiko.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ EXECUTIVE SUMMARY BANNER ═══ --}}
    <div class="mn-exec-banner">
        <div class="mn-exec-bg-1"></div>
        <div class="mn-exec-bg-2"></div>
        <div class="mn-exec-content">
            <div class="mn-exec-status">
                <div class="mn-pulse-dot"></div>
                <span class="mn-exec-status-text">Status Monitoring Hari Ini {{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <div class="mn-exec-grid">
                <div class="mn-exec-item">
                    <div class="mn-exec-val">{{ $critical->count() }}</div>
                    <div class="mn-exec-label mn-text-red"><i class="fas fa-exclamation-triangle"></i> Kritis (< 30 hari)</div>
                </div>
                <div class="mn-exec-item">
                    <div class="mn-exec-val">{{ $warning->count() }}</div>
                    <div class="mn-exec-label mn-text-amber"><i class="fas fa-clock"></i> Peringatan (31-90 hari)</div>
                </div>
                <div class="mn-exec-item">
                    <div class="mn-exec-val">{{ $idle->count() }}</div>
                    <div class="mn-exec-label mn-text-orange"><i class="fas fa-pause-circle"></i> Kerjasama Pasif</div>
                </div>
                <div class="mn-exec-item">
                    <div class="mn-exec-val">Rp {{ number_format($kontrakAktif, 0, ',', '.') }}</div>
                    <div class="mn-exec-label mn-text-green"><i class="fas fa-wallet"></i> Potensi Pendapatan Aktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ I. GRAFIK PERFORMA INSTANSI ═══ --}}
    <h3 class="mn-section-title"><i class="fas fa-chart-line" style="color:#6366f1"></i> Grafik Performa Instansi</h3>

    <div class="mn-grid-2">

        {{-- 1. Funnel MoU - MoA - IA --}}
        <div class="mn-card">
            <div class="mn-card-head">
                <h3>
                    <div class="mn-icon mn-rag-purple"><i class="fas fa-filter"></i></div> Rasio Efektivitas Kerjasama
                </h3>
                <span style="font-size:11px;color:var(--text-sub)">MoU - MoA - IA Conversion</span>
            </div>
            <div class="mn-card-body">
                @php $maxFunnel = max($mouCount, $moaCount, $iaCount, 1); @endphp
                <div class="mn-flex-col">
                    <div>
                        <div class="mn-funnel-row">
                            <span class="mn-funnel-label">MoU (Nota Kesepahaman)</span>
                            <span class="mn-funnel-val mn-funnel-color-1">{{ $mouCount }}</span>
                        </div>
                        <div class="mn-funnel-bar mn-funnel-bar-1" style="width:{{ $maxFunnel > 0 ? max(($mouCount / $maxFunnel) * 100, 15) : 15 }}%;">
                            {{ $mouCount }}
                        </div>
                    </div>
                    <div>
                        <div class="mn-funnel-row">
                            <span class="mn-funnel-label">MoA (Nota Kesepakatan)</span>
                            <span class="mn-funnel-val mn-funnel-color-2">{{ $moaCount }}</span>
                        </div>
                        <div class="mn-funnel-bar mn-funnel-bar-2" style="width:{{ $maxFunnel > 0 ? max(($moaCount / $maxFunnel) * 100, 15) : 15 }}%;">
                            {{ $moaCount }}
                        </div>
                    </div>
                    <div>
                        <div class="mn-funnel-row">
                            <span class="mn-funnel-label">IA (Perjanjian Implementasi)</span>
                            <span class="mn-funnel-val mn-funnel-color-3">{{ $iaCount }}</span>
                        </div>
                        <div class="mn-funnel-bar mn-funnel-bar-3" style="width:{{ $maxFunnel > 0 ? max(($iaCount / $maxFunnel) * 100, 15) : 15 }}%;">
                            {{ $iaCount }}
                        </div>
                    </div>
                </div>
                @php
                    // Stage 1: MoU → MoA
                    $rateMouToMoa = $mouCount > 0 ? round(($moaCount / $mouCount) * 100, 1) : 0;
                    // Stage 2: MoA → IA
                    $rateMoaToIa = $moaCount > 0 ? round(($iaCount / $moaCount) * 100, 1) : 0;
                    // Rata-rata konversi kedua tahap
                    $avgConversion = $mouCount > 0 ? round(($rateMouToMoa + $rateMoaToIa) / 2, 1) : 0;
                @endphp
                <div class="mn-funnel-conversion">
                    <span class="mn-funnel-conv-label">Conversion Rate MoU-MoA-IA:</span>
                    <span class="mn-funnel-conv-val">{{ $avgConversion }}%</span>
                    <div class="mn-funnel-conv-details">
                        <span class="mn-funnel-conv-detail-item">MoU→MoA: <strong class="mn-funnel-conv-detail-strong-1">{{ $rateMouToMoa }}%</strong></span>
                        <span class="mn-funnel-conv-detail-item">MoA→IA: <strong class="mn-funnel-conv-detail-strong-2">{{ $rateMoaToIa }}%</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Capaian IKU / Sasaran --}}
        <div class="mn-card">
            <div class="mn-card-head">
                <h3>
                    <div class="mn-icon mn-rag-green"><i class="fas fa-bullseye"></i></div> Capaian Indikator Kinerja (IKU)
                </h3>
            </div>
            <div class="mn-card-body">
                @forelse($sasarans as $s)
                    @php $pct = $totalSasaran > 0 ? round(($s->total / $totalSasaran) * 100, 1) : 0; @endphp
                    <div class="mn-iku-item">
                        <div class="mn-iku-header">
                            <span class="mn-iku-title" title="{{ $s->nama_sasaran }}">{{ $s->nama_sasaran }}</span>
                            <div class="mn-iku-stats">
                                <span class="mn-iku-count">{{ $s->total }} kerjasama</span>
                                <span class="mn-tag" style="background:{{ $pct >= 30 ? 'rgba(16,185,129,.1)' : ($pct >= 15 ? 'rgba(245,158,11,.1)' : 'rgba(239,68,68,.1)') }};color:{{ $pct >= 30 ? '#10b981' : ($pct >= 15 ? '#f59e0b' : '#ef4444') }}">{{ $pct }}%</span>
                            </div>
                        </div>
                        <div class="mn-progress">
                            <div class="mn-progress-fill" style="width:{{ $pct }}%;background:{{ $pct >= 30 ? '#10b981' : ($pct >= 15 ? '#f59e0b' : '#ef4444') }}"></div>
                        </div>
                    </div>
                @empty
                    <div class="mn-empty-state"><i class="fas fa-bullseye mn-empty-icon"></i>
                        <p class="mn-empty-text">Belum ada data sasaran terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mn-grid-2">
        {{-- 3. Revenue / Financial Trend --}}
        <div class="mn-card">
            <div class="mn-card-head">
                <h3>
                    <div class="mn-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="fas fa-chart-area"></i></div> Kontribusi Finansial
                </h3>
                <span style="font-size:11px;color:var(--text-sub)">Trend Nilai Kontrak</span>
            </div>
            <div class="mn-card-body">
                <div style="height:260px"><canvas id="financialTrendChart" data-trend='{!! json_encode($finance) !!}'></canvas></div>
            </div>
        </div>

        {{-- 4. Ranking Unit Pelaksana --}}
        <div class="mn-card">
            <div class="mn-card-head">
                <h3>
                    <div class="mn-icon mn-rag-blue"><i class="fas fa-ranking-star"></i></div> Ranking Unit Pelaksana
                </h3>
            </div>
            <div class="mn-card-body" style="padding:0">
                @forelse($ranking->take(8) as $idx => $unit)
                    <div class="mn-ranking-item">
                        <div class="mn-ranking-no {{ $idx < 3 ? 'top' : 'other' }}">
                            {{ $idx + 1 }}
                        </div>
                        <div class="mn-ranking-info">
                            <div class="mn-ranking-name">{{ $unit->nama }}</div>
                            <div class="mn-ranking-type">{{ $unit->tipe }}</div>
                        </div>
                        <div class="mn-ranking-score-wrap">
                            <div class="mn-ranking-bar-wrap">
                                <div class="mn-progress">
                                    <div class="mn-progress-fill" style="width:{{ $ranking->max('total') > 0 ? ($unit->total / $ranking->max('total')) * 100 : 0 }}%;background:#6366f1"></div>
                                </div>
                            </div>
                            <span class="mn-ranking-score">{{ $unit->total }}</span>
                        </div>
                    </div>
                @empty
                    <div class="mn-empty-state">
                        <p class="mn-empty-text">Belum ada data unit pelaksana.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    {{-- ═══ II. SISTEM PERINGATAN DINI ═══ --}}
    <h3 class="mn-section-title"><i class="fas fa-triangle-exclamation" style="color:#ef4444"></i> Sistem Peringatan Dini (Early Warning)</h3>

    <div class="mn-grid-3">

        {{-- 1. Critical Expiry (< 30 hari) --}} 
        <div class="mn-card mn-alert-card red">
            <div class="mn-card-head mn-alert-head-red">
                <h3>
                    <div class="mn-icon mn-rag-red"><i class="fas fa-fire"></i></div> Kritis (< 30 Hari)</h3>
                        <span class="mn-tag mn-rag-red" style="font-size:13px">{{ $critical->count() }}</span>
            </div>
            <div class="mn-alert-list-tall">
                @forelse($critical as $c)
                    @php $sisa = now()->diffInDays($c->end_date, false); @endphp
                    <div class="mn-alert-row">
                        <div style="flex:1;min-width:0">
                            <div class="mn-alert-title" title="{{ $c->title }}">{{ $c->title }}</div>
                            <div class="mn-alert-subtitle"><i class="fas fa-building"></i> {{ $c->mitra->nama_mitra ?? '-' }}</div>
                        </div>
                        <div class="mn-alert-actions">
                            <span class="mn-countdown" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="fas fa-hourglass-half"></i> {{ max(0, $sisa) }} hari</span>
                            @if($c->pjInternal)
                                <a href="mailto:" class="mn-alert-link" title="PJ: {{ $c->pjInternal->nama }}"><i class="fas fa-envelope"></i> Hubungi PJ</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="mn-empty-state"><i class="fas fa-check-circle" style="font-size:24px;color:#10b981;margin-bottom:8px;display:block"></i>
                        <p style="margin:0;font-size:12px">Tidak ada kerjasama kritis.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 2. Warning Expiry (31-90 hari) --}}
        <div class="mn-card mn-alert-card amber">
            <div class="mn-card-head mn-alert-head-amber">
                <h3>
                    <div class="mn-icon mn-rag-amber"><i class="fas fa-clock"></i></div> Peringatan (31-90 Hari)
                </h3>
                <span class="mn-tag mn-rag-amber" style="font-size:13px">{{ $warning->count() }}</span>
            </div>
            <div class="mn-alert-list-tall">
                @forelse($warning as $w)
                    @php $sisa = now()->diffInDays($w->end_date, false); @endphp
                    <div class="mn-alert-row">
                        <div style="flex:1;min-width:0">
                            <div class="mn-alert-title" title="{{ $w->title }}">{{ $w->title }}</div>
                            <div class="mn-alert-subtitle"><i class="fas fa-building"></i> {{ $w->mitra->nama_mitra ?? '-' }}</div>
                        </div>
                        <span class="mn-countdown" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="fas fa-hourglass-half"></i> {{ $sisa }} hari</span>
                    </div>
                @empty
                    <div class="mn-empty-state"><i class="fas fa-check-circle" style="font-size:24px;color:#10b981;margin-bottom:8px;display:block"></i>
                        <p style="margin:0;font-size:12px">Tidak ada peringatan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 3. Combined: Idle + Compliance --}}
        <div style="display:flex;flex-direction:column;gap:20px">
            {{-- Idle Cooperations --}}
            <div class="mn-card mn-alert-card orange" style="flex:1">
                <div class="mn-card-head mn-alert-head-orange">
                    <h3 style="font-size:13px">
                        <div class="mn-icon" style="background:rgba(251,146,60,.1);color:#fb923c;width:30px;height:30px;font-size:13px">
                            <i class="fas fa-pause"></i>
                        </div> Kerjasama Pasif
                    </h3>
                    <span class="mn-tag" style="background:rgba(251,146,60,.1);color:#fb923c;font-size:13px">{{ $idle->count() }}</span>
                </div>
                <div class="mn-alert-list-short">
                    @forelse($idle->take(3) as $i)
                        <div class="mn-alert-row" style="padding:10px 18px">
                            <div class="mn-alert-dot orange"></div>
                            <div style="flex:1;min-width:0">
                                <div class="mn-alert-text-small">{{ $i->title }}</div>
                                <div class="mn-alert-text-xs">{{ $i->mitra->nama_mitra ?? '-' }}</div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:16px;color:var(--text-sub);font-size:11px">Semua kerjasama aktif memiliki kegiatan.</div>
                    @endforelse
                </div>
            </div>

            {{-- Compliance Alert --}}
            <div class="mn-card mn-alert-card gray" style="flex:1">
                <div class="mn-card-head mn-alert-head-gray">
                    <h3 style="font-size:13px">
                        <div class="mn-icon" style="background:rgba(107,114,128,.1);color:#6b7280;width:30px;height:30px;font-size:13px">
                            <i class="fas fa-file-circle-exclamation"></i>
                        </div> Dokumen Tidak Lengkap
                    </h3>
                    <span class="mn-tag" style="background:rgba(107,114,128,.1);color:#6b7280;font-size:13px">{{ $compliance->count() }}</span>
                </div>
                <div class="mn-alert-list-short">
                    @forelse($compliance->take(3) as $doc)
                        <div class="mn-alert-row" style="padding:10px 18px">
                            <div class="mn-alert-dot gray"></div>
                            <div style="flex:1;min-width:0">
                                <div class="mn-alert-text-small">{{ $doc->title }}</div>
                                <div class="mn-alert-text-xs">
                                    @if(!$doc->document_link) <span style="color:#ef4444">⚠ Tanpa Dokumen</span> @endif
                                    @if(!$doc->pks_number) <span style="color:#f59e0b">⚠ Tanpa PKS</span> @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:16px;color:var(--text-sub);font-size:11px">Semua dokumen lengkap.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    {{-- ═══ III. TABEL MONITORING DETAIL ═══ --}}
    <div class="mn-card" style="margin-bottom:28px">
        <div class="mn-card-head">
            <h3>
                <div class="mn-icon mn-rag-blue"><i class="fas fa-table-list"></i></div> Tabel Monitoring Detail (Deep Dive)
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
                                <td><span class="mn-table-num">{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    <div class="mn-table-title">{{ $k->mitra->nama_mitra ?? '-' }}</div>
                                    <div class="mn-table-desc" title="{{ $k->title }}">{{ $k->title }}</div>
                                </td>
                                <td><span class="mn-tag" style="background:var(--bg);color:var(--text-sub);border:1px solid var(--border)">{{ $k->mitra->klasifikasi->nama ?? '-' }}</span></td>
                                <td><span class="mn-tag mn-rag-purple">{{ $k->jenis ?? '-' }}</span></td>
                                <td style="font-size:12px">{{ $luaran ?: '-' }}</td>
                                <td>
                                    @if($sisaHari !== null)
                                        @if($sisaHari < 0)
                                            <span class="mn-countdown" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="fas fa-times-circle"></i> Lewat {{ abs($sisaHari) }}hr</span>
                                        @elseif($sisaHari <= 30)
                                            <span class="mn-countdown" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="fas fa-fire"></i> {{ $sisaHari }} hari</span>
                                        @elseif($sisaHari <= 90)
                                            <span class="mn-countdown" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="fas fa-clock"></i> {{ $sisaHari }} hari</span>
                                        @else
                                            <span class="mn-countdown" style="background:rgba(16,185,129,.1);color:#10b981"><i class="fas fa-check-circle"></i> {{ $sisaHari }} hari</span>
                                        @endif
                                    @else
                                        <span style="font-size:12px;color:var(--text-sub)">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isAktif)
                                        <span class="mn-tag mn-rag-green"><i class="fas fa-circle mn-table-status-dot"></i> Aktif</span>
                                    @elseif($isExpired)
                                        <span class="mn-tag mn-rag-red"><i class="fas fa-circle mn-table-status-dot"></i> Kadaluarsa</span>
                                    @else
                                        <span class="mn-tag" style="background:var(--bg);color:var(--text-sub)"><i class="fas fa-circle mn-table-status-dot"></i> {{ ucwords($statusKerjasama ?: 'N/A') }}</span>
                                    @endif
                                </td>
                                <td style="text-align:center">
                                    <a href="{{ route('pimpinan.monitoring.detail', $k->id) }}" class="mn-table-actions" title="Detail"><i class="fas fa-eye" style="font-size:13px"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:60px 20px;color:var(--text-sub)"><i class="fas fa-folder-open" style="font-size:32px;margin-bottom:12px;display:block;opacity:.3"></i>Belum ada data kerjasama.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<script src="{{ asset('js/auth/pimpinan/monitoring.js') }}"></script>
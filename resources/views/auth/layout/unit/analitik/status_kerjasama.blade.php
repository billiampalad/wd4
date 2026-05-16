<link rel="stylesheet" href="{{ asset('css/auth/unit/analitik/status_kerjasama.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Status Kerjasama</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-chart-line"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Analitik Status Kerjasama</h2>
                    <p class="ud-subtitle">
                        Data status kerjasama Politeknik Negeri Manado Tahun {{ now()->year }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="sk-card sk-status-card">
        <header class="sk-card-head sk-card-head-muted">
            <div>
                <h2 class="sk-title">
                    <i class="fas fa-chart-pie"></i>
                    <span>Status Kerjasama</span>
                </h2>
                <p class="sk-desc">Proporsi kerjasama berdasarkan status/masa berlaku dokumen.</p>
            </div>
        </header>

        <div class="sk-donut-wrap">
            <canvas id="statusKerjasamaChart" aria-label="Grafik status kerjasama"></canvas>
        </div>

        <div class="sk-legend" aria-label="Legenda status kerjasama">
            @foreach ($statusKerjasamaData['labels'] as $index => $label)
                <div class="sk-legend-item">
                    <span class="sk-legend-swatch"
                        style="--swatch: {{ $statusKerjasamaData['colors'][$index] }}"></span>
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="sk-card sk-growth-card">
        <header class="sk-card-head">
            <h2 class="sk-title sk-title-compact">
                <i class="fas fa-chart-line"></i>
                <span>Pertumbuhan Kerjasama</span>
            </h2>
        </header>

        <div class="sk-line-wrap">
            <canvas id="pertumbuhanKerjasamaChart" aria-label="Grafik pertumbuhan kerjasama"></canvas>
        </div>

        <div class="sk-avg-row">
            <div class="sk-avg-item sk-avg-mou">
                <span>AVG MoU</span>
                <strong>{{ number_format($growthAverages['mou'] ?? 0) }}</strong>
                <small>/thn</small>
            </div>
            <div class="sk-avg-item sk-avg-moa">
                <span>AVG MoA</span>
                <strong>{{ number_format($growthAverages['moa'] ?? 0) }}</strong>
                <small>/thn</small>
            </div>
            <div class="sk-avg-item sk-avg-ia">
                <span>AVG IA</span>
                <strong>{{ number_format($growthAverages['ia'] ?? 0) }}</strong>
                <small>/thn</small>
            </div>
        </div>
    </section>

    <script type="application/json" id="statusKerjasamaData">@json($statusKerjasamaData)</script>
    <script type="application/json" id="pertumbuhanKerjasamaData">@json($growthData)</script>
</main>

<script src="{{ asset('js/auth/unit/analitik/status_kerjasama.js') }}" data-turbo-track="reload"></script>

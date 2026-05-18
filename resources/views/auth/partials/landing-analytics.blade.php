<section class="analytics-section" id="visualisasi-data" aria-labelledby="analytics-title">
    <div class="analytics-shell">
        <div class="analytics-wrap-head">
            <div>
                <span class="analytics-kicker">Visualisasi Data</span>
                <h3 class="analytics-heading" id="analytics-title">Pola penting dari portofolio kerjasama publik</h3>
            </div>
            @if ($analyticsContext)
                <p class="analytics-context">{{ $analyticsContext }}</p>
            @endif
        </div>

        <script type="application/json" data-analytics-payload>@json($chartPayload)</script>

        <div class="analytics-grid">
            <article class="analytics-card analytics-card-wide analytics-card-status" data-analytics-card>
                <div class="analytics-card-head">
                    <div>
                        <span class="analytics-card-label">Breakdown Status</span>
                        <h4 class="analytics-card-title">Sebaran status kerja sama saat ini</h4>
                    </div>
                    @if ($statusBreakdown['has_data'])
                        <p class="analytics-card-note">
                            Dominan: {{ $statusBreakdown['dominant_label'] }}
                            ({{ number_format($statusBreakdown['dominant_share'], 0, ',', '.') }}%)
                        </p>
                    @endif
                </div>

                @if ($statusBreakdown['has_data'])
                    <div class="analytics-card-body analytics-card-body-split">
                        <div class="analytics-canvas-wrap analytics-canvas-wrap-donut">
                            <canvas data-analytics-chart="status" aria-label="Grafik status kerja sama"></canvas>
                        </div>

                        <div class="analytics-legend-grid">
                            @foreach ($statusBreakdown['items'] as $item)
                                <div class="analytics-legend-item tone-{{ $item['tone'] }} {{ $item['count'] === 0 ? 'is-muted' : '' }}">
                                    <span class="analytics-legend-dot" aria-hidden="true"></span>
                                    <div class="analytics-legend-copy">
                                        <span class="analytics-legend-label">{{ $item['label'] }}</span>
                                        <span class="analytics-legend-meta">
                                            {{ number_format($item['count'], 0, ',', '.') }} data /
                                            {{ number_format($item['share'], 0, ',', '.') }}%
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="analytics-empty">
                        Belum ada portofolio kerja sama yang bisa divisualkan untuk kombinasi filter ini.
                    </div>
                @endif
            </article>

            <article class="analytics-card analytics-card-trend" data-analytics-card>
                <div class="analytics-card-head">
                    <div>
                        <span class="analytics-card-label">Tren Tahunan</span>
                        <h4 class="analytics-card-title">Pergerakan kerja sama per tahun</h4>
                    </div>
                    @if ($trendByYear['has_data'])
                        <p class="analytics-card-note">{{ $trendByYear['range_label'] }}</p>
                    @endif
                </div>

                @if ($trendByYear['has_data'])
                    <div class="analytics-canvas-wrap analytics-canvas-wrap-trend">
                        <canvas data-analytics-chart="trend" aria-label="Grafik tren kerja sama per tahun"></canvas>
                    </div>
                @else
                    <div class="analytics-empty">
                        Belum ada data tahun pelaksanaan yang cukup untuk menampilkan tren.
                    </div>
                @endif
            </article>

            <article class="analytics-card" data-analytics-card>
                <div class="analytics-card-head">
                    <div>
                        <span class="analytics-card-label">Komposisi Mitra</span>
                        <h4 class="analytics-card-title">Sebaran nasional dan internasional</h4>
                    </div>
                    @if ($mitraComposition['has_data'])
                        <p class="analytics-card-note">{{ number_format($mitraComposition['total'], 0, ',', '.') }} mitra</p>
                    @endif
                </div>

                @if ($mitraComposition['has_data'])
                    <div class="analytics-card-body analytics-card-body-split analytics-card-body-compact">
                        <div class="analytics-canvas-wrap analytics-canvas-wrap-donut-sm">
                            <canvas data-analytics-chart="mitra" aria-label="Grafik komposisi mitra"></canvas>
                        </div>

                        <div class="analytics-legend-stack">
                            @foreach ($mitraComposition['items'] as $item)
                                <div class="analytics-legend-item tone-{{ $item['tone'] }} {{ $item['count'] === 0 ? 'is-muted' : '' }}">
                                    <span class="analytics-legend-dot" aria-hidden="true"></span>
                                    <div class="analytics-legend-copy">
                                        <span class="analytics-legend-label">{{ $item['label'] }}</span>
                                        <span class="analytics-legend-meta">
                                            {{ number_format($item['count'], 0, ',', '.') }} mitra /
                                            {{ number_format($item['share'], 0, ',', '.') }}%
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="analytics-empty">
                        Komposisi mitra akan muncul setelah ada mitra yang sesuai dengan filter aktif.
                    </div>
                @endif
            </article>

            <article class="analytics-card" data-analytics-card>
                <div class="analytics-card-head">
                    <div>
                        <span class="analytics-card-label">Jenis Mitra</span>
                        <h4 class="analytics-card-title">Klasifikasi yang paling sering muncul</h4>
                    </div>
                    @if ($topClassifications['has_data'])
                        <p class="analytics-card-note">5 kategori teratas</p>
                    @endif
                </div>

                @if ($topClassifications['has_data'])
                    <div class="analytics-canvas-wrap analytics-canvas-wrap-bar">
                        <canvas data-analytics-chart="classifications" aria-label="Grafik klasifikasi mitra teratas"></canvas>
                    </div>
                @else
                    <div class="analytics-empty">
                        Belum ada klasifikasi mitra yang bisa dirangkum dari hasil saat ini.
                    </div>
                @endif
            </article>

            <article class="analytics-card" data-analytics-card>
                <div class="analytics-card-head">
                    <div>
                        <span class="analytics-card-label">Bidang Kerjasama</span>
                        <h4 class="analytics-card-title">Top area kolaborasi yang paling aktif</h4>
                    </div>
                    @if ($topFields['has_data'])
                        <p class="analytics-card-note">5 bidang teratas</p>
                    @endif
                </div>

                @if ($topFields['has_data'])
                    <div class="analytics-canvas-wrap analytics-canvas-wrap-bar">
                        <canvas data-analytics-chart="fields" aria-label="Grafik bidang kerja sama teratas"></canvas>
                    </div>
                @else
                    <div class="analytics-empty">
                        Bidang kerja sama akan terpetakan setelah detail kegiatan tersedia.
                    </div>
                @endif
            </article>

            <article class="analytics-card analytics-card-full" data-analytics-card>
                <div class="analytics-card-head">
                    <div>
                        <span class="analytics-card-label">Upcoming Attention</span>
                        <h4 class="analytics-card-title">{{ $attentionPanel['headline'] ?? 'Sorotan portofolio terbaru' }}</h4>
                    </div>
                    @if (! empty($attentionPanel['description']))
                        <p class="analytics-card-note">{{ $attentionPanel['description'] }}</p>
                    @endif
                </div>

                @if ($attentionPanel['has_data'])
                    <div class="attention-list" aria-label="Daftar perhatian portofolio publik">
                        @foreach ($attentionPanel['items'] as $item)
                            <article class="attention-item tone-{{ $item['tone'] }}">
                                <div class="attention-copy">
                                    <h5 class="attention-title">{{ $item['title'] }}</h5>
                                    <p class="attention-subtitle">{{ $item['partner'] }}</p>
                                </div>
                                <div class="attention-meta">
                                    <span class="attention-date">{{ $item['meta_label'] }}</span>
                                    <span class="attention-badge">{{ $item['supporting_label'] }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="analytics-empty">
                        {{ $attentionPanel['description'] ?? 'Belum ada item perhatian untuk ditampilkan.' }}
                    </div>
                @endif
            </article>
        </div>
    </div>
</section>

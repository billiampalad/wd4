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

             <article class="analytics-card analytics-card-full analytics-card-geo" data-analytics-card data-geo-card>
                 <div class="analytics-card-head geo-card-head">
                     <div>
                         <span class="analytics-card-label">Sebaran Geografis</span>
                         <h4 class="analytics-card-title">Choropleth kerja sama berdasarkan lokasi mitra</h4>
                     </div>

                    <div class="geo-controls">
                        <div class="geo-toggle" role="tablist" aria-label="Pilih peta">
                            <button type="button" class="geo-toggle-btn is-active" data-geo-toggle="world"
                                id="geo-tab-world" role="tab" aria-selected="true" aria-controls="geo-panel-world">
                                Dunia
                            </button>
                            <button type="button" class="geo-toggle-btn" data-geo-toggle="indonesia" id="geo-tab-indonesia"
                                role="tab" aria-selected="false" aria-controls="geo-panel-indonesia">
                                Indonesia
                            </button>
                        </div>

                        <div class="geo-metric-controls" aria-label="Pengaturan visualisasi peta">
                            <div class="geo-metric" x-data="{
                                open: false,
                                selected: 'cooperations_total',
                                options: [
                                    { value: 'cooperations_total', label: 'Jumlah kerja sama', icon: 'layers' },
                                    { value: 'cooperations_active', label: 'Kerja sama aktif', icon: 'pulse' },
                                    { value: 'mitras_unique', label: 'Mitra unik', icon: 'users' },
                                    { value: 'cooperations_expiring_90', label: 'Akan berakhir (90 hari)', icon: 'clock' }
                                ],
                                get current() {
                                    return this.options.find(option => option.value === this.selected) || this.options[0];
                                },
                                choose(option) {
                                    this.selected = option.value;
                                    this.open = false;
                                    this.$nextTick(() => {
                                        this.$refs.nativeMetric.value = option.value;
                                        this.$refs.nativeMetric.dispatchEvent(new Event('change', { bubbles: true }));
                                        this.$refs.trigger.focus();
                                    });
                                },
                                syncFromNative() {
                                    this.selected = this.$refs.nativeMetric.value || 'cooperations_total';
                                }
                            }" x-init="syncFromNative()" @click.outside="open = false" @keydown.escape.window="open = false">
                                <span class="geo-metric-label">Metrik</span>
                                <select class="geo-metric-native" data-geo-metric x-ref="nativeMetric"
                                    @change="syncFromNative()" tabindex="-1" aria-hidden="true">
                                    <option value="cooperations_total">Jumlah kerja sama</option>
                                    <option value="cooperations_active">Kerja sama aktif</option>
                                    <option value="mitras_unique">Mitra unik</option>
                                    <option value="cooperations_expiring_90">Akan berakhir (90 hari)</option>
                                </select>

                                <button type="button" class="geo-metric-trigger" x-ref="trigger"
                                    :class="{ 'is-open': open }" @click="open = !open"
                                    @keydown.arrow-down.prevent="open = true"
                                    :aria-expanded="open.toString()" aria-haspopup="listbox">
                                    <span class="geo-metric-trigger-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19V9M10 19V5M16 19v-7M22 19V3" />
                                        </svg>
                                    </span>
                                    <span class="geo-metric-trigger-copy">
                                        <small>Visualisasi berdasarkan</small>
                                        <strong x-text="current.label"></strong>
                                    </span>
                                    <svg class="geo-metric-chevron" :class="{ 'is-rotated': open }"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                        aria-hidden="true">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </button>

                                <div class="geo-metric-menu" x-show="open" x-transition.origin.top x-cloak
                                    role="listbox" aria-label="Pilih metrik peta">
                                    <template x-for="option in options" :key="option.value">
                                        <button type="button" class="geo-metric-option"
                                            :class="{ 'is-selected': selected === option.value }"
                                            @click="choose(option)" role="option"
                                            :aria-selected="(selected === option.value).toString()">
                                            <span class="geo-metric-option-dot" aria-hidden="true"></span>
                                            <span class="geo-metric-option-copy">
                                                <strong x-text="option.label"></strong>
                                                <small x-text="option.value === 'cooperations_total'
                                                    ? 'Total seluruh portofolio kerja sama'
                                                    : option.value === 'cooperations_active'
                                                        ? 'Kerja sama yang masih berjalan'
                                                        : option.value === 'mitras_unique'
                                                            ? 'Jumlah mitra berbeda di setiap wilayah'
                                                            : 'Berakhir dalam 90 hari mendatang'"></small>
                                            </span>
                                            <svg class="geo-metric-check" x-show="selected === option.value"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                                aria-hidden="true">
                                                <path d="m5 12 4 4L19 6" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="geo-scale" role="group" aria-label="Skala nilai peta">
                                <button type="button" class="geo-scale-btn is-active" data-geo-scale="absolute"
                                    aria-pressed="true">
                                    Absolut
                                </button>
                                <button type="button" class="geo-scale-btn" data-geo-scale="share" aria-pressed="false">
                                    Persen
                                </button>
                            </div>
                        </div>
                    </div>
                 </div>

                 <div class="geo-card-body">
                     <div class="geo-panel is-active" id="geo-panel-world" role="tabpanel" aria-labelledby="geo-tab-world">
                         <div class="geo-map" data-geo-map="world" role="img"
                             aria-label="Peta dunia choropleth sebaran kerja sama"></div>
                        <div class="geo-summary" data-geo-summary="world" aria-live="polite"></div>
                     </div>

                     <div class="geo-panel" id="geo-panel-indonesia" role="tabpanel" aria-labelledby="geo-tab-indonesia"
                         hidden>
                         <div class="geo-map" data-geo-map="indonesia" role="img"
                             aria-label="Peta Indonesia choropleth sebaran kerja sama"></div>
                        <div class="geo-summary" data-geo-summary="indonesia" aria-live="polite"></div>
                         <p class="geo-hint" data-geo-hint hidden>
                             Tip: lengkapi data provinsi mitra agar peta Indonesia lebih akurat.
                         </p>
                     </div>

                     <div class="geo-legend" aria-label="Legenda choropleth">
                         <span class="geo-legend-min" data-geo-legend-min>0</span>
                         <span class="geo-legend-bar" aria-hidden="true"></span>
                         <span class="geo-legend-max" data-geo-legend-max>0</span>
                     </div>
                    <p class="geo-footnote">
                        Klik negara/provinsi untuk memfilter daftar data di bawah.
                    </p>
                 </div>
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
                                    <h5 class="attention-title" title="{{ $item['title'] }}">{{ $item['title'] }}</h5>
                                    <p class="attention-subtitle">{{ $item['partner'] }}</p>
                                </div>
                                <div class="attention-meta">
                                    <span class="attention-date">{{ $item['meta_label'] }}</span>
                                    <span class="attention-badge">
                                        {{ \Illuminate\Support\Str::ucfirst(\Illuminate\Support\Str::lower(str_replace(['_', '-'], ' ', $item['supporting_label']))) }}
                                    </span>
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

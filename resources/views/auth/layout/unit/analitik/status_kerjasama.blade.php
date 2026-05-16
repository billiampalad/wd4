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

    <section class="sk-two-column">
        <div class="sk-two-column-grid">
            <div class="sk-two-column-stack">
                <div class="sk-two-column-panel sk-calendar-panel">
                    <header class="sk-calendar-head">
                        <h2 class="sk-calendar-title">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Kalender Kerjasama</span>
                        </h2>
                    </header>
                    <div class="sk-calendar-body">
                        <div class="sk-calendar-month">
                            <span>{{ $calendarData['month_label'] ?? now()->translatedFormat('F Y') }}</span>
                            <strong>{{ collect($calendarData['events'] ?? [])->count() }} agenda</strong>
                        </div>

                        <div class="sk-calendar-grid" aria-label="Kalender kerjasama bulan berjalan">
                            @foreach (($calendarData['weekdays'] ?? ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']) as $weekday)
                                <span class="sk-calendar-weekday">{{ $weekday }}</span>
                            @endforeach

                            @for ($i = 0; $i < ($calendarData['start_offset'] ?? 0); $i++)
                                <span class="sk-calendar-empty" aria-hidden="true"></span>
                            @endfor

                            @foreach (($calendarData['days'] ?? []) as $day)
                                <div class="sk-calendar-day {{ $day['is_today'] ? 'is-today' : '' }}">
                                    <span>{{ $day['day'] }}</span>
                                    @if (!empty($day['events']))
                                        <div class="sk-calendar-dots" aria-label="{{ count($day['events']) }} agenda">
                                            @foreach (array_slice($day['events'], 0, 3) as $event)
                                                <i class="sk-calendar-dot sk-calendar-dot-{{ $event['tone'] }}"></i>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="sk-calendar-events">
                            @forelse (($calendarData['events'] ?? []) as $event)
                                <article class="sk-calendar-event">
                                    <span class="sk-calendar-event-date">
                                        {{ $event['date_label'] ?? '-' }}
                                    </span>
                                    <div class="sk-calendar-event-copy">
                                        <strong>{{ $event['label'] ?? 'Agenda' }} - {{ $event['title'] }}</strong>
                                        <small>{{ $event['jenis'] }} - {{ $event['mitra'] }}</small>
                                    </div>
                                </article>
                            @empty
                                <div class="sk-calendar-empty-state">
                                    Belum ada agenda kerjasama pada bulan ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="sk-two-column-panel sk-due-panel"
                    x-data="{
                        open: false,
                        loading: false,
                        error: '',
                        selected: String(@js($dueDateData['year'] ?? now()->year)),
                        data: @js($dueDateData ?? []),
                        get pageCount() {
                            return Math.min(5, Math.max(1, Math.ceil((this.data.total || 0) / 5)));
                        },
                        async choose(year) {
                            const nextYear = String(year);
                            this.open = false;

                            if (nextYear === this.selected) return;

                            this.selected = nextYear;
                            this.loading = true;
                            this.error = '';

                            const url = new URL('{{ route('unit.analitik.status-kerjasama') }}', window.location.origin);
                            url.searchParams.set('due_year', nextYear);
                            url.searchParams.set('partial', 'due_date');

                            try {
                                const response = await fetch(url.toString(), {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });

                                if (!response.ok) throw new Error('Gagal memuat data due date.');

                                const payload = await response.json();
                                this.data = payload.dueDateData || this.data;
                                this.selected = String(this.data.year || nextYear);

                                const browserUrl = new URL(window.location.href);
                                browserUrl.searchParams.set('due_year', this.selected);
                                window.history.replaceState({}, '', browserUrl.toString());

                                this.$nextTick(() => initDueDateContributionGraph());
                            } catch (error) {
                                this.error = error.message || 'Gagal memuat data due date.';
                            } finally {
                                this.loading = false;
                            }
                        }
                    }" @keydown.escape.window="open = false" :class="{ 'is-loading': loading }">
                    <header class="sk-due-head">
                        <h2 class="sk-due-title">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Due date Kerjasama</span>
                        </h2>
                        <button type="button" class="sk-due-menu" aria-label="Menu due date">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </header>

                    <div class="sk-due-body">
                        <div class="sk-due-graph-shell">
                            <div class="sk-due-graph-head">
                                <div class="sk-due-year-form">
                                    <span class="sk-due-year-label">Tahun</span>
                                    <div class="sk-due-year-picker" @click.outside="open = false">
                                        <button type="button" class="sk-due-year-trigger" @click="open = !open"
                                            :aria-expanded="open.toString()" aria-haspopup="listbox" :disabled="loading">
                                            <span x-text="selected"></span>
                                            <i class="fas fa-chevron-down" :class="{ 'is-open': open }"></i>
                                        </button>
                                        <div class="sk-due-year-options" x-show="open" x-transition.origin.top.left
                                            x-cloak role="listbox">
                                            <template x-for="year in data.years" :key="year">
                                                <button type="button" class="sk-due-year-option"
                                                    :class="{ 'is-selected': String(year) === selected }"
                                                    :disabled="loading"
                                                    @click="choose(year)" role="option"
                                                    :aria-selected="(String(year) === selected).toString()">
                                                    <span x-text="year"></span>
                                                    <i class="fas fa-check" x-show="String(year) === selected"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="sk-due-legend" aria-label="Legenda intensitas due date">
                                    <span>Less</span>
                                    @for ($level = 1; $level <= 5; $level++)
                                        <i class="sk-due-cell sk-due-level-{{ $level }}"></i>
                                    @endfor
                                    <span>More</span>
                                </div>
                            </div>

                            <div class="sk-due-graph-scroll" tabindex="0" aria-label="Grafik kontribusi due date kerjasama">
                                <div class="sk-due-contrib">
                                    <div class="sk-due-month-labels">
                                        <template x-for="month in data.month_labels" :key="month.label + month.week">
                                            <span :style="`--month-week: ${month.week}`" x-text="month.label"></span>
                                        </template>
                                    </div>

                                    <div class="sk-due-weekday-labels" aria-hidden="true">
                                        <template x-for="weekday in data.weekdays" :key="weekday">
                                            <span x-text="weekday"></span>
                                        </template>
                                    </div>

                                    <div class="sk-due-weeks">
                                        <template x-for="(week, weekIndex) in data.weeks" :key="weekIndex">
                                            <div class="sk-due-week">
                                                <template x-for="(day, dayIndex) in week" :key="day ? day.date : `empty-${weekIndex}-${dayIndex}`">
                                                    <button type="button" class="sk-due-cell"
                                                        :class="day ? [
                                                            `sk-due-level-${day.level}`,
                                                            day.is_today ? 'is-today' : '',
                                                            day.is_month_start ? 'is-month-start' : ''
                                                        ] : 'sk-due-cell-empty'"
                                                        :disabled="!day"
                                                        :data-count="day ? day.count : 0"
                                                        :data-date="day ? day.label : ''"
                                                        :aria-hidden="(!day).toString()"
                                                        :aria-label="day ? `${day.count} due date pada ${day.label}` : ''">
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="sk-due-loading" x-show="loading" x-cloak>
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Memuat data tahun <span x-text="selected"></span>...</span>
                            </div>
                            <div class="sk-due-error" x-show="error" x-cloak x-text="error"></div>
                        </div>

                        <div class="sk-due-table-wrap">
                            <table class="sk-due-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Judul</th>
                                        <th>Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in data.rows" :key="row.id">
                                        <tr>
                                            <td x-text="index + 1"></td>
                                            <td>
                                                <div class="sk-due-doc" x-text="row.doc_number"></div>
                                                <div class="sk-due-row-title" x-text="row.title"></div>
                                                <div class="sk-due-actions">
                                                    <a :href="row.detail_url">Detail <i class="fas fa-angle-double-right"></i></a>
                                                    <span x-text="row.jenis"></span>
                                                </div>
                                            </td>
                                            <td x-text="row.due"></td>
                                        </tr>
                                    </template>
                                    <template x-if="!data.rows || data.rows.length === 0">
                                        <tr>
                                            <td colspan="3">
                                                <div class="sk-calendar-empty-state">Belum ada due date pada tahun ini.</div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="sk-due-footer">
                            <span x-text="`Showing ${(data.showing || 0) ? 1 : 0} to ${data.showing || 0} of ${data.total || 0} entries`"></span>
                            <div class="sk-due-pages" aria-label="Pagination due date">
                                <button type="button" disabled>Previous</button>
                                <template x-for="page in pageCount" :key="page">
                                    <button type="button" :class="{ 'is-active': page === 1 }" x-text="page"></button>
                                </template>
                                <button type="button" :disabled="(data.total || 0) <= 5">Next</button>
                            </div>
                        </div>

                        <div class="sk-due-scrollbar" aria-hidden="true">
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sk-two-column-panel"></div>
        </div>
    </section>

    <script type="application/json" id="statusKerjasamaData">@json($statusKerjasamaData)</script>
    <script type="application/json" id="pertumbuhanKerjasamaData">@json($growthData)</script>
</main>

<script src="{{ asset('js/auth/unit/analitik/status_kerjasama.js') }}" data-turbo-track="reload"></script>

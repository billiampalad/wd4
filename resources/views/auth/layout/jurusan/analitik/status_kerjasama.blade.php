<link rel="stylesheet" href="{{ asset('css/auth/unit/analitik/status_kerjasama.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('jurusan.dashboard') }}">Beranda</a>
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
                    <span class="sk-legend-swatch" style="--swatch: {{ $statusKerjasamaData['colors'][$index] }}"></span>
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

                <div class="sk-two-column-panel sk-due-panel" x-data="{
                        open: false,
                        loading: false,
                        error: '',
                        selected: String(@js($dueDateData['year'] ?? now()->year)),
                        data: @js($dueDateData ?? []),
                        
                        selectedDate: null,
                        currentPage: 1,
                        itemsPerPage: 5,
                        
                        get filteredRows() {
                            let rows = this.data.rows || [];
                            if (this.selectedDate) {
                                rows = rows.filter(row => row.created_at_label === this.selectedDate);
                            }
                            return rows;
                        },
                        get pageCount() {
                            return Math.max(1, Math.ceil(this.filteredRows.length / this.itemsPerPage));
                        },
                        get pagesToShow() {
                            const range = 1;
                            const pages = [];
                            const total = this.pageCount;
                            const current = this.currentPage;

                            if (total <= 5) {
                                for (let i = 1; i <= total; i++) {
                                    pages.push(i);
                                }
                                return pages;
                            }

                            pages.push(1);

                            const start = Math.max(2, current - range);
                            const end = Math.min(total - 1, current + range);

                            if (start > 2) {
                                pages.push('...');
                            }

                            for (let i = start; i <= end; i++) {
                                pages.push(i);
                            }

                            if (end < total - 1) {
                                pages.push('...');
                            }

                            pages.push(total);

                            return pages;
                        },
                        get paginatedRows() {
                            const start = (this.currentPage - 1) * this.itemsPerPage;
                            const end = start + this.itemsPerPage;
                            return this.filteredRows.slice(start, end);
                        },
                        get showingStart() {
                            if (this.filteredRows.length === 0) return 0;
                            return ((this.currentPage - 1) * this.itemsPerPage) + 1;
                        },
                        get showingEnd() {
                            return Math.min(this.currentPage * this.itemsPerPage, this.filteredRows.length);
                        },
                        filterByDate(dateLabel) {
                            if (this.selectedDate === dateLabel) {
                                this.selectedDate = null;
                            } else {
                                this.selectedDate = dateLabel;
                            }
                            this.currentPage = 1;
                        },
                        goToPage(page) {
                            if (page >= 1 && page <= this.pageCount) {
                                this.currentPage = page;
                            }
                        },

                        async choose(year) {
                            const nextYear = String(year);
                            this.open = false;

                            if (nextYear === this.selected) return;

                            this.selected = nextYear;
                            this.loading = true;
                            this.error = '';

                            const url = new URL('{{ route('jurusan.analitik.status-kerjasama') }}', window.location.origin);
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
                                
                                this.selectedDate = null;
                                this.currentPage = 1;

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
                                            :aria-expanded="open.toString()" aria-haspopup="listbox"
                                            :disabled="loading">
                                            <span x-text="selected"></span>
                                            <i class="fas fa-chevron-down" :class="{ 'is-open': open }"></i>
                                        </button>
                                        <div class="sk-due-year-options" x-show="open" x-transition.origin.top.left
                                            x-cloak role="listbox">
                                            <template x-for="year in data.years" :key="year">
                                                <button type="button" class="sk-due-year-option"
                                                    :class="{ 'is-selected': String(year) === selected }"
                                                    :disabled="loading" @click="choose(year)" role="option"
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

                            <div class="sk-due-graph-scroll" tabindex="0"
                                aria-label="Grafik kontribusi due date kerjasama">
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
                                                <template x-for="(day, dayIndex) in week"
                                                    :key="day ? day.date : `empty-${weekIndex}-${dayIndex}`">
                                                    <button type="button" class="sk-due-cell" :class="day ? [
                                                            `sk-due-level-${day.level}`,
                                                            day.is_today ? 'is-today' : '',
                                                            day.is_month_start ? 'is-month-start' : '',
                                                            (selectedDate === day.label) ? 'is-selected' : ''
                                                        ] : 'sk-due-cell-empty'" :disabled="!day"
                                                        :data-count="day ? day.count : 0"
                                                        :data-date="day ? day.label : ''"
                                                        :aria-hidden="(!day).toString()"
                                                        :aria-label="day ? (day.count ? `Pada ${day.label} : ${day.count}` : `Pada ${day.label}`) : ''"
                                                        @click="day && day.count ? filterByDate(day.label) : null">
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
                            <div class="sk-due-table-header" x-show="selectedDate" x-cloak>
                                <span>Filter Tanggal: <strong><span x-text="selectedDate"></span></strong></span>
                                <button type="button" @click="filterByDate(selectedDate)" class="sk-due-clear-btn"
                                    aria-label="Hapus filter"><i class="fas fa-times"></i> Hapus</button>
                            </div>
                            <table class="sk-due-table">
                                <thead>
                                    <tr>
                                        <th class="sk-col-no">No.</th>
                                        <th>Judul</th>
                                        <th>Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in paginatedRows" :key="row.id">
                                        <tr>
                                            <td x-text="showingStart + index" class="sk-col-no"></td>
                                            <td>
                                                <div class="sk-due-doc" x-text="row.doc_number"></div>
                                                <div class="sk-due-row-title" x-text="row.title"></div>
                                                <div class="sk-due-actions">
                                                    <a :href="row.detail_url" class="sk-due-action-btn">
                                                        Detail <i class="fas fa-angle-double-right"></i>
                                                    </a>
                                                    <span class="sk-due-badge" x-text="row.jenis"></span>
                                                </div>
                                            </td>
                                            <td x-text="row.due"></td>
                                        </tr>
                                    </template>
                                    <template x-if="filteredRows.length === 0">
                                        <tr>
                                            <td colspan="3">
                                                <div class="sk-calendar-empty-state">
                                                    <span x-show="selectedDate">Tidak ada data kerjasama pada tanggal
                                                        <span x-text="selectedDate"></span>.</span>
                                                    <span x-show="!selectedDate">Belum ada due date pada tahun
                                                        ini.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="sk-due-footer">
                            <span
                                x-text="`Showing ${showingStart} to ${showingEnd} of ${filteredRows.length} entries`"></span>
                            <div class="sk-due-pages" aria-label="Pagination due date">
                                <button type="button" :disabled="currentPage === 1"
                                    @click="goToPage(currentPage - 1)">Previous</button>
                                <template x-for="(page, idx) in pagesToShow" :key="idx">
                                    <button type="button" 
                                        :class="{ 'is-active': page === currentPage, 'sk-due-ellipsis': page === '...' }"
                                        :disabled="page === '...'"
                                        @click="page !== '...' ? goToPage(page) : null" 
                                        x-text="page"></button>
                                </template>
                                <button type="button" :disabled="currentPage === pageCount || pageCount === 0"
                                    @click="goToPage(currentPage + 1)">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sk-two-column-stack">
                <div class="sk-two-column-panel sk-mou-panel">
                    <header class="sk-mou-head">
                        <h2 class="sk-mou-title">
                            <i class="fas fa-chart-pie"></i>
                            <span>MoU vs MoA/IA</span>
                        </h2>
                    </header>
                    <div class="sk-mou-body">
                        <div class="sk-mou-chart-wrap">
                            <canvas id="mouVsMoaIaChart" aria-label="Grafik MoU vs MoA/IA"></canvas>
                        </div>
                    </div>
                </div>

                <div class="sk-two-column-panel sk-sebaran-panel">
                    <header class="sk-sebaran-head">
                        <h2 class="sk-sebaran-title">
                            <i class="fas fa-chart-bar"></i>
                            <span>Sebaran Dokumen</span>
                        </h2>
                    </header>
                    <div class="sk-sebaran-body">
                        <div class="sk-sebaran-chart-wrap">
                            <canvas id="sebaranDokumenChart" aria-label="Grafik Sebaran Dokumen"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="application/json" id="statusKerjasamaData">@json($statusKerjasamaData)</script>
    <script type="application/json" id="pertumbuhanKerjasamaData">@json($growthData)</script>
    <script type="application/json" id="mouVsMoaIaData">@json($mouVsMoaIaData)</script>
    <script type="application/json" id="sebaranDokumenData">@json($sebaranDokumenData)</script>
</main>

<script src="{{ asset('js/auth/unit/analitik/status_kerjasama.js') }}" data-turbo-track="reload"></script>

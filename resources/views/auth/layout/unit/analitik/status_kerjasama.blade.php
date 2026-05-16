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

                <div class="sk-two-column-panel sk-due-panel">
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
                        <div class="sk-due-map">
                            <div class="sk-due-year">{{ $dueDateData['year'] ?? now()->year }}</div>
                            <div class="sk-due-weekdays" aria-hidden="true">
                                <span>S</span>
                                <span>M</span>
                                <span>T</span>
                                <span>W</span>
                                <span>T</span>
                                <span>F</span>
                                <span>S</span>
                            </div>
                            <div class="sk-due-months">
                                @foreach (($dueDateData['months'] ?? []) as $month)
                                    <div class="sk-due-month">
                                        <span>{{ $month['label'] }}</span>
                                        <div class="sk-due-month-cells">
                                            @foreach (($month['weekdays'] ?? []) as $count)
                                                <i class="{{ $count > 0 ? 'is-active' : '' }}"></i>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
                                    @forelse (($dueDateData['rows'] ?? []) as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="sk-due-doc">{{ $row['doc_number'] }}</div>
                                                <div class="sk-due-row-title">{{ $row['title'] }}</div>
                                                <div class="sk-due-actions">
                                                    <a href="{{ route('unit.kerjasama.show', $row['id']) }}">Detail <i class="fas fa-angle-double-right"></i></a>
                                                    <span>{{ $row['jenis'] }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $row['due'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <div class="sk-calendar-empty-state">Belum ada due date pada tahun ini.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="sk-due-footer">
                            <span>Showing {{ ($dueDateData['showing'] ?? 0) ? 1 : 0 }} to {{ $dueDateData['showing'] ?? 0 }} of {{ $dueDateData['total'] ?? 0 }} entries</span>
                            <div class="sk-due-pages" aria-label="Pagination due date">
                                <button type="button" disabled>Previous</button>
                                @for ($page = 1; $page <= min(5, max(1, (int) ceil(($dueDateData['total'] ?? 0) / 5))); $page++)
                                    <button type="button" class="{{ $page === 1 ? 'is-active' : '' }}">{{ $page }}</button>
                                @endfor
                                <button type="button" {{ ($dueDateData['total'] ?? 0) <= 5 ? 'disabled' : '' }}>Next</button>
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

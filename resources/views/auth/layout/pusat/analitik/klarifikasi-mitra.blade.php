<link rel="stylesheet" href="{{ asset('css/auth/unit/analitik/status_kerjasama.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page klasifikasi-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('pusat.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Klarifikasi Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-chart-bar"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Analitik Klasifikasi Mitra</h2>
                    <p class="ud-subtitle">
                        Sebaran dan proporsi kerjasama berdasarkan kriteria / klasifikasi mitra industri dan instansi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stat cards grid -->
    <div class="klasifikasi-stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
        <div class="sk-card klasifikasi-stat-card"
            style="border-radius: 14px; padding: 18px; display: flex; flex-direction: row; align-items: center; gap: 16px;">
            <div
                style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: grid; place-items: center; font-size: 20px;">
                <i class="fas fa-handshake"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; display: block;">Total
                    Mitra Terdaftar</span>
                <strong style="font-size: 24px; font-weight: 850; color: var(--text);">{{ $totalMitras }}</strong>
            </div>
        </div>
        <div class="sk-card klasifikasi-stat-card"
            style="border-radius: 14px; padding: 18px; display: flex; flex-direction: row; align-items: center; gap: 16px;">
            <div
                style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: grid; place-items: center; font-size: 20px;">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; display: block;">Kriteria
                    Terbanyak</span>
                <strong
                    style="font-size: 14px; font-weight: 850; color: var(--text); display: block; max-width: 220px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $mostFrequentName }}</strong>
                <span style="font-size: 12px; color: var(--text-sub);">({{ $mostFrequentCount }} Mitra)</span>
            </div>
        </div>
        <div class="sk-card klasifikasi-stat-card"
            style="border-radius: 14px; padding: 18px; display: flex; flex-direction: row; align-items: center; gap: 16px;">
            <div
                style="width: 48px; height: 48px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: grid; place-items: center; font-size: 20px;">
                <i class="fas fa-award"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; display: block;">Mitra
                    Teraktif</span>
                <strong
                    style="font-size: 14px; font-weight: 850; color: var(--text); display: block; max-width: 220px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $topMitras->first() ? $topMitras->first()->nama_mitra : '-' }}</strong>
                <span
                    style="font-size: 12px; color: var(--text-sub);">({{ $topMitras->first() ? $topMitras->first()->cooperations_count : 0 }}
                    Kerjasama)</span>
            </div>
        </div>
    </div>

    <!-- Visualizations and Lists -->
    <div class="klasifikasi-main-grid"
        style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(340px, 380px); gap: 20px; align-items: start;">

        <!-- Left: Chart & Full List -->
        <div class="klasifikasi-left-stack" style="display: grid; gap: 20px;">

            <!-- Chart Card -->
            <section class="sk-card klasifikasi-card" style="border-radius: 18px; padding: 20px;">
                <header style="margin-bottom: 20px;">
                    <h2 class="sk-title" style="font-size: 16px; font-weight: 800;">
                        <i class="fas fa-chart-pie"></i>
                        <span>Proporsi Klasifikasi Mitra</span>
                    </h2>
                    <p style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Persentase mitra berdasarkan
                        kriteria klasifikasi.</p>
                </header>

                <div class="klasifikasi-chart-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: center;">
                    <div class="klasifikasi-chart-wrap" style="position: relative; height: 260px;">
                        <canvas id="klasifikasiMitraChart" aria-label="Grafik Klasifikasi Mitra"></canvas>
                    </div>
                    <div class="klasifikasi-legend-list"
                        style="max-height: 260px; overflow-y: auto; padding-right: 8px; display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($classifications as $index => $cls)
                            @if ($cls->mitras_count > 0)
                                <div class="klasifikasi-legend-item"
                                    style="display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 12px;">
                                    <div class="klasifikasi-legend-main" style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                                        <span class="klasifikasi-legend-swatch"
                                            style="width: 10px; height: 10px; border-radius: 3px; background: {{ $chartDataPayload['colors'][$index] }}; flex-shrink: 0;"></span>
                                        <span class="klasifikasi-legend-name" tabindex="0" title="{{ $cls->nama }}"
                                            style="color: var(--text); font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $cls->nama }}</span>
                                    </div>
                                    <strong class="klasifikasi-legend-count" style="color: var(--text);">{{ $cls->mitras_count }}</strong>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Table Card -->
            <section class="sk-card klasifikasi-card" style="border-radius: 18px; padding: 20px;">
                <header style="margin-bottom: 16px;">
                    <h2 class="sk-title" style="font-size: 16px; font-weight: 800;">
                        <i class="fas fa-list-ul"></i>
                        <span>Detail Klasifikasi Mitra</span>
                    </h2>
                </header>

                <div class="klasifikasi-table-wrap" style="overflow-x: auto;">
                    <table class="klasifikasi-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border); color: var(--text-sub);">
                                <th style="padding: 10px 8px; font-weight: 700; width: 60px;">#</th>
                                <th style="padding: 10px 8px; font-weight: 700;">Nama Klasifikasi</th>
                                <th style="padding: 10px 8px; font-weight: 700; text-align: center; width: 140px;">
                                    Jumlah Mitra</th>
                                <th style="padding: 10px 8px; font-weight: 700; text-align: center; width: 140px;">
                                    Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classifications as $cls)
                                <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;">
                                    <td data-label="#" style="padding: 12px 8px; color: var(--text-sub); font-weight: 600;">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td data-label="Nama Klasifikasi" style="padding: 12px 8px; color: var(--text); font-weight: 700;">{{ $cls->nama }}
                                    </td>
                                    <td data-label="Jumlah Mitra" style="padding: 12px 8px; text-align: center;">
                                        <span
                                            style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-weight: 700; padding: 3px 8px; border-radius: 6px; font-size: 12px;">
                                            {{ $cls->mitras_count }} Mitra
                                        </span>
                                    </td>
                                    <td data-label="Persentase"
                                        style="padding: 12px 8px; text-align: center; font-weight: 700; color: var(--text);">
                                        {{ $totalMitras > 0 ? round(($cls->mitras_count / $totalMitras) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-sub);">Belum
                                        ada data klasifikasi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Right: Top 5 Active Mitras -->
        <section class="sk-card klasifikasi-card klasifikasi-top-card" style="border-radius: 18px; padding: 20px;">
            <header style="margin-bottom: 20px;">
                <h2 class="sk-title" style="font-size: 16px; font-weight: 800;">
                    <i class="fas fa-trophy"></i>
                    <span>Top 5 Mitra Teraktif</span>
                </h2>
                <p style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Mitra industri dengan jumlah
                    kerjasama terbanyak.</p>
            </header>

            <div class="klasifikasi-top-list" style="display: grid; gap: 14px;">
                @forelse ($topMitras as $mitra)
                    <div
                        style="border: 1px solid var(--border); border-radius: 12px; padding: 14px; background: var(--surface2); display: flex; flex-direction: column; gap: 6px; position: relative;">
                        <span
                            style="position: absolute; right: 14px; top: 14px; background: linear-gradient(135deg, #8b5cf6, #6366f1); color: #fff; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 99px;">
                            Rank #{{ $loop->iteration }}
                        </span>
                        <strong
                            style="color: var(--text); font-size: 13.5px; font-weight: 800; max-width: 78%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                            {{ $mitra->nama_mitra }}
                        </strong>
                        <span
                            style="font-size: 11.5px; color: var(--text-sub); display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-map-marker-alt"></i> {{ $mitra->negara ?? 'Indonesia' }}
                        </span>
                        <div
                            style="margin-top: 4px; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #10b981;">
                            <i class="fas fa-file-contract"></i> {{ $mitra->cooperations_count }} Kerjasama
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 20px; color: var(--text-sub); font-size: 13px;">Belum ada data
                        mitra</div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Hidden Payload for Chart JS -->
    <script type="application/json" id="klasifikasiMitraChartData">@json($chartDataPayload)</script>
</main>

<script src="{{ asset('js/auth/unit/analitik/klarifikasi_mitra.js') }}" data-turbo-track="reload"></script>

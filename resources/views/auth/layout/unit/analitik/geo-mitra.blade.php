<link rel="stylesheet" href="{{ asset('css/auth/unit/analitik/status_kerjasama.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page geo-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Geo Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-globe-asia"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Geo Sebaran Mitra</h2>
                    <p class="ud-subtitle">
                        Sebaran geografis mitra kerjasama Politeknik Negeri Manado secara Nasional maupun Internasional.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stat cards grid -->
    <div class="geo-stat-grid">
        <div class="sk-card geo-stat-card">
            <div class="geo-stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="geo-stat-copy">
                <span>Total Mitra</span>
                <strong>{{ $totalMitras }}</strong>
            </div>
        </div>
        <div class="sk-card geo-stat-card">
            <div class="geo-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fas fa-flag"></i>
            </div>
            <div class="geo-stat-copy">
                <span>Mitra Nasional</span>
                <strong>{{ $nasionalCount }}</strong>
            </div>
        </div>
        <div class="sk-card geo-stat-card">
            <div class="geo-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fas fa-globe"></i>
            </div>
            <div class="geo-stat-copy">
                <span>Mitra Internasional</span>
                <strong>{{ $internasionalCount }}</strong>
            </div>
        </div>
        <div class="sk-card geo-stat-card">
            <div class="geo-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="geo-stat-copy">
                <span>Negara Terhubung</span>
                <strong>{{ $totalCountries }}</strong>
            </div>
        </div>
    </div>

    <!-- Visualizations and Lists -->
    <div class="geo-layout">
        
        <!-- Left: Charts & Full List -->
        <div class="geo-stack">
            
            <!-- Grid for two charts -->
            <div class="geo-chart-grid">
                
                <!-- Category Chart Card -->
                <section class="sk-card" style="border-radius: 18px; padding: 20px;">
                    <header style="margin-bottom: 16px;">
                        <h2 class="sk-title" style="font-size: 15px; font-weight: 800;">
                            <i class="fas fa-chart-pie"></i>
                            <span>Skala Wilayah</span>
                        </h2>
                    </header>
                    <div class="geo-chart-wrap geo-chart-wrap-kategori">
                        <canvas id="geoKategoriChart" aria-label="Grafik Skala Wilayah Mitra"></canvas>
                    </div>
                    <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                                <span style="color: var(--text-sub);">Nasional</span>
                            </div>
                            <strong style="color: var(--text);">{{ $nasionalCount }}</strong>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span>
                                <span style="color: var(--text-sub);">Internasional</span>
                            </div>
                            <strong style="color: var(--text);">{{ $internasionalCount }}</strong>
                        </div>
                    </div>
                </section>

                <!-- Country Chart Card -->
                <section class="sk-card" style="border-radius: 18px; padding: 20px;">
                    <header style="margin-bottom: 16px;">
                        <h2 class="sk-title" style="font-size: 15px; font-weight: 800;">
                            <i class="fas fa-chart-bar"></i>
                            <span>Top Negara Sebaran Mitra</span>
                        </h2>
                    </header>
                    <div class="geo-chart-wrap geo-chart-wrap-negara">
                        <canvas id="geoNegaraChart" aria-label="Grafik Negara Sebaran Mitra"></canvas>
                    </div>
                </section>
            </div>

            <!-- Table Card -->
            <section class="sk-card" style="border-radius: 18px; padding: 20px;">
                <header style="margin-bottom: 16px;">
                    <h2 class="sk-title" style="font-size: 16px; font-weight: 800;">
                        <i class="fas fa-list-ul"></i>
                        <span>Detail Sebaran Mitra per Negara</span>
                    </h2>
                </header>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border); color: var(--text-sub);">
                                <th style="padding: 10px 8px; font-weight: 700; width: 60px;">#</th>
                                <th style="padding: 10px 8px; font-weight: 700;">Nama Negara</th>
                                <th style="padding: 10px 8px; font-weight: 700; text-align: center;">Nasional</th>
                                <th style="padding: 10px 8px; font-weight: 700; text-align: center;">Internasional</th>
                                <th style="padding: 10px 8px; font-weight: 700; text-align: center; width: 140px;">Total Mitra</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rawCountries as $country)
                                <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;">
                                    <td style="padding: 12px 8px; color: var(--text-sub); font-weight: 600;">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td style="padding: 12px 8px; color: var(--text); font-weight: 700;">{{ $country->country_name }}</td>
                                    <td style="padding: 12px 8px; text-align: center; color: #10b981; font-weight: 700;">{{ $country->nasional_count }}</td>
                                    <td style="padding: 12px 8px; text-align: center; color: #3b82f6; font-weight: 700;">{{ $country->internasional_count }}</td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <span style="background: rgba(99, 102, 241, 0.1); color: #6366f1; font-weight: 700; padding: 3px 8px; border-radius: 6px; font-size: 12px;">
                                            {{ $country->mitras_count }} Mitra
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-sub);">Belum ada data negara sebaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Latest International Mitras -->
            <section class="sk-card" style="border-radius: 18px; padding: 20px;">
                <header style="margin-bottom: 20px;">
                    <h2 class="sk-title" style="font-size: 16px; font-weight: 800;">
                        <i class="fas fa-globe-americas"></i>
                        <span>Mitra Internasional Terbaru</span>
                    </h2>
                    <p style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Pendaftaran mitra berskala internasional teranyar.</p>
                </header>

                <div style="display: grid; gap: 14px;">
                    @forelse ($latestInternational as $mitra)
                        <div style="border: 1px solid var(--border); border-radius: 12px; padding: 14px; background: var(--surface2); display: flex; flex-direction: column; gap: 6px;">
                            <strong style="color: var(--text); font-size: 13.5px; font-weight: 800; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $mitra->nama_mitra }}
                            </strong>
                            <span style="font-size: 11.5px; color: var(--text-sub); display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-map-marker-alt"></i> {{ $mitra->negara }}
                            </span>
                            <div style="margin-top: 4px; font-size: 11px; color: var(--text-sub);">
                                Terdaftar: {{ $mitra->created_at ? $mitra->created_at->translatedFormat('d M Y') : '-' }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 20px; color: var(--text-sub); font-size: 13px;">Belum ada data mitra internasional</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <!-- Hidden Payload for Chart JS -->
    <script type="application/json" id="geoKategoriChartData">@json($categoryChartData)</script>
    <script type="application/json" id="geoNegaraChartData">@json($countryChartData)</script>
</main>

<script src="{{ asset('js/auth/unit/analitik/geo_mitra.js') }}?v={{ filemtime(public_path('js/auth/unit/analitik/geo_mitra.js')) }}" data-turbo-track="reload"></script>

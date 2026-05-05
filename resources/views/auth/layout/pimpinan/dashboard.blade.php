<!-- Main Content -->
<main id="mainContent">

    <!-- Page Header -->
    <div class="page-header" style="margin-bottom: 24px; background: linear-gradient(135deg, var(--surface) 0%, rgba(255,255,255,0) 100%); padding: 24px; border-radius: 16px; border: 1px solid var(--border); position: relative; overflow: hidden;">
        <!-- decorative background -->
        <div style="position: absolute; right: -50px; top: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(79,70,229,0.1) 0%, rgba(79,70,229,0) 70%); border-radius: 50%;"></div>
        <div style="position: relative; z-index: 1;">
            <div class="breadcrumb" style="margin-bottom: 8px;">
                <i class="fas fa-home" style="font-size:11px;"></i>
                <span class="sep">/</span>
                <span class="current" id="breadcrumbCurrent">Executive Overview</span>
            </div>
            <h2 id="pageTitle" style="font-size: 28px; font-weight: 800; background: linear-gradient(90deg, var(--text) 0%, var(--accent) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sistem Informasi Kerjasama (Executive)</h2>
            <p id="pageDesc" style="color: var(--text-sub); font-size: 14px; margin-top: 4px;">Gambaran besar aktivitas kerjasama Politeknik Negeri Manado Tahun {{ now()->year }}</p>
        </div>
        
        <!-- Global Filter & Export -->
        <div style="position: absolute; right: 24px; top: 50%; transform: translateY(-50%); display: flex; gap: 12px; z-index: 1;">
            <button class="btn btn-outline" style="border-radius: 12px; font-weight: 600; font-size: 13px; padding: 10px 16px; display: flex; align-items: center; gap: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text); cursor: pointer;">
                <i class="fas fa-filter" style="color: var(--text-sub);"></i> Filter Global
            </button>
            <button class="btn btn-primary" onclick="window.print()" style="border-radius: 12px; font-weight: 600; font-size: 13px; padding: 10px 16px; display: flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; box-shadow: 0 4px 12px rgba(79,70,229,0.3); cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-file-pdf"></i> Download Laporan
            </button>
        </div>
    </div>

    <!-- 1. KEY PERFORMANCE INDICATORS -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px;">
        
        <!-- Total Kerjasama Aktif -->
        <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px; position: relative; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            <div style="position: absolute; right: -20px; top: -20px; font-size: 80px; color: rgba(16,185,129,0.05); transform: rotate(15deg); pointer-events: none;"><i class="fas fa-check-circle"></i></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-handshake"></i>
                </div>
                <span class="tag" style="background: rgba(16,185,129,0.1); color: #10b981; font-weight: 700; border-radius: 8px;">Aktif</span>
            </div>
            <h3 style="font-size: 32px; font-weight: 800; color: var(--text); margin: 0; line-height: 1;">{{ $totalKerjasamaAktif }}</h3>
            <p style="font-size: 13px; color: var(--text-sub); margin-top: 8px; font-weight: 500;">Total Kerjasama Aktif</p>
        </div>

        <!-- Total Mitra -->
        <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px; position: relative; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            <div style="position: absolute; right: -20px; top: -20px; font-size: 80px; color: rgba(59,130,246,0.05); transform: rotate(15deg); pointer-events: none;"><i class="fas fa-building"></i></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59,130,246,0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-building-user"></i>
                </div>
                <span class="tag" style="background: rgba(59,130,246,0.1); color: #3b82f6; font-weight: 700; border-radius: 8px;">Entitas</span>
            </div>
            <h3 style="font-size: 32px; font-weight: 800; color: var(--text); margin: 0; line-height: 1;">{{ $totalMitra }}</h3>
            <p style="font-size: 13px; color: var(--text-sub); margin-top: 8px; font-weight: 500;">Total Mitra Terdaftar</p>
        </div>

        <!-- Total Nilai Kontrak -->
        <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px; position: relative; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            <div style="position: absolute; right: -20px; top: -20px; font-size: 80px; color: rgba(245,158,11,0.05); transform: rotate(15deg); pointer-events: none;"><i class="fas fa-coins"></i></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="tag" style="background: rgba(245,158,11,0.1); color: #f59e0b; font-weight: 700; border-radius: 8px;">Income</span>
            </div>
            <h3 style="font-size: 24px; font-weight: 800; color: var(--text); margin: 0; line-height: 1; padding-top:8px;">Rp {{ number_format($totalNilaiKontrak, 0, ',', '.') }}</h3>
            <p style="font-size: 13px; color: var(--text-sub); margin-top: 8px; font-weight: 500;">Total Nilai Kontrak Ekonomi</p>
        </div>

        <!-- Capaian Sasaran -->
        <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px; position: relative; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            <div style="position: absolute; right: -20px; top: -20px; font-size: 80px; color: rgba(139,92,246,0.05); transform: rotate(15deg); pointer-events: none;"><i class="fas fa-bullseye"></i></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(139,92,246,0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-bullseye"></i>
                </div>
                <span class="tag" style="background: rgba(139,92,246,0.1); color: #8b5cf6; font-weight: 700; border-radius: 8px;">Sasaran</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @forelse($capaianSasaran->take(2) as $sasaran)
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px;">
                        <span style="color: var(--text-sub); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 120px;" title="{{ $sasaran->nama_sasaran }}">{{ $sasaran->nama_sasaran }}</span>
                        <span style="font-weight: 700; color: var(--text);">{{ $sasaran->total }}</span>
                    </div>
                    <div style="width: 100%; background: var(--border); height: 4px; border-radius: 2px;">
                        <div style="width: {{ min(100, $sasaran->total * 5) }}%; background: #8b5cf6; height: 100%; border-radius: 2px;"></div>
                    </div>
                </div>
                @empty
                <p style="font-size: 12px; color: var(--text-sub); margin: 0;">Belum ada data sasaran.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 2. VISUALISASI DATA -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 28px;">
        
        <!-- Tren Kerjasama Tahunan -->
        <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text); margin:0;">Tren Kerjasama Tahunan</h3>
                    <p style="font-size: 12px; color: var(--text-sub); margin: 4px 0 0 0;">Produktivitas kerjasama berdasarkan tanggal mulai.</p>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center;"><i class="fas fa-chart-line"></i></div>
            </div>
            <div style="height: 280px; width: 100%;">
                <canvas id="trenTahunanChart" data-tren='{!! json_encode($trenTahunan) !!}'></canvas>
            </div>
        </div>

        <!-- Distribusi Jenis Kerjasama -->
        <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text); margin:0;">Distribusi Dokumen</h3>
                    <p style="font-size: 12px; color: var(--text-sub); margin: 4px 0 0 0;">MoU, MoA, vs IA.</p>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(236,72,153,0.1); color: #ec4899; display: flex; align-items: center; justify-content: center;"><i class="fas fa-chart-pie"></i></div>
            </div>
            <div style="height: 250px; width: 100%; display: flex; justify-content: center;">
                <canvas id="distribusiJenisChart" data-jenis='{!! json_encode($distribusiJenis) !!}'></canvas>
            </div>
        </div>
    </div>

    <!-- 3. VISUALISASI TAMBAHAN & GEOGRAFIS -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 28px;">
        <!-- Top 5 Jurusan Teraktif -->
        <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 24px;">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--text); margin:0 0 16px 0;"><i class="fas fa-university" style="color: #6366f1; margin-right: 8px;"></i> Top 5 Jurusan Teraktif</h3>
            <div style="height: 200px; width: 100%;">
                <canvas id="topJurusanChart" data-jurusan='{!! json_encode($topJurusan) !!}'></canvas>
            </div>
        </div>

        <!-- Peta Klasifikasi Mitra -->
        <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 24px;">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--text); margin:0 0 16px 0;"><i class="fas fa-layer-group" style="color: #f59e0b; margin-right: 8px;"></i> Klasifikasi Mitra</h3>
            <div style="height: 200px; width: 100%;">
                <canvas id="klasifikasiMitraChart" data-klasifikasi='{!! json_encode($klasifikasiMitra) !!}'></canvas>
            </div>
        </div>

        <!-- Nasional vs Internasional -->
        <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 24px; display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -30px; bottom: -30px; font-size: 120px; color: rgba(14,165,233,0.05); pointer-events: none;"><i class="fas fa-globe"></i></div>
            <h3 style="font-size: 15px; font-weight: 700; color: var(--text); margin:0 0 24px 0; align-self: flex-start; width: 100%;"><i class="fas fa-map-marker-alt" style="color: #0ea5e9; margin-right: 8px;"></i> Sebaran Geografis</h3>
            
            <div style="display: flex; justify-content: space-around; width: 100%; z-index: 1;">
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; margin: 0 auto 12px auto; box-shadow: 0 4px 12px rgba(14,165,233,0.3);">
                        {{ $nasional }}
                    </div>
                    <span style="font-weight: 700; color: var(--text); font-size: 14px;">Nasional</span>
                </div>
                <div style="width: 1px; background: var(--border); height: 60px; margin-top: 10px;"></div>
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; margin: 0 auto 12px auto; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                        {{ $internasional }}
                    </div>
                    <span style="font-weight: 700; color: var(--text); font-size: 14px;">Internasional</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. ACTIONABLE INSIGHTS & RINGKASAN TUGAS HARI INI -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 28px;">
        
        <!-- Kolom Kiri: Alerts & Logs -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Critical Alerts -->
            <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden;">
                <div style="background: linear-gradient(90deg, rgba(239,68,68,0.1) 0%, rgba(245,158,11,0.1) 100%); padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: white; border-radius: 8px; color: #ef4444; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);"><i class="fas fa-bell"></i></div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--text); margin:0;">Perhatian Segera</h3>
                </div>
                <div style="padding: 0;">
                    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></div>
                            <span style="font-size: 13px; font-weight: 600; color: var(--text);">Expiring Soon (< 60 hari)</span>
                        </div>
                        <span class="tag" style="background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 700;">{{ count($expiringSoon) }}</span>
                    </div>
                    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
                            <span style="font-size: 13px; font-weight: 600; color: var(--text);">Dalam Perpanjangan</span>
                        </div>
                        <span class="tag" style="background: rgba(245,158,11,0.1); color: #f59e0b; font-weight: 700;">{{ count($dalamPerpanjangan) }}</span>
                    </div>
                    <div style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #6b7280;"></div>
                            <span style="font-size: 13px; font-weight: 600; color: var(--text);">Dokumen Tanpa Link</span>
                        </div>
                        <span class="tag" style="background: rgba(107,114,128,0.1); color: #6b7280; font-weight: 700;">{{ count($dokumenTanpaLink) }}</span>
                    </div>
                </div>
            </div>

            <!-- Monitoring Implementasi -->
            <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: var(--text); margin:0 0 16px 0;"><i class="fas fa-history" style="color: #10b981; margin-right: 8px;"></i> Implementasi Terbaru</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($implementasiTerbaru as $imp)
                    <div style="display: flex; align-items: flex-start; gap: 12px; padding-bottom: 12px; border-bottom: 1px dashed var(--border);">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p style="font-size: 13px; font-weight: 600; color: var(--text); margin: 0; line-height: 1.4;">{{ $imp->cooperation->title ?? 'Kegiatan Baru' }}</p>
                            <p style="font-size: 11px; color: var(--text-sub); margin: 4px 0 0 0;">{{ $imp->cooperation->mitra->nama_mitra ?? '-' }} &bull; {{ $imp->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p style="font-size: 12px; color: var(--text-sub); text-align: center; margin: 10px 0;">Belum ada data implementasi.</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Realisasi Luaran -->
            <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: var(--text); margin:0 0 16px 0;"><i class="fas fa-box-open" style="color: #8b5cf6; margin-right: 8px;"></i> Realisasi Luaran</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @forelse($realisasiLuaran as $luaran)
                    <div style="background: var(--bg); border: 1px solid var(--border); padding: 8px 12px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 800; color: var(--accent); font-size: 14px;">{{ $luaran->total_volume }}</span>
                        <span style="font-size: 11px; font-weight: 600; color: var(--text-sub);">{{ $luaran->satuan_luaran }}</span>
                    </div>
                    @empty
                    <p style="font-size: 12px; color: var(--text-sub); text-align: center; margin: 10px 0; width: 100%;">Belum ada luaran yang direalisasikan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tabel Ringkasan untuk Pimpinan -->
        <div class="card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border); display: flex; flex-direction: column; overflow: hidden;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text); margin:0;"><i class="fas fa-table" style="color: var(--accent); margin-right: 8px;"></i> Daftar Kerjasama (Executive View)</h3>
                <a href="{{ route('pimpinan.monitoring') }}" style="font-size: 12px; font-weight: 600; color: var(--accent); text-decoration: none; padding: 4px 8px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='rgba(79,70,229,0.1)'" onmouseout="this.style.background='transparent'">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div style="overflow-x: auto; flex: 1;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--bg); border-bottom: 2px solid var(--border);">
                            <th style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: var(--text-sub); text-transform: uppercase;">Judul Kerjasama</th>
                            <th style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: var(--text-sub); text-transform: uppercase;">Mitra</th>
                            <th style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: var(--text-sub); text-transform: uppercase;">Jenis</th>
                            <th style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: var(--text-sub); text-transform: uppercase;">Tgl Berakhir</th>
                            <th style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: var(--text-sub); text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: var(--text-sub); text-transform: uppercase; text-align: right;">Nilai Kontrak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allDocs = \App\Models\Cooperation::with(['mitra', 'details'])->latest()->take(8)->get();
                        @endphp
                        @forelse($allDocs as $doc)
                        <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 20px;">
                                <div style="font-weight: 700; font-size: 13px; color: var(--text); margin-bottom: 4px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                <div style="font-size: 11px; color: var(--text-sub); font-family: 'DM Mono', monospace;">{{ $doc->doc_number ?? 'No Doc' }}</div>
                            </td>
                            <td style="padding: 14px 20px;">
                                <div style="font-size: 13px; font-weight: 500; color: var(--text);">{{ $doc->mitra->nama_mitra ?? '-' }}</div>
                            </td>
                            <td style="padding: 14px 20px;">
                                <span style="background: var(--bg); border: 1px solid var(--border); padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; color: var(--text-sub);">{{ $doc->jenis ?? 'N/A' }}</span>
                            </td>
                            <td style="padding: 14px 20px;">
                                @if($doc->end_date)
                                <div style="font-size: 12px; color: var(--text);">{{ $doc->end_date->format('d M Y') }}</div>
                                @else
                                <div style="font-size: 12px; color: var(--text-sub);">-</div>
                                @endif
                            </td>
                            <td style="padding: 14px 20px;">
                                @php
                                    $statusColor = '#10b981'; $statusBg = 'rgba(16,185,129,0.1)';
                                    $statusText = 'Aktif';
                                    if($doc->status === 'aktif' && $doc->end_date && $doc->end_date < now()->addDays(60) && $doc->end_date >= now()) {
                                        $statusColor = '#f59e0b'; $statusBg = 'rgba(245,158,11,0.1)';
                                    } elseif($doc->status === 'aktif' && $doc->end_date && $doc->end_date < now()) {
                                        $statusColor = '#ef4444'; $statusBg = 'rgba(239,68,68,0.1)';
                                        $statusText = 'Kadarluarsa';
                                    } elseif($doc->status === 'proses') {
                                        $statusColor = '#f59e0b'; $statusBg = 'rgba(245,158,11,0.1)';
                                        $statusText = 'Perpanjangan';
                                    } elseif($doc->status_dokumen === 'Menunggu Evaluasi' || $doc->status_dokumen === 'Menunggu Validasi') {
                                        $statusColor = '#3b82f6'; $statusBg = 'rgba(59,130,246,0.1)';
                                        $statusText = $doc->status_dokumen;
                                    }
                                @endphp
                                <span style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                                    <div style="width: 6px; height: 6px; border-radius: 50%; background: {{ $statusColor }};"></div>
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td style="padding: 14px 20px; text-align: right; font-size: 12px; font-weight: 600; color: var(--text);">
                                @php
                                    $nilai = $doc->details->sum('nilai_kontrak');
                                @endphp
                                {{ $nilai > 0 ? 'Rp ' . number_format($nilai, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-sub); font-size: 13px;">Belum ada data kerjasama.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</main>

{{-- ── Chart.js scripts ──────────────────────────────────────── --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
        const textColor = isDark ? '#8b92a8' : '#6b7280';
        const surfaceColor = isDark ? '#1a1d2e' : '#ffffff';

        // 1. Tren Tahunan (Line Chart)
        const trenCtx = document.getElementById('trenTahunanChart');
        if (trenCtx) {
            const rawTren = JSON.parse(trenCtx.getAttribute('data-tren') || '[]');
            const labels = rawTren.map(item => item.tahun);
            const data = rawTren.map(item => item.total);

            new Chart(trenCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Produktivitas Kerjasama',
                        data: data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { stepSize: 1, color: textColor } },
                        x: { grid: { display: false }, ticks: { color: textColor } }
                    }
                }
            });
        }

        // 2. Distribusi Jenis (Pie Chart)
        const jenisCtx = document.getElementById('distribusiJenisChart');
        if (jenisCtx) {
            const rawJenis = JSON.parse(jenisCtx.getAttribute('data-jenis') || '[]');
            const labels = rawJenis.map(item => item.jenis);
            const data = rawJenis.map(item => item.total);
            const colors = ['#ec4899', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b'];

            new Chart(jenisCtx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, data.length),
                        borderWidth: 2,
                        borderColor: surfaceColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: textColor, padding: 20, usePointStyle: true } }
                    }
                }
            });
        }

        // 3. Top 5 Jurusan (Bar Chart)
        const jurusanCtx = document.getElementById('topJurusanChart');
        if (jurusanCtx) {
            const rawJurusan = JSON.parse(jurusanCtx.getAttribute('data-jurusan') || '[]');
            const labels = rawJurusan.map(item => item.nama_jurusan);
            const data = rawJurusan.map(item => item.total);

            new Chart(jurusanCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Kerjasama',
                        data: data,
                        backgroundColor: 'rgba(99,102,241,0.8)',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: gridColor }, ticks: { stepSize: 1, color: textColor } },
                        y: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } }
                    }
                }
            });
        }

        // 4. Klasifikasi Mitra (Doughnut Chart)
        const klasifikasiCtx = document.getElementById('klasifikasiMitraChart');
        if (klasifikasiCtx) {
            const rawKlasifikasi = JSON.parse(klasifikasiCtx.getAttribute('data-klasifikasi') || '[]');
            const labels = rawKlasifikasi.map(item => item.klasifikasi);
            const data = rawKlasifikasi.map(item => item.total);
            const colors = ['#f59e0b', '#10b981', '#3b82f6', '#ec4899', '#8b5cf6', '#6366f1'];

            new Chart(klasifikasiCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, data.length),
                        borderWidth: 2,
                        borderColor: surfaceColor,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: textColor, font: { size: 10 }, usePointStyle: true } }
                    }
                }
            });
        }

    });
</script>

<link rel="stylesheet" href="{{ asset('css/auth/dashboard.css') }}" data-turbo-track="reload">

<main id="mainContent" class="unitdash">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <span>Beranda Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Selamat Datang, {{ $user->name }}</h2>
                    <p class="ud-subtitle">
                        Kelola semua aktivitas kerja sama dan pantau program magang mahasiswa dengan mudah.
                    </p>
                </div>
            </div>
        </div>
        <a href="{{ route('mitra.pengajuan.create') ?? '#' }}" class="ud-create-menu">
            <span class="ud-create-icon"><i class="fas fa-file-circle-plus"></i></span>
            <span class="ud-create-copy">
                <strong>Ajukan Kerja Sama Baru</strong>
                <small>Buat pengajuan MoU/MoA ke Politeknik</small>
            </span>
            <span class="ud-create-arrow"><i class="fas fa-arrow-right"></i></span>
        </a>
    </section>

    <!-- 2. Alert Center (Urgent Attention) -->
    @if($pendingReviewCount > 0 || $expiringCount > 0)
        <section class="ud-panel"
            style="background: rgba(245,158,11,0.1); border-left: 4px solid #f59e0b; margin-bottom: 24px; padding: 20px;">
            <h3 class="ud-panel-title" style="color: #d97706; margin: 0;"><i class="fas fa-bell"></i> Perhatian Mendesak
            </h3>
            <ul style="margin: 12px 0 0 24px; color: var(--text-main); font-size: 0.95rem;">
                @if($pendingReviewCount > 0)
                    <li>Anda memiliki <strong>{{ $pendingReviewCount }}</strong> dokumen kerja sama yang menunggu
                        persetujuan/tanda tangan. <a href="{{ route('mitra.dokumen.index') }}"
                            style="color: #4f46e5; font-weight: 600;">Review Sekarang</a></li>
                @endif
                @if($expiringCount > 0)
                    <li>Ada <strong>{{ $expiringCount }}</strong> kerja sama yang akan segera berakhir. Mohon ajukan
                        perpanjangan.</li>
                @endif
            </ul>
        </section>
    @endif

    <section class="ud-summary">
        <article class="ud-card ud-tone-emerald">
            <div class="ud-card-top">
                <div class="ud-icon"><i class="fas fa-handshake"></i></div>
                <div class="ud-metric-label">Kerja Sama Aktif</div>
            </div>
            <div class="ud-metric-hint">Total MoU/MoA/IA Aktif</div>
            <div class="ud-metric-value">{{ number_format($aktifCount) }}</div>
            <div class="ud-card-accent" aria-hidden="true"><i class="fas fa-handshake"></i></div>
        </article>

        <article class="ud-card ud-tone-amber">
            <div class="ud-card-top">
                <div class="ud-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="ud-metric-label">Menunggu Review</div>
            </div>
            <div class="ud-metric-hint">Draf Dokumen Baru</div>
            <div class="ud-metric-value">{{ number_format($pendingReviewCount) }}</div>
            <div class="ud-card-accent" aria-hidden="true"><i class="fas fa-file-invoice"></i></div>
        </article>

        <article class="ud-card ud-tone-blue">
            <div class="ud-card-top">
                <div class="ud-icon"><i class="fas fa-users-gear"></i></div>
                <div class="ud-metric-label">Mahasiswa Magang</div>
            </div>
            <div class="ud-metric-hint">Sedang aktif magang</div>
            <div class="ud-metric-value">{{ number_format($totalMahasiswaAktif) }}</div>
            <div class="ud-card-accent" aria-hidden="true"><i class="fas fa-users-gear"></i></div>
        </article>

        <article class="ud-card ud-tone-indigo">
            <div class="ud-card-top">
                <div class="ud-icon"><i class="fas fa-briefcase"></i></div>
                <div class="ud-metric-label">Alumni Terserap</div>
            </div>
            <div class="ud-metric-hint">Lulusan bekerja di Mitra</div>
            <div class="ud-metric-value">{{ number_format($alumniTerserap) }}</div>
            <div class="ud-card-accent" aria-hidden="true"><i class="fas fa-briefcase"></i></div>
        </article>
    </section>

    <!-- 4. Main Content Grid (Two Column equivalent) -->
    <div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: 24px; align-items: start; margin-top: 24px;">

        <!-- Left: Quick Actions -->
        <section class="ud-panel">
            <div class="ud-table-head">
                <div>
                    <h3 class="ud-panel-title"><i class="fas fa-bolt"></i> Aksi Cepat</h3>
                    <p class="ud-panel-desc">Akses cepat menu utama.</p>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 16px;">
                <a href="{{ route('mitra.dokumen.index') }}" class="ud-tab" style="justify-content: flex-start;">
                    <i class="fas fa-file-pen" style="color: var(--accent); width: 24px; text-align: center;"></i>
                    Review Draf Dokumen
                </a>
                <a href="#" class="ud-tab" style="justify-content: flex-start;">
                    <i class="fas fa-star" style="color: #f59e0b; width: 24px; text-align: center;"></i> Beri Penilaian
                    Mahasiswa
                </a>
                <a href="#" class="ud-tab" style="justify-content: flex-start;">
                    <i class="fas fa-comments" style="color: #10b981; width: 24px; text-align: center;"></i> Kirim Umpan
                    Balik
                </a>
                <a href="#" class="ud-tab" style="justify-content: flex-start;">
                    <i class="fas fa-headset" style="color: var(--text-sub); width: 24px; text-align: center;"></i>
                    Hubungi Administrator
                </a>
            </div>
        </section>

        <!-- Right: Recent Documents Table -->
        <section class="ud-panel ud-table-panel">
            <div class="ud-table-head">
                <div>
                    <h3 class="ud-panel-title"><i class="fas fa-history"></i> Dokumen Terakhir</h3>
                    <p class="ud-panel-desc">Ringkasan pengajuan kerja sama terbaru Anda.</p>
                </div>
            </div>

            <div class="ud-table-wrap">
                <table class="ud-table">
                    <thead>
                        <tr>
                            <th>No. Dokumen & Judul</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDocuments as $doc)
                            <tr data-kerjasama-row>
                                <td>
                                    <div class="ud-small">{{ $doc->nomor_dokumen ?? 'Menunggu Nomor' }}</div>
                                    <div class="ud-doc-title">{{ $doc->judul_kerjasama ?? 'Perjanjian Kerja Sama' }}</div>
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($doc->status ?? '');
                                        if (str_contains($status, 'aktif')) {
                                            $lbl = 'Aktif';
                                            $cls = 'is-active';
                                            $icn = 'fa-circle-check';
                                        } elseif (str_contains($status, 'draft') || str_contains($status, 'menunggu')) {
                                            $lbl = 'Menunggu Review';
                                            $cls = 'is-pending';
                                            $icn = 'fa-spinner';
                                        } elseif (str_contains($status, 'perpanjangan') || str_contains($status, 'kedaluwarsa')) {
                                            $lbl = 'Masa Tenggang';
                                            $cls = 'is-expired';
                                            $icn = 'fa-clock-rotate-left';
                                        } else {
                                            $lbl = $doc->status;
                                            $cls = '';
                                            $icn = 'fa-circle-question';
                                        }
                                    @endphp
                                    <span class="ud-status-badge {{ $cls }}">
                                        <i class="fas {{ $icn }}"></i> {{ $lbl }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('mitra.dokumen.index') }}" class="ud-action-btn" title="Detail">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-sub); padding: 32px;">
                                    Belum ada data kerja sama.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>
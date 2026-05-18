<main id="mainContent" class="submission-dashboard">
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('pimpinan.dashboard') }}"
                    style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <span class="current">Pengajuan Mitra</span>
            </div>
            <div class="dk-hero-main">
                <div class="dk-hero-icon"><i class="fas fa-handshake-angle"></i></div>
                <div>
                    <span class="dk-eyebrow">Inbox Pengajuan Publik</span>
                    <h2>Validasi Pengajuan Kerja Sama Mitra</h2>
                    <p>Tinjau data mitra dari landing page, lalu setujui agar masuk ke master mitra atau tolak
                        dengan catatan yang jelas.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="submission-stats">
        <article class="dk-stat-card">
            <span>Total Pengajuan</span>
            <strong>{{ number_format($submissionStats['total'] ?? 0, 0, ',', '.') }}</strong>
            <small>Seluruh pengajuan publik yang sudah masuk.</small>
        </article>
        <article class="dk-stat-card">
            <span>Menunggu Review</span>
            <strong>{{ number_format($submissionStats['pending'] ?? 0, 0, ',', '.') }}</strong>
            <small>Antrean yang masih membutuhkan keputusan pimpinan.</small>
        </article>
        <article class="dk-stat-card">
            <span>Disetujui</span>
            <strong>{{ number_format($submissionStats['approved'] ?? 0, 0, ',', '.') }}</strong>
            <small>Pengajuan yang sudah masuk ke data master mitra.</small>
        </article>
        <article class="dk-stat-card">
            <span>Ditolak</span>
            <strong>{{ number_format($submissionStats['rejected'] ?? 0, 0, ',', '.') }}</strong>
            <small>Pengajuan yang belum dapat ditindaklanjuti.</small>
        </article>
    </section>

    <section class="submission-stack">
        <div class="dk-card submission-section">
            <div class="dk-card-header">
                <div class="dk-card-title">
                    <span>Antrean Validasi</span>
                    <small>{{ $pendingSubmissions->count() }} pengajuan aktif</small>
                </div>
            </div>

            @forelse ($pendingSubmissions as $submission)
                @php
                    $websiteUrl = $submission->website ?: null;
                    $statusClass = match ($submission->status) {
                        'disetujui' => 'approved',
                        'ditolak' => 'rejected',
                        default => 'pending',
                    };
                @endphp

                <article class="submission-card">
                    <div class="submission-card-head">
                        <div class="submission-card-title">
                            <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                            <h3>{{ $submission->judul_pengajuan }}</h3>
                            <p class="submission-card-subtitle">
                                {{ $submission->nama_mitra }} &middot; {{ ucfirst($submission->kategori) }}
                                @if ($submission->submitted_at)
                                    &middot; Dikirim {{ $submission->submitted_at->format('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                        <span class="submission-status {{ $statusClass }}">
                            {{ $submission->status_label }}
                        </span>
                    </div>

                    <div class="submission-meta">
                        <span class="submission-chip">
                            <i class="fas fa-layer-group"></i>
                            {{ $submission->klasifikasi?->nama ?? 'Klasifikasi belum dipilih' }}
                        </span>
                        <span class="submission-chip">
                            <i class="fas fa-globe"></i>
                            {{ $submission->negara ?: 'Negara belum diisi' }}
                        </span>
                        <span class="submission-chip">
                            <i class="fas fa-phone"></i>
                            {{ $submission->telp }}
                        </span>
                    </div>

                    <div class="submission-detail-grid">
                        <div class="submission-detail">
                            <span class="submission-detail-label">Alamat Mitra</span>
                            <span class="submission-detail-value">{{ $submission->alamat }}</span>
                        </div>
                        <div class="submission-detail">
                            <span class="submission-detail-label">Website Mitra</span>
                            <span class="submission-detail-value">
                                @if ($websiteUrl)
                                    <a href="{{ $websiteUrl }}" target="_blank" rel="noreferrer">
                                        {{ $websiteUrl }}
                                    </a>
                                @else
                                    Belum ada website
                                @endif
                            </span>
                        </div>
                        <div class="submission-detail">
                            <span class="submission-detail-label">PIC Pengaju</span>
                            <span class="submission-detail-value">
                                {{ $submission->nama_pengaju }}
                                @if ($submission->jabatan_pengaju)
                                    <br>{{ $submission->jabatan_pengaju }}
                                @endif
                            </span>
                        </div>
                        <div class="submission-detail">
                            <span class="submission-detail-label">Kontak Pengaju</span>
                            <span class="submission-detail-value">
                                {{ $submission->email_pengaju }}<br>{{ $submission->telepon_pengaju }}
                            </span>
                        </div>
                    </div>

                    <div class="submission-note">
                        <div class="submission-detail">
                            <span class="submission-detail-label">Tujuan Pengajuan</span>
                            <span class="submission-detail-value">{{ $submission->tujuan_pengajuan }}</span>
                        </div>

                        @if ($submission->ruang_lingkup)
                            <div class="submission-detail">
                                <span class="submission-detail-label">Ruang Lingkup</span>
                                <span class="submission-detail-value">{{ $submission->ruang_lingkup }}</span>
                            </div>
                        @endif

                        @if ($submission->pesan_tambahan)
                            <div class="submission-note-box">
                                <strong>Catatan Mitra</strong><br>
                                {{ $submission->pesan_tambahan }}
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('pimpinan.pengajuan_mitra.review', $submission->id) }}" method="POST"
                        class="submission-form">
                        @csrf
                        <label for="catatan-{{ $submission->id }}">Catatan Pimpinan</label>
                        <textarea id="catatan-{{ $submission->id }}" name="catatan_pimpinan" class="submission-textarea"
                            placeholder="Tambahkan catatan validasi. Wajib diisi jika pengajuan ditolak."></textarea>

                        <div class="submission-actions">
                            <button type="submit" name="keputusan" value="ditolak" class="ev-btn-reject">
                                <i class="fas fa-ban"></i>
                                <span>Tolak Pengajuan</span>
                            </button>
                            <button type="submit" name="keputusan" value="disetujui" class="ev-btn-approve">
                                <i class="fas fa-circle-check"></i>
                                <span>Setujui &amp; Simpan Mitra</span>
                            </button>
                        </div>
                    </form>
                </article>
            @empty
                <div class="dk-empty-state">
                    <div class="dk-empty-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <p>Tidak ada pengajuan mitra yang sedang menunggu validasi.</p>
                </div>
            @endforelse
        </div>

        <div class="dk-card submission-section">
            <div class="dk-card-header">
                <div class="dk-card-title">
                    <span>Riwayat Review Terbaru</span>
                    <small>20 keputusan terakhir untuk pengajuan publik.</small>
                </div>
            </div>

            @if ($reviewedSubmissions->isEmpty())
                <div class="dk-empty-state">
                    <div class="dk-empty-icon">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                    <p>Belum ada riwayat review pengajuan mitra.</p>
                </div>
            @else
                <div class="submission-history-list">
                    @foreach ($reviewedSubmissions as $submission)
                        @php
                            $statusClass = match ($submission->status) {
                                'disetujui' => 'approved',
                                'ditolak' => 'rejected',
                                default => 'pending',
                            };
                        @endphp

                        <article class="submission-history-item">
                            <div class="submission-history-head">
                                <div>
                                    <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                                    <h3>{{ $submission->nama_mitra }}</h3>
                                    <p class="submission-history-meta">
                                        {{ $submission->judul_pengajuan }}
                                        @if ($submission->reviewed_at)
                                            &middot; Diproses {{ $submission->reviewed_at->format('d M Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                                <span class="submission-status {{ $statusClass }}">{{ $submission->status_label }}</span>
                            </div>

                            <div class="submission-history-meta">
                                Reviewer: {{ $submission->reviewer?->name ?? 'Pimpinan' }}<br>
                                Mitra terkait:
                                {{ $submission->mitra?->nama_mitra ?? 'Belum dikonversi ke master mitra' }}
                            </div>

                            @if ($submission->catatan_pimpinan)
                                <div class="submission-history-note">
                                    {{ $submission->catatan_pimpinan }}
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</main>

<link rel="stylesheet" href="{{ asset('css/auth/pimpinan/pmitra.css') }}">

<main id="mainContent" class="submission-dashboard">
    <section class="pimpinan-page-header">
        <div class="pimpinan-header-bg"></div>
        <div class="pimpinan-header-content">
            <div class="pimpinan-breadcrumb">
                <a href="{{ route('pimpinan.dashboard') }}" class="mn-breadcrumb-link"><i class="fas fa-home"></i></a>
                <span class="sep">/</span>
                <a href="{{ route('pimpinan.dashboard') }}" class="mn-breadcrumb-link current">Beranda</a>
                <span class="sep">/</span>
                <span class="current">Pengajuan Mitra</span>
            </div>
            <h2 id="pageTitle" class="pimpinan-page-title">Validasi Pengajuan Kerja Sama Mitra</h2>
            <p id="pageDesc" class="pimpinan-page-desc">Tinjau data mitra dari landing page, lalu setujui agar masuk ke master mitra atau tolak dengan catatan yang jelas.</p>
        </div>
    </section>

    <section class="submission-stats" aria-label="Ringkasan pengajuan mitra">
        <article class="dk-stat-card total">
            <div class="dk-stat-icon"><i class="fas fa-inbox"></i></div>
            <span>Total Pengajuan</span>
            <strong>{{ number_format($submissionStats['total'] ?? 0, 0, ',', '.') }}</strong>
            <small>Seluruh pengajuan publik yang sudah masuk.</small>
        </article>
        <article class="dk-stat-card pending">
            <div class="dk-stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <span>Menunggu Review</span>
            <strong>{{ number_format($submissionStats['pending'] ?? 0, 0, ',', '.') }}</strong>
            <small>Antrean yang masih membutuhkan keputusan pimpinan.</small>
        </article>
        <article class="dk-stat-card approved">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <span>Disetujui</span>
            <strong>{{ number_format($submissionStats['approved'] ?? 0, 0, ',', '.') }}</strong>
            <small>Pengajuan yang sudah masuk ke data master mitra.</small>
        </article>
        <article class="dk-stat-card rejected">
            <div class="dk-stat-icon"><i class="fas fa-circle-xmark"></i></div>
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
                <div class="submission-tools">
                    <label class="submission-search" for="submissionSearch">
                        <i class="fas fa-search"></i>
                        <input id="submissionSearch" type="search" placeholder="Cari mitra, kode, atau negara">
                    </label>
                    <div class="submission-filter-dropdown" x-data="{
                        open: false,
                        selectedValue: 'all',
                        selectedLabel: 'Semua kategori',
                        select(value, label) {
                            this.selectedValue = value;
                            this.selectedLabel = label;
                            this.open = false;
                            this.$refs.filterValue.value = value;
                            this.$refs.filterValue.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }" @click.outside="open = false">
                        <input id="submissionCategoryFilter" x-ref="filterValue" type="hidden" value="all">
                        <button type="button" class="submission-filter-trigger" @click="open = !open"
                            :aria-expanded="open.toString()" aria-haspopup="listbox" aria-label="Filter kategori">
                            <span class="submission-filter-icon"><i class="fas fa-filter"></i></span>
                            <span class="submission-filter-label" x-text="selectedLabel"></span>
                            <i class="fas fa-chevron-down submission-filter-chevron" :class="{ 'is-open': open }"></i>
                        </button>
                        <div class="submission-filter-menu" x-show="open" x-transition.origin.top.right x-cloak role="listbox">
                            <button type="button" class="submission-filter-option"
                                :class="{ 'is-selected': selectedValue === 'all' }"
                                :aria-selected="(selectedValue === 'all').toString()"
                                @click="select('all', 'Semua kategori')" role="option">
                                <span>Semua kategori</span>
                                <i class="fas fa-check" x-show="selectedValue === 'all'"></i>
                            </button>
                        @foreach ($pendingSubmissions->pluck('kategori')->filter()->unique()->sort() as $kategori)
                            <button type="button" class="submission-filter-option"
                                :class="{ 'is-selected': selectedValue === @js(strtolower($kategori)) }"
                                :aria-selected="(selectedValue === @js(strtolower($kategori))).toString()"
                                @click="select(@js(strtolower($kategori)), @js(ucfirst($kategori)))" role="option">
                                <span>{{ ucfirst($kategori) }}</span>
                                <i class="fas fa-check" x-show="selectedValue === @js(strtolower($kategori))"></i>
                            </button>
                        @endforeach
                        </div>
                    </div>
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

                <article class="submission-card"
                    data-submission-card
                    data-category="{{ strtolower($submission->kategori) }}"
                    data-search="{{ strtolower($submission->kode_pengajuan . ' ' . $submission->judul_pengajuan . ' ' . $submission->nama_mitra . ' ' . $submission->kategori . ' ' . ($submission->negara ?? '') . ' ' . ($submission->klasifikasi?->nama ?? '')) }}">
                    <div class="submission-card-head">
                        <div class="submission-card-title">
                            <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                            @if ($submission->mitra_id)
                                <span class="submission-status" style="background: rgba(245, 158, 11, 0.12); color: #d97706; border-color: rgba(245, 158, 11, 0.3); font-size: 0.72rem; padding: 2px 8px; border-radius: 99px; margin-left: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; border: 1px solid; vertical-align: middle;">
                                    <i class="fas fa-sync" style="font-size: 0.65rem;"></i> Perpanjangan
                                </span>
                            @endif
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
                        <div class="submission-form-head">
                            <label for="catatan-{{ $submission->id }}">Catatan Pimpinan</label>
                            <span class="submission-counter" data-note-counter>0 karakter</span>
                        </div>
                        <textarea id="catatan-{{ $submission->id }}" name="catatan_pimpinan" class="submission-textarea"
                            rows="4" placeholder="Tambahkan catatan validasi. Wajib diisi jika pengajuan ditolak."></textarea>

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
            <div class="dk-empty-state submission-filter-empty" hidden>
                <div class="dk-empty-icon">
                    <i class="fas fa-magnifying-glass"></i>
                </div>
                <p>Tidak ada pengajuan yang cocok dengan pencarian atau filter.</p>
            </div>
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

                        <article class="submission-history-item {{ $statusClass }}">
                            <div class="submission-history-head">
                                <div>
                                    <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                                    @if ($submission->mitra_id)
                                        <span class="submission-status" style="background: rgba(245, 158, 11, 0.12); color: #d97706; border-color: rgba(245, 158, 11, 0.3); font-size: 0.72rem; padding: 2px 8px; border-radius: 99px; margin-left: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; border: 1px solid; vertical-align: middle;">
                                            <i class="fas fa-sync" style="font-size: 0.65rem;"></i> Perpanjangan
                                        </span>
                                    @endif
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

<script src="{{ asset('js/auth/pimpinan/pmitra.js') }}"></script>

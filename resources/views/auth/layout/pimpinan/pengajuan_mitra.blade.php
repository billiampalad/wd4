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
            <p id="pageDesc" class="pimpinan-page-desc">Tinjau data mitra dari landing page, lalu setujui agar masuk ke
                master mitra atau tolak dengan catatan yang jelas.</p>
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
        {{-- SECTION 1: ANTREAN VALIDASI (DATA TABLE WITH TABS) --}}
        <div class="dk-card submission-section" x-data="{ activeTab: 'baru' }">
            <div class="dk-card-header">
                <div class="dk-card-title">
                    <span>Antrean Validasi</span>
                    <small>{{ $pendingBaru->count() + $pendingPerpanjangan->count() }} pengajuan aktif</small>
                </div>
                <div class="submission-tools">
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
                        <div class="submission-filter-menu" x-show="open" x-transition.origin.top.right x-cloak
                            role="listbox">
                            <button type="button" class="submission-filter-option"
                                :class="{ 'is-selected': selectedValue === 'all' }"
                                :aria-selected="(selectedValue === 'all').toString()"
                                @click="select('all', 'Semua kategori')" role="option">
                                <span>Semua kategori</span>
                                <i class="fas fa-check" x-show="selectedValue === 'all'"></i>
                            </button>
                            @php
                                $allPendingCategories = $pendingBaru->merge($pendingPerpanjangan)->pluck('kategori')->filter()->unique()->sort();
                            @endphp
                            @foreach ($allPendingCategories as $kategori)
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

            {{-- TAB NAVIGATION --}}
            <div class="submission-tab-nav">
                <button type="button" class="submission-tab-btn" :class="{ 'is-active': activeTab === 'baru' }" @click="activeTab = 'baru'">
                    <i class="fas fa-handshake"></i>
                    <span>Kerja Sama Baru</span>
                    <span class="submission-tab-count">{{ $pendingBaru->count() }}</span>
                </button>
                <button type="button" class="submission-tab-btn perpanjangan" :class="{ 'is-active': activeTab === 'perpanjangan' }" @click="activeTab = 'perpanjangan'">
                    <i class="fas fa-sync-alt"></i>
                    <span>Perpanjangan Kerja Sama</span>
                    <span class="submission-tab-count perpanjangan">{{ $pendingPerpanjangan->count() }}</span>
                </button>
            </div>

            {{-- TAB CONTENT 1: KERJA SAMA BARU --}}
            <div x-show="activeTab === 'baru'" class="submission-tab-content">
                @if ($pendingBaru->isEmpty())
                    <div class="dk-empty-state">
                        <div class="dk-empty-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <p>Tidak ada pengajuan kerja sama baru yang sedang menunggu validasi.</p>
                    </div>
                @else
                    <div class="table-wrap um-table-wrap dk-table-wrap">
                        <table class="um-table dk-table submission-data-table">
                            <thead>
                                <tr>
                                    <th class="um-th um-th-num">#</th>
                                    <th class="um-th">Kode &amp; Status</th>
                                    <th class="um-th">Nama Mitra</th>
                                    <th class="um-th">Judul Pengajuan</th>
                                    <th class="um-th">Klasifikasi / Kategori</th>
                                    <th class="um-th">Kontak</th>
                                    <th class="um-th">Dikirim</th>
                                    <th class="um-th um-th-aksi text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingBaru as $submission)
                                    @php
                                        $websiteUrl = $submission->website ?: null;
                                        $statusClass = match ($submission->status) {
                                            'disetujui' => 'approved',
                                            'ditolak' => 'rejected',
                                            default => 'pending',
                                        };
                                    @endphp
                                    <tr class="um-row dk-row submission-row" data-submission-row data-id="{{ $submission->id }}"
                                        data-category="{{ strtolower($submission->kategori) }}"
                                        data-mitra-id="{{ $submission->mitra_id }}"
                                        data-is-perpanjangan="{{ !empty($submission->doc_number) ? '1' : '0' }}"
                                        data-jenis-dokumen="{{ $submission->jenis ?: '-' }}"
                                        data-doc-number="{{ $submission->doc_number ?: '-' }}"
                                        data-start-date="{{ $submission->start_date ? \Carbon\Carbon::parse($submission->start_date)->format('d M Y') : '-' }}"
                                        data-end-date="{{ $submission->end_date ? \Carbon\Carbon::parse($submission->end_date)->format('d M Y') : '-' }}"
                                        data-file-surat="{{ $submission->file_surat ? asset('storage/' . $submission->file_surat) : '' }}"
                                        data-mitra-name="{{ $submission->nama_mitra }}" data-mitra-email="{{ $submission->email }}"
                                        data-mitra-phone="{{ $submission->telp }}"
                                        data-submission-code="{{ $submission->kode_pengajuan }}"
                                        data-submission-title="{{ $submission->judul_pengajuan }}"
                                        data-klasifikasi="{{ $submission->klasifikasi?->nama ?? 'Klasifikasi belum dipilih' }}"
                                        data-kategori="{{ ucfirst($submission->kategori) }}"
                                        data-negara="{{ $submission->negara ?: '-' }}" data-alamat="{{ $submission->alamat }}"
                                        data-website="{{ $websiteUrl }}"
                                        data-penandatangan-nama="{{ $submission->nama_penandatangan }}"
                                        data-penandatangan-jabatan="{{ $submission->jabatan_penandatangan ?: '-' }}"
                                        data-pj-nama="{{ $submission->nama_penanggung_jawab ?: '-' }}"
                                        data-pj-jabatan="{{ $submission->jabatan_penanggung_jawab ?: '-' }}"
                                        data-tujuan="{{ $submission->tujuan_pengajuan }}"
                                        data-ruang-lingkup="{{ $submission->ruang_lingkup ?: '-' }}"
                                        data-pesan-tambahan="{{ $submission->pesan_tambahan ?: '' }}"
                                        data-review-url="{{ route('pimpinan.pengajuan_mitra.review', $submission->id) }}"
                                        data-search="{{ strtolower($submission->kode_pengajuan . ' ' . $submission->judul_pengajuan . ' ' . $submission->nama_mitra . ' ' . $submission->kategori . ' ' . ($submission->negara ?? '') . ' ' . ($submission->klasifikasi?->nama ?? '') . ' ' . ($submission->nama_penandatangan ?? '') . ' ' . ($submission->email ?? '') . ' ' . ($submission->telp ?? '')) }}">
                                        <td class="um-td um-td-num">
                                            <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-code-cell">
                                                <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-mitra-cell">
                                                <strong class="sub-mitra-name">{{ $submission->nama_mitra }}</strong>
                                                <small class="sub-mitra-meta"><i class="fas fa-globe"></i>
                                                    {{ $submission->negara ?: '-' }}</small>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <span class="sub-title-cell"
                                                title="{{ $submission->judul_pengajuan }}">{{ $submission->judul_pengajuan }}</span>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-klas-cell">
                                                <span class="sub-chip-badge">{{ $submission->klasifikasi?->nama ?? 'Umum' }}</span>
                                                <small class="text-sub">{{ ucfirst($submission->kategori) }}</small>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-contact-cell">
                                                <small><i class="fas fa-envelope"></i> {{ $submission->email ?: '-' }}</small>
                                                <small><i class="fab fa-whatsapp"></i> {{ $submission->telp ?: '-' }}</small>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <small class="sub-date-cell">
                                                {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y') : '-' }}
                                            </small>
                                        </td>
                                        <td class="um-td um-td-aksi text-center">
                                            <div class="sub-action-group">
                                                <button type="button" class="sub-action-btn btn-detail" data-action="detail"
                                                    title="Lihat Detail Pengajuan">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="sub-action-btn btn-approve" data-action="approve"
                                                    title="Setujui Pengajuan">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="sub-action-btn btn-reject" data-action="reject"
                                                    title="Tolak Pengajuan">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- TAB CONTENT 2: PERPANJANGAN KERJA SAMA --}}
            <div x-show="activeTab === 'perpanjangan'" class="submission-tab-content" x-cloak>
                @if ($pendingPerpanjangan->isEmpty())
                    <div class="dk-empty-state">
                        <div class="dk-empty-icon is-perpanjangan">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <p>Tidak ada pengajuan perpanjangan kerja sama yang sedang menunggu validasi.</p>
                    </div>
                @else
                    <div class="table-wrap um-table-wrap dk-table-wrap">
                        <table class="um-table dk-table submission-data-table">
                            <thead>
                                <tr>
                                    <th class="um-th um-th-num">#</th>
                                    <th class="um-th">Kode &amp; Status</th>
                                    <th class="um-th">Nama Mitra</th>
                                    <th class="um-th">Judul Pengajuan</th>
                                    <th class="um-th">Klasifikasi / Kategori</th>
                                    <th class="um-th">Kontak</th>
                                    <th class="um-th">Dikirim</th>
                                    <th class="um-th um-th-aksi text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingPerpanjangan as $submission)
                                    @php
                                        $websiteUrl = $submission->website ?: null;
                                        $statusClass = match ($submission->status) {
                                            'disetujui' => 'approved',
                                            'ditolak' => 'rejected',
                                            default => 'pending',
                                        };
                                    @endphp
                                    <tr class="um-row dk-row submission-row" data-submission-row data-id="{{ $submission->id }}"
                                        data-category="{{ strtolower($submission->kategori) }}"
                                        data-mitra-id="{{ $submission->mitra_id }}"
                                        data-is-perpanjangan="{{ !empty($submission->doc_number) ? '1' : '0' }}"
                                        data-jenis-dokumen="{{ $submission->jenis ?: '-' }}"
                                        data-doc-number="{{ $submission->doc_number ?: '-' }}"
                                        data-start-date="{{ $submission->start_date ? \Carbon\Carbon::parse($submission->start_date)->format('d M Y') : '-' }}"
                                        data-end-date="{{ $submission->end_date ? \Carbon\Carbon::parse($submission->end_date)->format('d M Y') : '-' }}"
                                        data-file-surat="{{ $submission->file_surat ? asset('storage/' . $submission->file_surat) : '' }}"
                                        data-mitra-name="{{ $submission->nama_mitra }}" data-mitra-email="{{ $submission->email }}"
                                        data-mitra-phone="{{ $submission->telp }}"
                                        data-submission-code="{{ $submission->kode_pengajuan }}"
                                        data-submission-title="{{ $submission->judul_pengajuan }}"
                                        data-klasifikasi="{{ $submission->klasifikasi?->nama ?? 'Klasifikasi belum dipilih' }}"
                                        data-kategori="{{ ucfirst($submission->kategori) }}"
                                        data-negara="{{ $submission->negara ?: '-' }}" data-alamat="{{ $submission->alamat }}"
                                        data-website="{{ $websiteUrl }}"
                                        data-penandatangan-nama="{{ $submission->nama_penandatangan }}"
                                        data-penandatangan-jabatan="{{ $submission->jabatan_penandatangan ?: '-' }}"
                                        data-pj-nama="{{ $submission->nama_penanggung_jawab ?: '-' }}"
                                        data-pj-jabatan="{{ $submission->jabatan_penanggung_jawab ?: '-' }}"
                                        data-tujuan="{{ $submission->tujuan_pengajuan }}"
                                        data-ruang-lingkup="{{ $submission->ruang_lingkup ?: '-' }}"
                                        data-pesan-tambahan="{{ $submission->pesan_tambahan ?: '' }}"
                                        data-review-url="{{ route('pimpinan.pengajuan_mitra.review', $submission->id) }}"
                                        data-search="{{ strtolower($submission->kode_pengajuan . ' ' . $submission->judul_pengajuan . ' ' . $submission->nama_mitra . ' ' . $submission->kategori . ' ' . ($submission->negara ?? '') . ' ' . ($submission->klasifikasi?->nama ?? '') . ' ' . ($submission->nama_penandatangan ?? '') . ' ' . ($submission->email ?? '') . ' ' . ($submission->telp ?? '')) }}">
                                        <td class="um-td um-td-num">
                                            <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-code-cell">
                                                <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                                                <span class="sub-badge-perpanjangan" title="Pengajuan Perpanjangan">
                                                    <i class="fas fa-sync"></i> Perpanjangan
                                                </span>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-mitra-cell">
                                                <strong class="sub-mitra-name">{{ $submission->nama_mitra }}</strong>
                                                <small class="sub-mitra-meta"><i class="fas fa-globe"></i>
                                                    {{ $submission->negara ?: '-' }}</small>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <span class="sub-title-cell"
                                                title="{{ $submission->judul_pengajuan }}">{{ $submission->judul_pengajuan }}</span>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-klas-cell">
                                                <span class="sub-chip-badge">{{ $submission->klasifikasi?->nama ?? 'Umum' }}</span>
                                                <small class="text-sub">{{ ucfirst($submission->kategori) }}</small>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-contact-cell">
                                                <small><i class="fas fa-envelope"></i> {{ $submission->email ?: '-' }}</small>
                                                <small><i class="fab fa-whatsapp"></i> {{ $submission->telp ?: '-' }}</small>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <small class="sub-date-cell">
                                                {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y') : '-' }}
                                            </small>
                                        </td>
                                        <td class="um-td um-td-aksi text-center">
                                            <div class="sub-action-group">
                                                <button type="button" class="sub-action-btn btn-detail" data-action="detail"
                                                    title="Lihat Detail Pengajuan">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="sub-action-btn btn-approve" data-action="approve"
                                                    title="Setujui Pengajuan">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="sub-action-btn btn-reject" data-action="reject"
                                                    title="Tolak Pengajuan">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="dk-empty-state submission-filter-empty" hidden>
                <div class="dk-empty-icon">
                    <i class="fas fa-magnifying-glass"></i>
                </div>
                <p>Tidak ada pengajuan yang cocok dengan pencarian atau filter.</p>
            </div>
        </div>

        {{-- SECTION 2: RIWAYAT REVIEW TERBARU (DATA TABLE WITH TABS) --}}
        <div class="dk-card submission-section" x-data="{ activeTab: '{{ request('history_tab', 'baru') }}' }">
            <div class="dk-card-header">
                <div class="dk-card-title">
                    <span>Riwayat Pengajuan Kerja Sama</span>
                    <small>Data Riwayat Validasi Pimpinan</small>
                </div>
            </div>

            {{-- TAB NAVIGATION --}}
            <div class="submission-tab-nav">
                <button type="button" class="submission-tab-btn" :class="{ 'is-active': activeTab === 'baru' }" @click="activeTab = 'baru'">
                    <i class="fas fa-history"></i>
                    <span>Kerja Sama Baru</span>
                    <span class="submission-tab-count">{{ $reviewedBaru->count() }}</span>
                </button>
                <button type="button" class="submission-tab-btn perpanjangan" :class="{ 'is-active': activeTab === 'perpanjangan' }" @click="activeTab = 'perpanjangan'">
                    <i class="fas fa-sync-alt"></i>
                    <span>Perpanjangan Kerja Sama</span>
                    <span class="submission-tab-count perpanjangan">{{ $reviewedPerpanjangan->count() }}</span>
                </button>
            </div>

            {{-- TAB CONTENT 1: KERJA SAMA BARU (HISTORY) --}}
            <div x-show="activeTab === 'baru'" class="submission-tab-content">
                @if ($reviewedBaru->isEmpty())
                    <div class="dk-empty-state">
                        <div class="dk-empty-icon">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>
                        <p>Belum ada riwayat review pengajuan kerja sama baru.</p>
                    </div>
                @else
                    <div class="table-wrap um-table-wrap dk-table-wrap">
                        <table class="um-table dk-table submission-data-table">
                            <thead>
                                <tr>
                                    <th class="um-th um-th-num">#</th>
                                    <th class="um-th">Kode &amp; Status</th>
                                    <th class="um-th">Nama Mitra</th>
                                    <th class="um-th">Judul Pengajuan</th>
                                    <th class="um-th">Reviewer</th>
                                    <th class="um-th">Diproses</th>
                                    <th class="um-th um-th-aksi text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviewedBaru as $submission)
                                    @php
                                        $statusClass = match ($submission->status) {
                                            'disetujui' => 'approved',
                                            'ditolak' => 'rejected',
                                            default => 'pending',
                                        };
                                    @endphp
                                    <tr class="um-row dk-row submission-history-row" data-submission-row
                                        data-id="{{ $submission->id }}" data-category="{{ strtolower($submission->kategori) }}"
                                        data-mitra-id="{{ $submission->mitra_id }}"
                                        data-is-perpanjangan="{{ !empty($submission->doc_number) ? '1' : '0' }}"
                                        data-jenis-dokumen="{{ $submission->jenis ?: '-' }}"
                                        data-doc-number="{{ $submission->doc_number ?: '-' }}"
                                        data-start-date="{{ $submission->start_date ? \Carbon\Carbon::parse($submission->start_date)->format('d M Y') : '-' }}"
                                        data-end-date="{{ $submission->end_date ? \Carbon\Carbon::parse($submission->end_date)->format('d M Y') : '-' }}"
                                        data-file-surat="{{ $submission->file_surat ? asset('storage/' . $submission->file_surat) : '' }}"
                                        data-mitra-name="{{ $submission->nama_mitra }}" data-mitra-email="{{ $submission->email }}"
                                        data-mitra-phone="{{ $submission->telp }}"
                                        data-submission-code="{{ $submission->kode_pengajuan }}"
                                        data-submission-title="{{ $submission->judul_pengajuan }}"
                                        data-klasifikasi="{{ $submission->klasifikasi?->nama ?? '-' }}"
                                        data-kategori="{{ ucfirst($submission->kategori) }}"
                                        data-negara="{{ $submission->negara ?: '-' }}" data-alamat="{{ $submission->alamat }}"
                                        data-website="{{ $submission->website }}"
                                        data-penandatangan-nama="{{ $submission->nama_penandatangan }}"
                                        data-penandatangan-jabatan="{{ $submission->jabatan_penandatangan ?: '-' }}"
                                        data-pj-nama="{{ $submission->nama_penanggung_jawab ?: '-' }}"
                                        data-pj-jabatan="{{ $submission->jabatan_penanggung_jawab ?: '-' }}"
                                        data-tujuan="{{ $submission->tujuan_pengajuan }}"
                                        data-ruang-lingkup="{{ $submission->ruang_lingkup ?: '-' }}"
                                        data-pesan-tambahan="{{ $submission->pesan_tambahan ?: '' }}"
                                        data-catatan-pimpinan="{{ $submission->catatan_pimpinan ?: '' }}"
                                        data-status="{{ $submission->status }}"
                                        data-status-label="{{ $submission->status_label }}"
                                        data-status-class="{{ $statusClass }}"
                                        data-review-url="{{ route('pimpinan.pengajuan_mitra.review', $submission->id) }}">
                                        <td class="um-td um-td-num">
                                            <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-code-cell">
                                                <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                                                <span
                                                    class="submission-status {{ $statusClass }}">{{ $submission->status_label }}</span>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <strong class="sub-mitra-name">{{ $submission->nama_mitra }}</strong>
                                        </td>
                                        <td class="um-td">
                                            <span class="sub-title-cell"
                                                title="{{ $submission->judul_pengajuan }}">{{ $submission->judul_pengajuan }}</span>
                                        </td>
                                        <td class="um-td">
                                            <small class="text-sub">{{ $submission->reviewer?->name ?? 'Pimpinan' }}</small>
                                        </td>
                                        <td class="um-td">
                                            <small class="sub-date-cell">
                                                {{ $submission->reviewed_at ? $submission->reviewed_at->format('d M Y H:i') : '-' }}
                                            </small>
                                        </td>
                                        <td class="um-td um-td-aksi text-center">
                                            <div class="sub-action-group">
                                                <button type="button" class="sub-action-btn btn-detail" data-action="detail-history"
                                                    title="Lihat Detail History">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="sub-action-btn btn-send-notif" data-action="send-notif-history"
                                                    title="Kirim Notifikasi Email &amp; WhatsApp">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($reviewedBaru->hasPages())
                        <div class="dk-pagination-footer">
                            <div class="dk-pagination-info">
                                Menampilkan <strong>{{ $reviewedBaru->firstItem() ?? 0 }}</strong> - <strong>{{ $reviewedBaru->lastItem() ?? 0 }}</strong> dari <strong>{{ $reviewedBaru->total() }}</strong> riwayat pengajuan baru
                            </div>
                            <div class="dk-pagination-links">
                                {{ $reviewedBaru->appends(['history_tab' => 'baru', 'perp_page' => request('perp_page')])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- TAB CONTENT 2: PERPANJANGAN KERJA SAMA (HISTORY) --}}
            <div x-show="activeTab === 'perpanjangan'" class="submission-tab-content" x-cloak>
                @if ($reviewedPerpanjangan->isEmpty())
                    <div class="dk-empty-state">
                        <div class="dk-empty-icon is-perpanjangan">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>
                        <p>Belum ada riwayat review pengajuan perpanjangan kerja sama.</p>
                    </div>
                @else
                    <div class="table-wrap um-table-wrap dk-table-wrap">
                        <table class="um-table dk-table submission-data-table">
                            <thead>
                                <tr>
                                    <th class="um-th um-th-num">#</th>
                                    <th class="um-th">Kode &amp; Status</th>
                                    <th class="um-th">Nama Mitra</th>
                                    <th class="um-th">Judul Pengajuan</th>
                                    <th class="um-th">Reviewer</th>
                                    <th class="um-th">Diproses</th>
                                    <th class="um-th um-th-aksi text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviewedPerpanjangan as $submission)
                                    @php
                                        $statusClass = match ($submission->status) {
                                            'disetujui' => 'approved',
                                            'ditolak' => 'rejected',
                                            default => 'pending',
                                        };
                                    @endphp
                                    <tr class="um-row dk-row submission-history-row" data-submission-row
                                        data-id="{{ $submission->id }}" data-category="{{ strtolower($submission->kategori) }}"
                                        data-mitra-id="{{ $submission->mitra_id }}"
                                        data-is-perpanjangan="{{ !empty($submission->doc_number) ? '1' : '0' }}"
                                        data-jenis-dokumen="{{ $submission->jenis ?: '-' }}"
                                        data-doc-number="{{ $submission->doc_number ?: '-' }}"
                                        data-start-date="{{ $submission->start_date ? \Carbon\Carbon::parse($submission->start_date)->format('d M Y') : '-' }}"
                                        data-end-date="{{ $submission->end_date ? \Carbon\Carbon::parse($submission->end_date)->format('d M Y') : '-' }}"
                                        data-file-surat="{{ $submission->file_surat ? asset('storage/' . $submission->file_surat) : '' }}"
                                        data-mitra-name="{{ $submission->nama_mitra }}" data-mitra-email="{{ $submission->email }}"
                                        data-mitra-phone="{{ $submission->telp }}"
                                        data-submission-code="{{ $submission->kode_pengajuan }}"
                                        data-submission-title="{{ $submission->judul_pengajuan }}"
                                        data-klasifikasi="{{ $submission->klasifikasi?->nama ?? '-' }}"
                                        data-kategori="{{ ucfirst($submission->kategori) }}"
                                        data-negara="{{ $submission->negara ?: '-' }}" data-alamat="{{ $submission->alamat }}"
                                        data-website="{{ $submission->website }}"
                                        data-penandatangan-nama="{{ $submission->nama_penandatangan }}"
                                        data-penandatangan-jabatan="{{ $submission->jabatan_penandatangan ?: '-' }}"
                                        data-pj-nama="{{ $submission->nama_penanggung_jawab ?: '-' }}"
                                        data-pj-jabatan="{{ $submission->jabatan_penanggung_jawab ?: '-' }}"
                                        data-tujuan="{{ $submission->tujuan_pengajuan }}"
                                        data-ruang-lingkup="{{ $submission->ruang_lingkup ?: '-' }}"
                                        data-pesan_tambahan="{{ $submission->pesan_tambahan ?: '' }}"
                                        data-catatan-pimpinan="{{ $submission->catatan_pimpinan ?: '' }}"
                                        data-status="{{ $submission->status }}"
                                        data-status-label="{{ $submission->status_label }}"
                                        data-status-class="{{ $statusClass }}"
                                        data-review-url="{{ route('pimpinan.pengajuan_mitra.review', $submission->id) }}">
                                        <td class="um-td um-td-num">
                                            <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="um-td">
                                            <div class="sub-code-cell">
                                                <span class="submission-card-code">{{ $submission->kode_pengajuan }}</span>
                                                <span class="sub-badge-perpanjangan" title="Pengajuan Perpanjangan">
                                                    <i class="fas fa-sync"></i> Perpanjangan
                                                </span>
                                                <span class="submission-status {{ $statusClass }}">{{ $submission->status_label }}</span>
                                            </div>
                                        </td>
                                        <td class="um-td">
                                            <strong class="sub-mitra-name">{{ $submission->nama_mitra }}</strong>
                                        </td>
                                        <td class="um-td">
                                            <span class="sub-title-cell"
                                                title="{{ $submission->judul_pengajuan }}">{{ $submission->judul_pengajuan }}</span>
                                        </td>
                                        <td class="um-td">
                                            <small class="text-sub">{{ $submission->reviewer?->name ?? 'Pimpinan' }}</small>
                                        </td>
                                        <td class="um-td">
                                            <small class="sub-date-cell">
                                                {{ $submission->reviewed_at ? $submission->reviewed_at->format('d M Y H:i') : '-' }}
                                            </small>
                                        </td>
                                        <td class="um-td um-td-aksi text-center">
                                            <div class="sub-action-group">
                                                <button type="button" class="sub-action-btn btn-detail" data-action="detail-history"
                                                    title="Lihat Detail History">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="sub-action-btn btn-send-notif" data-action="send-notif-history"
                                                    title="Kirim Notifikasi Email &amp; WhatsApp">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($reviewedPerpanjangan->hasPages())
                        <div class="dk-pagination-footer">
                            <div class="dk-pagination-info">
                                Menampilkan <strong>{{ $reviewedPerpanjangan->firstItem() ?? 0 }}</strong> - <strong>{{ $reviewedPerpanjangan->lastItem() ?? 0 }}</strong> dari <strong>{{ $reviewedPerpanjangan->total() }}</strong> riwayat perpanjangan
                            </div>
                            <div class="dk-pagination-links">
                                {{ $reviewedPerpanjangan->appends(['history_tab' => 'perpanjangan', 'baru_page' => request('baru_page')])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>

    {{-- MODAL DETAIL: KERJA SAMA BARU & PERPANJANGAN --}}
    @include('auth.layout.pimpinan.modal-pengajuan.modal-kerjasama-baru')
    @include('auth.layout.pimpinan.modal-pengajuan.modal-perpanjangan-kerjasama')

    {{-- MODAL 2: KONFIRMASI NOTIFIKASI EMAIL & WHATSAPP --}}
    <div id="notifConfirmModal" class="notif-modal-overlay" hidden>
        <div class="notif-modal" role="dialog" aria-modal="true" aria-labelledby="notifModalTitle">
            <div class="notif-modal-header" id="notifModalHeader">
                <div class="notif-modal-icon" id="notifModalIcon">
                    <i class="fas fa-circle-check"></i>
                </div>
                <h3 id="notifModalTitle">Konfirmasi Keputusan</h3>
                <p id="notifModalSubtitle" class="notif-modal-subtitle"></p>
            </div>

            <div class="notif-modal-body">
                {{-- Info penerima --}}
                <div class="notif-recipient-info">
                    <div id="notifPerpanjanganBadgeRow" class="notif-recipient-row" hidden>
                        <i class="fas fa-sync-alt"></i>
                        <span class="sub-badge-perpanjangan"><i class="fas fa-sync"></i> Perpanjangan Kerja Sama</span>
                    </div>
                    <div class="notif-recipient-row">
                        <i class="fas fa-building"></i>
                        <span id="notifMitraName">—</span>
                    </div>
                    <div class="notif-recipient-row">
                        <i class="fas fa-envelope"></i>
                        <span id="notifMitraEmail">—</span>
                    </div>
                    <div class="notif-recipient-row">
                        <i class="fab fa-whatsapp"></i>
                        <span id="notifMitraPhone">—</span>
                    </div>
                </div>

                {{-- Channel toggles --}}
                <div class="notif-channels">
                    <span class="notif-channels-label">Kirim notifikasi via:</span>
                    <div class="notif-channel-toggles">
                        <label class="notif-toggle" for="notifToggleEmail">
                            <input type="checkbox" id="notifToggleEmail" checked>
                            <span class="notif-toggle-track">
                                <span class="notif-toggle-thumb"></span>
                            </span>
                            <span class="notif-toggle-label"><i class="fas fa-envelope"></i> Email</span>
                        </label>
                        <label class="notif-toggle" for="notifToggleWa">
                            <input type="checkbox" id="notifToggleWa" checked>
                            <span class="notif-toggle-track">
                                <span class="notif-toggle-thumb"></span>
                            </span>
                            <span class="notif-toggle-label"><i class="fab fa-whatsapp"></i> WhatsApp</span>
                        </label>
                    </div>
                </div>

                {{-- Tab switch email / whatsapp --}}
                <div class="notif-preview-tabs">
                    <button type="button" class="notif-tab active" data-notif-tab="email">
                        <i class="fas fa-envelope"></i> Preview Email
                    </button>
                    <button type="button" class="notif-tab" data-notif-tab="whatsapp">
                        <i class="fab fa-whatsapp"></i> Preview WhatsApp
                    </button>
                </div>

                <div class="notif-preview-area" id="notifPreviewEmail">
                    <label for="notifMessageEmail" class="notif-preview-label">Isi pesan email (dapat diedit):</label>
                    <textarea id="notifMessageEmail" class="notif-preview-textarea" rows="6"></textarea>
                </div>

                <div class="notif-preview-area" id="notifPreviewWa" hidden>
                    <label for="notifMessageWa" class="notif-preview-label">Isi pesan WhatsApp (dapat diedit):</label>
                    <textarea id="notifMessageWa" class="notif-preview-textarea" rows="6"></textarea>
                </div>
            </div>

            <div class="notif-modal-footer">
                <button type="button" class="notif-btn-cancel" id="notifBtnCancel">
                    <i class="fas fa-xmark"></i> Batal
                </button>
                <button type="button" class="notif-btn-confirm" id="notifBtnConfirm">
                    <i class="fas fa-paper-plane"></i>
                    <span id="notifBtnConfirmText">Konfirmasi &amp; Kirim</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Form tersembunyi untuk submit review secara dinamis --}}
    <form id="submissionHiddenForm" action="" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="keputusan" id="hiddenKeputusan">
        <input type="hidden" name="catatan_pimpinan" id="hiddenCatatanPimpinan">
        <input type="hidden" name="send_email" id="hiddenSendEmail">
        <input type="hidden" name="send_whatsapp" id="hiddenSendWa">
        <input type="hidden" name="custom_message_email" id="hiddenCustomEmail">
        <input type="hidden" name="custom_message_whatsapp" id="hiddenCustomWa">
    </form>
</main>

<script src="{{ asset('js/auth/pimpinan/pmitra.js') }}"></script>
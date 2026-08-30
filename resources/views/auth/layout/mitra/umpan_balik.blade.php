{{-- ══════════════════════════════════════════════════════════════════════════════
     PORTAL MITRA - UMPAN BALIK & EVALUASI KERJA SAMA (CSAT / UC26)
     File: resources/views/auth/layout/mitra/umpan_balik.blade.php
     Ref: UC26 | Flowchart Section 7.4 | DFD Process 6.6 | ERD Section 4.6
     ══════════════════════════════════════════════════════════════════════════════ --}}

@php
    $user = auth()->user();
    $mitra = $user->mitra;
    $mitraName = $mitra->nama_mitra ?? ($user->name ?? 'Mitra Eksternal');

    $cooperations = $cooperations ?? collect();
    $totalCooperations = $totalCooperations ?? $cooperations->count();
    $filledCooperations = $filledCooperations ?? $cooperations->filter(fn($c) => $c->evaluasis->where('tipe_evaluasi', 'Umpan_Balik_Mitra')->count() > 0)->count();
    $pendingCooperations = $pendingCooperations ?? max(0, $totalCooperations - $filledCooperations);

    $allScores = $cooperations->flatMap(fn($c) => $c->evaluasis->where('tipe_evaluasi', 'Umpan_Balik_Mitra'))->pluck('score')->filter();
    $avgScore = $allScores->count() > 0 ? round($allScores->avg(), 1) : 0;
    $csatPercent = $avgScore > 0 ? round(($avgScore / 5) * 100) : 0;

    $availableJenis = $availableJenis ?? $cooperations->pluck('jenis')->filter()->unique()->values();
    $availableYears = $availableYears ?? $cooperations->map(fn($c) => $c->start_date ? $c->start_date->format('Y') : ($c->created_at ? $c->created_at->format('Y') : null))->filter()->unique()->sortDesc()->values();
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/auth/layout/mitra/umpan_balik.css') }}" data-turbo-track="reload">

<main id="mainContent" class="dk-page" x-data="mitraUmpanBalikApp()" x-cloak>

    {{-- ═══ TOPBAR / HERO SECTION ═══ --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Umpan Balik &amp; Evaluasi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-star-half-stroke"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Umpan Balik &amp; Evaluasi Kemitraan (CSAT)</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Sampaikan penilaian kepuasan, evaluasi pelaksanaan kerja sama, dan saran konstruktif untuk meningkatkan mutu layanan &amp; kolaborasi Politeknik Negeri Manado bersama instansi <strong>{{ $mitraName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
        <div class="ud-hero-actions" style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
            <button type="button" @click="openCreateFeedbackModal()" class="dk-primary-btn">
                <i class="fas fa-comment-medical"></i>
                <span>Beri Umpan Balik Baru</span>
            </button>
        </div>
    </section>

    {{-- ═══ 4 KARTU STATISTIK (KPI CARDS) ═══ --}}
    <section class="dk-stats-grid" aria-label="Ringkasan evaluasi kepuasan mitra">
        {{-- Card 1: Total Dokumen Kerjasama --}}
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-folder-tree"></i></div>
            <div>
                <span class="dk-stat-label">Total Dokumen Kerjasama</span>
                <strong>{{ number_format($totalCooperations) }}</strong>
            </div>
        </div>

        {{-- Card 2: Umpan Balik Terisi --}}
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Umpan Balik Terkirim</span>
                <strong>{{ number_format($filledCooperations) }}</strong>
            </div>
        </div>

        {{-- Card 3: Menunggu Umpan Balik --}}
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-clock-rotate-left"></i></div>
            <div>
                <span class="dk-stat-label">Menunggu Umpan Balik</span>
                <strong>{{ number_format($pendingCooperations) }}</strong>
            </div>
        </div>

        {{-- Card 4: Indeks Kepuasan Mitra (CSAT Rating) --}}
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #f59e0b;">
            <div class="dk-stat-icon" style="color: #f59e0b; background: rgba(245,158,11,0.12);">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <span class="dk-stat-label">Indeks Kepuasan (CSAT)</span>
                <strong>{{ $avgScore > 0 ? number_format($avgScore, 1) . ' / 5.0' : '-' }}</strong>
            </div>
        </div>
    </section>

    {{-- ═══ FILTER TOOLBAR ACCORDION (ALPINE.JS) ═══ --}}
    <div class="report-filter-container" x-data="{ showFilters: false }">
        <div class="rfc-header" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;"
            @click="showFilters = !showFilters">
            <div class="rfc-title-area">
                <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                <div class="rfc-text">
                    <h3>Filter Dokumen &amp; Umpan Balik</h3>
                    <p>Saring dokumen kerja sama berdasarkan jenis kesepakatan, status pengisian survei, periode tahun, atau pencarian nama</p>
                </div>
            </div>
            <div style="color: var(--text-sub); font-size: 16px; transition: transform 0.3s;"
                :style="showFilters ? 'transform: rotate(180deg)' : 'transform: rotate(0)'">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <div class="rfc-body" x-show="showFilters" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-4">
            
            <div class="rfc-grid">
                {{-- 1. Filter Jenis Dokumen --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Jenis Dokumen' },
                        @foreach($availableJenis as $jns)
                            { id: '{{ strtolower($jns) }}', label: '{{ strtoupper($jns) }}' },
                        @endforeach
                    ],
                    get selectedLabel() {
                        const item = this.items.find(i => i.id === jenisFilter);
                        return item ? item.label : 'Semua Jenis Dokumen';
                    }
                }">
                    <label>Jenis Dokumen</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-file-contract" style="color: #9ca3af; font-size: 13px;"></i>
                                <span x-text="selectedLabel"></span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                :style="open ? 'transform: rotate(180deg)' : ''"></i>
                        </div>
                        <div class="ad-menu" x-show="open" x-transition x-cloak>
                            <template x-for="item in items" :key="item.id">
                                <div class="ad-item" :class="{ 'selected': jenisFilter === item.id }"
                                    @click="jenisFilter = item.id; open = false" x-text="item.label"></div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 2. Filter Status Pengisian Umpan Balik --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Status Umpan Balik' },
                        { id: 'sudah', label: 'Sudah Diisi (Terkirim)' },
                        { id: 'belum', label: 'Belum Diisi (Menunggu)' }
                    ],
                    get selectedLabel() {
                        const item = this.items.find(i => i.id === statusFilter);
                        return item ? item.label : 'Semua Status Umpan Balik';
                    }
                }">
                    <label>Status Survei Kepuasan</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-star-half-stroke" style="color: #9ca3af; font-size: 13px;"></i>
                                <span x-text="selectedLabel"></span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                :style="open ? 'transform: rotate(180deg)' : ''"></i>
                        </div>
                        <div class="ad-menu" x-show="open" x-transition x-cloak>
                            <template x-for="item in items" :key="item.id">
                                <div class="ad-item" :class="{ 'selected': statusFilter === item.id }"
                                    @click="statusFilter = item.id; open = false" x-text="item.label"></div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 3. Filter Periode Tahun --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Periode Tahun' },
                        @foreach($availableYears as $year)
                            { id: '{{ $year }}', label: 'Tahun {{ $year }}' },
                        @endforeach
                    ],
                    get selectedLabel() {
                        const item = this.items.find(i => i.id === String(tahunFilter));
                        return item ? item.label : 'Semua Periode Tahun';
                    }
                }">
                    <label>Tahun Kesepakatan</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-calendar-alt" style="color: #9ca3af; font-size: 13px;"></i>
                                <span x-text="selectedLabel"></span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                :style="open ? 'transform: rotate(180deg)' : ''"></i>
                        </div>
                        <div class="ad-menu" x-show="open" x-transition x-cloak>
                            <template x-for="item in items" :key="item.id">
                                <div class="ad-item" :class="{ 'selected': String(tahunFilter) === String(item.id) }"
                                    @click="tahunFilter = item.id; open = false" x-text="item.label"></div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 4. Pencarian Teks --}}
                <div class="rfc-group">
                    <label>Cari Kerjasama</label>
                    <div class="rfc-input-wrap">
                        <i class="fas fa-search rfc-input-icon"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari judul kerjasama / nomor PKS..."
                            class="rfc-input">
                    </div>
                </div>
            </div>

            <div class="rfc-footer">
                <button type="button" @click="resetFilters()" class="rfc-btn">
                    <i class="fas fa-rotate-left"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ CARD TABEL DOKUMEN & UMPAN BALIK ═══ --}}
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-comments"></i></span>
                <span>
                    <strong>Daftar Dokumen Kerjasama &amp; Status Umpan Balik</strong>
                    <small id="cooperationCount">{{ $cooperations->count() }} dokumen tercatat</small>
                </span>
            </div>

            <div class="mn-table-controls" style="display: flex; gap: 16px; align-items: center; margin-left: auto;">
                <div class="mn-table-entries"
                    style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-sub);">
                    <span>Tampilkan</span>
                    <div class="mn-entry-dropdown" @click.outside="perPageOpen = false" style="position: relative;">
                        <button type="button" class="mn-entry-trigger" @click="perPageOpen = !perPageOpen"
                            style="display: flex; align-items: center; justify-content: space-between; min-width: 64px; padding: 8px 12px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 10px; cursor: pointer; color: var(--text); font-weight: 600; font-size: 13px; transition: all 0.2s;">
                            <span x-text="perPage">10</span>
                            <i class="fas fa-chevron-down"
                                style="font-size: 10px; margin-left: 8px; color: var(--text-sub);"></i>
                        </button>
                        <div class="mn-entry-menu" x-show="perPageOpen" x-cloak x-transition.opacity
                            style="position: absolute; top: calc(100% + 4px); left: 0; width: 100%; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 50; overflow: hidden; display: flex; flex-direction: column;">
                            <template x-for="option in perPageOptions" :key="option">
                                <button type="button" class="mn-entry-option" @click="setPerPage(option)"
                                    style="width: 100%; padding: 8px 12px; text-align: left; background: transparent; border: none; cursor: pointer; font-size: 13px; color: var(--text); transition: 0.2s; font-weight: 500;"
                                    onmouseover="this.style.background='var(--surface2)'"
                                    onmouseout="this.style.background='transparent'">
                                    <span x-text="option"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <span>data</span>
                </div>
            </div>
        </div>

        {{-- Table Body --}}
        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 380px; min-width: 280px;">Dokumen Kerjasama</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berlaku</th>
                            <th class="um-th" style="min-width: 180px;">Indeks Kepuasan (CSAT)</th>
                            <th class="um-th">Status Evaluasi</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody x-ref="rows">
                        @forelse($cooperations as $index => $c)
                            @php
                                $evaluasi = $c->evaluasis->where('tipe_evaluasi', 'Umpan_Balik_Mitra')->first();
                                $hasFeedback = !empty($evaluasi);
                                $statusFeedbackStr = $hasFeedback ? 'sudah' : 'belum';

                                $docYear = $c->start_date ? $c->start_date->format('Y') : ($c->created_at ? $c->created_at->format('Y') : '');
                                $jenisBadgeClass = match(strtoupper($c->jenis ?? 'MOU')) {
                                    'MOU' => 'dk-badge-indigo',
                                    'MOA' => 'dk-badge-emerald',
                                    'IA' => 'dk-badge-sky',
                                    default => 'dk-badge-amber'
                                };

                                $startDate = $c->start_date ? $c->start_date->format('d/m/Y') : '-';
                                $endDate = $c->end_date ? $c->end_date->format('d/m/Y') : '-';
                                $ratingScore = $evaluasi ? (float) $evaluasi->score : 0;
                            @endphp
                            <tr data-row="true"
                                class="um-row dk-row"
                                data-judul="{{ strtolower($c->judul ?? '') }}"
                                data-nomor="{{ strtolower($c->doc_number ?? '') }}"
                                data-jenis="{{ strtolower($c->jenis ?? '') }}"
                                data-status="{{ $statusFeedbackStr }}"
                                data-tahun="{{ $docYear }}"
                                x-show="isRowVisible($el)">

                                {{-- Col 1: Index Number --}}
                                <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                    <span class="um-num dk-num" x-text="rowNumber($el)">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                {{-- Col 2: Dokumen Kerjasama --}}
                                <td class="um-td dk-title-cell" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-doc-cell">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                            <span class="dk-stat-badge {{ $jenisBadgeClass }}">
                                                {{ strtoupper($c->jenis ?? 'MOU') }}
                                            </span>
                                            <span class="dk-doc-number">#{{ $c->doc_number ?: 'Tanpa Nomor' }}</span>
                                        </div>
                                        <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5;">{{ $c->judul }}</span>
                                        @if($c->ruang_lingkup)
                                            <span class="dk-doc-kind" style="font-size: 11.5px; color: var(--text-sub); display: block; margin-top: 2px;">
                                                Lingkup: {{ \Illuminate\Support\Str::limit($c->ruang_lingkup, 70) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Col 3: Masa Berlaku --}}
                                <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-date-range-compact">
                                        <span class="date-val">{{ $startDate }}</span>
                                        <span class="date-sep">s/d</span>
                                        <span class="date-val">{{ $endDate }}</span>
                                    </div>
                                    <small style="display: block; font-size: 11px; color: var(--text-sub); margin-top: 4px;">
                                        Status: {{ $c->status_dokumen ?: 'Aktif' }}
                                    </small>
                                </td>

                                {{-- Col 4: Indeks Kepuasan & Rating Bintang --}}
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    @if($hasFeedback)
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <div class="ub-stars-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= round($ratingScore) ? '' : 'empty' }}"></i>
                                                @endfor
                                                <span class="ub-csat-pill" style="margin-left: 6px;">{{ number_format($ratingScore, 1) }}</span>
                                            </div>
                                            <span style="font-size: 11.5px; font-weight: 700; color: #059669;">
                                                Mutu: {{ $evaluasi->kesimpulan ?: 'Baik' }}
                                            </span>
                                        </div>
                                    @else
                                        <span style="font-size: 12px; color: var(--text-sub); font-style: italic;">
                                            Belum ada survei kepuasan
                                        </span>
                                    @endif
                                </td>

                                {{-- Col 5: Status Evaluasi --}}
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    @if($hasFeedback)
                                        <span class="dk-status dk-status-active">
                                            <i class="fas fa-circle-check"></i>
                                            Sudah Mengisi
                                        </span>
                                    @else
                                        <span class="dk-status dk-status-warning">
                                            <i class="fas fa-clock"></i>
                                            Menunggu Umpan Balik
                                        </span>
                                    @endif
                                </td>

                                {{-- Col 6: Aksi --}}
                                <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                    <div class="um-actions dk-actions-compact">
                                        @if(!$hasFeedback)
                                            {{-- Action: Beri Umpan Balik Baru (Hijau) --}}
                                            <button type="button"
                                                @click="openCreateFeedbackModal({
                                                    id: '{{ $c->id }}',
                                                    judul: '{{ addslashes($c->judul) }}',
                                                    nomor: '{{ addslashes($c->doc_number ?? '-') }}',
                                                    jenis: '{{ addslashes($c->jenis ?? 'MoU') }}'
                                                })"
                                                class="dk-action-btn"
                                                title="Beri Umpan Balik Kepuasan"
                                                style="color: #059669; background: rgba(5, 150, 105, 0.12); border: 1px solid rgba(5, 150, 105, 0.25); cursor: pointer;">
                                                <i class="fas fa-star-half-stroke"></i>
                                            </button>
                                        @else
                                            {{-- Action: Ubah Umpan Balik (Kuning Amber) --}}
                                            <button type="button"
                                                @click="openEditFeedbackModal({
                                                    evaluasi_id: '{{ $evaluasi->id }}',
                                                    cooperation_id: '{{ $c->id }}',
                                                    judul: '{{ addslashes($c->judul) }}',
                                                    nomor: '{{ addslashes($c->doc_number ?? '-') }}',
                                                    jenis: '{{ addslashes($c->jenis ?? 'MoU') }}',
                                                    kepuasan: {{ $evaluasi->kepuasan ?? 5 }},
                                                    sesuai_rencana: {{ $evaluasi->sesuai_rencana ?? 5 }},
                                                    kualitas: {{ $evaluasi->kualitas ?? 5 }},
                                                    keterlibatan: {{ $evaluasi->keterlibatan ?? 5 }},
                                                    efisiensi: {{ $evaluasi->efisiensi ?? 5 }},
                                                    ringkasan: '{{ addslashes($evaluasi->ringkasan ?? '') }}',
                                                    kendala: '{{ addslashes($evaluasi->kendala ?? '') }}',
                                                    rekomendasi: '{{ addslashes($evaluasi->rekomendasi ?? '') }}',
                                                    kesimpulan: '{{ addslashes($evaluasi->kesimpulan ?? 'Baik') }}',
                                                    tindak_lanjut: '{{ addslashes($evaluasi->tindak_lanjut ?? '') }}'
                                                })"
                                                class="dk-action-btn edit"
                                                title="Ubah Umpan Balik"
                                                style="color: #d97706; background: rgba(217, 119, 6, 0.12); border: 1px solid rgba(217, 119, 6, 0.25); cursor: pointer;">
                                                <i class="fas fa-pen-to-square"></i>
                                            </button>

                                            {{-- Action: Detail Umpan Balik (Biru Indigo) --}}
                                            <button type="button"
                                                @click="openDetailModal({
                                                    judul: '{{ addslashes($c->judul) }}',
                                                    nomor: '{{ addslashes($c->doc_number ?? '-') }}',
                                                    jenis: '{{ addslashes($c->jenis ?? 'MoU') }}',
                                                    periode: '{{ addslashes($startDate . ' s/d ' . $endDate) }}',
                                                    score: '{{ number_format($ratingScore, 1) }}',
                                                    kepuasan: {{ $evaluasi->kepuasan ?? 5 }},
                                                    sesuai_rencana: {{ $evaluasi->sesuai_rencana ?? 5 }},
                                                    kualitas: {{ $evaluasi->kualitas ?? 5 }},
                                                    keterlibatan: {{ $evaluasi->keterlibatan ?? 5 }},
                                                    efisiensi: {{ $evaluasi->efisiensi ?? 5 }},
                                                    ringkasan: '{{ addslashes($evaluasi->ringkasan ?? 'Tidak ada ulasan.') }}',
                                                    kendala: '{{ addslashes($evaluasi->kendala ?? 'Tidak ada kendala.') }}',
                                                    rekomendasi: '{{ addslashes($evaluasi->rekomendasi ?? 'Tidak ada saran.') }}',
                                                    kesimpulan: '{{ addslashes($evaluasi->kesimpulan ?? 'Baik') }}',
                                                    tindak_lanjut: '{{ addslashes($evaluasi->tindak_lanjut ?? 'Bersedia Melanjutkan Kerjasama') }}',
                                                    updated_at: '{{ $evaluasi->updated_at ? $evaluasi->updated_at->translatedFormat('d F Y, H:i') : '-' }}'
                                                })"
                                                class="dk-action-btn view"
                                                title="Lihat Detail Umpan Balik"
                                                style="color: #4f46e5; background: rgba(79, 70, 229, 0.1); border: 1px solid rgba(79, 70, 229, 0.2); cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-folder-open"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada dokumen kerjasama</p>
                                        <p class="um-empty-sub">Saat ini instansi Anda belum memiliki dokumen kerjasama tercatat di sistem.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Pagination Footer --}}
            <div class="table-pagination-controls" x-show="totalFiltered > 0" x-cloak>
                <div class="pagination-info">
                    Menampilkan <strong x-text="startRange">0</strong> sampai <strong x-text="endRange">0</strong> dari
                    <strong x-text="totalFiltered">{{ $cooperations->count() }}</strong> data
                </div>

                <div class="pagination-buttons" aria-label="Navigasi Halaman">
                    <button type="button" class="pag-btn" @click="goToPage(1)" :disabled="currentPage === 1" title="Halaman pertama">
                        <i class="fas fa-angles-left"></i>
                    </button>
                    <button type="button" class="pag-btn" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" title="Halaman sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template x-for="page in pageNumbers()" :key="page">
                        <button type="button" class="pag-btn" :class="{ 'active': page === currentPage }" @click="goToPage(page)" x-text="page"></button>
                    </template>
                    <button type="button" class="pag-btn" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages" title="Halaman berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button type="button" class="pag-btn" @click="goToPage(totalPages)" :disabled="currentPage === totalPages" title="Halaman terakhir">
                        <i class="fas fa-angles-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ 1. MODAL FORM KUESIONER SURVEI KEPUASAN MITRA (ALPINE.JS) ═══ --}}
    <template x-if="feedbackModalOpen">
        <div class="modal-overlay"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
            @click.self="feedbackModalOpen = false" x-cloak>
            <div class="modal-card"
                style="background: var(--surface); border-radius: 24px; width: 100%; max-width: 680px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35); border: 1px solid var(--border);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                
                {{-- Modal Header --}}
                <div style="padding: 20px 28px; border-bottom: 1px solid var(--border); background: var(--surface2); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245,158,11,0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="fas fa-star-half-stroke"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;"
                                x-text="isEditMode ? 'Perbarui Umpan Balik Kemitraan' : 'Kuesioner Survei Kepuasan Mitra (CSAT)'"></h3>
                            <p style="margin: 3px 0 0 0; font-size: 12px; color: var(--text-sub);">Evaluasi pelaksanaan kerjasama bersama Politeknik Negeri Manado</p>
                        </div>
                    </div>
                    <button type="button" @click="feedbackModalOpen = false"
                        style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 16px; transition: 0.2s; border-radius: 8px;"
                        onmouseover="this.style.color='#ef4444'; this.style.background='rgba(239,68,68,0.1)'"
                        onmouseout="this.style.color='var(--text-sub)'; this.style.background='transparent'"
                        title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Form Survey --}}
                <form :action="isEditMode ? ('/mitra/umpan-balik/' + formData.id) : '{{ route('mitra.umpan_balik.store') }}'"
                    method="POST" id="feedbackForm" @submit.prevent="submitFeedback($event)"
                    style="display: flex; flex-direction: column; overflow: hidden; flex: 1; min-height: 0; background: var(--surface);">
                    @csrf
                    <template x-if="isEditMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div style="display: flex; flex-direction: column; gap: 18px; padding: 24px; overflow-y: auto; flex: 1; min-height: 0; background: var(--surface);">
                        
                        {{-- 1. Pilihan Dokumen Kerjasama (Jika Baru) / Info Banner (Jika Edit/Row Click) --}}
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                Dokumen Kerjasama yang Dievaluasi <span style="color: #ef4444;">*</span>
                            </label>
                            
                            <template x-if="!formData.cooperation_id || (!isEditMode && !formData.cooperation_title)">
                                <select name="cooperation_id" class="rfc-input" required
                                    @change="onCooperationSelectChange($event, {{ json_encode($cooperations) }})"
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface2); color: var(--text); font-size: 13px; font-weight: 600;">
                                    <option value="">-- Pilih Dokumen Kerjasama Mitra --</option>
                                    @foreach($cooperations as $coop)
                                        <option value="{{ $coop->id }}">
                                            [{{ strtoupper($coop->jenis) }}] {{ $coop->judul }} (#{{ $coop->doc_number ?: 'Tanpa No' }})
                                        </option>
                                    @endforeach
                                </select>
                            </template>

                            <template x-if="formData.cooperation_id && formData.cooperation_title">
                                <div style="padding: 12px 16px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                    <div>
                                        <input type="hidden" name="cooperation_id" :value="formData.cooperation_id">
                                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                                            <span class="dk-stat-badge dk-badge-indigo" x-text="formData.cooperation_jenis"></span>
                                            <span class="dk-doc-number" x-text="'#' + formData.cooperation_number"></span>
                                        </div>
                                        <strong style="color: var(--text); font-size: 13.5px;" x-text="formData.cooperation_title"></strong>
                                    </div>
                                    <template x-if="!isEditMode">
                                        <button type="button" @click="formData.cooperation_id = ''; formData.cooperation_title = ''"
                                            style="font-size: 11.5px; color: #4f46e5; background: transparent; border: none; cursor: pointer; text-decoration: underline; font-weight: 600;">
                                            Ganti Dokumen
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- 2. Rating Bintang Keseluruhan (Overall CSAT) --}}
                        <div class="ub-csat-summary-box">
                            <div>
                                <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #d97706; display: block;">Tingkat Kepuasan Keseluruhan</span>
                                <strong style="font-size: 14px; color: var(--text);" x-text="ratingLabel"></strong>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="hidden" name="kepuasan" :value="formData.kepuasan">
                                <div class="ub-star-rating">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button type="button" class="ub-star-btn"
                                            :class="{ 'active': star <= formData.kepuasan }"
                                            @click="formData.kepuasan = star; formData.sesuai_rencana = star; formData.kualitas = star; formData.keterlibatan = star; formData.efisiensi = star"
                                            :title="star + ' Bintang'">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </template>
                                </div>
                                <span class="ub-csat-pill" style="font-size: 15px; padding: 4px 12px;" x-text="formData.kepuasan + '.0 / 5.0'"></span>
                            </div>
                        </div>

                        {{-- 3. 4 Parameter Aspek Evaluasi Kemitraan (Likert 1-5 Bintang) --}}
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <label style="font-size: 13px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-sliders" style="color: #4f46e5;"></i>
                                Penilaian 4 Aspek Kemitraan (Skala 1 - 5)
                            </label>

                            {{-- Aspek 1: Komunikasi & Responsivitas --}}
                            <div class="ub-aspect-card">
                                <div class="ub-aspect-header">
                                    <div>
                                        <span class="ub-aspect-title">1. Komunikasi &amp; Responsivitas Kampus</span>
                                        <span class="ub-aspect-desc">Kecepatan koordinasi, kejelasan informasi, dan keterbukaan komunikasi tim POLIMDO</span>
                                    </div>
                                    <div class="ub-aspect-stars">
                                        <input type="hidden" name="sesuai_rencana" :value="formData.sesuai_rencana">
                                        <template x-for="s in [1, 2, 3, 4, 5]" :key="s">
                                            <button type="button" class="ub-aspect-star" :class="{ 'active': s <= formData.sesuai_rencana }"
                                                @click="formData.sesuai_rencana = s">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </template>
                                        <span class="dk-doc-number" style="margin-left: 6px;" x-text="formData.sesuai_rencana + '/5'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Aspek 2: Kualitas Mahasiswa & Luaran --}}
                            <div class="ub-aspect-card">
                                <div class="ub-aspect-header">
                                    <div>
                                        <span class="ub-aspect-title">2. Kualitas Mahasiswa &amp; Luaran Program</span>
                                        <span class="ub-aspect-desc">Kompetensi teknis, etika kerja mahasiswa magang/penyerapan, dan relevansi kurikulum</span>
                                    </div>
                                    <div class="ub-aspect-stars">
                                        <input type="hidden" name="kualitas" :value="formData.kualitas">
                                        <template x-for="s in [1, 2, 3, 4, 5]" :key="s">
                                            <button type="button" class="ub-aspect-star" :class="{ 'active': s <= formData.kualitas }"
                                                @click="formData.kualitas = s">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </template>
                                        <span class="dk-doc-number" style="margin-left: 6px;" x-text="formData.kualitas + '/5'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Aspek 3: Kemudahan Administrasi --}}
                            <div class="ub-aspect-card">
                                <div class="ub-aspect-header">
                                    <div>
                                        <span class="ub-aspect-title">3. Kemudahan Administrasi &amp; Tata Kelola</span>
                                        <span class="ub-aspect-desc">Kecepatan legalitas dokumen (MoU/MoA/IA), kemudahan birokrasi, dan kepatuhan SOP</span>
                                    </div>
                                    <div class="ub-aspect-stars">
                                        <input type="hidden" name="keterlibatan" :value="formData.keterlibatan">
                                        <template x-for="s in [1, 2, 3, 4, 5]" :key="s">
                                            <button type="button" class="ub-aspect-star" :class="{ 'active': s <= formData.keterlibatan }"
                                                @click="formData.keterlibatan = s">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </template>
                                        <span class="dk-doc-number" style="margin-left: 6px;" x-text="formData.keterlibatan + '/5'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Aspek 4: Dukungan Teknis & Fasilitas --}}
                            <div class="ub-aspect-card">
                                <div class="ub-aspect-header">
                                    <div>
                                        <span class="ub-aspect-title">4. Dukungan Teknis &amp; Fasilitas Pendukung</span>
                                        <span class="ub-aspect-desc">Peran dosen pembimbing, kesiapan sarana kampus, dan pendampingan proyek bersama</span>
                                    </div>
                                    <div class="ub-aspect-stars">
                                        <input type="hidden" name="efisiensi" :value="formData.efisiensi">
                                        <template x-for="s in [1, 2, 3, 4, 5]" :key="s">
                                            <button type="button" class="ub-aspect-star" :class="{ 'active': s <= formData.efisiensi }"
                                                @click="formData.efisiensi = s">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </template>
                                        <span class="dk-doc-number" style="margin-left: 6px;" x-text="formData.efisiensi + '/5'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Ulasan & Kendala --}}
                        <div style="display: grid; grid-template-columns: 1fr; gap: 14px;">
                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    <i class="fas fa-comment-dots" style="color: #4f46e5; margin-right: 6px;"></i>
                                    Ulasan Umum Kemitraan
                                </label>
                                <textarea name="ringkasan" x-model="formData.ringkasan" class="rfc-input" rows="2"
                                    placeholder="Tuliskan pengalaman positif dan poin apresiasi selama bekerjasama dengan POLIMDO..."
                                    style="height: auto; min-height: 60px; background: var(--surface); color: var(--text);"></textarea>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    <i class="fas fa-triangle-exclamation" style="color: #d97706; margin-right: 6px;"></i>
                                    Kendala yang Dihadapi (Opsional)
                                </label>
                                <textarea name="kendala" x-model="formData.kendala" class="rfc-input" rows="2"
                                    placeholder="Tuliskan hambatan atau kendala teknis/operasional yang dialami (jika ada)..."
                                    style="height: auto; min-height: 50px; background: var(--surface); color: var(--text);"></textarea>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    <i class="fas fa-lightbulb" style="color: #059669; margin-right: 6px;"></i>
                                    Saran &amp; Rekomendasi Peningkatan Mutu untuk POLIMDO
                                </label>
                                <textarea name="rekomendasi" x-model="formData.rekomendasi" class="rfc-input" rows="2"
                                    placeholder="Tuliskan masukan konkret agar kerjasama berikutnya dapat berjalan lebih optimal..."
                                    style="height: auto; min-height: 60px; background: var(--surface); color: var(--text);"></textarea>
                            </div>
                        </div>

                        {{-- 5. Kesimpulan & Kesediaan Perpanjangan --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; padding-top: 10px; border-top: 1px solid var(--border);">
                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    Kesimpulan Kemitraan <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="kesimpulan" x-model="formData.kesimpulan" class="rfc-input" required
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                    <option value="Sangat Baik">Sangat Baik</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Cukup">Cukup</option>
                                    <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    Kesediaan Melanjutkan Kerjasama <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="tindak_lanjut" x-model="formData.tindak_lanjut" class="rfc-input" required
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                    <option value="Sangat Bersedia Melanjutkan Kerjasama">Sangat Bersedia Melanjutkan</option>
                                    <option value="Bersedia Melanjutkan Kerjasama">Bersedia Melanjutkan</option>
                                    <option value="Dipertimbangkan Sesuai Kebutuhan">Dipertimbangkan Sesuai Kebutuhan</option>
                                    <option value="Tidak Melanjutkan Kerjasama">Tidak Melanjutkan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--surface2); border-radius: 0 0 24px 24px; flex-shrink: 0;">
                        <button type="button" class="rfc-btn" @click="feedbackModalOpen = false"
                            style="padding: 9px 18px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer;">
                            Batal
                        </button>
                        <button type="submit" class="dk-primary-btn" :disabled="isSubmitting">
                            <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : (isEditMode ? 'fa-check' : 'fa-paper-plane')"></i>
                            <span x-text="isSubmitting ? 'Menyimpan...' : (isEditMode ? 'Perbarui Umpan Balik' : 'Kirim Umpan Balik')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ═══ 2. MODAL DETAIL UMPAN BALIK MITRA (ALPINE.JS) ═══ --}}
    <template x-if="detailModalOpen">
        <div class="modal-overlay"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
            @click.self="detailModalOpen = false" x-cloak>
            <div class="modal-card"
                style="background: var(--surface); border-radius: 24px; width: 100%; max-width: 600px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35); border: 1px solid var(--border);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                
                {{-- Modal Header --}}
                <div style="padding: 20px 28px; border-bottom: 1px solid var(--border); background: var(--surface2); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">Rekapitulasi Umpan Balik</h3>
                            <p style="margin: 3px 0 0 0; font-size: 12px; color: var(--text-sub);">Hasil evaluasi survei kepuasan mitra</p>
                        </div>
                    </div>
                    <button type="button" @click="detailModalOpen = false"
                        style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 16px; transition: 0.2s; border-radius: 8px;"
                        onmouseover="this.style.color='#ef4444'; this.style.background='rgba(239,68,68,0.1)'"
                        onmouseout="this.style.color='var(--text-sub)'; this.style.background='transparent'"
                        title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div style="display: flex; flex-direction: column; gap: 16px; padding: 24px; overflow-y: auto; flex: 1; min-height: 0; background: var(--surface);">
                    
                    {{-- Header Card Dokumen --}}
                    <div class="dk-entity" style="padding: 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px;">
                        <span class="dk-entity-icon dk-entity-indigo" style="width: 44px; height: 44px; font-size: 16px; font-weight: 800;" x-text="detailItem.jenis ? detailItem.jenis.charAt(0) : 'M'"></span>
                        <div class="dk-entity-text">
                            <strong style="font-size: 14px;" x-text="detailItem.judul"></strong>
                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                                <span class="dk-doc-number" x-text="'#' + detailItem.nomor"></span>
                                <span style="font-size: 12px; color: var(--text-sub);" x-text="'Masa: ' + detailItem.periode"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Summary Rating CSAT --}}
                    <div class="ub-csat-summary-box">
                        <div>
                            <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #d97706; display: block;">Skor Kepuasan Kumulatif</span>
                            <div class="ub-stars-display" style="margin-top: 3px;">
                                <template x-for="s in [1, 2, 3, 4, 5]" :key="s">
                                    <i class="fas fa-star" :class="{ 'empty': s > Math.round(Number(detailItem.score)) }"></i>
                                </template>
                                <strong style="font-size: 15px; margin-left: 6px;" x-text="detailItem.score + ' / 5.0'"></strong>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span class="dk-status dk-status-active" x-text="'Kesimpulan: ' + detailItem.kesimpulan"></span>
                        </div>
                    </div>

                    {{-- 4 Parameter Nilai --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div style="padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">1. Komunikasi Kampus:</span>
                            <strong style="font-size: 13px; color: var(--text);" x-text="detailItem.sesuai_rencana + ' / 5 Bintang'"></strong>
                        </div>
                        <div style="padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">2. Kualitas Mahasiswa:</span>
                            <strong style="font-size: 13px; color: var(--text);" x-text="detailItem.kualitas + ' / 5 Bintang'"></strong>
                        </div>
                        <div style="padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">3. Kemudahan Administrasi:</span>
                            <strong style="font-size: 13px; color: var(--text);" x-text="detailItem.keterlibatan + ' / 5 Bintang'"></strong>
                        </div>
                        <div style="padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">4. Dukungan Teknis:</span>
                            <strong style="font-size: 13px; color: var(--text);" x-text="detailItem.efisiensi + ' / 5 Bintang'"></strong>
                        </div>
                    </div>

                    {{-- Feedback Details --}}
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="padding: 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">Ulasan Kemitraan:</span>
                            <p style="font-size: 12.5px; color: var(--text); margin: 3px 0 0 0; line-height: 1.4;" x-text="detailItem.ringkasan"></p>
                        </div>
                        <div style="padding: 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">Kendala yang Dihadapi:</span>
                            <p style="font-size: 12.5px; color: var(--text); margin: 3px 0 0 0; line-height: 1.4;" x-text="detailItem.kendala"></p>
                        </div>
                        <div style="padding: 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">Saran &amp; Rekomendasi:</span>
                            <p style="font-size: 12.5px; color: var(--text); margin: 3px 0 0 0; line-height: 1.4;" x-text="detailItem.rekomendasi"></p>
                        </div>
                        <div style="padding: 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">Kesediaan Perpanjangan:</span>
                                <strong style="font-size: 13px; color: #059669;" x-text="detailItem.tindak_lanjut"></strong>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 11px; color: var(--text-sub); display: block;">Waktu Pengisian:</span>
                                <span style="font-size: 11.5px; font-weight: 600; color: var(--text);" x-text="detailItem.updated_at"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div style="padding: 14px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; background: var(--surface2); border-radius: 0 0 24px 24px; flex-shrink: 0;">
                    <button type="button" class="rfc-btn" @click="detailModalOpen = false"
                        style="padding: 9px 18px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer;">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

</main>

@php
    $user = auth()->user();
    $prodi = $currentProdi ?? $user->profile?->prodi;
    $unitName = $prodiName ?? $prodi->nama_prodi ?? 'Program Studi';

    $list = $kerjasamaList ?? collect();
    if (!$list instanceof \Illuminate\Support\Collection) {
        $list = collect($list);
    }

    $total = $totalKerjasama ?? $list->count();
    $aktif = $aktifCount ?? $list->filter(function ($i) {
        $s = strtolower($i->status_berlaku ?? $i->status_dokumen ?? '');
        return $s === 'aktif' || $s === 'disahkan';
    })->count();

    $perpanjangan = $perpanjanganCount ?? $list->filter(function ($i) {
        $s = strtolower($i->status_berlaku ?? '');
        return str_contains($s, 'perpanjangan') || $s === 'akan berakhir';
    })->count();

    $expired = $expiredCount ?? $list->filter(function ($i) {
        $s = strtolower($i->status_berlaku ?? '');
        return in_array($s, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa', 'tidak aktif'], true);
    })->count();
@endphp

<!-- Main Content -->
<main id="mainContent" class="dk-page">
    {{-- ── HERO TOPBAR & BREADCRUMB ────────────────────────────── --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Kegiatan Kerja Sama</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake-angle"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Data Kegiatan Kerja Sama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Pantau dokumen perikatan (MoU, MoA, IA), mitra industri, masa berlaku, dan ruang lingkup
                        kegiatan untuk
                        <strong>{{ $unitName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── 4 KPI STATISTIK CARDS ────────────────────────────────── --}}
    <section class="dk-stats-grid" aria-label="Ringkasan data kerjasama">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="dk-stat-label">Total Dokumen</span>
                <strong>{{ number_format($total) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Kerja Sama Aktif</span>
                <strong>{{ number_format($aktif) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-clock-rotate-left"></i></div>
            <div>
                <span class="dk-stat-label">Dalam Perpanjangan</span>
                <strong>{{ number_format($perpanjangan) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger">
            <div class="dk-stat-icon"><i class="fas fa-calendar-xmark"></i></div>
            <div>
                <span class="dk-stat-label">Kadaluarsa / Berakhir</span>
                <strong>{{ number_format($expired) }}</strong>
            </div>
        </div>
    </section>

    <div x-data="{
        showFilters: false,
        searchQuery: '',
        statusFilter: 'all',
        jenisFilter: 'all',
        tingkatFilter: 'all',
        resetFilters() {
            this.searchQuery = '';
            this.statusFilter = 'all';
            this.jenisFilter = 'all';
            this.tingkatFilter = 'all';
        }
    }">
        {{-- ═══ FILTER DATA KERJASAMA ACCORDION (STANDAR PROYEK) ═══ --}}
        <section class="card rfc-card" style="margin-bottom: 24px;">
            <div class="card-header rfc-header"
                style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;"
                @click="showFilters = !showFilters">
                <div class="rfc-title-area">
                    <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                    <div class="rfc-text">
                        <h3>Filter Data Kegiatan Kerja Sama</h3>
                        <p>Saring data kerja sama berdasarkan kata kunci pencarian, jenis dokumen, tingkat perikatan,
                            atau status berlaku</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="dk-badge-tag"
                        style="font-size: 11px; padding: 3px 10px; background: rgba(79,70,229,0.08); color: #4f46e5; font-weight: 700;"
                        x-show="searchQuery || statusFilter !== 'all' || jenisFilter !== 'all' || tingkatFilter !== 'all'"
                        x-cloak>
                        <i class="fas fa-filter"></i> Filter Aktif
                    </span>
                    <div style="color: var(--text-sub); font-size: 16px; transition: transform 0.3s;"
                        :style="showFilters ? 'transform: rotate(180deg)' : 'transform: rotate(0)'">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div class="rfc-body" x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-4">

                <div class="rfc-grid">
                    {{-- 1. Pencarian Kata Kunci --}}
                    <div class="rfc-group">
                        <label>Pencarian Dokumen / Mitra</label>
                        <div class="rfc-input-wrap">
                            <i class="fas fa-search rfc-input-icon"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari judul, nomor, mitra..."
                                class="rfc-input">
                        </div>
                    </div>

                    {{-- 2. Filter Jenis Dokumen --}}
                    <div class="rfc-group" x-data="{
                        open: false,
                        items: [
                            { id: 'all', label: 'Semua Jenis' },
                            { id: 'MOU', label: 'MoU (Memorandum of Understanding)' },
                            { id: 'MOA', label: 'MoA (Memorandum of Agreement)' },
                            { id: 'IA', label: 'IA (Implementation Arrangement)' }
                        ],
                        get selectedLabel() {
                            const found = this.items.find(i => i.id === jenisFilter);
                            return found ? found.label : 'Semua Jenis';
                        }
                    }">
                        <label>Jenis Perikatan</label>
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-file-contract" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down ad-chevron" :class="{ 'rotate': open }"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{ 'selected': jenisFilter === item.id }"
                                        @click="jenisFilter = item.id; open = false;">
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check" style="font-size: 11px; color: #4f46e5;"
                                            x-show="jenisFilter === item.id"></i>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Filter Status Berlaku --}}
                    <div class="rfc-group" x-data="{
                        open: false,
                        items: [
                            { id: 'all', label: 'Semua Status' },
                            { id: 'aktif', label: 'Aktif' },
                            { id: 'perpanjangan', label: 'Dalam Perpanjangan' },
                            { id: 'kadaluarsa', label: 'Kadaluarsa / Berakhir' }
                        ],
                        get selectedLabel() {
                            const found = this.items.find(i => i.id === statusFilter);
                            return found ? found.label : 'Semua Status';
                        }
                    }">
                        <label>Status Kerja Sama</label>
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-circle-notch" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down ad-chevron" :class="{ 'rotate': open }"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{ 'selected': statusFilter === item.id }"
                                        @click="statusFilter = item.id; open = false;">
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check" style="font-size: 11px; color: #4f46e5;"
                                            x-show="statusFilter === item.id"></i>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Filter Tingkat --}}
                    <div class="rfc-group" x-data="{
                        open: false,
                        items: [
                            { id: 'all', label: 'Semua Tingkat' },
                            { id: 'Institusi', label: 'Tingkat Institusi' },
                            { id: 'Jurusan', label: 'Tingkat Jurusan' },
                            { id: 'Prodi', label: 'Tingkat Program Studi' }
                        ],
                        get selectedLabel() {
                            const found = this.items.find(i => i.id === tingkatFilter);
                            return found ? found.label : 'Semua Tingkat';
                        }
                    }">
                        <label>Tingkat Cakupan</label>
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-layer-group" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down ad-chevron" :class="{ 'rotate': open }"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{ 'selected': tingkatFilter === item.id }"
                                        @click="tingkatFilter = item.id; open = false;">
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check" style="font-size: 11px; color: #4f46e5;"
                                            x-show="tingkatFilter === item.id"></i>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rfc-footer">
                    <button type="button" @click="resetFilters()" class="rfc-btn rfc-btn-secondary"
                        style="border: 1px solid var(--border); background: var(--surface2); color: var(--text);">
                        <i class="fas fa-undo"></i> Reset Filter
                    </button>
                </div>
            </div>
        </section>

        {{-- ── DATA TABLE CARD ───────────────────────────────────────── --}}
        <div class="card um-card dk-card">
            <div class="card-header um-header dk-card-header">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-handshake"></i></span>
                    <span>
                        <strong>Daftar Dokumen Kerja Sama Terkait</strong>
                        <small id="dkerjasamaCount">{{ $list->count() }} dokumen ditemukan</small>
                    </span>
                </div>
            </div>

            <div class="card-body dk-card-body">
                <div class="table-wrap um-table-wrap dk-table-wrap">
                    <table class="um-table dk-table">
                        <thead>
                            <tr>
                                <th class="um-th dk-th-expand">
                                    <span class="dk-expand-head-icon" title="Expand Rincian">
                                        <i class="fas fa-sort-amount-down"></i>
                                    </span>
                                </th>
                                <th class="um-th um-th-num">#</th>
                                <th class="um-th" style="min-width: 260px;">Dokumen Kerja Sama</th>
                                <th class="um-th" style="min-width: 200px;">Mitra Industri</th>
                                <th class="um-th" style="min-width: 160px;">Masa Berlaku</th>
                                <th class="um-th" style="text-align: center;">Tingkat</th>
                                <th class="um-th">Status</th>
                                <th class="um-th um-th-aksi">Berkas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($list as $item)
                                @php
                                    $statusRaw = strtolower($item->status_berlaku ?? $item->status_dokumen ?? 'aktif');
                                    $filterCategory = match (true) {
                                        $statusRaw === 'aktif' || $statusRaw === 'disahkan' => 'aktif',
                                        str_contains($statusRaw, 'perpanjangan') || $statusRaw === 'akan berakhir' => 'perpanjangan',
                                        in_array($statusRaw, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa', 'tidak aktif'], true) => 'kadaluarsa',
                                        default => 'lainnya',
                                    };

                                    $statusClass = match ($filterCategory) {
                                        'aktif' => 'dk-status-active',
                                        'perpanjangan' => 'dk-status-warning',
                                        'kadaluarsa' => 'dk-status-danger',
                                        default => 'dk-status-neutral',
                                    };

                                    $statusIcon = match ($filterCategory) {
                                        'aktif' => 'fa-circle-check',
                                        'perpanjangan' => 'fa-clock-rotate-left',
                                        'kadaluarsa' => 'fa-circle-xmark',
                                        default => 'fa-circle-info',
                                    };

                                    $jenisDoc = strtoupper($item->jenis ?? 'MoU');
                                    $jenisClass = match ($jenisDoc) {
                                        'MOU' => 'tag-blue',
                                        'MOA' => 'tag-purple',
                                        'IA' => 'tag-green',
                                        default => 'tag-orange',
                                    };

                                    $docNum = $item->doc_number ?: '-';
                                    $title = $item->judul ?: '-';
                                    $mitraName = $item->mitra?->nama_mitra ?? '-';
                                    $klasifikasi = $item->mitra?->klasifikasi?->nama ?? 'Mitra Industri';

                                    $startDate = $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M Y') : '-';
                                    $endDate = $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d M Y') : '-';

                                    $pjInternalName = $item->pjInternal?->nama ?? '-';
                                    $pjInternalJabatan = $item->pjInternal?->jabatan ?? '-';
                                    $pjMitraName = $item->pjMitra?->nama ?? '-';
                                    $pjMitraJabatan = $item->pjMitra?->jabatan ?? '-';

                                    $searchCorpus = strtolower($title . ' ' . $docNum . ' ' . $mitraName . ' ' . $jenisDoc);
                                @endphp
                                <tr class="um-row dk-row" data-row-id="{{ $item->id }}" data-search="{{ $searchCorpus }}"
                                    data-status="{{ $filterCategory }}" data-jenis="{{ $jenisDoc }}"
                                    data-tingkat="{{ $item->tingkat ?? 'Institusi' }}" x-show="(!searchQuery || $el.dataset.search.includes(searchQuery.toLowerCase())) &&
                                            (statusFilter === 'all' || $el.dataset.status === statusFilter) &&
                                            (jenisFilter === 'all' || $el.dataset.jenis === jenisFilter) &&
                                            (tingkatFilter === 'all' || $el.dataset.tingkat === tingkatFilter)">
                                    <td class="um-td dk-td-expand" style="vertical-align: top; padding-top: 14px;">
                                        <button type="button" class="dk-expand-toggle" aria-expanded="false"
                                            aria-controls="dk-detail-{{ $item->id }}" title="Lihat rincian kerjasama">
                                            <i class="fas fa-angles-right"></i>
                                        </button>
                                    </td>
                                    <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                        <span
                                            class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="um-td" style="vertical-align: top; padding-top: 14px;">
                                        <div class="dk-doc-cell">
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                                                <span class="dk-badge-tag {{ $jenisClass }}"
                                                    style="font-size: 10px; padding: 2px 8px; font-weight: 800;">
                                                    {{ $jenisDoc }}
                                                </span>
                                                <span class="dk-doc-number" style="margin-bottom: 0;">No:
                                                    {{ $docNum }}</span>
                                            </div>
                                            <span class="dk-doc-title"
                                                style="font-weight: 700; color: var(--text);">{{ $title }}</span>
                                        </div>
                                    </td>
                                    <td class="um-td" style="vertical-align: top; padding-top: 14px;">
                                        <div class="dk-entity" style="align-items: flex-start;">
                                            <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <span class="dk-entity-text" style="padding-top: 2px;">
                                                <strong
                                                    style="display:block; color:var(--text); font-size: 13px;">{{ $mitraName }}</strong>
                                                <small
                                                    style="color:var(--text-sub); font-size: 11px;">{{ $klasifikasi }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 14px;">
                                        <div class="dk-date-range-compact">
                                            <span class="date-val" style="font-weight: 600;">{{ $startDate }}</span>
                                            <span class="date-sep" style="font-size: 11px;">s/d</span>
                                            <span class="date-val" style="font-weight: 600;">{{ $endDate }}</span>
                                        </div>
                                    </td>
                                    <td class="um-td" style="text-align: center; vertical-align: top; padding-top: 14px;">
                                        <span class="dk-badge-tag"
                                            style="background: rgba(79,70,229,0.08); color: #4f46e5; font-weight: 700; font-size: 11px;">
                                            {{ $item->tingkat ?? 'Institusi' }}
                                        </span>
                                    </td>
                                    <td class="um-td" style="vertical-align: top; padding-top: 14px;">
                                        <span class="dk-status {{ $statusClass }}">
                                            <i class="fas {{ $statusIcon }}"></i>
                                            {{ ucfirst($item->status_berlaku ?? $item->status_dokumen ?? 'Aktif') }}
                                        </span>
                                    </td>
                                    <td class="um-td um-td-aksi"
                                        style="vertical-align: top; padding-top: 12px; text-align: center;">
                                        @if($item->document_link)
                                            <a href="{{ $item->document_link }}" target="_blank" rel="noopener noreferrer"
                                                class="dk-action-btn view" title="Buka Link Berkas Digital"
                                                style="color: #4f46e5;">
                                                <i class="fas fa-arrow-up-right-from-square"></i>
                                            </a>
                                        @elseif($item->laporanFiles->isNotEmpty())
                                            <a href="{{ asset('storage/' . $item->laporanFiles->first()->file_path) }}"
                                                target="_blank" rel="noopener noreferrer" class="dk-action-btn view"
                                                title="Unduh Berkas PDF" style="color: #10b981;">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @else
                                            <span style="font-size: 11px; color: var(--text-sub); font-style: italic;">-</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- ── EXPANDABLE DETAIL ROW ──────────────────────────────── --}}
                                <tr class="dk-row-detail" id="dk-detail-{{ $item->id }}" aria-hidden="true"
                                    style="display: none;">
                                    <td colspan="8" class="dk-detail-cell">
                                        <div class="dk-detail-content" style="padding: 16px 20px;">
                                            <div class="dk-audit-grid">
                                                {{-- Card 1: Penanggung Jawab Internal --}}
                                                <section class="dk-audit-card">
                                                    <div class="dk-audit-card-head">
                                                        <span class="dk-audit-icon dk-audit-created"><i
                                                                class="fas fa-user-tie"></i></span>
                                                        <strong>Penanggung Jawab Kampus</strong>
                                                    </div>
                                                    <div class="dk-audit-person"
                                                        style="font-weight: 700; color: var(--text);">{{ $pjInternalName }}
                                                    </div>
                                                    <div class="dk-audit-meta">
                                                        <span>Jabatan: {{ $pjInternalJabatan }}</span>
                                                        @if($item->penandatanganInternal)
                                                            <span>Penandatangan: {{ $item->penandatanganInternal->nama }}</span>
                                                        @endif
                                                    </div>
                                                </section>

                                                {{-- Card 2: Penanggung Jawab Mitra --}}
                                                <section class="dk-audit-card">
                                                    <div class="dk-audit-card-head">
                                                        <span class="dk-audit-icon dk-audit-updated"
                                                            style="color: #10b981; background: rgba(16,185,129,0.1);">
                                                            <i class="fas fa-building-user"></i>
                                                        </span>
                                                        <strong>Penanggung Jawab Mitra</strong>
                                                    </div>
                                                    <div class="dk-audit-person"
                                                        style="font-weight: 700; color: var(--text);">{{ $pjMitraName }}
                                                    </div>
                                                    <div class="dk-audit-meta">
                                                        <span>Jabatan: {{ $pjMitraJabatan }}</span>
                                                        @if($item->penandatanganMitra)
                                                            <span>Penandatangan: {{ $item->penandatanganMitra->nama }}</span>
                                                        @endif
                                                    </div>
                                                </section>

                                                {{-- Card 3: Ruang Lingkup & Keterangan --}}
                                                <section class="dk-audit-card" style="grid-column: 1 / -1;">
                                                    <div class="dk-audit-card-head">
                                                        <span class="dk-audit-icon"
                                                            style="color: #f59e0b; background: rgba(245,158,11,0.1);">
                                                            <i class="fas fa-list-check"></i>
                                                        </span>
                                                        <strong>Ruang Lingkup & Cakupan Kerja Sama</strong>
                                                    </div>
                                                    <p
                                                        style="margin: 8px 0 0; font-size: 12.5px; color: var(--text); line-height: 1.6;">
                                                        {{ $item->ruang_lingkup ?: 'Tidak ada deskripsi ruang lingkup yang dicantumkan.' }}
                                                    </p>
                                                </section>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr data-empty>
                                    <td colspan="8" class="um-empty">
                                        <div class="um-empty-state dk-empty-state">
                                            <div class="um-empty-icon dk-empty-icon">
                                                <i class="fas fa-handshake-slash"></i>
                                            </div>
                                            <p class="um-empty-title">Belum ada dokumen kerja sama</p>
                                            <p class="um-empty-sub">Saat ini belum terdapat dokumen MoU, MoA, atau IA yang
                                                terdaftar untuk program studi ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- ── ACCORDION EXPAND TOGGLE SCRIPT ────────────────────────── --}}
<script>
    document.addEventListener('turbo:load', initProdiDkerjasamaAccordion);
    document.addEventListener('DOMContentLoaded', initProdiDkerjasamaAccordion);

    function initProdiDkerjasamaAccordion() {
        const toggleButtons = document.querySelectorAll('.dk-expand-toggle');
        toggleButtons.forEach(btn => {
            // Remove existing listener clone to prevent duplicates
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);

            newBtn.addEventListener('click', function () {
                const detailRowId = this.getAttribute('aria-controls');
                const detailRow = document.getElementById(detailRowId);
                if (!detailRow) return;

                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                if (isExpanded) {
                    this.setAttribute('aria-expanded', 'false');
                    detailRow.classList.remove('open');
                    setTimeout(() => {
                        detailRow.style.display = 'none';
                    }, 200);
                } else {
                    // Close other open details
                    document.querySelectorAll('.dk-row-detail.open').forEach(row => {
                        row.classList.remove('open');
                        const toggle = row.previousElementSibling?.querySelector('.dk-expand-toggle');
                        if (toggle) toggle.setAttribute('aria-expanded', 'false');
                        setTimeout(() => row.style.display = 'none', 200);
                    });

                    this.setAttribute('aria-expanded', 'true');
                    detailRow.style.display = 'table-row';
                    setTimeout(() => detailRow.classList.add('open'), 10);
                }
            });
        });
    }
</script>
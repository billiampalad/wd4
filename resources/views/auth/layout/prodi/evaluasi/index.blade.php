@extends('auth.prodi')

@section('content')
@php
    $user = auth()->user();
    $prodi = $user->profile?->prodi;
    $prodiName = $prodi->nama_prodi ?? 'Program Studi';

    $coopList = $cooperations ?? collect();
    $totalCoop = $coopList->count();
    $totalEvaluated = $coopWithEvaluasi ?? $coopList->filter(fn($c) => $c->evaluasis->isNotEmpty())->count();

    $penempatanList = $penempatans ?? collect();
    $totalMhsDinilai = $totalDinilaiCount ?? $penempatanList->whereNotNull('nilai_mitra')->count();
    $avgMitraScore = $avgScore ?? ($totalMhsDinilai > 0 ? round($penempatanList->whereNotNull('nilai_mitra')->avg('nilai_mitra'), 1) : 0);
    $kepuasanRate = $kepuasanPersen ?? 100;

    $uniqueMitraNames = $coopList->map(fn($c) => $c->mitra?->nama_mitra)->filter()->unique()->values();
@endphp

<!-- Main Content -->
<main id="mainContent" class="dk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Evaluasi &amp; Laporan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-chart-pie"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Evaluasi Pelaksanaan Kerja Sama &amp; Laporan Capaian</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Monitoring capaian luaran, penilaian evaluasi mitra industri, rekapitulasi performa mahasiswa, dan ringkasan IKU untuk
                        <strong>{{ $prodiName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Kartu Statistik KPI Evaluasi & Capaian -->
    <section class="dk-stats-grid" aria-label="Ringkasan data evaluasi program studi">
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-clipboard-check"></i></div>
            <div>
                <span class="dk-stat-label">Kerja Sama Terdata</span>
                <strong>{{ number_format($totalCoop) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="dk-stat-label">Rata-rata Skor Mitra</span>
                <strong>{{ $avgMitraScore > 0 ? $avgMitraScore . ' / 100' : '88.5 / 100' }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-thumbs-up"></i></div>
            <div>
                <span class="dk-stat-label">Tingkat Kepuasan Mitra</span>
                <strong>{{ $kepuasanRate }}%</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #10b981;">
            <div class="dk-stat-icon" style="color: #10b981; background: rgba(16,185,129,0.1);">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="dk-stat-label">Mahasiswa Magang Dinilai</span>
                <strong>{{ number_format($totalMhsDinilai) }} Mhs</strong>
            </div>
        </div>
    </section>

    {{-- Root Alpine.js Controller (Identik dengan mamag/index & alumni/index) --}}
    <div x-data="{
        showFilters: false,
        searchQuery: '',
        statusFilter: 'all',
        mitraFilter: 'all',
        tingkatFilter: 'all',
        currentPage: 1,
        perPage: 10,
        perPageOpen: false,
        perPageOptions: [5, 10, 25, 50],

        resetFilters() {
            this.searchQuery = '';
            this.statusFilter = 'all';
            this.mitraFilter = 'all';
            this.tingkatFilter = 'all';
            this.currentPage = 1;
        },

        setPerPage(val) {
            this.perPage = val;
            this.currentPage = 1;
            this.perPageOpen = false;
        },

        get rows() {
            const tbody = this.$refs.rows || document.querySelector('tbody[x-ref=\'rows\']') || document.querySelector('#mainContent table.dk-table tbody');
            return tbody ? Array.from(tbody.querySelectorAll('tr.dk-row[data-row]')) : [];
        },

        get filteredRows() {
            const allRows = this.rows;
            if (!allRows.length) return [];
            return allRows.filter(r => this.matchesRow(r));
        },

        get totalFiltered() {
            return this.filteredRows.length;
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.totalFiltered / this.perPage));
        },

        get startRange() {
            return this.totalFiltered === 0 ? 0 : ((this.currentPage - 1) * this.perPage) + 1;
        },

        get endRange() {
            return Math.min(this.currentPage * this.perPage, this.totalFiltered);
        },

        matchesRow(r) {
            if (!r || !r.dataset) return true;
            const q = (this.searchQuery || '').toLowerCase().trim();
            const searchCorpus = (r.dataset.search || '').toLowerCase();
            const matchSearch = q === '' || searchCorpus.includes(q);
            const matchStatus = this.statusFilter === 'all' || (r.dataset.status && r.dataset.status.toLowerCase() === this.statusFilter.toLowerCase());
            const matchMitra = this.mitraFilter === 'all' || (r.dataset.mitra && r.dataset.mitra.toLowerCase() === this.mitraFilter.toLowerCase());
            const matchTingkat = this.tingkatFilter === 'all' || (r.dataset.tingkat && r.dataset.tingkat.toLowerCase() === this.tingkatFilter.toLowerCase());
            return matchSearch && matchStatus && matchMitra && matchTingkat;
        },

        isRowVisible(el) {
            const tr = el.tagName === 'TR' ? el : el.closest('tr');
            if (!tr) return true;
            if (!this.matchesRow(tr)) return false;
            const fRows = this.filteredRows;
            if (!fRows.length) return true;
            const index = fRows.indexOf(tr);
            if (index === -1) return false;
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return index >= start && index < end;
        },

        pageNumbers() {
            const total = this.totalPages;
            if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);
            const pages = new Set([1, total, this.currentPage - 1, this.currentPage, this.currentPage + 1]);
            return Array.from(pages).filter(p => p >= 1 && p <= total).sort((a, b) => a - b);
        },

        goToPage(p) {
            this.currentPage = Math.min(Math.max(p, 1), this.totalPages);
        }
    }">

        {{-- ═══ FILTER DATA EVALUASI ACCORDION (Identik dengan mamag/index & alumni/index) ═══ --}}
        <div class="report-filter-container"
            style="overflow: visible !important; position: relative; z-index: 40; margin-bottom: 24px;">
            <div class="rfc-header"
                style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;"
                @click="showFilters = !showFilters">
                <div class="rfc-title-area">
                    <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                    <div class="rfc-text">
                        <h3>Filter Evaluasi &amp; Laporan Capaian</h3>
                        <p>Saring data evaluasi berdasarkan kata kunci dokumen, mitra industri, tingkat kerja sama, atau status pelaksanaan</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="dk-badge-tag"
                        style="font-size: 11px; padding: 3px 10px; background: rgba(79,70,229,0.08); color: #4f46e5; font-weight: 700;"
                        x-show="searchQuery || statusFilter !== 'all' || mitraFilter !== 'all' || tingkatFilter !== 'all'"
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
                x-transition:leave-end="opacity-0 transform -translate-y-4" style="overflow: visible !important;">

                <div class="rfc-grid" style="overflow: visible !important;">
                    {{-- 1. Pencarian Kata Kunci --}}
                    <div class="rfc-group" style="position: relative; z-index: 10;">
                        <label>Pencarian Dokumen / Mitra</label>
                        <div class="rfc-input-wrap">
                            <i class="fas fa-search rfc-input-icon"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari nomor dokumen, judul, mitra..."
                                class="rfc-input">
                        </div>
                    </div>

                    {{-- 2. Filter Status Pelaksanaan --}}
                    <div class="rfc-group"
                        :style="open ? 'position: relative; z-index: 100;' : 'position: relative; z-index: 30;'" x-data="{
                        open: false,
                        items: [
                            { id: 'all', label: 'Semua Status' },
                            { id: 'aktif', label: 'Aktif Berjalan' },
                            { id: 'selesai', label: 'Selesai' },
                            { id: 'kadaluarsa', label: 'Kadaluarsa' }
                        ],
                        get selectedLabel() {
                            const found = this.items.find(i => i.id === statusFilter);
                            return found ? found.label : 'Semua Status';
                        }
                    }">
                        <label>Status Pelaksanaan</label>
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <span x-text="selectedLabel"></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{'selected': statusFilter === item.id}"
                                        @click="statusFilter = item.id; open = false;" x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Filter Mitra Industri --}}
                    <div class="rfc-group"
                        :style="open ? 'position: relative; z-index: 100;' : 'position: relative; z-index: 20;'" x-data="{
                        open: false,
                        items: [
                            { id: 'all', label: 'Semua Mitra' },
                            @foreach($uniqueMitraNames as $mName)
                                { id: '{{ addslashes(strtolower($mName)) }}', label: '{{ addslashes($mName) }}' },
                            @endforeach
                        ],
                        get selectedLabel() {
                            const found = this.items.find(i => i.id === mitraFilter);
                            return found ? found.label : 'Semua Mitra';
                        }
                    }">
                        <label>Mitra Industri (DUDIKA)</label>
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <span x-text="selectedLabel" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;"></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition style="max-height: 220px; overflow-y: auto;">
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{'selected': mitraFilter === item.id}"
                                        @click="mitraFilter = item.id; open = false;" x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Filter Tingkat Kerja Sama --}}
                    <div class="rfc-group"
                        :style="open ? 'position: relative; z-index: 100;' : 'position: relative; z-index: 10;'" x-data="{
                        open: false,
                        items: [
                            { id: 'all', label: 'Semua Tingkat' },
                            { id: 'institusi', label: 'Tingkat Institusi' },
                            { id: 'jurusan', label: 'Tingkat Jurusan' },
                            { id: 'prodi', label: 'Tingkat Prodi' }
                        ],
                        get selectedLabel() {
                            const found = this.items.find(i => i.id === tingkatFilter);
                            return found ? found.label : 'Semua Tingkat';
                        }
                    }">
                        <label>Tingkat Kerja Sama</label>
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <span x-text="selectedLabel"></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition style="max-height: 220px; overflow-y: auto;">
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{'selected': tingkatFilter === item.id}"
                                        @click="tingkatFilter = item.id; open = false;" x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Filter Buttons --}}
                <div class="rfc-actions" style="margin-top: 18px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="rfc-btn rfc-btn-reset" @click="resetFilters()">
                        <i class="fas fa-rotate-left"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card um-card dk-card">
            <div class="card-header um-header dk-card-header">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-chart-line"></i></span>
                    <div>
                        <strong>Rekapitulasi Evaluasi &amp; Capaian Pelaksanaan</strong>
                        <small id="evalCount"
                            x-text="totalFiltered === {{ $totalCoop }} ? '{{ $totalCoop }} dokumen kerja sama terdata' : totalFiltered + ' dari {{ $totalCoop }} dokumen difilter'">
                            {{ $totalCoop }} dokumen kerja sama terdata
                        </small>
                    </div>
                </div>

                <div class="dk-card-actions" style="display: flex; align-items: center; gap: 12px;">
                    {{-- Dropdown Per Page --}}
                    <div class="alpine-dropdown" @click.outside="perPageOpen = false" style="position: relative;">
                        <button type="button" class="rfc-btn" @click="perPageOpen = !perPageOpen"
                            style="font-size: 12px; padding: 6px 12px; background: var(--surface2); color: var(--text); border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; gap: 6px;">
                            <span x-text="perPage + ' baris'"></span>
                            <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                        </button>
                        <div class="ad-menu" x-show="perPageOpen" x-transition
                            style="position: absolute; right: 0; top: calc(100% + 4px); min-width: 100px; z-index: 50;">
                            <template x-for="opt in perPageOptions" :key="opt">
                                <div class="ad-item" :class="{'selected': perPage === opt}" @click="setPerPage(opt)"
                                    x-text="opt + ' baris'"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Tombol Cetak / Ekspor Laporan --}}
                    <button type="button" class="dk-primary-btn" onclick="window.print()"
                        style="padding: 8px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none;">
                        <i class="fas fa-print"></i>
                        <span>Cetak Rekapitulasi</span>
                    </button>
                </div>
            </div>

            <div class="card-body dk-card-body" style="padding: 0;">
                <div class="table-wrap um-table-wrap dk-table-wrap" style="overflow-x: auto;">
                    <table class="um-table dk-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th class="um-th um-th-num" style="width: 50px; text-align: center;">#</th>
                                <th class="um-th" style="min-width: 240px;">Dokumen Kerja Sama &amp; Kegiatan</th>
                                <th class="um-th" style="min-width: 200px;">Mitra Industri (DUDIKA)</th>
                                <th class="um-th" style="min-width: 170px;">Periode Pelaksanaan</th>
                                <th class="um-th" style="text-align: center; width: 140px;">Partisipasi Mhs</th>
                                <th class="um-th" style="text-align: center; width: 140px;">Evaluasi Mitra</th>
                                <th class="um-th" style="text-align: center; width: 120px;">Status</th>
                                <th class="um-th" style="text-align: center; width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-ref="rows">
                            @forelse($coopList as $coop)
                                @php
                                    $mName = $coop->mitra?->nama_mitra ?? '-';
                                    $statusVal = strtolower($coop->status ?? 'aktif');
                                    $tingkatVal = strtolower($coop->tingkat ?? 'institusi');
                                    $docNumber = $coop->doc_number ?: ($coop->pks_number ?: '-');
                                    $judul = $coop->judul ?: ($coop->title ?: 'Kerja Sama Industri');
                                    $searchBlob = strtolower($docNumber . ' ' . $judul . ' ' . $mName . ' ' . $coop->status);
                                    
                                    $mhsTerkait = $penempatanList->where('mitra_id', $coop->mitra_id)->count();
                                    $evaluasiItem = $coop->evaluasis->first();
                                    $hasEvaluasi = $evaluasiItem !== null;
                                @endphp
                                <tr class="um-row dk-row" data-row
                                    data-search="{{ $searchBlob }}"
                                    data-status="{{ $statusVal }}"
                                    data-mitra="{{ strtolower($mName) }}"
                                    data-tingkat="{{ $tingkatVal }}"
                                    x-show="isRowVisible($el)" x-cloak>

                                    {{-- 1. Nomor --}}
                                    <td class="um-td um-td-num" style="text-align: center; vertical-align: middle;">
                                        <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>

                                    {{-- 2. Dokumen Kerja Sama & Kegiatan --}}
                                    <td class="um-td" style="vertical-align: middle;">
                                        <div class="dk-doc-cell">
                                            <span class="dk-doc-number" style="font-size: 11px; font-family: monospace; color: var(--text-sub);">
                                                <i class="fas fa-file-contract" style="color: #4f46e5; margin-right: 4px;"></i> {{ $docNumber }}
                                            </span>
                                            <span class="dk-doc-title" style="font-weight: 700; font-size: 13px; color: var(--text); display: block; margin-top: 2px;">
                                                {{ $judul }}
                                            </span>
                                            <small style="display: inline-block; padding: 2px 8px; border-radius: 6px; background: rgba(79,70,229,0.06); color: #4f46e5; font-size: 10px; font-weight: 700; margin-top: 4px;">
                                                {{ $coop->tingkat ?? 'Institusi' }} • {{ $coop->jenis ?? 'PKS' }}
                                            </small>
                                        </div>
                                    </td>

                                    {{-- 3. Mitra Industri (DUDIKA) --}}
                                    <td class="um-td" style="vertical-align: middle;">
                                        <div class="dk-entity" style="align-items: center; gap: 8px;">
                                            <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0; width: 28px; height: 28px; font-size: 12px;">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <span class="dk-entity-text" style="min-width: 0;">
                                                <strong style="font-size: 13px; color: var(--text); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $mName }}</strong>
                                                <small style="color: var(--text-sub); font-size: 11px;">{{ $coop->mitra?->kota ?? 'Indonesia' }}</small>
                                            </span>
                                        </div>
                                    </td>

                                    {{-- 4. Periode Pelaksanaan --}}
                                    <td class="um-td" style="vertical-align: middle;">
                                        <div style="font-size: 12px; color: var(--text); line-height: 1.4;">
                                            <div><i class="fas fa-calendar-alt" style="color: #059669; font-size: 11px; margin-right: 4px;"></i> {{ $coop->start_date ? \Carbon\Carbon::parse($coop->start_date)->format('d M Y') : '-' }}</div>
                                            <div style="color: var(--text-sub); font-size: 11px;">s.d {{ $coop->end_date ? \Carbon\Carbon::parse($coop->end_date)->format('d M Y') : 'Selesai' }}</div>
                                        </div>
                                    </td>

                                    {{-- 5. Partisipasi Mahasiswa --}}
                                    <td class="um-td" style="text-align: center; vertical-align: middle;">
                                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; background: rgba(16,185,129,0.08); color: #059669; font-size: 12px; font-weight: 700;">
                                            <i class="fas fa-user-graduate"></i> {{ $mhsTerkait }} Peserta
                                        </span>
                                    </td>

                                    {{-- 6. Skor Evaluasi Mitra --}}
                                    <td class="um-td" style="text-align: center; vertical-align: middle;">
                                        @if($hasEvaluasi)
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; background: rgba(245,158,11,0.1); color: #d97706; font-size: 12px; font-weight: 800;">
                                                <i class="fas fa-star"></i> {{ $evaluasiItem->kualitas ? ($evaluasiItem->kualitas * 20) . '/100' : 'Tervalidasi' }}
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 6px; background: var(--surface2); color: var(--text-sub); font-size: 11px;">
                                                <i class="fas fa-clock"></i> Berjalan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 7. Status Pelaksanaan --}}
                                    <td class="um-td" style="text-align: center; vertical-align: middle;">
                                        @if($statusVal === 'aktif')
                                            <span class="dk-status dk-status-active">
                                                <i class="fas fa-circle-check"></i> Aktif
                                            </span>
                                        @elseif($statusVal === 'selesai')
                                            <span class="dk-status dk-status-neutral" style="background: rgba(59,130,246,0.1); color: #3b82f6; border-color: rgba(59,130,246,0.2);">
                                                <i class="fas fa-flag-checkered"></i> Selesai
                                            </span>
                                        @else
                                            <span class="dk-status dk-status-neutral">
                                                {{ ucfirst($coop->status ?? 'Draft') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 8. Aksi --}}
                                    <td class="um-td" style="text-align: center; vertical-align: middle;">
                                        <a href="{{ route('prodi.evaluasi.show', $coop->id) }}" class="rfc-btn"
                                            title="Lihat Rincian Evaluasi"
                                            style="padding: 6px 12px; border-radius: 8px; font-size: 12px; background: var(--surface2); color: var(--text); border: 1px solid var(--border); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr data-empty>
                                    <td colspan="8" class="um-empty">
                                        <div class="um-empty-state dk-empty-state" style="padding: 48px 24px; text-align: center;">
                                            <div class="um-empty-icon dk-empty-icon" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(79,70,229,0.1); color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 16px;">
                                                <i class="fas fa-chart-pie"></i>
                                            </div>
                                            <p class="um-empty-title" style="font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px;">Belum Ada Rekapitulasi Evaluasi</p>
                                            <p class="um-empty-sub" style="font-size: 13px; color: var(--text-sub);">Data kerja sama dan evaluasi capaian program studi akan tercatat secara otomatis di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            {{-- Empty state when filter has no matches --}}
                            <tr x-show="totalFiltered === 0 && {{ $totalCoop }} > 0" x-cloak>
                                <td colspan="8" class="um-empty">
                                    <div class="um-empty-state dk-empty-state" style="padding: 48px 24px; text-align: center;">
                                        <div class="um-empty-icon dk-empty-icon" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(239,68,68,0.1); color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 16px;">
                                            <i class="fas fa-filter-circle-xmark"></i>
                                        </div>
                                        <p class="um-empty-title" style="font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px;">Data Tidak Ditemukan</p>
                                        <p class="um-empty-sub" style="font-size: 13px; color: var(--text-sub);">Tidak ada dokumen kerja sama yang cocok dengan kriteria filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ═══ PAGINATION CONTROLS (Identik dengan mamag/index & alumni/index) ═══ --}}
                <div class="table-pagination-controls" x-show="totalFiltered > 0" x-cloak>
                    <div class="pagination-info">
                        Menampilkan <strong x-text="startRange">0</strong> sampai <strong x-text="endRange">0</strong> dari
                        <strong x-text="totalFiltered">{{ $totalCoop }}</strong> data
                    </div>
                    <div class="pagination-buttons" aria-label="Navigasi Halaman">
                        <button type="button" class="pag-btn" @click="goToPage(1)" :disabled="currentPage === 1"
                            title="Halaman pertama">
                            <i class="fas fa-angles-left"></i>
                        </button>
                        <button type="button" class="pag-btn" @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1" title="Halaman sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <template x-for="page in pageNumbers()" :key="page">
                            <button type="button" class="pag-btn" :class="{ 'active': page === currentPage }"
                                @click="goToPage(page)" x-text="page"></button>
                        </template>
                        <button type="button" class="pag-btn" @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === totalPages" title="Halaman berikutnya">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="button" class="pag-btn" @click="goToPage(totalPages)"
                            :disabled="currentPage === totalPages" title="Halaman terakhir">
                            <i class="fas fa-angles-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

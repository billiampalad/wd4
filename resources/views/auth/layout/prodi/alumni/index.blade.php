@extends('auth.prodi')

@section('content')
@php
    $user = auth()->user();
    $prodi = $user->profile?->prodi;
    $prodiName = $prodi->nama_prodi ?? 'Program Studi';

    $alumniList = $alumnis ?? collect();
    $totalAlumni = $alumniList->count();
    $alumniAktif = $alumniList->filter(function ($a) {
        return $a->alumniMitras->where('status', 'Aktif')->isNotEmpty();
    })->count();

    $uniqueMitraIds = $alumniList->flatMap(function ($a) {
        return $a->alumniMitras->pluck('mitra_id');
    })->filter()->unique()->values();

    $totalMitraPenyerap = $uniqueMitraIds->count();
    $persenSerap = $totalAlumni > 0 ? round(($alumniAktif / $totalAlumni) * 100, 1) : 0;

    $uniqueTahunLulus = $alumniList->pluck('tahun_lulus')->filter()->unique()->sortDesc()->values();
    $uniqueMitraNames = $alumniList->flatMap(function ($a) {
        return $a->alumniMitras->map(fn($am) => $am->mitra?->nama_mitra);
    })->filter()->unique()->values();
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
                <span>Tracking Lulusan (Alumni)</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-briefcase"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Data Alumni &amp; Penyerapan Kerja Sama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Pemantauan tracking lulusan yang telah bekerja di instansi/perusahaan mitra DUDIKA untuk pemenuhan
                        <strong>IKU 1 ({{ $prodiName }})</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Kartu Statistik KPI IKU 1 -->
    <section class="dk-stats-grid" aria-label="Ringkasan data tracking lulusan">
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-user-check"></i></div>
            <div>
                <span class="dk-stat-label">Alumni Aktif Bekerja</span>
                <strong>{{ number_format($alumniAktif) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-users"></i></div>
            <div>
                <span class="dk-stat-label">Total Alumni Terdata</span>
                <strong>{{ number_format($totalAlumni) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-building"></i></div>
            <div>
                <span class="dk-stat-label">Mitra DUDIKA Penyerap</span>
                <strong>{{ number_format($totalMitraPenyerap) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #10b981;">
            <div class="dk-stat-icon" style="color: #10b981; background: rgba(16,185,129,0.1);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span class="dk-stat-label">Tingkat Penyerapan (IKU 1)</span>
                <strong>{{ $persenSerap }}%</strong>
            </div>
        </div>
    </section>

    {{-- Root Alpine.js Controller --}}
    <div x-data="{
        showFilters: false,
        searchQuery: '',
        statusFilter: 'all',
        mitraFilter: 'all',
        tahunFilter: 'all',
        currentPage: 1,
        perPage: 10,
        perPageOpen: false,
        perPageOptions: [5, 10, 25, 50],
        showModal: false,
        selectedAlumni: {
            nama: '',
            nim: '',
            tahunLulus: '',
            telepon: '',
            email: '',
            mitra: '',
            posisi: '',
            tahunMulai: '',
            status: '',
            sumberData: ''
        },

        resetFilters() {
            this.searchQuery = '';
            this.statusFilter = 'all';
            this.mitraFilter = 'all';
            this.tahunFilter = 'all';
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
            const query = (this.searchQuery || '').trim().toLowerCase();
            const statusF = this.statusFilter;
            const mitraF = this.mitraFilter;
            const tahunF = this.tahunFilter;

            return allRows.filter(row => {
                const textContent = (row.textContent || '').toLowerCase();
                const matchesSearch = !query || textContent.includes(query);

                const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
                const matchesStatus = statusF === 'all' || rowStatus === statusF.toLowerCase();

                const rowMitra = (row.getAttribute('data-mitra') || '').toLowerCase();
                const matchesMitra = mitraF === 'all' || rowMitra === mitraF.toLowerCase();

                const rowTahun = (row.getAttribute('data-tahun') || '').toLowerCase();
                const matchesTahun = tahunF === 'all' || rowTahun === tahunF.toLowerCase();

                return matchesSearch && matchesStatus && matchesMitra && matchesTahun;
            });
        },

        get totalItems() {
            return this.filteredRows.length;
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.totalItems / this.perPage));
        },

        get startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },

        get endIndex() {
            return Math.min(this.startIndex + this.perPage, this.totalItems);
        },

        updateVisibility() {
            const allRows = this.rows;
            const visibleSet = new Set(this.filteredRows.slice(this.startIndex, this.endIndex));
            
            allRows.forEach(row => {
                row.style.display = visibleSet.has(row) ? '' : 'none';
            });

            const emptyRow = document.querySelector('tr[data-empty]');
            if (emptyRow) {
                emptyRow.style.display = this.totalItems === 0 ? '' : 'none';
            }
        },

        openDetail(data) {
            this.selectedAlumni = data;
            this.showModal = true;
        }
    }" x-init="
        $watch('searchQuery', () => { currentPage = 1; $nextTick(() => updateVisibility()); });
        $watch('statusFilter', () => { currentPage = 1; $nextTick(() => updateVisibility()); });
        $watch('mitraFilter', () => { currentPage = 1; $nextTick(() => updateVisibility()); });
        $watch('tahunFilter', () => { currentPage = 1; $nextTick(() => updateVisibility()); });
        $watch('currentPage', () => { $nextTick(() => updateVisibility()); });
        $watch('perPage', () => { $nextTick(() => updateVisibility()); });
        $nextTick(() => updateVisibility());
    ">

        <!-- Filter Card -->
        <div class="card um-card dk-card" style="margin-bottom: 24px;" x-show="showFilters" x-collapse.duration.300ms>
            <div class="card-header um-header dk-card-header" style="background: var(--surface2);">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-sliders"></i></span>
                    <span>
                        <strong>Filter Data Tracking Lulusan</strong>
                        <small>Saring data alumni berdasarkan mitra, tahun kelulusan, atau status kerja.</small>
                    </span>
                </div>
                <button type="button" class="rfc-btn" @click="resetFilters()"
                    style="font-size: 12px; padding: 6px 14px; background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 8px;">
                    <i class="fas fa-rotate-left"></i> Reset Filter
                </button>
            </div>
            <div class="card-body dk-card-body" style="padding: 20px 24px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                    {{-- Filter Mitra --}}
                    <div class="mc-group">
                        <label class="mc-label">Mitra Industri (DUDIKA)</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-building mc-icon-left" style="color: #4f46e5;"></i>
                            <select x-model="mitraFilter" class="mc-input" style="padding-left: 38px;">
                                <option value="all">Semua Mitra</option>
                                @foreach($uniqueMitraNames as $mName)
                                    <option value="{{ $mName }}">{{ $mName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Filter Tahun Lulus --}}
                    <div class="mc-group">
                        <label class="mc-label">Tahun Lulus</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-calendar-check mc-icon-left" style="color: #059669;"></i>
                            <select x-model="tahunFilter" class="mc-input" style="padding-left: 38px;">
                                <option value="all">Semua Angkatan</option>
                                @foreach($uniqueTahunLulus as $tLulus)
                                    <option value="{{ $tLulus }}">Lulusan {{ $tLulus }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Filter Status --}}
                    <div class="mc-group">
                        <label class="mc-label">Status Penyerapan</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-signal mc-icon-left" style="color: #d97706;"></i>
                            <select x-model="statusFilter" class="mc-input" style="padding-left: 38px;">
                                <option value="all">Semua Status</option>
                                <option value="Aktif">Aktif Bekerja</option>
                                <option value="Resign">Resign</option>
                                <option value="Pensiun">Pensiun / Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card um-card dk-card">
            <div class="card-header um-header dk-card-header">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-users"></i></span>
                    <div>
                        <strong>Daftar Alumni Bekerja di Mitra</strong>
                        <small x-text="`${totalItems} dari {{ $totalAlumni }} alumni terdata`"></small>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    {{-- Search Box --}}
                    <div class="dk-search-box" style="position: relative; min-width: 240px;">
                        <i class="fas fa-search"
                            style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px;"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari nama, NIM, mitra..."
                            style="width: 100%; padding: 8px 14px 8px 36px; border: 1.5px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>

                    {{-- Toggle Filter Button --}}
                    <button type="button" class="rfc-btn" @click="showFilters = !showFilters"
                        :style="showFilters ? 'background: rgba(79,70,229,0.1); color: #4f46e5; border-color: rgba(79,70,229,0.3);' : 'background: var(--surface2); color: var(--text); border: 1px solid var(--border);'"
                        style="padding: 8px 16px; border-radius: 10px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-filter"></i>
                        <span x-text="showFilters ? 'Tutup Filter' : 'Filter Data'"></span>
                    </button>

                    {{-- Tambah Data Button --}}
                    <a href="{{ route('prodi.alumni.create') }}" class="dk-primary-btn"
                        style="text-decoration: none; padding: 8px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Data Alumni</span>
                    </a>
                </div>
            </div>

            <div class="card-body dk-card-body" style="padding: 0;">
                <div class="table-wrap um-table-wrap dk-table-wrap" style="overflow-x: auto;">
                    <table class="um-table dk-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th class="um-th um-th-num" style="width: 50px; text-align: center;">#</th>
                                <th class="um-th" style="min-width: 220px;">Identitas Alumni</th>
                                <th class="um-th" style="min-width: 180px;">Kontak</th>
                                <th class="um-th" style="text-align: center; width: 120px;">Tahun Lulus</th>
                                <th class="um-th" style="min-width: 220px;">Mitra Tempat Bekerja</th>
                                <th class="um-th" style="min-width: 180px;">Posisi &amp; Masa Kerja</th>
                                <th class="um-th" style="text-align: center; width: 130px;">Status</th>
                                <th class="um-th" style="text-align: center; width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-ref="rows">
                            @forelse($alumniList as $alumni)
                                @php
                                    $mitraRelation = $alumni->alumniMitras->first();
                                    $mName = $mitraRelation->mitra->nama_mitra ?? '-';
                                    $statusVal = $mitraRelation->status ?? 'Aktif';
                                    $initials = collect(explode(' ', $alumni->nama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
                                @endphp
                                <tr class="um-row dk-row" data-row
                                    data-status="{{ strtolower($statusVal) }}"
                                    data-mitra="{{ strtolower($mName) }}"
                                    data-tahun="{{ strtolower((string)$alumni->tahun_lulus) }}">
                                    
                                    {{-- 1. Nomor --}}
                                    <td class="um-td um-td-num" style="text-align: center; vertical-align: middle;">
                                        <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>

                                    {{-- 2. Identitas Alumni --}}
                                    <td class="um-td" style="vertical-align: middle;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(79,70,229,0.25);">
                                                {{ strtoupper($initials ?: 'AL') }}
                                            </div>
                                            <div style="min-width: 0;">
                                                <strong style="display: block; font-size: 13px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $alumni->nama }}</strong>
                                                <small style="display: inline-block; font-size: 11px; color: var(--text-sub); font-family: monospace;">NIM: {{ $alumni->nim }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 3. Kontak Alumni --}}
                                    <td class="um-td" style="vertical-align: middle;">
                                        <div style="display: flex; flex-direction: column; gap: 4px; font-size: 12px;">
                                            @if($alumni->telepon)
                                                @php
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $alumni->telepon);
                                                    if (str_starts_with($cleanPhone, '0')) {
                                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                                    }
                                                @endphp
                                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
                                                    style="display: inline-flex; align-items: center; gap: 6px; color: #10b981; text-decoration: none; font-weight: 600;">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span>{{ $alumni->telepon }}</span>
                                                </a>
                                            @endif
                                            @if($alumni->email)
                                                <a href="mailto:{{ $alumni->email }}"
                                                    style="display: inline-flex; align-items: center; gap: 6px; color: var(--text-sub); text-decoration: none; font-size: 11px;">
                                                    <i class="fas fa-envelope"></i>
                                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">{{ $alumni->email }}</span>
                                                </a>
                                            @endif
                                            @if(!$alumni->telepon && !$alumni->email)
                                                <span style="color: var(--text-sub); font-size: 11px;">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 4. Tahun Lulus --}}
                                    <td class="um-td" style="text-align: center; vertical-align: middle;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 8px; background: rgba(79,70,229,0.08); color: #4f46e5; font-size: 12px; font-weight: 700;">
                                            {{ $alumni->tahun_lulus }}
                                        </span>
                                    </td>

                                    {{-- 5. Mitra Tempat Bekerja --}}
                                    <td class="um-td" style="vertical-align: middle;">
                                        @if($mitraRelation && $mitraRelation->mitra)
                                            <div class="dk-entity" style="align-items: center; gap: 8px;">
                                                <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0; width: 28px; height: 28px; font-size: 12px;">
                                                    <i class="fas fa-building"></i>
                                                </span>
                                                <span class="dk-entity-text" style="min-width: 0;">
                                                    <strong style="font-size: 13px; color: var(--text); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $mitraRelation->mitra->nama_mitra }}</strong>
                                                    @if($mitraRelation->mitra->alamat)
                                                        <small style="color: var(--text-sub); font-size: 11px; display: block;">{{ $mitraRelation->mitra->alamat }}</small>
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <span style="color: var(--text-sub);">-</span>
                                        @endif
                                    </td>

                                    {{-- 6. Posisi & Masa Kerja --}}
                                    <td class="um-td" style="vertical-align: middle;">
                                        <div style="line-height: 1.3;">
                                            <strong style="font-size: 13px; color: var(--text); display: block;">{{ $mitraRelation->posisi ?? '-' }}</strong>
                                            @if($mitraRelation && $mitraRelation->tahun_mulai)
                                                <small style="font-size: 11px; color: var(--text-sub); display: block; margin-top: 2px;">
                                                    <i class="fas fa-calendar-alt" style="font-size: 10px; margin-right: 4px;"></i> Mulai {{ $mitraRelation->tahun_mulai }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 7. Status Penyerapan --}}
                                    <td class="um-td" style="text-align: center; vertical-align: middle;">
                                        @if($statusVal === 'Aktif')
                                            <span class="dk-status dk-status-active">
                                                <i class="fas fa-circle-check"></i> Aktif
                                            </span>
                                        @elseif($statusVal === 'Resign')
                                            <span class="dk-status dk-status-warning" style="background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2);">
                                                <i class="fas fa-clock"></i> Resign
                                            </span>
                                        @else
                                            <span class="dk-status dk-status-neutral">
                                                {{ $statusVal }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 8. Aksi --}}
                                    <td class="um-td" style="text-align: center; vertical-align: middle;">
                                        <button type="button" class="rfc-btn"
                                            @click="openDetail({
                                                nama: '{{ addslashes($alumni->nama) }}',
                                                nim: '{{ addslashes($alumni->nim) }}',
                                                tahunLulus: '{{ $alumni->tahun_lulus }}',
                                                telepon: '{{ addslashes($alumni->telepon ?? '-') }}',
                                                email: '{{ addslashes($alumni->email ?? '-') }}',
                                                mitra: '{{ addslashes($mName) }}',
                                                posisi: '{{ addslashes($mitraRelation->posisi ?? '-') }}',
                                                tahunMulai: '{{ $mitraRelation->tahun_mulai ?? '-' }}',
                                                status: '{{ $statusVal }}',
                                                sumberData: '{{ $mitraRelation->sumber_data ?? 'Prodi' }}'
                                            })"
                                            title="Lihat Detail Alumni"
                                            style="padding: 6px 12px; border-radius: 8px; font-size: 12px; background: var(--surface2); color: var(--text); border: 1px solid var(--border); cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr data-empty>
                                    <td colspan="8" class="um-empty">
                                        <div class="um-empty-state dk-empty-state" style="padding: 48px 24px; text-align: center;">
                                            <div class="um-empty-icon dk-empty-icon" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(79,70,229,0.1); color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 16px;">
                                                <i class="fas fa-briefcase"></i>
                                            </div>
                                            <p class="um-empty-title" style="font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px;">Belum Ada Data Alumni Terserap</p>
                                            <p class="um-empty-sub" style="font-size: 13px; color: var(--text-sub); margin-bottom: 16px;">Mulai catat data lulusan yang telah bekerja di mitra industri untuk pemenuhan IKU 1.</p>
                                            <a href="{{ route('prodi.alumni.create') }}" class="dk-primary-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-plus"></i> Tambah Data Alumni
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="dk-pagination"
                    style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-top: 1px solid var(--border); flex-wrap: wrap; gap: 14px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 13px; color: var(--text-sub);"
                            x-text="`Menampilkan ${totalItems > 0 ? startIndex + 1 : 0}-${endIndex} dari ${totalItems} data`"></span>
                        
                        {{-- Per Page Dropdown --}}
                        <div class="alpine-dropdown" @click.outside="perPageOpen = false"
                            style="position: relative; display: inline-block;">
                            <button type="button" class="rfc-btn" @click="perPageOpen = !perPageOpen"
                                style="font-size: 12px; padding: 4px 10px; border-radius: 6px; background: var(--surface2); color: var(--text); border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 6px;">
                                <span x-text="`${perPage} per hal`"></span>
                                <i class="fas fa-chevron-down" style="font-size: 9px;"></i>
                            </button>
                            <div class="ad-menu" x-show="perPageOpen" x-transition
                                style="position: absolute; bottom: calc(100% + 4px); left: 0; min-width: 90px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 100;">
                                <template x-for="opt in perPageOptions" :key="opt">
                                    <div class="ad-item" :class="{'selected': perPage === opt}"
                                        @click="setPerPage(opt)"
                                        style="padding: 6px 12px; font-size: 12px; cursor: pointer;"
                                        x-text="`${opt} per hal`"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Page Navigation Buttons --}}
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <button type="button" class="rfc-btn" :disabled="currentPage <= 1"
                            @click="currentPage = Math.max(1, currentPage - 1)"
                            style="padding: 6px 12px; border-radius: 8px; font-size: 12px; background: var(--surface2); color: var(--text); border: 1px solid var(--border);"
                            :style="currentPage <= 1 ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;'">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <template x-for="page in totalPages" :key="page">
                            <button type="button" class="rfc-btn"
                                x-show="page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)"
                                @click="currentPage = page"
                                :style="currentPage === page ? 'background: #4f46e5; color: #fff; border-color: #4f46e5;' : 'background: var(--surface2); color: var(--text); border: 1px solid var(--border);'"
                                style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;"
                                x-text="page">
                            </button>
                        </template>

                        <button type="button" class="rfc-btn" :disabled="currentPage >= totalPages"
                            @click="currentPage = Math.min(totalPages, currentPage + 1)"
                            style="padding: 6px 12px; border-radius: 8px; font-size: 12px; background: var(--surface2); color: var(--text); border: 1px solid var(--border);"
                            :style="currentPage >= totalPages ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;'">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick View Modal --}}
        <div x-show="showModal" style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;">
            
            <div @click.outside="showModal = false"
                style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 540px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
                
                {{-- Modal Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(124,58,237,0.04));">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 34px; height: 34px; border-radius: 8px; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: var(--text);">Rincian Data Alumni</h3>
                            <small style="color: var(--text-sub); font-size: 11px;">Informasi penyerapan di mitra industri DUDIKA</small>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false"
                        style="background: none; border: none; font-size: 16px; color: var(--text-sub); cursor: pointer; padding: 4px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div style="background: var(--surface2); padding: 12px 16px; border-radius: 10px; border: 1px solid var(--border);">
                            <small style="font-size: 11px; color: var(--text-sub); display: block;">Nama Lengkap</small>
                            <strong style="font-size: 13px; color: var(--text);" x-text="selectedAlumni.nama"></strong>
                        </div>
                        <div style="background: var(--surface2); padding: 12px 16px; border-radius: 10px; border: 1px solid var(--border);">
                            <small style="font-size: 11px; color: var(--text-sub); display: block;">NIM Alumni</small>
                            <strong style="font-size: 13px; color: var(--text);" x-text="selectedAlumni.nim"></strong>
                        </div>
                        <div style="background: var(--surface2); padding: 12px 16px; border-radius: 10px; border: 1px solid var(--border);">
                            <small style="font-size: 11px; color: var(--text-sub); display: block;">Tahun Lulus</small>
                            <strong style="font-size: 13px; color: var(--text);" x-text="selectedAlumni.tahunLulus"></strong>
                        </div>
                        <div style="background: var(--surface2); padding: 12px 16px; border-radius: 10px; border: 1px solid var(--border);">
                            <small style="font-size: 11px; color: var(--text-sub); display: block;">Status Bekerja</small>
                            <span style="font-size: 12px; font-weight: 700; color: #10b981;" x-text="selectedAlumni.status"></span>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 16px;">
                        <h4 style="margin: 0 0 12px 0; font-size: 13px; font-weight: 800; color: var(--text);">
                            <i class="fas fa-building" style="color: #059669; margin-right: 6px;"></i> Penempatan di Mitra Industri
                        </h4>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.6;">
                            <div><strong>Perusahaan:</strong> <span x-text="selectedAlumni.mitra"></span></div>
                            <div><strong>Posisi/Jabatan:</strong> <span x-text="selectedAlumni.posisi"></span></div>
                            <div><strong>Tahun Mulai:</strong> <span x-text="selectedAlumni.tahunMulai"></span></div>
                            <div><strong>Kontak Alumni:</strong> <span x-text="selectedAlumni.telepon"></span> • <span x-text="selectedAlumni.email"></span></div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div style="display: flex; justify-content: flex-end; padding: 14px 24px; border-top: 1px solid var(--border); background: var(--surface2);">
                    <button type="button" class="rfc-btn" @click="showModal = false"
                        style="padding: 8px 18px; border-radius: 8px; font-size: 12px; background: var(--surface); color: var(--text); border: 1px solid var(--border); cursor: pointer;">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

    </div>
</main>
@endsection

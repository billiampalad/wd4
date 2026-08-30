@php
    $user = auth()->user();
    $mitra = $user->mitra;
    $mitraName = $mitra->nama_mitra ?? ($user->name ?? 'Mitra Eksternal');

    $alumniMitras = $alumniMitras ?? collect();

    // Stats
    $totalAlumni = $totalAlumni ?? $alumniMitras->count();
    $aktifCount = $aktifCount ?? $alumniMitras->filter(fn($item) => strtolower($item->status ?? '') === 'aktif')->count();
    $nonAktifCount = $nonAktifCount ?? $alumniMitras->filter(fn($item) => in_array(strtolower($item->status ?? ''), ['resign', 'kontrak selesai', 'pensiun', 'tidak aktif']))->count();
    
    $prodisCovered = $alumniMitras->map(fn($item) => $item->alumni?->prodi?->nama_prodi)->filter()->unique()->values();
    $totalProdiCount = $totalProdiCount ?? $prodisCovered->count();

    $availableProdis = $availableProdis ?? \App\Models\Prodi::orderBy('nama_prodi')->get();
    $availableYears = $availableYears ?? $alumniMitras->map(fn($item) => $item->alumni?->tahun_lulus)->filter()->unique()->sortDesc()->values();
    $masterAlumnis = $masterAlumnis ?? \App\Models\Alumni::with('prodi')->orderBy('nama')->get();
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<main id="mainContent" class="dk-page" x-data="mitraTrackingApp()" x-cloak>

    {{-- ═══ TOPBAR / HERO SECTION ═══ --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Tracking Lulusan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-briefcase"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Tracking &amp; Penyerapan Alumni POLIMDO</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Kelola dan verifikasi data alumni Politeknik Negeri Manado yang berkarir di instansi <strong>{{ $mitraName }}</strong> guna mendukung capaian IKU 1.
                    </p>
                </div>
            </div>
        </div>
        <div class="ud-hero-actions" style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
            <button type="button" @click="openCreateModal()" class="dk-primary-btn">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Data Alumni</span>
            </button>
        </div>
    </section>

    {{-- ═══ 4 KARTU STATISTIK (KPI CARDS) ═══ --}}
    <section class="dk-stats-grid" aria-label="Ringkasan data tracking alumni">
        {{-- Card 1: Total Alumni --}}
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-user-tie"></i></div>
            <div>
                <span class="dk-stat-label">Total Alumni Tercatat</span>
                <strong>{{ number_format($totalAlumni) }}</strong>
            </div>
        </div>

        {{-- Card 2: Karyawan Aktif --}}
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Karyawan Aktif</span>
                <strong>{{ number_format($aktifCount) }}</strong>
            </div>
        </div>

        {{-- Card 3: Resign / Selesai Kontrak --}}
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-user-clock"></i></div>
            <div>
                <span class="dk-stat-label">Resign / Selesai Kontrak</span>
                <strong>{{ number_format($nonAktifCount) }}</strong>
            </div>
        </div>

        {{-- Card 4: Cakupan Program Studi --}}
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #0284c7;">
            <div class="dk-stat-icon" style="color: #0284c7; background: rgba(2,132,199,0.1);">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <span class="dk-stat-label">Cakupan Program Studi</span>
                <strong>{{ number_format($totalProdiCount) }}</strong>
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
                    <h3>Filter Data Alumni</h3>
                    <p>Saring data alumni berdasarkan program studi asal, status kerja, periode kelulusan, atau pencarian nama</p>
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
                {{-- 1. Filter Program Studi --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Program Studi' },
                        @foreach($availableProdis as $prodi)
                            { id: '{{ strtolower($prodi->nama_prodi) }}', label: '{{ $prodi->nama_prodi }}' },
                        @endforeach
                    ],
                    get selectedLabel() {
                        const item = this.items.find(i => i.id === prodiFilter);
                        return item ? item.label : 'Semua Program Studi';
                    }
                }">
                    <label>Program Studi</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-graduation-cap" style="color: #9ca3af; font-size: 13px;"></i>
                                <span x-text="selectedLabel"></span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                :style="open ? 'transform: rotate(180deg)' : ''"></i>
                        </div>
                        <div class="ad-menu" x-show="open" x-transition x-cloak>
                            <template x-for="item in items" :key="item.id">
                                <div class="ad-item" :class="{ 'selected': prodiFilter === item.id }"
                                    @click="prodiFilter = item.id; open = false" x-text="item.label"></div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 2. Filter Status Kerja --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Status Kerja' },
                        { id: 'aktif', label: 'Aktif Bekerja' },
                        { id: 'kontrak selesai', label: 'Kontrak Selesai' },
                        { id: 'resign', label: 'Resign' },
                        { id: 'pensiun', label: 'Pensiun' }
                    ],
                    get selectedLabel() {
                        const item = this.items.find(i => i.id === statusFilter);
                        return item ? item.label : 'Semua Status Kerja';
                    }
                }">
                    <label>Status Kerja</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-briefcase" style="color: #9ca3af; font-size: 13px;"></i>
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

                {{-- 3. Filter Periode Tahun Kelulusan --}}
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
                    <label>Tahun Kelulusan</label>
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
                    <label>Cari Alumni</label>
                    <div class="rfc-input-wrap">
                        <i class="fas fa-search rfc-input-icon"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari nama / NIM / posisi..."
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

    {{-- ═══ CARD TABEL ALUMNI MITRA ═══ --}}
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-briefcase"></i></span>
                <span>
                    <strong>Daftar Alumni POLIMDO di Instansi Anda</strong>
                    <small id="alumniCount">{{ $alumniMitras->count() }} data ditemukan</small>
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
                            <th class="um-th dk-th-title" style="width: 340px; min-width: 250px;">Mahasiswa &amp; Alumni</th>
                            <th class="um-th" style="min-width: 220px;">Posisi &amp; Jabatan</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berkarir</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody x-ref="rows">
                        @forelse($alumniMitras as $index => $item)
                            @php
                                $alumni = $item->alumni;
                                $prodiName = $alumni?->prodi?->nama_prodi ?? 'Program Studi';
                                $statusStr = strtolower($item->status ?? 'aktif');
                                
                                $statusClass = match(true) {
                                    $statusStr === 'aktif' => 'dk-status-active',
                                    $statusStr === 'kontrak selesai' || $statusStr === 'pensiun' => 'dk-status-warning',
                                    $statusStr === 'resign' => 'dk-status-danger',
                                    default => 'dk-status-muted'
                                };

                                $statusIcon = match(true) {
                                    $statusStr === 'aktif' => 'fa-circle-check',
                                    $statusStr === 'kontrak selesai' || $statusStr === 'pensiun' => 'fa-clock',
                                    $statusStr === 'resign' => 'fa-circle-xmark',
                                    default => 'fa-circle-info'
                                };

                                $statusLabel = match(true) {
                                    $statusStr === 'aktif' => 'Aktif Bekerja',
                                    $statusStr === 'kontrak selesai' => 'Kontrak Selesai',
                                    $statusStr === 'resign' => 'Resign',
                                    $statusStr === 'pensiun' => 'Pensiun',
                                    default => ucwords($statusStr)
                                };

                                $tahunMulai = (int) $item->tahun_mulai;
                                $currentYear = (int) date('Y');
                                $durasiTahun = max(1, $currentYear - $tahunMulai + 1);
                            @endphp
                            <tr data-row="true"
                                class="um-row dk-row"
                                data-nim="{{ strtolower($alumni?->nim ?? '') }}"
                                data-nama="{{ strtolower($alumni?->nama ?? '') }}"
                                data-prodi="{{ strtolower($prodiName) }}"
                                data-posisi="{{ strtolower($item->posisi ?? '') }}"
                                data-status="{{ $statusStr }}"
                                data-tahun="{{ $alumni?->tahun_lulus ?? '' }}"
                                x-show="isRowVisible($el)">

                                {{-- Col 1: Index Number --}}
                                <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                    <span class="um-num dk-num" x-text="rowNumber($el)">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                {{-- Col 2: Identitas Alumni --}}
                                <td class="um-td dk-title-cell" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-doc-cell">
                                        <span class="dk-doc-number">#{{ $alumni?->nim ?? '-' }}</span>
                                        <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5;">{{ $alumni?->nama ?? '-' }}</span>
                                        <span class="dk-doc-kind">{{ $prodiName }} • Lulus {{ $alumni?->tahun_lulus ?? '-' }}</span>
                                    </div>
                                </td>

                                {{-- Col 3: Posisi & Jabatan --}}
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-entity" style="align-items: flex-start;">
                                        <span class="dk-entity-icon dk-entity-indigo" style="flex-shrink: 0;">
                                            <i class="fas fa-briefcase"></i>
                                        </span>
                                        <span class="dk-entity-text" style="padding-top: 2px;">
                                            <strong style="display: block; font-size: 13px; color: var(--text);">{{ $item->posisi }}</strong>
                                            <small style="display: block; font-size: 11px; color: var(--text-sub); margin-top: 2px;">
                                                Sumber: {{ $item->sumber_data ?? 'Mitra' }}
                                            </small>
                                        </span>
                                    </div>
                                </td>

                                {{-- Col 4: Masa Berkarir --}}
                                <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-date-range-compact">
                                        <span class="date-val">Sejak {{ $item->tahun_mulai }}</span>
                                        <span class="date-sep">&bull;</span>
                                        <span class="date-val">{{ $durasiTahun }} Thn</span>
                                    </div>
                                </td>

                                {{-- Col 5: Status --}}
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <span class="dk-status {{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }}"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Col 6: Aksi --}}
                                <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                    <div class="um-actions dk-actions-compact">
                                        {{-- Action: Edit Status (Warna Kuning Amber) --}}
                                        <button type="button"
                                            @click="openEditModal({
                                                id: '{{ $item->id }}',
                                                nama: '{{ addslashes($alumni?->nama ?? '') }}',
                                                nim: '{{ addslashes($alumni?->nim ?? '') }}',
                                                prodi: '{{ addslashes($prodiName) }}',
                                                tahunLulus: '{{ $alumni?->tahun_lulus ?? '' }}',
                                                posisi: '{{ addslashes($item->posisi ?? '') }}',
                                                tahunMulai: '{{ $item->tahun_mulai ?? '' }}',
                                                status: '{{ $item->status ?? 'Aktif' }}'
                                            })"
                                            class="dk-action-btn edit"
                                            title="Ubah Status &amp; Posisi"
                                            style="color: #d97706; background: rgba(217, 119, 6, 0.12); border: 1px solid rgba(217, 119, 6, 0.25); cursor: pointer;">
                                            <i class="fas fa-pen-to-square"></i>
                                        </button>

                                        {{-- Action: Detail Alumni (Warna Biru Indigo) --}}
                                        <button type="button"
                                            @click="openDetailModal({
                                                id: '{{ $item->id }}',
                                                nama: '{{ addslashes($alumni?->nama ?? '') }}',
                                                nim: '{{ addslashes($alumni?->nim ?? '') }}',
                                                prodi: '{{ addslashes($prodiName) }}',
                                                jurusan: '{{ addslashes($alumni?->prodi?->jurusan?->nama_jurusan ?? '-') }}',
                                                tahunLulus: '{{ $alumni?->tahun_lulus ?? '-' }}',
                                                email: '{{ addslashes($alumni?->email ?? '-') }}',
                                                telepon: '{{ addslashes($alumni?->telepon ?? '-') }}',
                                                posisi: '{{ addslashes($item->posisi ?? '') }}',
                                                tahunMulai: '{{ $item->tahun_mulai ?? '-' }}',
                                                durasi: '{{ $durasiTahun }} Tahun',
                                                status: '{{ $item->status ?? 'Aktif' }}',
                                                sumber: '{{ $item->sumber_data ?? 'Mitra' }}'
                                            })"
                                            class="dk-action-btn view"
                                            title="Lihat Detail Alumni"
                                            style="color: #4f46e5; background: rgba(79, 70, 229, 0.1); border: 1px solid rgba(79, 70, 229, 0.2); cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- Action: Hapus Relasi --}}
                                        <button type="button"
                                            @click="confirmDelete('{{ route('mitra.alumni.destroy', $item->id) }}', '{{ addslashes($alumni?->nama ?? '') }}')"
                                            class="dk-action-btn"
                                            title="Hapus Data Alumni dari Instansi"
                                            style="color: #ef4444; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); cursor: pointer;">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada data alumni tercatat</p>
                                        <p class="um-empty-sub">Klik tombol "Tambah Data Alumni" di atas untuk menambahkan lulusan POLIMDO yang berkarir di instansi Anda.</p>
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
                    <strong x-text="totalFiltered">{{ $alumniMitras->count() }}</strong> data
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

    {{-- ═══ 1. MODAL TAMBAH DATA ALUMNI (ALPINE.JS) ═══ --}}
    <template x-if="createModalOpen">
        <div class="modal-overlay"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
            @click.self="createModalOpen = false" x-cloak>
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
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">Tambah Data Alumni di Mitra</h3>
                            <p style="margin: 3px 0 0 0; font-size: 12px; color: var(--text-sub);">Catat lulusan POLIMDO yang berkarir di instansi {{ $mitraName }}</p>
                        </div>
                    </div>
                    <button type="button" @click="createModalOpen = false"
                        style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 16px; transition: 0.2s; border-radius: 8px;"
                        onmouseover="this.style.color='#ef4444'; this.style.background='rgba(239,68,68,0.1)'"
                        onmouseout="this.style.color='var(--text-sub)'; this.style.background='transparent'"
                        title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form action="{{ route('mitra.alumni.store') }}" method="POST" id="createAlumniForm" @submit.prevent="submitCreate($event)"
                    style="display: flex; flex-direction: column; overflow: hidden; flex: 1; min-height: 0; background: var(--surface);">
                    @csrf

                    <div style="display: flex; flex-direction: column; gap: 18px; padding: 24px; overflow-y: auto; flex: 1; min-height: 0; background: var(--surface);">
                        
                        {{-- Mode Selector Tabs --}}
                        <div class="tr-modal-tabs" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; background: var(--surface2); padding: 6px; border-radius: 12px; border: 1px solid var(--border);">
                            <button type="button" class="tr-modal-tab-btn" :class="{ 'active': createMode === 'select' }" @click="createMode = 'select'"
                                style="padding: 10px 14px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-address-book"></i>
                                <span>Pilih Dari Database POLIMDO</span>
                            </button>
                            <button type="button" class="tr-modal-tab-btn" :class="{ 'active': createMode === 'new' }" @click="createMode = 'new'"
                                style="padding: 10px 14px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-user-pen"></i>
                                <span>Input Alumni Baru</span>
                            </button>
                        </div>

                        {{-- Mode 1: Select Existing Alumni --}}
                        <div x-show="createMode === 'select'" style="display: flex; flex-direction: column; gap: 14px;">
                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    Pilih Alumni Terdaftar <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="alumni_id" class="rfc-input"
                                    @change="onAlumniSelectChange($event, {{ json_encode($masterAlumnis) }})"
                                    :required="createMode === 'select'"
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface2); color: var(--text); font-size: 13px; font-weight: 600;">
                                    <option value="">-- Pilih Nama Alumni / NIM --</option>
                                    @foreach($masterAlumnis as $mAlumni)
                                        <option value="{{ $mAlumni->id }}">
                                            {{ $mAlumni->nama }} ({{ $mAlumni->nim }}) — {{ $mAlumni->prodi?->nama_prodi ?? 'Prodi' }} [Lulus {{ $mAlumni->tahun_lulus }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Selected Alumni Info Preview --}}
                            <template x-if="selectedAlumniInfo">
                                <div style="padding: 12px 16px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                    <div>
                                        <strong style="color: var(--text); font-size: 14px;" x-text="selectedAlumniInfo.nama"></strong>
                                        <div style="font-size: 12px; color: var(--text-sub); margin-top: 2px;">
                                            NIM: <span class="dk-doc-number" x-text="selectedAlumniInfo.nim"></span> &bull; 
                                            <span x-text="selectedAlumniInfo.prodi ? selectedAlumniInfo.prodi.nama_prodi : '-'"></span>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <span style="font-size: 11px; color: var(--text-sub); display: block;">Tahun Kelulusan:</span>
                                        <span style="font-size: 12px; font-weight: 700; color: #4f46e5;" x-text="'Lulus ' + selectedAlumniInfo.tahun_lulus"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Mode 2: Input New Alumni Record --}}
                        <div x-show="createMode === 'new'" style="display: flex; flex-direction: column; gap: 14px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        NIM Alumni <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="text" name="nim" class="rfc-input" placeholder="Misal: 21021001" :required="createMode === 'new'"
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        Nama Lengkap <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="text" name="nama" class="rfc-input" placeholder="Nama lengkap alumni" :required="createMode === 'new'"
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        Program Studi <span style="color: #ef4444;">*</span>
                                    </label>
                                    <select name="prodi_id" class="rfc-input" :required="createMode === 'new'"
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                        <option value="">-- Pilih Program Studi --</option>
                                        @foreach($availableProdis as $prodi)
                                            <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        Tahun Kelulusan <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="number" name="tahun_lulus" class="rfc-input" placeholder="YYYY (Contoh: {{ date('Y') - 1 }})" min="2000" max="{{ date('Y') }}" :required="createMode === 'new'"
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        Email Kontak
                                    </label>
                                    <input type="email" name="email" class="rfc-input" placeholder="alumni@email.com"
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        Nomor WhatsApp / Telepon
                                    </label>
                                    <input type="text" name="telepon" class="rfc-input" placeholder="08xxxxxxxxxx"
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                </div>
                            </div>
                        </div>

                        {{-- Data Karir di Mitra --}}
                        <div style="padding-top: 14px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 14px;">
                            <label style="font-size: 13px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-building-user" style="color: #4f46e5;"></i>
                                Informasi Penempatan Kerja di {{ $mitraName }}
                            </label>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        Posisi / Jabatan Pekerjaan <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="text" name="posisi" class="rfc-input" placeholder="Contoh: Frontend Engineer / Staff IT" required
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                        Tahun Mulai Bekerja <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="number" name="tahun_mulai" class="rfc-input" placeholder="YYYY (Contoh: {{ date('Y') }})" value="{{ date('Y') }}" min="2000" max="{{ date('Y') + 1 }}" required
                                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                </div>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    Status Kerja Karyawan <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="status" class="rfc-input" required
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                    <option value="Aktif">Aktif Bekerja</option>
                                    <option value="Kontrak Selesai">Kontrak Selesai</option>
                                    <option value="Resign">Resign</option>
                                    <option value="Pensiun">Pensiun</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--surface2); border-radius: 0 0 24px 24px; flex-shrink: 0;">
                        <button type="button" class="rfc-btn" @click="createModalOpen = false"
                            style="padding: 9px 18px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer;">
                            Batal
                        </button>
                        <button type="submit" class="dk-primary-btn" :disabled="isSubmitting">
                            <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Data Alumni'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ═══ 2. MODAL UPDATE STATUS & POSISI (ALPINE.JS) ═══ --}}
    <template x-if="editModalOpen">
        <div class="modal-overlay"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
            @click.self="editModalOpen = false" x-cloak>
            <div class="modal-card"
                style="background: var(--surface); border-radius: 24px; width: 100%; max-width: 620px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35); border: 1px solid var(--border);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                
                {{-- Modal Header --}}
                <div style="padding: 20px 28px; border-bottom: 1px solid var(--border); background: var(--surface2); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(217,119,6,0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="fas fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">Perbarui Status &amp; Posisi Alumni</h3>
                            <p style="margin: 3px 0 0 0; font-size: 12px; color: var(--text-sub);">Perbarui jabatan atau status penyerapan alumni di instansi Anda</p>
                        </div>
                    </div>
                    <button type="button" @click="editModalOpen = false"
                        style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 16px; transition: 0.2s; border-radius: 8px;"
                        onmouseover="this.style.color='#ef4444'; this.style.background='rgba(239,68,68,0.1)'"
                        onmouseout="this.style.color='var(--text-sub)'; this.style.background='transparent'"
                        title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form :action="'/mitra/alumni/' + editItem.id" method="POST" id="editAlumniForm" @submit.prevent="submitEdit($event)"
                    style="display: flex; flex-direction: column; overflow: hidden; flex: 1; min-height: 0; background: var(--surface);">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; flex-direction: column; gap: 18px; padding: 24px; overflow-y: auto; flex: 1; min-height: 0; background: var(--surface);">
                        
                        {{-- Mini Profile Banner --}}
                        <div style="padding: 12px 16px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <strong style="color: var(--text); font-size: 14px;" x-text="editItem.nama"></strong>
                                <div style="font-size: 12px; color: var(--text-sub); margin-top: 2px;">
                                    NIM: <span class="dk-doc-number" x-text="editItem.nim"></span> &bull; 
                                    <span x-text="editItem.prodi"></span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 11px; color: var(--text-sub); display: block;">Kelulusan:</span>
                                <span style="font-size: 12px; font-weight: 700; color: #d97706;" x-text="'Lulus ' + editItem.tahunLulus"></span>
                            </div>
                        </div>

                        {{-- Posisi & Tahun Mulai --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    Posisi / Jabatan Saat Ini <span style="color: #ef4444;">*</span>
                                </label>
                                <input type="text" name="posisi" x-model="editItem.posisi" class="rfc-input" required
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                    Tahun Mulai Bekerja <span style="color: #ef4444;">*</span>
                                </label>
                                <input type="number" name="tahun_mulai" x-model="editItem.tahunMulai" class="rfc-input" min="2000" max="{{ date('Y') + 1 }}" required
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                            </div>
                        </div>

                        {{-- Status Kerja --}}
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                                Status Karyawan <span style="color: #ef4444;">*</span>
                            </label>
                            <select name="status" x-model="editItem.status" class="rfc-input" required
                                style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                                <option value="Aktif">Aktif Bekerja</option>
                                <option value="Kontrak Selesai">Kontrak Selesai</option>
                                <option value="Resign">Resign</option>
                                <option value="Pensiun">Pensiun</option>
                            </select>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--surface2); border-radius: 0 0 24px 24px; flex-shrink: 0;">
                        <button type="button" class="rfc-btn" @click="editModalOpen = false"
                            style="padding: 9px 18px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer;">
                            Batal
                        </button>
                        <button type="submit" class="dk-primary-btn" :disabled="isSubmitting"
                            style="background: #d97706;">
                            <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Perbarui Status'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ═══ 3. MODAL DETAIL LENGKAP ALUMNI (ALPINE.JS) ═══ --}}
    <template x-if="detailModalOpen">
        <div class="modal-overlay"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
            @click.self="detailModalOpen = false" x-cloak>
            <div class="modal-card"
                style="background: var(--surface); border-radius: 24px; width: 100%; max-width: 580px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35); border: 1px solid var(--border);"
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
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">Detail Profil Alumni</h3>
                            <p style="margin: 3px 0 0 0; font-size: 12px; color: var(--text-sub);">Informasi akademik &amp; kontak alumni POLIMDO</p>
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
                    
                    {{-- Profile Header Card --}}
                    <div class="dk-entity" style="padding: 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px;">
                        <span class="dk-entity-icon dk-entity-indigo" style="width: 44px; height: 44px; font-size: 16px; font-weight: 800;" x-text="detailItem.nama ? detailItem.nama.charAt(0) : 'A'"></span>
                        <div class="dk-entity-text">
                            <strong style="font-size: 15px;" x-text="detailItem.nama"></strong>
                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                                <span class="dk-doc-number" x-text="detailItem.nim"></span>
                                <span style="font-size: 12px; color: var(--text-sub);" x-text="detailItem.prodi + ' • Lulus ' + detailItem.tahunLulus"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Grid --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div style="padding: 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">Posisi / Jabatan:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="detailItem.posisi"></span>
                        </div>
                        <div style="padding: 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">Masa Berkarir:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="'Sejak ' + detailItem.tahunMulai + ' (' + detailItem.durasi + ')'"></span>
                        </div>
                        <div style="padding: 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">Email Kontak:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="detailItem.email"></span>
                        </div>
                        <div style="padding: 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block; text-transform: uppercase;">WhatsApp / Telepon:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="detailItem.telepon"></span>
                        </div>
                    </div>

                    {{-- Status Banner --}}
                    <div style="padding: 14px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; display: block;">Status Karyawan:</span>
                            <strong style="font-size: 13px; color: var(--text);" x-text="detailItem.status"></strong>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; display: block;">Sumber Pencatatan:</span>
                            <span class="dk-status dk-status-active" x-text="detailItem.sumber"></span>
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

{{-- ═══ EXTERNAL ALPINE.JS APPLICATION LOGIC ═══ --}}
<script src="{{ asset('js/auth/mitra/tracking.js') }}"></script>

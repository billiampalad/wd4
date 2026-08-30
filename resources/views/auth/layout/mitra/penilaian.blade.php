@php
    $user = auth()->user();
    $mitra = $user->mitra;
    $mitraName = $mitra->nama_mitra ?? ($user->name ?? 'Mitra Eksternal');

    $penempatans = $penempatans ?? collect();

    // Stats
    $totalMahasiswa = $totalMahasiswa ?? $penempatans->count();
    $aktifCount = $aktifCount ?? $penempatans->filter(fn($item) => in_array(strtolower($item->status ?? ''), ['aktif', 'berjalan']))->count();
    $belumDinilaiCount = $belumDinilaiCount ?? $penempatans->filter(fn($item) => is_null($item->nilai_mitra))->count();
    $sudahDinilaiCount = $sudahDinilaiCount ?? $penempatans->filter(fn($item) => !is_null($item->nilai_mitra))->count();
    $avgNilai = $avgNilai ?? ($sudahDinilaiCount > 0 ? round($penempatans->whereNotNull('nilai_mitra')->avg('nilai_mitra'), 1) : 0);

    $availableProdis = $availableProdis ?? $penempatans->map(fn($item) => $item->mahasiswa?->prodi?->nama_prodi)->filter()->unique()->values();
    $availableYears = $availableYears ?? $penempatans->map(function ($item) {
        if ($item->periode_mulai) {
            return $item->periode_mulai instanceof \Carbon\Carbon 
                ? $item->periode_mulai->format('Y') 
                : substr((string)$item->periode_mulai, 0, 4);
        }
        return null;
    })->filter()->unique()->sortDesc()->values();
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<main id="mainContent" class="dk-page" x-data="mitraPenilaianApp()" x-cloak>

    {{-- ═══ TOPBAR / HERO SECTION ═══ --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Kegiatan &amp; Magang</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-user-graduate"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Kegiatan &amp; Penilaian Magang</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Pantau penempatan mahasiswa, evaluasi perkembangan magang, dan berikan penilaian industri secara berkala untuk
                        <strong>{{ $mitraName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ 4 KARTU STATISTIK (KPI CARDS) ═══ --}}
    <section class="dk-stats-grid" aria-label="Ringkasan data mahasiswa magang">
        {{-- Card 1: Total Mahasiswa --}}
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div>
                <span class="dk-stat-label">Total Peserta Magang</span>
                <strong>{{ number_format($totalMahasiswa) }}</strong>
            </div>
        </div>

        {{-- Card 2: Sedang Magang (Aktif) --}}
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Sedang Magang (Aktif)</span>
                <strong>{{ number_format($aktifCount) }}</strong>
            </div>
        </div>

        {{-- Card 3: Menunggu Penilaian --}}
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-clipboard-question"></i></div>
            <div>
                <span class="dk-stat-label">Menunggu Penilaian</span>
                <strong>{{ number_format($belumDinilaiCount) }}</strong>
            </div>
        </div>

        {{-- Card 4: Rata-rata Skor Penilaian --}}
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #8b5cf6;">
            <div class="dk-stat-icon" style="color: #8b5cf6; background: rgba(139,92,246,0.1);">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <span class="dk-stat-label">Rata-rata Skor Industri</span>
                <strong>{{ $avgNilai > 0 ? number_format($avgNilai, 1) : '-' }}</strong>
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
                    <h3>Filter Data Peserta Magang</h3>
                    <p>Saring data mahasiswa berdasarkan program studi, status penilaian industri, atau periode pelaksanaan</p>
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
                            { id: '{{ $prodi }}', label: '{{ $prodi }}' },
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

                {{-- 2. Filter Status Penilaian --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Status Penilaian' },
                        { id: 'belum_dinilai', label: 'Belum Dinilai' },
                        { id: 'sudah_dinilai', label: 'Sudah Dinilai' },
                        { id: 'aktif', label: 'Sedang Berjalan (Aktif)' }
                    ],
                    get selectedLabel() {
                        const item = this.items.find(i => i.id === statusFilter);
                        return item ? item.label : 'Semua Status Penilaian';
                    }
                }">
                    <label>Status Penilaian</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-clipboard-check" style="color: #9ca3af; font-size: 13px;"></i>
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

                {{-- 3. Filter Periode / Tahun --}}
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
                    <label>Periode / Tahun</label>
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
                    <label>Cari Mahasiswa</label>
                    <div class="rfc-input-wrap">
                        <i class="fas fa-search rfc-input-icon"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari nama / NIM / program..."
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

    {{-- ═══ CARD TABEL MAHASISWA & PENILAIAN ═══ --}}
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-list-check"></i></span>
                <span>
                    <strong>Daftar Mahasiswa &amp; Lembar Penilaian</strong>
                    <small id="penilaianCount">{{ $penempatans->count() }} data ditemukan</small>
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

        {{-- Table Container --}}
        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 380px; min-width: 260px;">Mahasiswa</th>
                            <th class="um-th">Program &amp; Kegiatan</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Magang</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody x-ref="rows">
                        @forelse($penempatans as $index => $item)
                            @php
                                $mhs = $item->mahasiswa;
                                $prodiName = $mhs?->prodi?->nama_prodi ?? 'Program Studi';
                                $kegiatanName = $item->kegiatan?->nama_kegiatan ?? 'Program Magang Industri';
                                $hasScore = !is_null($item->nilai_mitra);
                                $score = (float) $item->nilai_mitra;
                                $grade = match(true) {
                                    $score >= 85 => 'A',
                                    $score >= 75 => 'B+',
                                    $score >= 65 => 'B',
                                    $score >= 55 => 'C',
                                    default => 'D'
                                };

                                $dosen = $item->pembimbings->where('tipe', 'Internal')->first();
                                $mentor = $item->pembimbings->where('tipe', 'Eksternal')->first();

                                $mulai = $item->periode_mulai ? \Carbon\Carbon::parse($item->periode_mulai)->format('d M Y') : '-';
                                $selesai = $item->periode_selesai ? \Carbon\Carbon::parse($item->periode_selesai)->format('d M Y') : '-';
                                $tahunStr = $item->periode_mulai ? \Carbon\Carbon::parse($item->periode_mulai)->format('Y') : '';
                                $isAktif = strtolower($item->status ?? '') === 'aktif';

                                $statusCategory = $hasScore ? 'sudah_dinilai' : ($isAktif ? 'aktif' : 'belum_dinilai');
                                $statusClass = $hasScore ? 'dk-status-active' : ($isAktif ? 'dk-status-warning' : 'dk-status-muted');
                                $statusIcon = $hasScore ? 'fa-circle-check' : ($isAktif ? 'fa-clock' : 'fa-circle-info');
                                $statusLabel = $hasScore ? number_format($score, 1) . " ($grade)" : ($isAktif ? 'Magang Aktif' : 'Belum Dinilai');
                            @endphp
                            <tr data-row="true"
                                class="um-row dk-row"
                                data-nim="{{ strtolower($mhs?->nim ?? '') }}"
                                data-nama="{{ strtolower($mhs?->nama ?? '') }}"
                                data-prodi="{{ strtolower($prodiName) }}"
                                data-kegiatan="{{ strtolower($kegiatanName) }}"
                                data-status="{{ $statusCategory }}"
                                data-tahun="{{ $tahunStr }}"
                                x-show="isRowVisible($el)">
                                
                                {{-- Col 1: Index Number --}}
                                <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                    <span class="um-num dk-num" x-text="rowNumber($el)">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                {{-- Col 2: Mahasiswa Identity --}}
                                <td class="um-td dk-title-cell" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-doc-cell">
                                        <span class="dk-doc-number">#{{ $mhs?->nim ?? '-' }}</span>
                                        <span class="dk-doc-title"
                                            style="font-weight: 700; line-height: 1.5;">{{ $mhs?->nama ?? '-' }}</span>
                                        <span class="dk-doc-kind">{{ $prodiName }} {{ $mhs?->angkatan ? '• Angkatan ' . $mhs->angkatan : '' }}</span>
                                    </div>
                                </td>

                                {{-- Col 3: Program & Kegiatan --}}
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <div style="display: grid; gap: 8px;">
                                        <div class="dk-entity" style="align-items: flex-start;">
                                            <span class="dk-entity-icon dk-entity-indigo" style="flex-shrink: 0;">
                                                <i class="fas fa-user-graduate"></i>
                                            </span>
                                            <span class="dk-entity-text" style="padding-top: 2px;">
                                                <small
                                                    style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--text-sub); margin-bottom:2px;">Kegiatan Magang</small>
                                                {{ $kegiatanName }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Col 4: Periode Magang --}}
                                <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-date-range-compact">
                                        <span class="date-val">{{ $mulai }}</span>
                                        <span class="date-sep">s/d</span>
                                        <span class="date-val">{{ $selesai }}</span>
                                    </div>
                                </td>

                                {{-- Col 5: Status Nilai --}}
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <span class="dk-status {{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }}"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Col 6: Aksi --}}
                                <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                    <div class="um-actions dk-actions-compact">
                                        {{-- Action: Penilaian Modal --}}
                                        <button type="button"
                                            @click="openGradingModal({
                                                id: '{{ $item->id }}',
                                                nim: '{{ addslashes($mhs?->nim ?? '') }}',
                                                nama: '{{ addslashes($mhs?->nama ?? '') }}',
                                                prodi: '{{ addslashes($prodiName) }}',
                                                angkatan: '{{ addslashes($mhs?->angkatan ?? '') }}',
                                                kegiatan: '{{ addslashes($kegiatanName) }}',
                                                dosen: '{{ addslashes($dosen?->nama_pembimbing ?? '-') }}',
                                                mentor: '{{ addslashes($mentor?->nama_pembimbing ?? $mitraName) }}',
                                                periode: '{{ addslashes($mulai . ' - ' . $selesai) }}',
                                                nilai: {{ $score ? $score : 85 }},
                                                catatan: '{{ addslashes($item->catatan_mitra ?? '') }}',
                                                hasScore: {{ $hasScore ? 'true' : 'false' }}
                                            })"
                                            class="dk-action-btn edit"
                                            title="{{ $hasScore ? 'Ubah Penilaian' : 'Beri Penilaian Mahasiswa' }}"
                                            style="color: #4f46e5; background: rgba(79,70,229,0.1); border:none; cursor:pointer;">
                                            <i class="fas {{ $hasScore ? 'fa-pen-to-square' : 'fa-clipboard-check' }}"></i>
                                        </button>

                                        {{-- Action: Detail Modal --}}
                                        <button type="button"
                                            @click="openDetailModal({
                                                id: '{{ $item->id }}',
                                                nim: '{{ addslashes($mhs?->nim ?? '') }}',
                                                nama: '{{ addslashes($mhs?->nama ?? '') }}',
                                                email: '{{ addslashes($mhs?->email ?? '-') }}',
                                                telepon: '{{ addslashes($mhs?->telepon ?? '-') }}',
                                                prodi: '{{ addslashes($prodiName) }}',
                                                angkatan: '{{ addslashes($mhs?->angkatan ?? '') }}',
                                                kegiatan: '{{ addslashes($kegiatanName) }}',
                                                cooperation: '{{ addslashes($item->kegiatan?->cooperation?->judul ?? 'Kerjasama POLIMDO') }}',
                                                dosen: '{{ addslashes($dosen?->nama_pembimbing ?? '-') }}',
                                                dosenKontak: '{{ addslashes($dosen?->kontak ?? '-') }}',
                                                mentor: '{{ addslashes($mentor?->nama_pembimbing ?? $mitraName) }}',
                                                mentorKontak: '{{ addslashes($mentor?->kontak ?? '-') }}',
                                                periode: '{{ addslashes($mulai . ' - ' . $selesai) }}',
                                                status: '{{ $item->status ?? 'Aktif' }}',
                                                nilai: '{{ $hasScore ? number_format($score, 1) : '-' }}',
                                                grade: '{{ $hasScore ? $grade : '-' }}',
                                                catatan: '{{ addslashes($item->catatan_mitra ?? 'Belum ada catatan evaluasi.') }}'
                                            })"
                                            class="dk-action-btn view"
                                            title="Lihat Detail Penempatan"
                                            style="border:none; cursor:pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada mahasiswa magang</p>
                                        <p class="um-empty-sub">Saat ini belum ada mahasiswa yang ditempatkan di instansi Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Showing & Pagination Footer --}}
            <div class="table-pagination-controls" x-show="totalFiltered > 0" x-cloak>
                <div class="pagination-info">
                    Menampilkan <strong x-text="startRange">0</strong> sampai <strong x-text="endRange">0</strong> dari
                    <strong x-text="totalFiltered">{{ $penempatans->count() }}</strong> data
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

    {{-- ═══ MODAL PENILAIAN MAHASISWA (ALPINE.JS) ═══ --}}
    <template x-if="gradingModalOpen">
        <div class="review-modal-overlay" @click.self="gradingModalOpen = false" x-cloak>
            <div class="review-modal-card" style="max-width: 680px;"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                
                {{-- Modal Header --}}
                <div class="review-modal-header">
                    <div class="review-modal-title">
                        <div class="icon-box">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <h3 x-text="activeItem.hasScore ? 'Perbarui Lembar Penilaian' : 'Input Penilaian Mahasiswa Magang'"></h3>
                            <p>Evaluasi capaian performa &amp; kompetensi mahasiswa</p>
                        </div>
                    </div>
                    <button type="button" class="review-modal-close" @click="gradingModalOpen = false" title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Form Penilaian --}}
                <form :action="'/mitra/penilaian/' + activeItem.id" method="POST" id="gradingForm" @submit.prevent="submitGrading($event)">
                    @csrf
                    @method('PUT')

                    <div class="review-modal-body" style="display: flex; flex-direction: column; gap: 18px; padding: 24px;">
                        
                        {{-- Mini Profile Banner --}}
                        <div style="padding: 12px 16px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <strong style="color: var(--text); font-size: 14px;" x-text="activeItem.nama"></strong>
                                <div style="font-size: 12px; color: var(--text-sub); margin-top: 2px;">
                                    NIM: <span class="dk-doc-number" x-text="activeItem.nim"></span> &bull; 
                                    <span x-text="activeItem.prodi"></span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 11px; color: var(--text-sub); display: block;">Program Kegiatan:</span>
                                <span style="font-size: 12px; font-weight: 700; color: #4f46e5;" x-text="activeItem.kegiatan"></span>
                            </div>
                        </div>

                        {{-- 5 Parameter Kinerja (Flowchart 6.3) --}}
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <label style="font-size: 13px; font-weight: 700; color: var(--text);">
                                <i class="fas fa-sliders" style="color: #4f46e5; margin-right: 6px;"></i>
                                5 Aspek Penilaian Kinerja Mahasiswa (0 - 100)
                            </label>

                            {{-- Aspek 1: Kedisiplinan & Etika --}}
                            <div style="padding: 10px 14px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div>
                                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text);">1. Kedisiplinan &amp; Etika Kerja</span>
                                        <span style="font-size: 11px; color: var(--text-sub); display: block;">Kehadiran, kepatuhan SOP perusahaan, tata krama (Bobot 20%)</span>
                                    </div>
                                    <span class="dk-doc-number" x-text="aspekKedisiplinan"></span>
                                </div>
                                <input type="range" min="0" max="100" x-model.number="aspekKedisiplinan" @input="calculateTotalScore()"
                                    style="width: 100%; accent-color: #4f46e5; cursor: pointer;">
                            </div>

                            {{-- Aspek 2: Kompetensi Teknis --}}
                            <div style="padding: 10px 14px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div>
                                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text);">2. Kompetensi Teknis &amp; Praktik</span>
                                        <span style="font-size: 11px; color: var(--text-sub); display: block;">Kualitas hasil kerja, penguasaan materi teknis (Bobot 30%)</span>
                                    </div>
                                    <span class="dk-doc-number" x-text="aspekTeknis"></span>
                                </div>
                                <input type="range" min="0" max="100" x-model.number="aspekTeknis" @input="calculateTotalScore()"
                                    style="width: 100%; accent-color: #4f46e5; cursor: pointer;">
                            </div>

                            {{-- Aspek 3: Kerja Sama Tim --}}
                            <div style="padding: 10px 14px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div>
                                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text);">3. Kerja Sama Tim &amp; Adaptasi</span>
                                        <span style="font-size: 11px; color: var(--text-sub); display: block;">Kolaborasi tim, respons terhadap arahan mentor (Bobot 20%)</span>
                                    </div>
                                    <span class="dk-doc-number" x-text="aspekKerjasama"></span>
                                </div>
                                <input type="range" min="0" max="100" x-model.number="aspekKerjasama" @input="calculateTotalScore()"
                                    style="width: 100%; accent-color: #4f46e5; cursor: pointer;">
                            </div>

                            {{-- Aspek 4: Inisiatif & Problem Solving --}}
                            <div style="padding: 10px 14px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div>
                                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text);">4. Inisiatif &amp; Problem Solving</span>
                                        <span style="font-size: 11px; color: var(--text-sub); display: block;">Kemandirian, keaktifan mencari solusi tugas (Bobot 15%)</span>
                                    </div>
                                    <span class="dk-doc-number" x-text="aspekInisiatif"></span>
                                </div>
                                <input type="range" min="0" max="100" x-model.number="aspekInisiatif" @input="calculateTotalScore()"
                                    style="width: 100%; accent-color: #4f46e5; cursor: pointer;">
                            </div>

                            {{-- Aspek 5: Komunikasi & Tanggung Jawab --}}
                            <div style="padding: 10px 14px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div>
                                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text);">5. Komunikasi &amp; Tanggung Jawab</span>
                                        <span style="font-size: 11px; color: var(--text-sub); display: block;">Penyampaian gagasan, ketuntasan pekerjaan (Bobot 15%)</span>
                                    </div>
                                    <span class="dk-doc-number" x-text="aspekKomunikasi"></span>
                                </div>
                                <input type="range" min="0" max="100" x-model.number="aspekKomunikasi" @input="calculateTotalScore()"
                                    style="width: 100%; accent-color: #4f46e5; cursor: pointer;">
                            </div>
                        </div>

                        {{-- Live Final Score Summary --}}
                        <div class="dk-alert dk-alert-success" style="justify-content: space-between; padding: 14px 18px;">
                            <div>
                                <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">Kalkulasi Nilai Akhir</span>
                                <strong style="font-size: 13px;">Skor Kumulatif Industri</strong>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="hidden" name="nilai_mitra" :value="calculatedScore">
                                <span style="font-size: 22px; font-weight: 800; font-family: 'DM Mono', monospace;" x-text="calculatedScore.toFixed(1)"></span>
                                <span class="dk-status dk-status-active" x-text="'Grade: ' + calculatedGrade"></span>
                            </div>
                        </div>

                        {{-- Catatan Kualitatif / Feedback --}}
                        <div>
                            <label style="display: block; font-weight: 700; color: var(--text); margin-bottom: 6px; font-size: 12.5px;">
                                <i class="fas fa-comment-dots" style="color: #6366f1; margin-right: 6px;"></i>
                                Catatan Kualitatif / Umpan Balik Mentor Industri
                            </label>
                            <textarea name="catatan_mitra" x-model="catatanMitra" class="rfc-input" rows="3"
                                placeholder="Tuliskan ulasan performa, kelebihan, dan saran perbaikan bagi mahasiswa..."
                                style="height: auto; min-height: 70px;"></textarea>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--surface2); border-radius: 0 0 16px 16px;">
                        <button type="button" class="rfc-btn" @click="gradingModalOpen = false">
                            Batal
                        </button>
                        <button type="submit" class="dk-primary-btn" :disabled="isSubmitting">
                            <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Penilaian'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ═══ MODAL DETAIL MAHASISWA (ALPINE.JS) ═══ --}}
    <template x-if="detailModalOpen">
        <div class="review-modal-overlay" @click.self="detailModalOpen = false" x-cloak>
            <div class="review-modal-card" style="max-width: 580px;"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                
                <div class="review-modal-header">
                    <div class="review-modal-title">
                        <div class="icon-box">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h3>Detail Mahasiswa Magang</h3>
                            <p>Informasi penempatan dan kontak pembimbing</p>
                        </div>
                    </div>
                    <button type="button" class="review-modal-close" @click="detailModalOpen = false" title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="review-modal-body" style="display: flex; flex-direction: column; gap: 16px; padding: 24px;">
                    
                    {{-- Profile Header Card --}}
                    <div class="dk-entity" style="padding: 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px;">
                        <span class="dk-entity-icon dk-entity-indigo" style="width: 44px; height: 44px; font-size: 16px; font-weight: 800;" x-text="detailItem.nama ? detailItem.nama.charAt(0) : 'M'"></span>
                        <div class="dk-entity-text">
                            <strong style="font-size: 15px;" x-text="detailItem.nama"></strong>
                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                                <span class="dk-doc-number" x-text="detailItem.nim"></span>
                                <span style="font-size: 12px; color: var(--text-sub);" x-text="detailItem.prodi + ' (Angkatan ' + detailItem.angkatan + ')'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Grid --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div style="padding: 10px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">Email:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="detailItem.email"></span>
                        </div>
                        <div style="padding: 10px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">Telepon / WA:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="detailItem.telepon"></span>
                        </div>
                        <div style="padding: 10px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">Dosen Pembimbing:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="detailItem.dosen"></span>
                            <span style="font-size: 11px; color: var(--text-sub);" x-text="'Kontak: ' + detailItem.dosenKontak"></span>
                        </div>
                        <div style="padding: 10px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">Mentor Industri:</span>
                            <span style="font-size: 12.5px; font-weight: 600; color: var(--text);" x-text="detailItem.mentor"></span>
                            <span style="font-size: 11px; color: var(--text-sub);" x-text="'Kontak: ' + detailItem.mentorKontak"></span>
                        </div>
                    </div>

                    {{-- Status Nilai & Catatan --}}
                    <div style="padding: 14px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; display: flex; flex-direction: column; gap: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase;">Status Evaluasi:</span>
                            <span class="dk-status dk-status-active" x-text="'Nilai: ' + detailItem.nilai + ' (' + detailItem.grade + ')'"></span>
                        </div>
                        <div style="margin-top: 4px;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-sub); display: block;">Catatan Mitra:</span>
                            <p style="font-size: 12.5px; color: var(--text); margin: 3px 0 0 0; line-height: 1.4; font-style: italic;" x-text="'“' + detailItem.catatan + '”'"></p>
                        </div>
                    </div>

                </div>

                <div style="padding: 14px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; background: var(--surface2); border-radius: 0 0 16px 16px;">
                    <button type="button" class="rfc-btn" @click="detailModalOpen = false">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

</main>

{{-- ═══ ALPINE.JS APPLICATION LOGIC ═══ --}}
<script>
    function mitraPenilaianApp() {
        return {
            searchQuery: '',
            prodiFilter: 'all',
            statusFilter: 'all',
            tahunFilter: 'all',

            currentPage: 1,
            perPage: 10,
            perPageOpen: false,
            perPageOptions: [5, 10, 25, 50],

            gradingModalOpen: false,
            detailModalOpen: false,
            isSubmitting: false,

            activeItem: {},
            detailItem: {},

            // Grading Sub-Aspects (0-100)
            aspekKedisiplinan: 85,
            aspekTeknis: 85,
            aspekKerjasama: 85,
            aspekInisiatif: 85,
            aspekKomunikasi: 85,
            calculatedScore: 85.0,
            calculatedGrade: 'A',
            catatanMitra: '',

            setPerPage(value) {
                this.perPage = value;
                this.currentPage = 1;
                this.perPageOpen = false;
            },

            init() {
                this.$watch('searchQuery', () => this.currentPage = 1);
                this.$watch('prodiFilter', () => this.currentPage = 1);
                this.$watch('statusFilter', () => this.currentPage = 1);
                this.$watch('tahunFilter', () => this.currentPage = 1);
                this.$watch('perPage', () => this.currentPage = 1);
            },

            resetFilters() {
                this.searchQuery = '';
                this.prodiFilter = 'all';
                this.statusFilter = 'all';
                this.tahunFilter = 'all';
                this.currentPage = 1;
            },

            get rows() {
                return this.$refs.rows ? Array.from(this.$refs.rows.querySelectorAll('tr[data-row]')) : [];
            },

            get filteredRows() {
                return this.rows.filter(row => this.matchesRow(row));
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

            matchesRow(row) {
                const q = this.searchQuery.toLowerCase().trim();
                const matchSearch = q === '' ||
                    row.dataset.nim.includes(q) ||
                    row.dataset.nama.includes(q) ||
                    row.dataset.prodi.includes(q) ||
                    row.dataset.kegiatan.includes(q);

                const matchProdi = this.prodiFilter === 'all' || row.dataset.prodi === this.prodiFilter.toLowerCase();
                const matchStatus = this.statusFilter === 'all' || row.dataset.status === this.statusFilter;
                const matchTahun = this.tahunFilter === 'all' || row.dataset.tahun === String(this.tahunFilter);

                return matchSearch && matchProdi && matchStatus && matchTahun;
            },

            isRowVisible(row) {
                const index = this.filteredRows.indexOf(row);
                if (index === -1) return false;
                return index >= ((this.currentPage - 1) * this.perPage) && index < (this.currentPage * this.perPage);
            },

            rowNumber(row) {
                const index = this.filteredRows.indexOf(row);
                return index === -1 ? 0 : String(index + 1).padStart(2, '0');
            },

            pageNumbers() {
                const total = this.totalPages;
                if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);
                const pages = new Set([1, total, this.currentPage - 1, this.currentPage, this.currentPage + 1]);
                return Array.from(pages).filter(p => p >= 1 && p <= total).sort((a, b) => a - b);
            },

            goToPage(p) {
                this.currentPage = Math.min(Math.max(p, 1), this.totalPages);
            },

            openGradingModal(item) {
                this.activeItem = item;
                const baseScore = item.nilai || 85;
                this.aspekKedisiplinan = baseScore;
                this.aspekTeknis = baseScore;
                this.aspekKerjasama = baseScore;
                this.aspekInisiatif = baseScore;
                this.aspekKomunikasi = baseScore;
                this.catatanMitra = item.catatan || '';
                this.calculateTotalScore();
                this.gradingModalOpen = true;
            },

            openDetailModal(item) {
                this.detailItem = item;
                this.detailModalOpen = true;
            },

            calculateTotalScore() {
                const total = (this.aspekKedisiplinan * 0.20) +
                              (this.aspekTeknis * 0.30) +
                              (this.aspekKerjasama * 0.20) +
                              (this.aspekInisiatif * 0.15) +
                              (this.aspekKomunikasi * 0.15);

                this.calculatedScore = Math.min(100, Math.max(0, total));

                if (this.calculatedScore >= 85) this.calculatedGrade = 'A';
                else if (this.calculatedScore >= 75) this.calculatedGrade = 'B+';
                else if (this.calculatedScore >= 65) this.calculatedGrade = 'B';
                else if (this.calculatedScore >= 55) this.calculatedGrade = 'C';
                else this.calculatedGrade = 'D';
            },

            submitGrading(event) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Simpan Penilaian?',
                        text: `Anda akan memberikan nilai ${this.calculatedScore.toFixed(1)} (${this.calculatedGrade}) untuk mahasiswa ${this.activeItem.nama}.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#64748b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.isSubmitting = true;
                            event.target.submit();
                        }
                    });
                } else {
                    if (confirm(`Simpan penilaian untuk mahasiswa ${this.activeItem.nama}?`)) {
                        this.isSubmitting = true;
                        event.target.submit();
                    }
                }
            }
        };
    }
</script>

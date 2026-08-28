@php
    $user = auth()->user();
    $mitra = $user->mitra;
    $mitraName = $mitra->nama_mitra ?? 'Mitra Eksternal';

    // Fetch kerjasama if not passed from controller
    $kerjasamaList = $kerjasamaList ?? collect();
    if ($kerjasamaList->isEmpty() && $mitra) {
        $kerjasamaList = \App\Models\Cooperation::with(['jurusans', 'upas', 'pusats', 'createdBy', 'updatedBy'])
            ->where('mitra_id', $mitra->id)
            ->get();
    }

    $totalKerjasama = $kerjasamaList->count();
    $aktifCount = $kerjasamaList->filter(fn($item) => strtolower($item->status ?? '') === 'aktif')->count();
    $perpanjanganCount = $kerjasamaList->filter(fn($item) => str_contains(strtolower($item->status ?? ''), 'perpanjangan'))->count();
    $expiredCount = $kerjasamaList->filter(function ($item) {
        $status = strtolower($item->status ?? '');
        return in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
    })->count();

    // Use stats passed from controller if available, else default
    $totalMhsMagang = $totalMahasiswaAktif ?? 0;
    $alumniCount = $alumniTerserap ?? 0;

    $auditUserLabel = function ($user = null) {
        $roleName = $user?->role?->role_name;
        $roleLabel = match (strtolower($roleName ?? '')) {
            'unit_kerja' => 'Humas',
            'jurusan' => $user?->profile?->jurusan?->nama_jurusan
            ? 'Jurusan - ' . $user->profile->jurusan->nama_jurusan
            : 'Jurusan',
            'pusat' => $user?->profile?->pusat?->nama_pusat
            ? 'Pusat - ' . $user->profile->pusat->nama_pusat
            : 'Pusat',
            'upa' => $user?->profile?->upa?->nama_upa
            ? 'UPA - ' . $user->profile->upa->nama_upa
            : 'UPA',
            default => $roleName ? ucfirst($roleName) : '-',
        };

        return [
            'name' => $user?->name ?: '-',
            'jabatan' => $user?->profile?->jabatan ?: '-',
            'role' => $roleLabel,
        ];
    };
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<!-- Main Content -->
<main id="mainContent" class="dk-page" x-data="mitraDashboard()" x-cloak>
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Dashboard Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake-angle"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Portal Kerja Sama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Selamat datang, pantau dokumen kerja sama, mahasiswa magang, dan alumni untuk
                        <strong>{{ $mitraName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Kartu Statistik -->
    <section class="dk-stats-grid" aria-label="Ringkasan data kerjasama">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-folder-open"></i></div>
            <div>
                <span class="dk-stat-label">Total Dokumen KS</span>
                <strong>{{ number_format($totalKerjasama) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Dokumen Aktif</span>
                <strong>{{ number_format($aktifCount) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div>
                <span class="dk-stat-label">Mhs Magang Aktif</span>
                <strong>{{ number_format($totalMhsMagang) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #8b5cf6;">
            <div class="dk-stat-icon" style="color: #8b5cf6; background: rgba(139,92,246,0.1);"><i
                    class="fas fa-briefcase"></i></div>
            <div>
                <span class="dk-stat-label">Alumni Terserap</span>
                <strong>{{ number_format($alumniCount) }}</strong>
            </div>
        </div>
    </section>

    @php
        $years = $kerjasamaList->map(function ($k) {
            return $k->start_date ? $k->start_date->format('Y') : null;
        })->filter()->unique()->sortDesc();

        $draftCount = $kerjasamaList->filter(function ($i) {
            $s = strtolower($i->status ?? '');
            return in_array($s, ['draft', 'menunggu evaluasi', 'menunggu review']);
        })->count();
    @endphp

    {{-- Filter Data Kerjasama (Accordion) --}}
    <div class="report-filter-container" x-data="{ showFilters: false }">
        <div class="rfc-header"
            style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;"
            @click="showFilters = !showFilters">
            <div class="rfc-title-area">
                <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                <div class="rfc-text">
                    <h3>Filter Data Kerjasama</h3>
                    <p>Saring dokumen kerjasama berdasarkan jenis, periode tahun, status, atau kata kunci pencarian</p>
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
                        { id: 'all', label: 'Semua Jenis' },
                        { id: 'MoU', label: 'MoU (Nota Kesepahaman)' },
                        { id: 'MoA', label: 'MoA (Perjanjian Kerjasama)' },
                        { id: 'IA', label: 'IA (Implementation Arrangement)' }
                    ],
                    get selectedLabel() {
                        const selectedItem = this.items.find((item) => item.id === jenisFilter);
                        return selectedItem ? selectedItem.label : 'Semua Jenis';
                    }
                }">
                    <label>Jenis Dokumen</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-file-signature" style="color: #9ca3af; font-size: 13px;"></i>
                                <span x-text="selectedLabel"></span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                :style="open ? 'transform: rotate(180deg)' : ''"></i>
                        </div>
                        <div class="ad-menu" x-show="open" x-transition>
                            <template x-for="item in items" :key="item.id">
                                <div class="ad-item" :class="{ 'selected': jenisFilter == item.id }"
                                    @click="jenisFilter = item.id; open = false" x-text="item.label"></div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 2. Filter Periode Tahun --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Periode' },
                        @foreach($years as $year)
                            { id: '{{ $year }}', label: 'Tahun {{ $year }}' },
                        @endforeach
                    ],
                    get selectedLabel() {
                        const selectedItem = this.items.find((item) => item.id === String(periodeFilter));
                        return selectedItem ? selectedItem.label : 'Semua Periode';
                    }
                }">
                    <label>Periode Kerjasama</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-calendar-alt" style="color: #9ca3af; font-size: 13px;"></i>
                                <span x-text="selectedLabel"></span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                :style="open ? 'transform: rotate(180deg)' : ''"></i>
                        </div>
                        <div class="ad-menu" x-show="open" x-transition>
                            <template x-for="item in items" :key="item.id">
                                <div class="ad-item" :class="{ 'selected': String(periodeFilter) == item.id }"
                                    @click="periodeFilter = item.id; open = false" x-text="item.label"></div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 3. Filter Status Dokumen --}}
                <div class="rfc-group" x-data="{
                    open: false,
                    items: [
                        { id: 'all', label: 'Semua Status' },
                        { id: 'aktif', label: 'Dokumen Aktif' },
                        { id: 'draft', label: 'Menunggu Review {{ $draftCount > 0 ? "($draftCount)" : "" }}' },
                        { id: 'perpanjangan', label: 'Masa Tenggang / Perpanjangan' },
                        { id: 'kadarluarsa', label: 'Kadaluarsa' },
                        { id: 'tidak aktif', label: 'Tidak Aktif' }
                    ],
                    get selectedLabel() {
                        const selectedItem = this.items.find((item) => item.id === statusFilter);
                        return selectedItem ? selectedItem.label : 'Semua Status';
                    }
                }">
                    <label>Status Dokumen</label>
                    <div class="alpine-dropdown" @click.outside="open = false">
                        <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-info-circle" style="color: #9ca3af; font-size: 13px;"></i>
                                <span x-text="selectedLabel"></span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                :style="open ? 'transform: rotate(180deg)' : ''"></i>
                        </div>
                        <div class="ad-menu" x-show="open" x-transition>
                            <template x-for="item in items" :key="item.id">
                                <div class="ad-item" :class="{ 'selected': statusFilter == item.id }"
                                    @click="statusFilter = item.id; open = false" x-text="item.label"></div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 4. Pencarian Teks --}}
                <div class="rfc-group">
                    <label>Cari Dokumen</label>
                    <div class="rfc-input-wrap">
                        <i class="fas fa-search rfc-input-icon"></i>
                        <input type="text" x-model="searchFilter" placeholder="Cari judul / no. dokumen..."
                            class="rfc-input">
                    </div>
                </div>
            </div>

            <div class="rfc-footer"
                style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--border);">
                <button type="button" class="rfc-btn rfc-btn-primary"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <button type="button" @click="resetFilters()" class="rfc-btn"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface2); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-rotate-left"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                <span>
                    <strong>Daftar Dokumen Kerjasama Saya</strong>
                    <small id="dkerjasamaCount">{{ $kerjasamaList->count() }} data ditemukan</small>
                </span>
            </div>

            <div class="dk-card-tools" x-data="{ showModal: false }">
                <button @click="showModal = true" class="dk-primary-btn">
                    <i class="fas fa-plus"></i>
                    <span>Tindakan Baru</span>
                </button>

                {{-- MODAL PILIH TINDAKAN --}}
                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="modal-overlay"
                    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
                    @click.self="showModal = false" x-cloak>

                    <div class="modal-card" x-show="showModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                        style="background: var(--surface); border-radius: 24px; width: 100%; max-width: 550px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--border);">

                        {{-- Modal Header --}}
                        <div
                            style="padding: 24px 32px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, var(--surface), var(--surface2));">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 40px; height: 40px; border-radius: 12px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                    <h3
                                        style="margin: 0; font-size: 18px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">
                                        Pilih Tindakan</h3>
                                </div>
                                <button @click="showModal = false"
                                    style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 14px; transition: 0.2s;"
                                    onmouseover="this.style.color='#ef4444'">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Modal Body --}}
                        <div style="padding: 32px;">
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                {{-- Opsi 1: Pengajuan Baru --}}
                                <a href="{{ route('mitra.pengajuan.create') ?? '#' }}" class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); group;"
                                    onmouseover="this.style.borderColor='#4f46e5'; this.style.background='rgba(79,70,229,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div
                                        style="width: 56px; height: 56px; border-radius: 16px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-file-signature"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span
                                            style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Ajukan
                                            Kerja Sama Baru</span>
                                        <p
                                            style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">
                                            Gunakan ini untuk mengajukan dokumen kerja sama baru (MoU / MoA / IA) dengan
                                            Polimdo.</p>
                                    </div>
                                    <i class="fas fa-chevron-right"
                                        style="color: #9ca3af; font-size: 14px; opacity: 0; transition: 0.3s; transform: translateX(-10px);"></i>
                                </a>

                                {{-- Opsi 2: Perpanjangan --}}
                                <a href="#" class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    onmouseover="this.style.borderColor='#d97706'; this.style.background='rgba(217,119,6,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div
                                        style="width: 56px; height: 56px; border-radius: 16px; background: rgba(217,119,6,0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-clock-rotate-left"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span
                                            style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Ajukan
                                            Perpanjangan</span>
                                        <p
                                            style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">
                                            Ajukan perpanjangan untuk dokumen kerja sama yang masa berlakunya hampir
                                            habis.</p>
                                    </div>
                                    <i class="fas fa-chevron-right"
                                        style="color: #9ca3af; font-size: 14px; opacity: 0; transition: 0.3s; transform: translateX(-10px);"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div
                            style="padding: 20px 32px; background: var(--surface2); border-top: 1px solid var(--border); text-align: center;">
                            <span
                                style="font-size: 11px; color: var(--text-sub); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pilih
                                tindakan yang ingin Anda lakukan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body dk-card-body" x-data="{ 
            currentPage: 1, 
            perPage: 10,
            totalRows: {{ $kerjasamaList->count() }},
            get totalPages() { return Math.ceil(this.totalRows / this.perPage); },
            get startRange() { return (this.currentPage - 1) * this.perPage + 1; },
            get endRange() { return Math.min(this.currentPage * this.perPage, this.totalRows); }
        }">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th dk-th-expand">
                                <span class="dk-expand-head-icon" title="Expand">
                                    <i class="fas fa-sort-amount-down"></i>
                                </span>
                            </th>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 450px; min-width: 300px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana Kampus</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berlaku</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kerjasamaList as $kegiatan)
                            @php
                                $status = strtolower($kegiatan->status ?? '');
                                $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
                                $isExtended = str_contains($status, 'perpanjangan');

                                $tabCategory = match (true) {
                                    $status === 'aktif' => 'aktif',
                                    $status === 'draft' || str_contains($status, 'menunggu') => 'draft',
                                    $isExtended || $isExpired => 'perpanjangan',
                                    default => 'other',
                                };

                                $statusClass = match (true) {
                                    $status === 'aktif' => 'dk-status-active',
                                    $status === 'proses' || str_contains($status, 'menunggu') => 'dk-status-info',
                                    $isExtended => 'dk-status-warning',
                                    $isExpired => 'dk-status-danger',
                                    $status === 'tidak aktif' => 'dk-status-muted',
                                    default => 'dk-status-neutral',
                                };
                                $statusIcon = match (true) {
                                    $status === 'aktif' => 'fa-circle-check',
                                    $status === 'proses' || str_contains($status, 'menunggu') => 'fa-spinner fa-spin',
                                    $isExtended => 'fa-clock',
                                    $isExpired => 'fa-circle-xmark',
                                    $status === 'tidak aktif' => 'fa-circle-minus',
                                    default => 'fa-circle-info',
                                };
                                $statusLabel = match (true) {
                                    $status === 'aktif' => 'Aktif',
                                    $isExtended => 'Perpanjangan',
                                    $isExpired => 'Kadaluarsa',
                                    $status === 'tidak aktif' => 'Tidak Aktif',
                                    $status !== '' => ucwords(str_replace('_', ' ', $status)),
                                    default => 'Belum Diatur',
                                };

                                $pelaksanaGroups = collect();
                                if ($kegiatan->jurusans && $kegiatan->jurusans->isNotEmpty()) {
                                    $pelaksanaGroups->push(['type' => 'Jurusan', 'icon' => 'fa-building', 'class' => 'dk-entity-indigo', 'names' => $kegiatan->jurusans->pluck('nama_jurusan')->toArray()]);
                                }
                                if ($kegiatan->upas && $kegiatan->upas->isNotEmpty()) {
                                    $pelaksanaGroups->push(['type' => 'UPA', 'icon' => 'fa-building', 'class' => 'dk-entity-emerald', 'names' => $kegiatan->upas->pluck('nama_upa')->toArray()]);
                                }
                                if ($kegiatan->pusats && $kegiatan->pusats->isNotEmpty()) {
                                    $pelaksanaGroups->push(['type' => 'Pusat', 'icon' => 'fa-building', 'class' => 'dk-entity-amber', 'names' => $kegiatan->pusats->pluck('nama_pusat')->toArray()]);
                                }
                                if ($pelaksanaGroups->isEmpty()) {
                                    $pelaksanaGroups->push(['type' => 'Polimdo', 'icon' => 'fa-university', 'class' => 'dk-entity-indigo', 'names' => ['Tingkat Institusi']]);
                                }

                                $mulai = $kegiatan->start_date?->format('d M Y') ?? '-';
                                $selesai = $kegiatan->end_date?->format('d M Y') ?? '-';
                                $docNumber = $kegiatan->doc_number ?? '-';
                                $title = $kegiatan->judul ?? '-';
                                $docJenis = $kegiatan->jenis ?? '';
                                $docTahun = $kegiatan->start_date ? $kegiatan->start_date->format('Y') : '';
                            @endphp
                            <tr class="um-row dk-row" data-row-id="{{ $kegiatan->id }}"
                                x-show="(statusFilter === 'all' || statusFilter === '{{ $tabCategory }}' || statusFilter === '{{ $status }}') && (jenisFilter === 'all' || jenisFilter === '{{ $docJenis }}') && (periodeFilter === 'all' || String(periodeFilter) === '{{ $docTahun }}') && (searchFilter === '' || '{{ strtolower(addslashes($title . ' ' . $docNumber)) }}'.includes(searchFilter.toLowerCase()))">
                                <td class="um-td dk-td-expand" style="vertical-align: top; padding-top: 12px;">
                                    <button type="button" class="dk-expand-toggle" aria-expanded="false"
                                        aria-controls="dk-detail-{{ $kegiatan->id }}" title="Lihat metadata">
                                        <i class="fas fa-angles-right"></i>
                                    </button>
                                </td>
                                <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-title-cell" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-doc-cell">
                                        <span class="dk-doc-number">#{{ $docNumber }}</span>
                                        <span class="dk-doc-title"
                                            style="font-weight: 700; line-height: 1.5;">{{ $title }}</span>
                                        <span class="dk-doc-kind">{{ $kegiatan->jenis ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <div style="display: grid; gap: 8px;">
                                        @foreach ($pelaksanaGroups as $group)
                                            <div class="dk-entity" style="align-items: flex-start;">
                                                <span class="dk-entity-icon {{ $group['class'] }}" style="flex-shrink: 0;">
                                                    <i class="fas {{ $group['icon'] }}"></i>
                                                </span>
                                                <span class="dk-entity-text" style="padding-top: 2px;">
                                                    <small
                                                        style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--text-sub); margin-bottom:2px;">{{ $group['type'] }}</small>
                                                    {{ implode(', ', $group['names']) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-date-range-compact">
                                        <span class="date-val">{{ $mulai }}</span>
                                        <span class="date-sep">s/d</span>
                                        <span class="date-val">{{ $selesai }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <span class="dk-status {{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }}"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                    <div class="um-actions dk-actions-compact">
                                        @if($tabCategory === 'draft')
                                            <button
                                                @click="openReview({{ $kegiatan->id }}, '{{ $docNumber }}', '{{ addslashes($title) }}', '{{ route('mitra.dokumen.show', $kegiatan->id) }}')"
                                                class="dk-action-btn edit" title="Review Draf Online"
                                                style="color: #4f46e5; background: rgba(79,70,229,0.1); border:none; cursor:pointer;">
                                                <i class="fas fa-file-signature"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('mitra.dokumen.show', $kegiatan->id) ?? '#' }}"
                                                class="dk-action-btn view" title="Lihat Dokumen">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr class="dk-row-detail" id="dk-detail-{{ $kegiatan->id }}" aria-hidden="true">
                                <td colspan="7" class="dk-detail-cell">
                                    <div class="dk-detail-content">
                                        <div class="dk-audit-grid">
                                            <section class="dk-audit-card">
                                                <div class="dk-audit-card-head">
                                                    <span class="dk-audit-icon dk-audit-created"><i
                                                            class="fas fa-user-plus"></i></span>
                                                    <strong>Diusulkan oleh Kampus</strong>
                                                </div>
                                                <div class="dk-audit-person">{{ $kegiatan->createdBy?->name ?? 'Sistem' }}
                                                </div>
                                                <div class="dk-audit-meta">
                                                    <span>Dibuat:
                                                        {{ $kegiatan->created_at?->format('d M Y') ?? '-' }}</span>
                                                </div>
                                            </section>
                                            <section class="dk-audit-card">
                                                <div class="dk-audit-card-head">
                                                    <span class="dk-audit-icon dk-audit-updated"
                                                        style="color: #4f46e5; background: rgba(79,70,229,0.1);"><i
                                                            class="fas fa-list-check"></i></span>
                                                    <strong>Status Draf Mitra</strong>
                                                </div>
                                                <div class="dk-audit-person">Menunggu Review</div>
                                                <div class="dk-audit-meta">
                                                    <span>Silakan cek dokumen di tombol Review Draf</span>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="7" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-folder-open"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada dokumen kerjasama</p>
                                        <p class="um-empty-sub">Anda belum memiliki dokumen kerja sama aktif dengan
                                            Politeknik Negeri Manado.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Review Modal UC13 -->
    <template x-if="showReviewModal">
        <div class="review-modal-overlay" @click.self="showReviewModal = false" x-cloak>
            <div class="review-modal-card" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Modal Header -->
                <div class="review-modal-header">
                    <div class="review-modal-title">
                        <div class="icon-box">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <h3>Review Draf Online</h3>
                            <p>Dokumen <strong x-text="reviewDocNumber"></strong> - <span
                                    x-text="reviewDocTitle"></span></p>
                        </div>
                    </div>
                    <button class="review-modal-close" @click="showReviewModal = false" title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body (Split View) -->
                <div class="review-modal-body">
                    <!-- Kiri: PDF Preview -->
                    <div class="review-pdf-panel">
                        <object :data="reviewPdfUrl" type="application/pdf" width="100%" height="100%">
                            <div
                                style="padding: 20px; color: white; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <i class="fas fa-file-pdf"
                                    style="font-size: 48px; margin-bottom: 16px; color: #94a3b8;"></i>
                                <p>Browser Anda tidak mendukung preview PDF langsung.</p>
                                <a :href="reviewPdfUrl" target="_blank"
                                    style="color: #60a5fa; text-decoration: underline; margin-top: 8px;">Unduh PDF</a>
                            </div>
                        </object>
                    </div>

                    <!-- Kanan: Form Catatan -->
                    <div class="review-form-panel">
                        <form method="POST" :action="`/mitra/dokumen/${reviewDocId}/review`">
                            @csrf
                            <div class="review-check-list">
                                <h4><i class="fas fa-list-check"></i> Poin Pemeriksaan</h4>
                                <div class="review-check-item">
                                    <i class="fas fa-check-circle"></i> <span>Kesesuaian identitas pihak yang bertanda
                                        tangan.</span>
                                </div>
                                <div class="review-check-item">
                                    <i class="fas fa-check-circle"></i> <span>Ketentuan hak dan kewajiban masing-masing
                                        pihak.</span>
                                </div>
                                <div class="review-check-item">
                                    <i class="fas fa-check-circle"></i> <span>Periode masa berlaku dan penyelesaian
                                        masalah.</span>
                                </div>
                            </div>

                            <label
                                style="display: block; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Catatan
                                Review Klausul</label>
                            <textarea name="catatan_review" class="review-textarea"
                                placeholder="Tuliskan catatan Anda di sini jika ada pasal/klausul yang perlu diperbaiki... Jika draf sudah sesuai, Anda bisa mengosongkannya."></textarea>

                            <div class="review-action-btns">
                                <button type="submit" name="action" value="revisi"
                                    class="btn-review-submit btn-review-warning">
                                    <i class="fas fa-pen-to-square"></i> Kirim Revisi
                                </button>
                                <button type="submit" name="action" value="setuju"
                                    class="btn-review-submit btn-review-success">
                                    <i class="fas fa-check-double"></i> Setujui Draf
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</main>

<script>
    function initDkerjasamaFilter() {
        var form = document.getElementById('filterForm');
        var previewBody = document.getElementById('previewBody');
        var btnTampilkan = document.getElementById('btnTampilkan');
        var countLabel = document.getElementById('dkerjasamaCount');

        if (!form || !previewBody || !btnTampilkan) return;
    }

    document.addEventListener('DOMContentLoaded', initDkerjasamaFilter);
    document.addEventListener('turbo:load', initDkerjasamaFilter);
    if (document.readyState !== 'loading') {
        initDkerjasamaFilter();
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('mitraDashboard', () => ({
            jenisFilter: 'all',
            periodeFilter: 'all',
            statusFilter: 'all',
            searchFilter: '',
            showReviewModal: false,
            reviewDocId: null,
            reviewDocNumber: '',
            reviewDocTitle: '',
            reviewPdfUrl: '',

            resetFilters() {
                this.jenisFilter = 'all';
                this.periodeFilter = 'all';
                this.statusFilter = 'all';
                this.searchFilter = '';
            },

            openReview(id, docNumber, title, pdfUrl) {
                this.reviewDocId = id;
                this.reviewDocNumber = docNumber;
                this.reviewDocTitle = title;
                this.reviewPdfUrl = pdfUrl;
                this.showReviewModal = true;
            }
        }));
    });

    document.addEventListener('turbo:load', function () {
        const toggleButtons = document.querySelectorAll('.dk-expand-toggle');
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const tr = this.closest('.dk-row');
                const detailRowId = this.getAttribute('aria-controls');
                const detailRow = document.getElementById(detailRowId);
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                if (isExpanded) {
                    this.setAttribute('aria-expanded', 'false');
                    detailRow.classList.remove('open');
                    setTimeout(() => {
                        detailRow.style.display = 'none';
                    }, 300);
                } else {
                    document.querySelectorAll('.dk-row-detail.open').forEach(row => {
                        row.classList.remove('open');
                        row.previousElementSibling.querySelector('.dk-expand-toggle').setAttribute('aria-expanded', 'false');
                        setTimeout(() => row.style.display = 'none', 300);
                    });

                    this.setAttribute('aria-expanded', 'true');
                    detailRow.style.display = 'table-row';
                    setTimeout(() => detailRow.classList.add('open'), 10);
                }
            });
        });
    });
</script>
@php
$kerjasamaList = $kerjasamaUnit ?? collect();
if (! $kerjasamaList instanceof \Illuminate\Support\Collection) {
$kerjasamaList = collect($kerjasamaList);
}

$unitName = auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja';
$totalKerjasama = $kerjasamaList->count();
$aktifCount = $kerjasamaList->filter(fn ($item) => strtolower($item->status ?? '') === 'aktif')->count();
$perpanjanganCount = $kerjasamaList->filter(fn ($item) => str_contains(strtolower($item->status ?? ''), 'perpanjangan'))->count();
$expiredCount = $kerjasamaList->filter(function ($item) {
$status = strtolower($item->status ?? '');
return in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
})->count();
@endphp

<!-- Main Content -->
<main id="mainContent" class="dk-page">
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('unit.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('unit.dashboard') }}" style="text-decoration: none; color: inherit;">
                    <span class="current" id="breadcrumbCurrent">Beranda</span>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('unit.dkerjasama') }}" style="text-decoration: none; color: inherit;">
                    <span class="current">Daftar Kerjasama</span>
                </a>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon">
                    <i class="fas fa-handshake-angle"></i>
                </div>
                <div>
                    <span class="dk-eyebrow">Repositori Unit</span>
                    <h2 id="pageTitle">Data Kerjasama</h2>
                    <p id="pageDesc">
                        Pantau dokumen, mitra, masa berlaku, dan status kerjasama untuk
                        <strong>{{ $unitName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stats-grid" aria-label="Ringkasan data kerjasama">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="dk-stat-label">Total Dokumen</span>
                <strong>{{ number_format($totalKerjasama) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Aktif</span>
                <strong>{{ number_format($aktifCount) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-clock-rotate-left"></i></div>
            <div>
                <span class="dk-stat-label">Perpanjangan</span>
                <strong>{{ number_format($perpanjanganCount) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger">
            <div class="dk-stat-icon"><i class="fas fa-calendar-xmark"></i></div>
            <div>
                <span class="dk-stat-label">Kadaluarsa</span>
                <strong>{{ number_format($expiredCount) }}</strong>
            </div>
        </div>
    </section>

    <div class="report-filter-container" x-data="{ showFilters: false }">
        <div class="rfc-header"
            style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;"
            @click="showFilters = !showFilters">
            <div class="rfc-title-area">
                <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                <div class="rfc-text">
                    <h3>Filter Data Kerjasama</h3>
                    <p>Menampilkan sesuai data yang anda filter dan data tersebut anda bisa donwload berupa file dokumen</p>
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
            <form id="filterForm" class="rfc-form" method="GET" action="{{ route('unit.dkerjasama') }}"
                data-preview-url="{{ route('unit.dkerjasama.preview') }}"
                data-pdf-url="{{ route('unit.dkerjasama.pdf') }}"
                data-excel-url="{{ route('unit.dkerjasama.excel') }}"
                data-show-url-template="{{ route('unit.kerjasama.show', '__ID__') }}"
                data-edit-url-template="{{ route('unit.kerjasama.edit', '__ID__') }}"
                data-delete-url-template="{{ route('unit.kerjasama.destroy', '__ID__') }}">

                <div class="rfc-grid">
                    <div class="rfc-group" x-data="datepicker('')">
                        <label>From</label>
                        <div class="alpine-datepicker" @click.outside="show = false">
                            <div class="adp-input-wrap">
                                <i class="fas fa-calendar-day mc-icon-left"></i>
                                <input type="text" name="tanggal_awal" x-model="formattedDate" readonly
                                    @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()" x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>
                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{ 'selected': month === index }"
                                            @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..."
                                            style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                            @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{ 'selected': year === y }"
                                            @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>
                                <div class="adp-grid">
                                    <template x-for="day in dayNames"><div class="adp-day-name" x-text="day"></div></template>
                                    <template x-for="blankday in blanks"><div class="adp-day empty"></div></template>
                                    <template x-for="date in days">
                                        <div class="adp-day" :class="{ 'today': isToday(date), 'selected': isSelected(date) }"
                                            @click="selectDate(date)" x-text="date"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rfc-group" x-data="datepicker('')">
                        <label>To</label>
                        <div class="alpine-datepicker" @click.outside="show = false">
                            <div class="adp-input-wrap">
                                <i class="fas fa-calendar-check mc-icon-left"></i>
                                <input type="text" name="tanggal_akhir" x-model="formattedDate" readonly
                                    @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()" x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>
                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{ 'selected': month === index }"
                                            @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..."
                                            style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                            @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{ 'selected': year === y }"
                                            @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>
                                <div class="adp-grid">
                                    <template x-for="day in dayNames"><div class="adp-day-name" x-text="day"></div></template>
                                    <template x-for="blankday in blanks"><div class="adp-day empty"></div></template>
                                    <template x-for="date in days">
                                        <div class="adp-day" :class="{ 'today': isToday(date), 'selected': isSelected(date) }"
                                            @click="selectDate(date)" x-text="date"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: @js(request('jenis_dokumentasi', 'all')),
                        items: @js(($jenisDokumentasiOptions ?? collect())->map(fn($jenis) => ['id' => $jenis, 'label' => $jenis])->prepend(['id' => 'all', 'label' => 'Semua Jenis'])->values()),
                        get selectedLabel() {
                            const selectedItem = this.items.find((item) => item.id === this.selected);
                            return selectedItem ? selectedItem.label : 'Semua Jenis Dokumentasi';
                        }
                    }">
                        <label>Jenis Dokumentasi</label>
                        <input type="hidden" name="jenis_dokumentasi" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-file-signature" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{ 'selected': selected == item.id }"
                                        @click="selected = item.id; open = false"
                                        x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="rfc-group" x-data="{ open: false, selected: 'all', selectedLabel: 'Semua Unit', items: [{ id: 'all', label: 'Semua Unit' }, { id: 'jurusan', label: 'Jurusan' }, { id: 'upa', label: 'UPA' }, { id: 'pusat', label: 'Pusat' }] }">
                        <label>Unit Pelaksana</label>
                        <input type="hidden" name="tipe_pelaksana" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-sitemap" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items">
                                    <div class="ad-item" :class="{ 'selected': selected == item.id }"
                                        @click="selected = item.id; selectedLabel = item.label; open = false"
                                        x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="rfc-group" x-data="{ open: false, selected: 'all', selectedLabel: 'Semua Jurusan', items: @js(($jurusans ?? collect())->map(fn($jurusan) => ['id' => $jurusan->id, 'label' => $jurusan->nama_jurusan])->prepend(['id' => 'all', 'label' => 'Semua Jurusan'])->values()) }">
                        <label>Jurusan</label>
                        <input type="hidden" name="jurusan_id" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-microchip" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items">
                                    <div class="ad-item" :class="{ 'selected': selected == item.id }"
                                        @click="selected = item.id; selectedLabel = item.label; open = false"
                                        x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="rfc-group" x-data="{ open: false, selected: 'all', selectedLabel: 'Semua UPA', items: @js(($upas ?? collect())->map(fn($upa) => ['id' => $upa->id, 'label' => $upa->nama_upa])->prepend(['id' => 'all', 'label' => 'Semua UPA'])->values()) }">
                        <label>UPA</label>
                        <input type="hidden" name="upa_id" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-building-columns" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items">
                                    <div class="ad-item" :class="{ 'selected': selected == item.id }"
                                        @click="selected = item.id; selectedLabel = item.label; open = false"
                                        x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="rfc-group" x-data="{ open: false, selected: 'all', selectedLabel: 'Semua Pusat', items: @js(($pusats ?? collect())->map(fn($pusat) => ['id' => $pusat->id, 'label' => $pusat->nama_pusat])->prepend(['id' => 'all', 'label' => 'Semua Pusat'])->values()) }">
                        <label>Pusat</label>
                        <input type="hidden" name="pusat_id" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-landmark" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items">
                                    <div class="ad-item" :class="{ 'selected': selected == item.id }"
                                        @click="selected = item.id; selectedLabel = item.label; open = false"
                                        x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="rfc-group" x-data="{ open: false, selected: 'all', selectedLabel: 'Semua Status', items: [{ id: 'all', label: 'Semua Status' }, { id: 'aktif', label: 'Aktif' }, { id: 'proses', label: 'Proses' }, { id: 'dalam perpanjangan', label: 'Dalam Perpanjangan' }, { id: 'kadarluarsa', label: 'Kadarluarsa' }, { id: 'tidak aktif', label: 'Tidak Aktif' }] }">
                        <label>Status</label>
                        <input type="hidden" name="status" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-info-circle" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items">
                                    <div class="ad-item" :class="{ 'selected': selected == item.id }"
                                        @click="selected = item.id; selectedLabel = item.label; open = false"
                                        x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rfc-footer">
                    <button type="submit" id="btnTampilkan" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                    <button type="button" id="btnCetakPdf" class="rfc-btn rfc-btn-danger">
                        <i class="fas fa-file-pdf"></i> Cetak PDF
                    </button>
                    <button type="button" id="btnExportExcel" class="rfc-btn rfc-btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="dk-alert dk-alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="dk-alert dk-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                <span>
                    <strong>Daftar Kerjasama</strong>
                    <small id="dkerjasamaCount">{{ $kerjasamaList->count() }} data ditemukan</small>
                </span>
            </div>

            <div class="dk-card-tools" x-data="{ showModal: false }">
                <button @click="showModal = true" class="dk-primary-btn">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Data</span>
                </button>

                {{-- ══ MODAL PILIH JENIS INPUT ══ --}}
                <div x-show="showModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="modal-overlay"
                    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
                    @click.self="showModal = false"
                    x-cloak>

                    <div class="modal-card"
                        x-show="showModal"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        style="background: var(--surface); border-radius: 24px; width: 100%; max-width: 550px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--border);">

                        {{-- Modal Header --}}
                        <div style="padding: 24px 32px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, var(--surface), var(--surface2));">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                        <i class="fas fa-folder-plus"></i>
                                    </div>
                                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">Pilih Jenis Input Dokumen</h3>
                                </div>
                                <button @click="showModal = false" style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 14px; transition: 0.2s;" onmouseover="this.style.color='#ef4444'">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Modal Body --}}
                        <div style="padding: 32px;">
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                {{-- Opsi 1: Pengajuan Baru --}}
                                <a href="{{ route('unit.kerjasama.create', ['type' => 'baru']) }}"
                                    class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); group;"
                                    onmouseover="this.style.borderColor='#4f46e5'; this.style.background='rgba(79,70,229,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-file-circle-plus"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Pengajuan Kerja Sama Baru</span>
                                        <p style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">Gunakan ini untuk dokumen yang baru akan dibuat, sedang diproses, atau menunggu pengesahan Pimpinan.</p>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: #9ca3af; font-size: 14px; opacity: 0; transition: 0.3s; transform: translateX(-10px);"></i>
                                </a>

                                {{-- Opsi 2: Arsip Lama --}}
                                <a href="{{ route('unit.kerjasama.create', ['type' => 'arsip']) }}"
                                    class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    onmouseover="this.style.borderColor='#d97706'; this.style.background='rgba(217,119,6,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(217,119,6,0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-box-archive"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Input Arsip Lama (Data Historis)</span>
                                        <p style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">Gunakan ini untuk memindahkan data tahun sebelumnya yang sudah selesai atau kadaluarsa ke sistem digital.</p>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: #9ca3af; font-size: 14px; opacity: 0; transition: 0.3s; transform: translateX(-10px);"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div style="padding: 20px 32px; background: var(--surface2); border-top: 1px solid var(--border); text-align: center;">
                            <span style="font-size: 11px; color: var(--text-sub); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pilih salah satu untuk melanjutkan pengisian data</span>
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
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 450px; min-width: 400px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berlaku</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="previewBody">
                        @forelse($kerjasamaList as $kegiatan)
                        @php
                        $status = strtolower($kegiatan->status ?? '');
                        $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
                        $isExtended = str_contains($status, 'perpanjangan');
                        $statusValue = $isExtended ? 'dalam perpanjangan' : ($isExpired ? 'kadarluarsa' : $status);

                        $statusClass = match (true) {
                        $status === 'aktif' => 'dk-status-active',
                        $status === 'proses' => 'dk-status-info',
                        $isExtended => 'dk-status-warning',
                        $isExpired => 'dk-status-danger',
                        $status === 'tidak aktif' => 'dk-status-muted',
                        default => 'dk-status-neutral',
                        };
                        $statusIcon = match (true) {
                        $status === 'aktif' => 'fa-circle-check',
                        $status === 'proses' => 'fa-spinner fa-spin',
                        $isExtended => 'fa-clock',
                        $isExpired => 'fa-circle-xmark',
                        $status === 'tidak aktif' => 'fa-circle-minus',
                        default => 'fa-circle-info',
                        };
                        $statusLabel = match (true) {
                        $status === 'aktif' => 'Aktif',
                        $isExtended => 'Perpanjangan',
                        $isExpired => 'Kadarluarsa',
                        $status === 'tidak aktif' => 'Tidak Aktif',
                        $status !== '' => ucwords($status),
                        default => 'Belum Diatur',
                        };

                        $pelaksanaIcon = $kegiatan->pelaksana_icon;
                        $pelaksanaClass = $kegiatan->pelaksana_class;
                        $pelaksanaName = $kegiatan->pelaksana_name;

                        $mulai = $kegiatan->start_date?->format('d M Y');
                        $selesai = $kegiatan->end_date?->format('d M Y');
                        $docNumber = $kegiatan->doc_number ?? '';
                        $title = $kegiatan->title ?? '';
                        $mitraName = $kegiatan->mitra?->nama_mitra ?? '';
                        @endphp
                        <tr class="um-row dk-row">
                            <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td dk-title-cell" style="width: 450px; min-width: 400px; vertical-align: top; padding-top: 15px;">
                                <div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">
                                    <span class="dk-doc-number">#{{ $docNumber ?: '-' }}</span>
                                    <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">{{ $title ?: '-' }}</span>
                                    <span class="dk-doc-kind">{{ $kegiatan->jenis ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon {{ $pelaksanaClass }}" style="flex-shrink: 0;">
                                        <i class="fas {{ $pelaksanaIcon }}"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $pelaksanaName }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 4px;">{{ $mitraName ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                <div class="dk-date-range-compact">
                                    <span class="date-val">{{ $mulai ?? '-' }}</span>
                                    <span class="date-sep">s/d</span>
                                    <span class="date-val">{{ $selesai ?? '-' }}</span>
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
                                    <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="dk-action-btn view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('unit.kerjasama.edit', $kegiatan->id) }}" class="dk-action-btn edit" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('unit.kerjasama.destroy', $kegiatan->id) }}" method="POST"
                                        class="dk-delete-form" style="display: inline;"
                                        onsubmit="return confirm('Yakin ingin menghapus data kerjasama ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dk-action-btn delete" title="Hapus">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
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
                                    <p class="um-empty-title">Belum ada data kerjasama</p>
                                    <p class="um-empty-sub">Tambahkan dokumen pertama agar repositori unit mulai terisi.</p>
                                    <a href="{{ route('unit.kerjasama.create') }}" class="dk-empty-btn">
                                        <i class="fas fa-plus"></i>
                                        Tambah Data
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    (function() {
    function initDkerjasamaFilter() {
        var form = document.getElementById('filterForm');
        var previewBody = document.getElementById('previewBody');
        var btnTampilkan = document.getElementById('btnTampilkan');
        var countLabel = document.getElementById('dkerjasamaCount');
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        if (!form || !previewBody || !btnTampilkan) return;
        if (form.dataset.filterBound === 'true') return;
        form.dataset.filterBound = 'true';

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function routeFromTemplate(template, id) {
            return String(template || '').replace('__ID__', encodeURIComponent(id));
        }

        function getFormParams() {
            var fd = new FormData(form);
            var params = new URLSearchParams();
            fd.forEach(function(val, key) {
                if (val && val !== 'all') params.append(key, val);
            });
            return params.toString();
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            var str = String(dateStr).split('T')[0];
            var d = new Date(str);
            if (isNaN(d.getTime())) return escapeHtml(dateStr);
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            return String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        function setCount(total) {
            if (countLabel) countLabel.textContent = total + ' data ditemukan';
        }

        function showLoading() {
            previewBody.innerHTML =
                '<tr><td colspan="7" style="text-align:center; padding: 40px 0;"><i class="fas fa-spinner fa-spin" style="font-size: 20px; color: var(--accent); opacity: 0.6;"></i><p style="margin-top: 10px; font-size: 12px; color: var(--text-sub);">Memuat data kerjasama...</p></td></tr>';
        }

        function showEmpty() {
            setCount(0);
            previewBody.innerHTML =
                '<tr data-empty><td colspan="7" class="um-empty"><div class="um-empty-state dk-empty-state"><div class="um-empty-icon dk-empty-icon"><i class="fas fa-folder-open"></i></div><p class="um-empty-title">Tidak ada data ditemukan</p><p class="um-empty-sub">Coba ubah filter untuk menampilkan data lain.</p></div></td></tr>';
        }

        function showError() {
            previewBody.innerHTML =
                '<tr><td colspan="7" class="um-empty"><div class="um-empty-state dk-empty-state"><p class="um-empty-title" style="color:#ef4444;">Gagal memuat data</p><p class="um-empty-sub">Terjadi kesalahan. Silakan coba lagi.</p></div></td></tr>';
        }

        function buildRow(item, idx) {
            var title = escapeHtml(item.title || '-');
            var docNumber = escapeHtml(item.doc_number || '-');
            var jenis = escapeHtml(item.jenis || '-');
            var mitraName = escapeHtml((item.mitra && item.mitra.nama_mitra) ? item.mitra.nama_mitra : '-');

            var pelaksanaIcon = 'fa-building';
            var pelaksanaClass = 'dk-entity-indigo';
            var pelaksanaName = item.pelaksana_name || '-';
            if (item.tipe_pelaksana === 'jurusan') {
                pelaksanaIcon = 'fa-microchip';
            } else if (item.tipe_pelaksana === 'upa') {
                pelaksanaIcon = 'fa-building-columns';
                pelaksanaClass = 'dk-entity-cyan';
            } else if (item.tipe_pelaksana === 'pusat') {
                pelaksanaIcon = 'fa-landmark';
                pelaksanaClass = 'dk-entity-violet';
            } else {
                pelaksanaIcon = item.pelaksana_icon || pelaksanaIcon;
                pelaksanaClass = item.pelaksana_class || pelaksanaClass;
            }

            var status = (item.status || '').toLowerCase();
            var isExpired = ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'].indexOf(status) !== -1;
            var isExtended = status.indexOf('perpanjangan') !== -1;
            var statusClass = 'dk-status-neutral';
            var statusIcon = 'fa-circle-info';
            var statusLabel = 'Belum Diatur';

            if (status === 'aktif') {
                statusClass = 'dk-status-active';
                statusIcon = 'fa-circle-check';
                statusLabel = 'Aktif';
            } else if (status === 'proses' || status === 'menunggu_validasi') {
                statusClass = 'dk-status-info';
                statusIcon = 'fa-spinner fa-spin';
                statusLabel = status === 'proses' ? 'Proses' : 'Menunggu Validasi';
            } else if (isExtended) {
                statusClass = 'dk-status-warning';
                statusIcon = 'fa-clock';
                statusLabel = 'Perpanjangan';
            } else if (isExpired) {
                statusClass = 'dk-status-danger';
                statusIcon = 'fa-circle-xmark';
                statusLabel = 'Kadarluarsa';
            } else if (status === 'tidak aktif') {
                statusClass = 'dk-status-muted';
                statusIcon = 'fa-circle-minus';
                statusLabel = 'Tidak Aktif';
            } else if (status !== '') {
                statusLabel = status.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            }

            var showUrl = routeFromTemplate(form.dataset.showUrlTemplate, item.id);
            var editUrl = routeFromTemplate(form.dataset.editUrlTemplate, item.id);
            var deleteUrl = routeFromTemplate(form.dataset.deleteUrlTemplate, item.id);

            var tr = document.createElement('tr');
            tr.className = 'um-row dk-row';
            tr.innerHTML =
                '<td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;"><span class="um-num dk-num">' + String(idx + 1).padStart(2, '0') + '</span></td>' +
                '<td class="um-td dk-title-cell" style="width: 450px; min-width: 400px; vertical-align: top; padding-top: 15px;"><div class="dk-doc-cell" style="white-space: normal; word-break: break-word;"><span class="dk-doc-number">#' + docNumber + '</span><span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">' + title + '</span><span class="dk-doc-kind">' + jenis + '</span></div></td>' +
                '<td class="um-td" style="vertical-align: top; padding-top: 15px;"><div class="dk-entity" style="align-items: flex-start;"><span class="dk-entity-icon ' + pelaksanaClass + '" style="flex-shrink: 0;"><i class="fas ' + pelaksanaIcon + '"></i></span><span class="dk-entity-text" style="padding-top: 4px;">' + escapeHtml(pelaksanaName) + '</span></div></td>' +
                '<td class="um-td" style="vertical-align: top; padding-top: 15px;"><div class="dk-entity" style="align-items: flex-start;"><span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;"><i class="fas fa-building"></i></span><span class="dk-entity-text" style="padding-top: 4px;">' + mitraName + '</span></div></td>' +
                '<td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;"><div class="dk-date-range-compact"><span class="date-val">' + formatDate(item.start_date) + '</span><span class="date-sep">s/d</span><span class="date-val">' + formatDate(item.end_date) + '</span></div></td>' +
                '<td class="um-td" style="vertical-align: top; padding-top: 15px;"><span class="dk-status ' + statusClass + '"><i class="fas ' + statusIcon + '"></i> ' + statusLabel + '</span></td>' +
                '<td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;"><div class="um-actions dk-actions-compact"><a href="' + showUrl + '" class="dk-action-btn view" title="Detail"><i class="fas fa-eye"></i></a><a href="' + editUrl + '" class="dk-action-btn edit" title="Edit"><i class="fas fa-pen-to-square"></i></a><form action="' + deleteUrl + '" method="POST" class="dk-delete-form" style="display: inline;" onsubmit="return confirm(&quot;Yakin ingin menghapus data kerjasama ini?&quot;)"><input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="dk-action-btn delete" title="Hapus"><i class="fas fa-trash-can"></i></button></form></div></td>';
            return tr;
        }

        function loadData() {
            showLoading();
            btnTampilkan.disabled = true;
            btnTampilkan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';

            var query = getFormParams();
            var url = form.dataset.previewUrl + (query ? '?' + query : '');

            fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data || data.length === 0) {
                        showEmpty();
                        return;
                    }

                    setCount(data.length);
                    var fragment = document.createDocumentFragment();
                    data.forEach(function(item, idx) {
                        fragment.appendChild(buildRow(item, idx));
                    });
                    previewBody.innerHTML = '';
                    previewBody.appendChild(fragment);
                })
                .catch(function(err) {
                    console.error(err);
                    showError();
                })
                .finally(function() {
                    btnTampilkan.disabled = false;
                    btnTampilkan.innerHTML = '<i class="fas fa-search"></i> Tampilkan';
                });
        }

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            loadData();
        });

        document.getElementById('btnCetakPdf').addEventListener('click', function() {
            var query = getFormParams();
            window.open(form.dataset.pdfUrl + (query ? '?' + query : ''), '_blank');
        });

        document.getElementById('btnExportExcel').addEventListener('click', function() {
            var query = getFormParams();
            window.open(form.dataset.excelUrl + (query ? '?' + query : ''), '_blank');
        });
    }

    document.addEventListener('DOMContentLoaded', initDkerjasamaFilter);
    document.addEventListener('turbo:load', initDkerjasamaFilter);
    if (document.readyState !== 'loading') {
        initDkerjasamaFilter();
    }
    })();
</script>

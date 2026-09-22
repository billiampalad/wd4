@extends('admin.dashboard')

@section('content')
<main class="main-content admin-dashboard">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <span>Program Studi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-graduation-cap"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Program Studi</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data Program Studi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @php
        $totalProdi = $prodis->count();
        $d3Count = $prodis->where('jenjang', 'D3')->count();
        $d4Count = $prodis->where('jenjang', 'D4')->count();
        $s1Count = $prodis->where('jenjang', 'S1')->count();
        $s2Count = $prodis->where('jenjang', 'S2')->count();

        $presetItems = [
            ['label' => 'Semua Jenjang', 'value' => 'all', 'icon' => 'fas fa-layer-group', 'count' => $totalProdi],
        ];
        if ($d3Count > 0) $presetItems[] = ['label' => 'D3', 'value' => 'd3', 'icon' => 'fas fa-award', 'count' => $d3Count];
        if ($d4Count > 0) $presetItems[] = ['label' => 'D4', 'value' => 'd4', 'icon' => 'fas fa-graduation-cap', 'count' => $d4Count];
        if ($s1Count > 0) $presetItems[] = ['label' => 'S1', 'value' => 's1', 'icon' => 'fas fa-user-graduate', 'count' => $s1Count];
        if ($s2Count > 0) $presetItems[] = ['label' => 'S2', 'value' => 's2', 'icon' => 'fas fa-book-open-reader', 'count' => $s2Count];
    @endphp

    <x-filter 
        id="prodiFilter"
        title="Filter Data Program Studi"
        subtitle="Saring data program studi berdasarkan jenjang pendidikan, jurusan terkait, dan kode/nama prodi"
        icon="fas fa-sliders-h"
        variant="panel"
        :collapsible="true"
        :collapsed="false"
        presetName="jenjang"
        :presets="$presetItems"
        target=".um-table tbody tr.um-row"
        emptyTarget="#prodiSearchEmptyRow"
        resetText="Reset"
    >
        <!-- Field 1: Jurusan (Alpine.js Dropdown) -->
        <div class="custom-filter-group" x-data="{
            open: false,
            selected: 'all',
            items: @js($jurusans->map(fn ($jurusan) => [
                'id' => strtolower($jurusan->nama_jurusan),
                'label' => $jurusan->nama_jurusan,
            ])->prepend(['id' => 'all', 'label' => 'Semua Jurusan'])->values()),
            get selectedLabel() {
                const found = this.items.find(i => i.id === this.selected);
                return found ? found.label : 'Semua Jurusan';
            }
        }">
            <label class="custom-filter-label"><i class="fas fa-microchip"></i> Jurusan</label>
            <input type="hidden" name="jurusan" :value="selected">
            
            <div class="alpine-dropdown" @click.outside="open = false">
                <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                    <div class="ad-trigger-content">
                        <i class="fas fa-building-columns ad-trigger-icon"></i>
                        <span x-text="selectedLabel"></span>
                    </div>
                    <i class="fas fa-chevron-down ad-trigger-chevron"></i>
                </div>
                <div class="ad-menu" x-show="open" x-transition>
                    <template x-for="item in items" :key="item.id">
                        <div class="ad-item" :class="{ 'selected': selected === item.id }"
                            @click="selected = item.id; open = false; $nextTick(() => { document.querySelector('input[name=jurusan]').dispatchEvent(new Event('change', { bubbles: true })); })">
                            <span x-text="item.label"></span>
                            <i class="fas fa-check ad-item-check"></i>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Field 2: Jenjang Pendidikan (Alpine.js Dropdown) -->
        <div class="custom-filter-group" x-data="{
            open: false,
            selected: 'all',
            items: [
                { id: 'all', label: 'Semua Jenjang' },
                { id: 'd3', label: 'Diploma 3 (D3)' },
                { id: 'd4', label: 'Diploma 4 (D4 / Sarjana Terapan)' },
                { id: 's1', label: 'Sarjana (S1)' },
                { id: 's2', label: 'Magister Terapan (S2)' }
            ],
            get selectedLabel() {
                const found = this.items.find(i => i.id === this.selected);
                return found ? found.label : 'Semua Jenjang';
            }
        }">
            <label class="custom-filter-label"><i class="fas fa-layer-group"></i> Jenjang Pendidikan</label>
            <input type="hidden" name="jenjang" :value="selected">
            
            <div class="alpine-dropdown" @click.outside="open = false">
                <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                    <div class="ad-trigger-content">
                        <i class="fas fa-graduation-cap ad-trigger-icon"></i>
                        <span x-text="selectedLabel"></span>
                    </div>
                    <i class="fas fa-chevron-down ad-trigger-chevron"></i>
                </div>
                <div class="ad-menu" x-show="open" x-transition>
                    <template x-for="item in items" :key="item.id">
                        <div class="ad-item" :class="{ 'selected': selected === item.id }"
                            @click="selected = item.id; open = false; $nextTick(() => { document.querySelector('input[name=jenjang]').dispatchEvent(new Event('change', { bubbles: true })); })">
                            <span x-text="item.label"></span>
                            <i class="fas fa-check ad-item-check"></i>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Field 3: Cari Kode / Nama Prodi -->
        <div class="custom-filter-group">
            <label class="custom-filter-label"><i class="fas fa-search"></i> Kode / Nama Prodi</label>
            <div class="custom-filter-control-wrap has-left-icon">
                <input type="text" name="prodi_query" class="custom-filter-input" placeholder="Ketik kode atau nama prodi...">
                <span class="custom-filter-input-icon"><i class="fas fa-barcode"></i></span>
            </div>
        </div>
    </x-filter>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-graduation-cap"></i> Daftar Program Studi</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="prodiSearchInput" placeholder="Cari program studi..." target=".um-table tbody tr.um-row"
                    emptyTarget="#prodiSearchEmptyRow" querySpan="#prodiSearchQueryText" />
                <button type="button" class="um-btn-add" onclick="openCreateProdiModal()">
                    <i class="fas fa-plus"></i> Tambah Prodi
                </button>
            </div>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Kode</th>
                        <th class="um-th">Nama Prodi</th>
                        <th class="um-th">Jurusan</th>
                        <th class="um-th">Jenjang</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $i => $prodi)
                        <tr class="um-row"
                            data-filter-jenjang="{{ strtolower($prodi->jenjang ?? '') }}"
                            data-filter-jurusan="{{ strtolower($prodi->jurusan?->nama_jurusan ?? '') }}"
                        >
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-meta" style="font-family: monospace;">{{ $prodi->kode_prodi ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $prodi->nama_prodi }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-meta">{{ $prodi->jurusan->nama_jurusan ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <span class="tag tag-blue" style="font-size: 11px;">{{ $prodi->jenjang }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $prodi->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <button type="button" class="btn-action edit um-btn-edit" title="Edit"
                                        onclick="openEditProdiModal({{ $prodi->id }}, '{{ $prodi->jurusan_id }}', '{{ addslashes($prodi->kode_prodi ?? '') }}', '{{ addslashes($prodi->nama_prodi) }}', '{{ addslashes($prodi->jenjang) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="form-delete-prodi-{{ $prodi->id }}" action="{{ route('prodi.destroy', $prodi->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                            title: 'Hapus Program Studi',
                                            message: 'Menghapus program studi <span class=\'custom-alert-highlight\'>{{ addslashes($prodi->nama_prodi) }}</span> tidak dapat dikembalikan.',
                                            type: 'danger',
                                            confirmText: 'Hapus',
                                            confirmColor: 'danger',
                                            onConfirm: () => document.getElementById('form-delete-prodi-{{ $prodi->id }}').submit()
                                        })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="um-empty">
                                <div class="um-empty-state">
                                    <div class="um-empty-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data Program Studi</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah Prodi</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="prodiSearchEmptyRow" style="display: none;">
                        <td colspan="7" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada program studi yang cocok dengan kata kunci "<span
                                        id="prodiSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="prodiTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>

    {{-- Modal Tambah Prodi --}}
    <div id="createProdiModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'createProdiModal')">
        <div class="adm-modal-container adm-modal-md" style="overflow: visible;">
            <div class="adm-modal-header" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-indigo">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Tambah Program Studi</h3>
                        <p class="adm-modal-subtitle">Isi formulir untuk menambahkan program studi baru.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('createProdiModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('prodi.store') }}" method="POST" id="createProdiForm">
                @csrf
                <div class="adm-modal-body">
                    {{-- Dropdown Jurusan dengan Alpine.js --}}
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_jurusan_id">
                            <i class="fas fa-microchip"></i> Jurusan <span class="adm-required">*</span>
                        </label>
                        <div
                            class="uc-alpine-select"
                            x-data="adminUserSelect({
                                placeholder: '-- Pilih Jurusan --',
                                selectedValue: '',
                                items: @js($jurusans->map(fn ($jurusan) => [
                                    'value' => (string) $jurusan->id,
                                    'label' => $jurusan->nama_jurusan,
                                ])->values())
                            })"
                            x-init="init()"
                            :class="{ 'is-open': open }"
                            @click.outside="open = false"
                        >
                            <select
                                id="create_jurusan_id"
                                name="jurusan_id"
                                class="uc-native-select"
                                x-model="selectedValue"
                                @change="syncFromNative()"
                                tabindex="-1"
                                aria-hidden="true"
                                required
                            >
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            </select>
                            <button
                                type="button"
                                class="uc-select-trigger"
                                :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                @click="toggle()"
                                :disabled="disabled"
                            >
                                <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                <i class="fas fa-chevron-down uc-select-chevron"></i>
                            </button>
                            <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                <template x-for="item in items" :key="item.value">
                                    <button
                                        type="button"
                                        class="uc-select-option"
                                        :class="{ 'is-selected': selectedValue === item.value }"
                                        @click="choose(item)"
                                    >
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Nama Program Studi --}}
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_nama_prodi">
                            <i class="fas fa-graduation-cap"></i> Nama Program Studi <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="create_nama_prodi" name="nama_prodi" class="adm-form-input"
                            placeholder="Contoh: Teknik Informatika" required maxlength="150">
                    </div>

                    {{-- Grid Kode & Jenjang --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="create_kode_prodi">
                                <i class="fas fa-barcode"></i> Kode Prodi
                            </label>
                            <input type="text" id="create_kode_prodi" name="kode_prodi" class="adm-form-input"
                                placeholder="Contoh: TI01 (opsional)" maxlength="20">
                        </div>

                        {{-- Dropdown Jenjang dengan Alpine.js --}}
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="create_jenjang">
                                <i class="fas fa-layer-group"></i> Jenjang <span class="adm-required">*</span>
                            </label>
                            <div
                                class="uc-alpine-select"
                                x-data="adminUserSelect({
                                    placeholder: '-- Pilih Jenjang --',
                                    selectedValue: 'D4',
                                    items: [
                                        { value: 'D3', label: 'D3' },
                                        { value: 'D4', label: 'D4' },
                                        { value: 'S1', label: 'S1' },
                                        { value: 'S2', label: 'S2' }
                                    ]
                                })"
                                x-init="init()"
                                :class="{ 'is-open': open }"
                                @click.outside="open = false"
                            >
                                <select
                                    id="create_jenjang"
                                    name="jenjang"
                                    class="uc-native-select"
                                    x-model="selectedValue"
                                    @change="syncFromNative()"
                                    tabindex="-1"
                                    aria-hidden="true"
                                    required
                                >
                                    <option value="D3">D3</option>
                                    <option value="D4" selected>D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                </select>
                                <button
                                    type="button"
                                    class="uc-select-trigger"
                                    :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                    @click="toggle()"
                                    :disabled="disabled"
                                >
                                    <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                    <i class="fas fa-chevron-down uc-select-chevron"></i>
                                </button>
                                <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                    <template x-for="item in items" :key="item.value">
                                        <button
                                            type="button"
                                            class="uc-select-option"
                                            :class="{ 'is-selected': selectedValue === item.value }"
                                            @click="choose(item)"
                                        >
                                            <span x-text="item.label"></span>
                                            <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="adm-modal-footer" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('createProdiModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Prodi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Prodi --}}
    <div id="editProdiModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'editProdiModal')">
        <div class="adm-modal-container adm-modal-md" style="overflow: visible;">
            <div class="adm-modal-header" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-emerald">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Edit Program Studi</h3>
                        <p class="adm-modal-subtitle">Ubah data program studi yang sudah ada.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('editProdiModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editProdiForm">
                @csrf
                @method('PUT')
                <div class="adm-modal-body">
                    {{-- Dropdown Jurusan Edit dengan Alpine.js --}}
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_jurusan_id">
                            <i class="fas fa-microchip"></i> Jurusan <span class="adm-required">*</span>
                        </label>
                        <div
                            class="uc-alpine-select"
                            x-data="adminUserSelect({
                                placeholder: '-- Pilih Jurusan --',
                                selectedValue: '',
                                items: @js($jurusans->map(fn ($jurusan) => [
                                    'value' => (string) $jurusan->id,
                                    'label' => $jurusan->nama_jurusan,
                                ])->values())
                            })"
                            x-init="init()"
                            :class="{ 'is-open': open }"
                            @click.outside="open = false"
                        >
                            <select
                                id="edit_jurusan_id"
                                name="jurusan_id"
                                class="uc-native-select"
                                x-model="selectedValue"
                                @change="syncFromNative()"
                                tabindex="-1"
                                aria-hidden="true"
                                required
                            >
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            </select>
                            <button
                                type="button"
                                class="uc-select-trigger"
                                :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                @click="toggle()"
                                :disabled="disabled"
                            >
                                <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                <i class="fas fa-chevron-down uc-select-chevron"></i>
                            </button>
                            <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                <template x-for="item in items" :key="item.value">
                                    <button
                                        type="button"
                                        class="uc-select-option"
                                        :class="{ 'is-selected': selectedValue === item.value }"
                                        @click="choose(item)"
                                    >
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Nama Program Studi --}}
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_nama_prodi">
                            <i class="fas fa-graduation-cap"></i> Nama Program Studi <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="edit_nama_prodi" name="nama_prodi" class="adm-form-input"
                            placeholder="Ubah nama program studi" required maxlength="150">
                    </div>

                    {{-- Grid Kode & Jenjang --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="edit_kode_prodi">
                                <i class="fas fa-barcode"></i> Kode Prodi
                            </label>
                            <input type="text" id="edit_kode_prodi" name="kode_prodi" class="adm-form-input"
                                placeholder="Contoh: TI01 (opsional)" maxlength="20">
                        </div>

                        {{-- Dropdown Jenjang Edit dengan Alpine.js --}}
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="edit_jenjang">
                                <i class="fas fa-layer-group"></i> Jenjang <span class="adm-required">*</span>
                            </label>
                            <div
                                class="uc-alpine-select"
                                x-data="adminUserSelect({
                                    placeholder: '-- Pilih Jenjang --',
                                    selectedValue: '',
                                    items: [
                                        { value: 'D3', label: 'D3' },
                                        { value: 'D4', label: 'D4' },
                                        { value: 'S1', label: 'S1' },
                                        { value: 'S2', label: 'S2' }
                                    ]
                                })"
                                x-init="init()"
                                :class="{ 'is-open': open }"
                                @click.outside="open = false"
                            >
                                <select
                                    id="edit_jenjang"
                                    name="jenjang"
                                    class="uc-native-select"
                                    x-model="selectedValue"
                                    @change="syncFromNative()"
                                    tabindex="-1"
                                    aria-hidden="true"
                                    required
                                >
                                    <option value="">-- Pilih --</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                </select>
                                <button
                                    type="button"
                                    class="uc-select-trigger"
                                    :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                    @click="toggle()"
                                    :disabled="disabled"
                                >
                                    <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                    <i class="fas fa-chevron-down uc-select-chevron"></i>
                                </button>
                                <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                    <template x-for="item in items" :key="item.value">
                                        <button
                                            type="button"
                                            class="uc-select-option"
                                            :class="{ 'is-selected': selectedValue === item.value }"
                                            @click="choose(item)"
                                        >
                                            <span x-text="item.label"></span>
                                            <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="adm-modal-footer" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('editProdiModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function openCreateProdiModal() {
        const createJurusanSelect = document.getElementById('create_jurusan_id');
        if (createJurusanSelect) {
            createJurusanSelect.value = '';
            createJurusanSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
        const createJenjangSelect = document.getElementById('create_jenjang');
        if (createJenjangSelect) {
            createJenjangSelect.value = 'D4';
            createJenjangSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        AdminModal.open('createProdiModal', {
            focusSelector: '#create_nama_prodi',
            resetForm: true
        });
    }

    function openEditProdiModal(id, jurusanId, kodeProdi, namaProdi, jenjang) {
        const form = document.getElementById('editProdiForm');
        if (form) {
            form.action = "{{ route('prodi.update', ':id') }}".replace(':id', id);
        }
        const kodeInput = document.getElementById('edit_kode_prodi');
        const namaInput = document.getElementById('edit_nama_prodi');
        if (kodeInput) kodeInput.value = kodeProdi || '';
        if (namaInput) namaInput.value = namaProdi || '';

        const jurusanSelect = document.getElementById('edit_jurusan_id');
        if (jurusanSelect) {
            jurusanSelect.value = String(jurusanId || '');
            jurusanSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const jenjangSelect = document.getElementById('edit_jenjang');
        if (jenjangSelect) {
            jenjangSelect.value = jenjang || 'D4';
            jenjangSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        AdminModal.open('editProdiModal', {
            focusSelector: '#edit_nama_prodi'
        });
    }
</script>
@endsection

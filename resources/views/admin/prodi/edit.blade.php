@extends('admin.dashboard')

@section('content')
<main class="main-content admin-dashboard prodi-form-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <a href="{{ route('prodi.index') }}" class="ud-breadcrumb-link">Program Studi</a>
                <span>/</span>
                <span>Edit Prodi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Edit Program Studi</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Ubah data program studi yang sudah ada.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="uc-layout prodi-form-layout">
        <div class="uc-form-col">
            <div class="card uc-form-card prodi-form-card">
                <div class="card-header uc-form-header prodi-form-header">
                    <div class="card-title"><i class="fas fa-edit"></i> Formulir Edit Prodi</div>
                    <span class="prodi-edit-badge">
                        <i class="fas fa-circle"></i> Mode Edit
                    </span>
                </div>

                <form action="{{ route('prodi.update', $prodi->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body uc-body prodi-form-body">
                        <div class="uc-section-label">
                            <span class="uc-section-num">01</span>
                            <span>Informasi Program Studi</span>
                        </div>

                        <div class="uc-form-group prodi-field-full">
                            <label class="uc-label" for="jurusan_id">
                                <i class="fas fa-microchip uc-label-icon"></i>
                                Jurusan
                                <span class="uc-required">*</span>
                            </label>
                            <div
                                class="uc-alpine-select @error('jurusan_id') uc-select-trigger-error @enderror"
                                x-data="adminUserSelect({
                                    placeholder: '-- Pilih Jurusan --',
                                    selectedValue: @js((string) old('jurusan_id', $prodi->jurusan_id)),
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
                                    id="jurusan_id"
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
                                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $prodi->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                                <button
                                    type="button"
                                    class="uc-select-trigger @error('jurusan_id') uc-select-trigger-error @enderror"
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
                            @error('jurusan_id')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="uc-grid-2 prodi-form-grid">
                            <div class="uc-form-group">
                                <label class="uc-label" for="kode_prodi">
                                    <i class="fas fa-barcode uc-label-icon"></i>
                                    Kode Prodi
                                </label>
                                <input
                                    type="text"
                                    id="kode_prodi"
                                    name="kode_prodi"
                                    class="uc-input @error('kode_prodi') uc-input-error @enderror"
                                    value="{{ old('kode_prodi', $prodi->kode_prodi) }}"
                                    placeholder="Contoh: TI01"
                                    maxlength="20"
                                >
                                @error('kode_prodi')
                                    <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="uc-form-group">
                                <label class="uc-label" for="nama_prodi">
                                    <i class="fas fa-graduation-cap uc-label-icon"></i>
                                    Nama Program Studi
                                    <span class="uc-required">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_prodi"
                                    name="nama_prodi"
                                    class="uc-input @error('nama_prodi') uc-input-error @enderror"
                                    value="{{ old('nama_prodi', $prodi->nama_prodi) }}"
                                    placeholder="Ubah nama program studi"
                                    required
                                    maxlength="150"
                                >
                                @error('nama_prodi')
                                    <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="uc-form-group prodi-field-jenjang">
                                <label class="uc-label" for="jenjang">
                                    <i class="fas fa-layer-group uc-label-icon"></i>
                                    Jenjang
                                    <span class="uc-required">*</span>
                                </label>
                                <div
                                    class="uc-alpine-select @error('jenjang') uc-select-trigger-error @enderror"
                                    x-data="adminUserSelect({
                                        placeholder: '-- Pilih Jenjang --',
                                        selectedValue: @js((string) old('jenjang', $prodi->jenjang)),
                                        items: @js(collect(['D3', 'D4', 'S1', 'S2'])->map(fn ($jenjang) => [
                                            'value' => $jenjang,
                                            'label' => $jenjang,
                                        ])->values())
                                    })"
                                    x-init="init()"
                                    :class="{ 'is-open': open }"
                                    @click.outside="open = false"
                                >
                                    <select
                                        id="jenjang"
                                        name="jenjang"
                                        class="uc-native-select"
                                        x-model="selectedValue"
                                        @change="syncFromNative()"
                                        tabindex="-1"
                                        aria-hidden="true"
                                        required
                                    >
                                        @foreach(['D3', 'D4', 'S1', 'S2'] as $j)
                                            <option value="{{ $j }}" {{ old('jenjang', $prodi->jenjang) == $j ? 'selected' : '' }}>
                                                {{ $j }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="uc-select-trigger @error('jenjang') uc-select-trigger-error @enderror"
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
                                @error('jenjang')
                                    <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer uc-footer prodi-form-footer">
                        <a href="{{ route('prodi.index') }}" class="uc-btn-cancel">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="uc-btn-submit">
                            <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

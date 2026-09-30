@extends('admin.dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/unit/mitra/modal_create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/unit/mitra/modal_edit.css') }}">
@endsection

@section('content')
@php
    $mitraKlasifikasiItems = ($klasifikasis ?? collect())->map(fn ($klas) => [
        'id' => (string) $klas->id,
        'label' => $klas->nama,
    ])->values();
@endphp

<main class="main-content admin-dashboard mitra-form-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <a href="{{ route('mitra.index') }}" class="ud-breadcrumb-link">Mitra</a>
                <span>/</span>
                <span>Edit Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Edit Data Mitra</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Perbarui informasi instansi mitra kerjasama sesuai formulir di bawah ini.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="uc-layout mitra-form-layout">
        <div class="uc-form-col">
            <div class="card uc-form-card mitra-page-card">
                <div class="mitra-edit-header mitra-page-form-header">
                    <div class="mitra-edit-header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="mitra-edit-header-text">
                        <h3 class="mitra-edit-title">Formulir Edit Data Mitra</h3>
                        <p class="mitra-edit-subtitle">Perbarui informasi instansi mitra kerjasama</p>
                    </div>
                </div>

                <form action="{{ route('mitra.update', $mitra->id) }}" method="POST" x-data="adminMitraEditForm({
                    klasifikasiItems: {{ Js::from($mitraKlasifikasiItems) }},
                    selectedKlasifikasi: {{ Js::from((string) old('id_klasifikasi', $mitra->id_klasifikasi)) }},
                    kategori: {{ Js::from(old('kategori', $mitra->kategori)) }},
                    negara: {{ Js::from(old('negara', $mitra->negara ?: 'Indonesia')) }},
                    provinsi: {{ Js::from(old('provinsi', $mitra->provinsi ?? '')) }},
                    kota: {{ Js::from(old('kota', $mitra->kota ?? '')) }},
                    kecamatan: {{ Js::from(old('kecamatan', $mitra->kecamatan ?? '')) }},
                    kelurahan: {{ Js::from(old('kelurahan', $mitra->kelurahan ?? '')) }}
                })" x-init="init()">
                    @csrf
                    @method('PUT')
                    
                    {{-- Hidden inputs for bound state --}}
                    <input type="hidden" name="id_klasifikasi" :value="klasifikasiSelected">
                    <input type="hidden" name="kategori" :value="kategori">
                    <input type="hidden" name="negara" :value="kategori === 'internasional' ? negara : 'Indonesia'">
                    <input type="hidden" name="provinsi" :value="kategori === 'nasional' ? provinsi : ''">
                    <input type="hidden" name="kota" :value="kategori === 'nasional' ? kota : kotaInternasional">
                    <input type="hidden" name="kecamatan" :value="kategori === 'nasional' ? kecamatan : ''">
                    <input type="hidden" name="kelurahan" :value="kategori === 'nasional' ? kelurahan : ''">

                    <div class="mitra-edit-content mitra-page-form-content">
                        {{-- 1. Klasifikasi Mitra --}}
                        <div class="mc-group mitra-edit-section">
                            <label class="mc-label">Klasifikasi Mitra</label>
                            <div class="alpine-dropdown" @click.outside="klasifikasiOpen = false; klasifikasiSearch = ''">
                                <div class="ad-trigger no-icon" :class="{'active': klasifikasiOpen}"
                                    @click="klasifikasiOpen = !klasifikasiOpen; $nextTick(() => { if (klasifikasiOpen) $refs.mkeSearch.focus() })">
                                    <div class="mitra-edit-trigger-content">
                                        <div class="mitra-edit-field-icon">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <span x-show="!selectedKlasifikasi" class="mitra-edit-placeholder">- Pilih Klasifikasi -</span>
                                        <span x-show="selectedKlasifikasi" x-text="selectedKlasifikasi ? selectedKlasifikasi.label : ''" class="mitra-edit-selected"></span>
                                    </div>
                                    <i class="fas fa-chevron-down mitra-edit-chevron" :class="{'is-open': klasifikasiOpen}"></i>
                                </div>

                                <div class="ad-menu mitra-edit-menu is-scrollable" x-show="klasifikasiOpen" x-transition>
                                    <div class="mitra-edit-search-wrap">
                                        <div class="mitra-edit-search">
                                            <i class="fas fa-search"></i>
                                            <input x-ref="mkeSearch" x-model="klasifikasiSearch" type="text" placeholder="Cari klasifikasi..." @click.stop>
                                        </div>
                                    </div>
                                    <div class="mitra-edit-menu-list">
                                        <template x-for="item in filteredKlasifikasi" :key="item.id">
                                            <div class="ad-item mitra-edit-check-row" :class="{'selected': klasifikasiSelected === item.id}"
                                                @click="klasifikasiSelected = item.id; klasifikasiOpen = false; klasifikasiSearch = ''">
                                                <div class="mitra-edit-check" :class="{'is-selected': klasifikasiSelected === item.id}">
                                                    <i class="fas fa-check" x-show="klasifikasiSelected === item.id"></i>
                                                </div>
                                                <span x-text="item.label" class="mitra-edit-item-text"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredKlasifikasi.length === 0" class="mitra-edit-empty">Tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>
                            @error('id_klasifikasi')
                                <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 2. Nama Mitra & Kategori --}}
                        <div class="mc-grid-2 mitra-edit-row">
                            <div class="mc-group">
                                <label class="mc-label">Nama Instansi / Mitra <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <input type="text" name="nama_mitra" required placeholder="Masukkan nama instansi/mitra"
                                        class="mc-input no-icon @error('nama_mitra') uc-input-error @enderror"
                                        value="{{ old('nama_mitra', $mitra->nama_mitra) }}">
                                </div>
                                @error('nama_mitra')
                                    <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mc-group" x-data="{ katOpen: false }">
                                <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                                <div class="alpine-dropdown" @click.outside="katOpen = false">
                                    <div class="ad-trigger no-icon" :class="{'active': katOpen}" @click="katOpen = !katOpen">
                                        <span x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '- Pilih Kategori -')" class="mitra-edit-item-text"></span>
                                        <i class="fas fa-chevron-down mitra-edit-chevron is-small" :class="{'is-open': katOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-edit-menu" x-show="katOpen" x-transition>
                                        <div class="ad-item" :class="{'selected': kategori === 'nasional'}" @click="kategori = 'nasional'; negara = 'Indonesia'; katOpen = false">Nasional</div>
                                        <div class="ad-item" :class="{'selected': kategori === 'internasional'}" @click="kategori = 'internasional'; if (negara === 'Indonesia') negara = ''; katOpen = false">Internasional</div>
                                    </div>
                                </div>
                                @error('kategori')
                                    <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- 3. Wilayah Indonesia (Nasional) --}}
                        <div x-show="kategori === 'nasional'" x-transition class="mitra-edit-row">
                            <div class="mc-grid-2">
                                {{-- Provinsi --}}
                                <div class="mc-group">
                                    <label class="mc-label">
                                        <i class="fas fa-map-marked-alt mitra-edit-label-icon"></i>Provinsi
                                    </label>
                                    <div class="alpine-dropdown" @click.outside="provinceOpen = false; provinceSearch = ''">
                                        <div class="ad-trigger no-icon" :class="{'active': provinceOpen}"
                                            @click="provinceOpen = !provinceOpen; $nextTick(() => { if (provinceOpen) $refs.mkeProvinceSearch.focus() })">
                                            <div class="mitra-edit-trigger-content is-compact">
                                                <i class="fas fa-map-pin mitra-edit-muted-icon"></i>
                                                <span x-show="!provinsi" class="mitra-edit-placeholder">- Pilih Provinsi -</span>
                                                <span x-show="provinsi" x-text="provinsi" class="mitra-edit-selected is-normal"></span>
                                            </div>
                                            <i class="fas fa-chevron-down mitra-edit-chevron is-small" :class="{'is-open': provinceOpen}"></i>
                                        </div>
                                        <div class="ad-menu mitra-edit-menu is-scrollable" x-show="provinceOpen" x-transition>
                                            <div class="mitra-edit-search-wrap">
                                                <div class="mitra-edit-search">
                                                    <i class="fas fa-search"></i>
                                                    <input x-ref="mkeProvinceSearch" x-model="provinceSearch" type="text" placeholder="Cari provinsi..." @click.stop>
                                                </div>
                                            </div>
                                            <div class="mitra-edit-menu-list is-country">
                                                <div class="ad-item" :class="{'selected': !provinsi}" @click="selectProvince(null)">- Pilih Provinsi -</div>
                                                <template x-for="item in filteredProvinces" :key="item.id || item.name">
                                                    <div class="ad-item" :class="{'selected': provinsi === item.name}" @click="selectProvince(item)" x-text="item.name"></div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @error('provinsi')
                                        <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Kota / Kabupaten --}}
                                <div class="mc-group">
                                    <label class="mc-label">
                                        <i class="fas fa-city mitra-edit-label-icon"></i>Kota / Kabupaten
                                    </label>
                                    <div class="alpine-dropdown" @click.outside="cityOpen = false; citySearch = ''">
                                        <div class="ad-trigger no-icon" :class="{'active': cityOpen, 'disabled': !provinsi}"
                                            @click="if (!provinsi) return; cityOpen = !cityOpen; $nextTick(() => { if (cityOpen) $refs.mkeCitySearch.focus() })">
                                            <div class="mitra-edit-trigger-content is-compact">
                                                <i class="fas fa-city mitra-edit-muted-icon"></i>
                                                <span x-show="!kota && !provinsi" class="mitra-edit-placeholder">- Pilih Provinsi Dahulu -</span>
                                                <span x-show="!kota && provinsi" class="mitra-edit-placeholder">- Pilih Kota / Kabupaten -</span>
                                                <span x-show="kota" x-text="kota" class="mitra-edit-selected is-normal"></span>
                                            </div>
                                            <i class="fas fa-chevron-down mitra-edit-chevron is-small" :class="{'is-open': cityOpen}"></i>
                                        </div>
                                        <div class="ad-menu mitra-edit-menu is-scrollable" x-show="cityOpen" x-transition>
                                            <div class="mitra-edit-search-wrap">
                                                <div class="mitra-edit-search">
                                                    <i class="fas fa-search"></i>
                                                    <input x-ref="mkeCitySearch" x-model="citySearch" type="text" placeholder="Cari kota/kabupaten..." @click.stop>
                                                </div>
                                            </div>
                                            <div class="mitra-edit-menu-list is-country">
                                                <div class="ad-item" :class="{'selected': !kota}" @click="selectCity(null)">- Pilih Kota / Kabupaten -</div>
                                                <div x-show="loadingCities" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                    <i class="fas fa-spinner fa-spin me-1"></i> Memuat data...
                                                </div>
                                                <template x-for="item in filteredCities" :key="item.id || item.name">
                                                    <div class="ad-item" :class="{'selected': kota === item.name}" @click="selectCity(item)" x-text="item.name"></div>
                                                </template>
                                                <div x-show="!loadingCities && filteredCities.length === 0 && citySearch" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                    Tidak ada kota/kabupaten ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('kota')
                                        <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mc-grid-2" style="margin-top: 12px;">
                                {{-- Kecamatan --}}
                                <div class="mc-group">
                                    <label class="mc-label">
                                        <i class="fas fa-map-location-dot mitra-edit-label-icon"></i>Kecamatan
                                    </label>
                                    <div class="alpine-dropdown" @click.outside="districtOpen = false; districtSearch = ''">
                                        <div class="ad-trigger no-icon" :class="{'active': districtOpen, 'disabled': !kota}"
                                            @click="if (!kota) return; districtOpen = !districtOpen; $nextTick(() => { if (districtOpen) $refs.mkeDistrictSearch.focus() })">
                                            <div class="mitra-edit-trigger-content is-compact">
                                                <i class="fas fa-map-location-dot mitra-edit-muted-icon"></i>
                                                <span x-show="!kecamatan && !kota" class="mitra-edit-placeholder">- Pilih Kota Dahulu -</span>
                                                <span x-show="!kecamatan && kota" class="mitra-edit-placeholder">- Pilih Kecamatan (Opsional) -</span>
                                                <span x-show="kecamatan" x-text="kecamatan" class="mitra-edit-selected is-normal"></span>
                                            </div>
                                            <i class="fas fa-chevron-down mitra-edit-chevron is-small" :class="{'is-open': districtOpen}"></i>
                                        </div>
                                        <div class="ad-menu mitra-edit-menu is-scrollable" x-show="districtOpen" x-transition>
                                            <div class="mitra-edit-search-wrap">
                                                <div class="mitra-create-search">
                                                    <i class="fas fa-search"></i>
                                                    <input x-ref="mkeDistrictSearch" x-model="districtSearch" type="text" placeholder="Cari kecamatan..." @click.stop>
                                                </div>
                                            </div>
                                            <div class="mitra-edit-menu-list is-country">
                                                <div class="ad-item" :class="{'selected': !kecamatan}" @click="selectDistrict(null)">- Pilih Kecamatan -</div>
                                                <div x-show="loadingDistricts" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                    <i class="fas fa-spinner fa-spin me-1"></i> Memuat data...
                                                </div>
                                                <template x-for="item in filteredDistricts" :key="item.id || item.name">
                                                    <div class="ad-item" :class="{'selected': kecamatan === item.name}" @click="selectDistrict(item)" x-text="item.name"></div>
                                                </template>
                                                <div x-show="!loadingDistricts && filteredDistricts.length === 0 && districtSearch" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                    Tidak ada kecamatan ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('kecamatan')
                                        <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Kelurahan / Desa --}}
                                <div class="mc-group">
                                    <label class="mc-label">
                                        <i class="fas fa-signs-post mitra-edit-label-icon"></i>Kelurahan / Desa
                                    </label>
                                    <div class="alpine-dropdown" @click.outside="villageOpen = false; villageSearch = ''">
                                        <div class="ad-trigger no-icon" :class="{'active': villageOpen, 'disabled': !kecamatan}"
                                            @click="if (!kecamatan) return; villageOpen = !villageOpen; $nextTick(() => { if (villageOpen) $refs.mkeVillageSearch.focus() })">
                                            <div class="mitra-edit-trigger-content is-compact">
                                                <i class="fas fa-signs-post mitra-edit-muted-icon"></i>
                                                <span x-show="!kelurahan && !kecamatan" class="mitra-edit-placeholder">- Pilih Kecamatan Dahulu -</span>
                                                <span x-show="!kelurahan && kecamatan" class="mitra-edit-placeholder">- Pilih Kelurahan (Opsional) -</span>
                                                <span x-show="kelurahan" x-text="kelurahan" class="mitra-edit-selected is-normal"></span>
                                            </div>
                                            <i class="fas fa-chevron-down mitra-edit-chevron is-small" :class="{'is-open': villageOpen}"></i>
                                        </div>
                                        <div class="ad-menu mitra-edit-menu is-scrollable" x-show="villageOpen" x-transition>
                                            <div class="mitra-edit-search-wrap">
                                                <div class="mitra-edit-search">
                                                    <i class="fas fa-search"></i>
                                                    <input x-ref="mkeVillageSearch" x-model="villageSearch" type="text" placeholder="Cari kelurahan/desa..." @click.stop>
                                                </div>
                                            </div>
                                            <div class="mitra-edit-menu-list is-country">
                                                <div class="ad-item" :class="{'selected': !kelurahan}" @click="selectVillage(null)">- Pilih Kelurahan / Desa -</div>
                                                <div x-show="loadingVillages" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                    <i class="fas fa-spinner fa-spin me-1"></i> Memuat data...
                                                </div>
                                                <template x-for="item in filteredVillages" :key="item.id || item.name">
                                                    <div class="ad-item" :class="{'selected': kelurahan === item.name}" @click="selectVillage(item)" x-text="item.name"></div>
                                                </template>
                                                <div x-show="!loadingVillages && filteredVillages.length === 0 && villageSearch" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                    Tidak ada kelurahan/desa ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('kelurahan')
                                        <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- 4. Kategori Internasional --}}
                        <div x-show="kategori === 'internasional'" x-transition class="mitra-edit-row">
                            <div class="mc-grid-2">
                                <div class="mc-group">
                                    <label class="mc-label">
                                        <i class="fas fa-globe-americas mitra-edit-label-icon"></i>Negara
                                    </label>
                                    <div class="alpine-dropdown" @click.outside="countryOpen = false; countrySearch = ''">
                                        <div class="ad-trigger no-icon" :class="{'active': countryOpen}"
                                            @click="countryOpen = !countryOpen; $nextTick(() => { if (countryOpen) $refs.mkeCountrySearch.focus() })">
                                            <div class="mitra-edit-trigger-content is-compact">
                                                <i class="fas fa-flag mitra-edit-muted-icon"></i>
                                                <span x-show="!negara" class="mitra-edit-placeholder">- Pilih Negara -</span>
                                                <span x-show="negara" x-text="negara" class="mitra-edit-selected is-normal"></span>
                                            </div>
                                            <i class="fas fa-chevron-down mitra-edit-chevron is-small" :class="{'is-open': countryOpen}"></i>
                                        </div>
                                        <div class="ad-menu mitra-edit-menu is-scrollable" x-show="countryOpen" x-transition>
                                            <div class="mitra-edit-search-wrap">
                                                <div class="mitra-edit-search">
                                                    <i class="fas fa-search"></i>
                                                    <input x-ref="mkeCountrySearch" x-model="countrySearch" type="text" placeholder="Cari negara..." @click.stop>
                                                </div>
                                            </div>
                                            <div class="mitra-edit-menu-list is-country">
                                                <template x-for="country in filteredCountries" :key="country">
                                                    <div class="ad-item" :class="{'selected': negara === country}"
                                                        @click="negara = country; countryOpen = false; countrySearch = ''" x-text="country"></div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @error('negara')
                                        <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mc-group">
                                    <label class="mc-label">
                                        <i class="fas fa-city mitra-edit-label-icon"></i>City / Kota
                                    </label>
                                    <div class="mc-input-wrap">
                                        <i class="fas fa-city mc-icon-left"></i>
                                        <input type="text" x-model="kotaInternasional" placeholder="Contoh: Tokyo / Munich" class="mc-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 5. Alamat Lengkap --}}
                        <div class="mc-group mitra-edit-row">
                            <label class="mc-label">Alamat</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-map-marker-alt mc-icon-left mitra-edit-textarea-icon"></i>
                                <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap mitra..."
                                    class="mc-input mitra-edit-textarea @error('alamat') uc-input-error @enderror">{{ old('alamat', $mitra->alamat) }}</textarea>
                            </div>
                            @error('alamat')
                                <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 6. Telepon & Website --}}
                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">Nomor Telepon</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-phone mc-icon-left"></i>
                                    <input type="text" name="telp" placeholder="Contoh: 021-12345678"
                                        class="mc-input @error('telp') uc-input-error @enderror"
                                        value="{{ old('telp', $mitra->telp) }}">
                                </div>
                                @error('telp')
                                    <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Website</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-globe mc-icon-left"></i>
                                    <input type="text" name="website" placeholder="https://www.example.com"
                                        class="mc-input @error('website') uc-input-error @enderror"
                                        value="{{ old('website', $mitra->website) }}">
                                </div>
                                @error('website')
                                    <span class="mitra-edit-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mitra-edit-footer mitra-page-form-footer">
                        <a href="{{ route('mitra.index') }}" class="mitra-edit-btn mitra-edit-btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="mitra-edit-btn mitra-edit-btn-primary">
                            <span class="mitra-edit-btn-content"><i class="fas fa-save"></i> Perbarui Mitra</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
(function () {
    const countries = [
        'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia',
        'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia', 'Cameroon',
        'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica', 'Croatia',
        'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'DR Congo', 'East Timor', 'Ecuador',
        'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France',
        'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau',
        'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland',
        'Israel', 'Italy', 'Ivory Coast', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kosovo',
        'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania',
        'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius',
        'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia',
        'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway',
        'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland',
        'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines',
        'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore',
        'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan',
        'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Togo', 'Tonga',
        'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates',
        'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
    ];

    const defaultProvinces = [
        { id: '11', name: 'Aceh' },
        { id: '12', name: 'Sumatera Utara' },
        { id: '13', name: 'Sumatera Barat' },
        { id: '14', name: 'Riau' },
        { id: '15', name: 'Jambi' },
        { id: '16', name: 'Sumatera Selatan' },
        { id: '17', name: 'Bengkulu' },
        { id: '18', name: 'Lampung' },
        { id: '19', name: 'Kepulauan Bangka Belitung' },
        { id: '21', name: 'Kepulauan Riau' },
        { id: '31', name: 'DKI Jakarta' },
        { id: '32', name: 'Jawa Barat' },
        { id: '33', name: 'Jawa Tengah' },
        { id: '34', name: 'DI Yogyakarta' },
        { id: '35', name: 'Jawa Timur' },
        { id: '36', name: 'Banten' },
        { id: '51', name: 'Bali' },
        { id: '52', name: 'Nusa Tenggara Barat' },
        { id: '53', name: 'Nusa Tenggara Timur' },
        { id: '61', name: 'Kalimantan Barat' },
        { id: '62', name: 'Kalimantan Tengah' },
        { id: '63', name: 'Kalimantan Selatan' },
        { id: '64', name: 'Kalimantan Timur' },
        { id: '65', name: 'Kalimantan Utara' },
        { id: '71', name: 'Sulawesi Utara' },
        { id: '72', name: 'Sulawesi Tengah' },
        { id: '73', name: 'Sulawesi Selatan' },
        { id: '74', name: 'Sulawesi Tenggara' },
        { id: '75', name: 'Gorontalo' },
        { id: '76', name: 'Sulawesi Barat' },
        { id: '81', name: 'Maluku' },
        { id: '82', name: 'Maluku Utara' },
        { id: '91', name: 'Papua Barat' },
        { id: '92', name: 'Papua Barat Daya' },
        { id: '93', name: 'Papua Selatan' },
        { id: '94', name: 'Papua' },
        { id: '95', name: 'Papua Tengah' },
        { id: '96', name: 'Papua Pegunungan' }
    ];

    function toTitleCase(str) {
        if (!str) return '';
        return str.toLowerCase()
            .replace(/(?:^|\s|\/|-|\()\S/g, function (match) { return match.toUpperCase(); })
            .replace(/\b(Dki|Di|Upa)\b/g, function (match) { return match.toUpperCase(); })
            .replace(/\bIii\b/g, 'III')
            .replace(/\bIi\b/g, 'II')
            .replace(/\bIv\b/g, 'IV')
            .replace(/\bVi\b/g, 'VI')
            .replace(/\bV\b/g, 'V');
    }

    const WilayahService = {
        cache: {
            provinces: null,
            regencies: {},
            districts: {},
            villages: {}
        },
        async getProvinces() {
            if (this.cache.provinces) return this.cache.provinces;
            try {
                const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                if (res.ok) {
                    const data = await res.json();
                    this.cache.provinces = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.provinces;
                }
            } catch (e) {
                console.error('Failed to fetch provinces, fallback used:', e);
            }
            this.cache.provinces = defaultProvinces;
            return this.cache.provinces;
        },
        async getRegencies(provinceId) {
            if (!provinceId) return [];
            if (this.cache.regencies[provinceId]) return this.cache.regencies[provinceId];
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`);
                if (res.ok) {
                    const data = await res.json();
                    this.cache.regencies[provinceId] = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.regencies[provinceId];
                }
            } catch (e) {
                console.error('Failed to fetch regencies:', e);
            }
            return [];
        },
        async getDistricts(regencyId) {
            if (!regencyId) return [];
            if (this.cache.districts[regencyId]) return this.cache.districts[regencyId];
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`);
                if (res.ok) {
                    const data = await res.json();
                    this.cache.districts[regencyId] = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.districts[regencyId];
                }
            } catch (e) {
                console.error('Failed to fetch districts:', e);
            }
            return [];
        },
        async getVillages(districtId) {
            if (!districtId) return [];
            if (this.cache.villages[districtId]) return this.cache.villages[districtId];
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`);
                if (res.ok) {
                    const data = await res.json();
                    this.cache.villages[districtId] = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.villages[districtId];
                }
            } catch (e) {
                console.error('Failed to fetch villages:', e);
            }
            return [];
        }
    };

    window.adminMitraEditForm = function (config) {
        return {
            kategori: config.kategori || 'nasional',
            negara: config.negara || 'Indonesia',
            provinsi: config.provinsi || '',
            kota: config.kota || '',
            kecamatan: config.kecamatan || '',
            kelurahan: config.kelurahan || '',
            kotaInternasional: (config.kategori === 'internasional' ? config.kota : '') || '',

            klasifikasiOpen: false,
            klasifikasiSearch: '',
            klasifikasiSelected: config.selectedKlasifikasi || '',
            klasifikasiItems: config.klasifikasiItems || [],

            countryOpen: false,
            countrySearch: '',
            countries: countries,

            provinceOpen: false,
            provinceSearch: '',
            provinceList: [],

            cityOpen: false,
            citySearch: '',
            cityList: [],
            loadingCities: false,

            districtOpen: false,
            districtSearch: '',
            districtList: [],
            loadingDistricts: false,

            villageOpen: false,
            villageSearch: '',
            villageList: [],
            loadingVillages: false,

            async init() {
                this.provinceList = await WilayahService.getProvinces();
                if (this.kategori === 'nasional' && this.provinsi) {
                    const found = this.provinceList.find(p => p.name.toLowerCase() === this.provinsi.toLowerCase());
                    if (found) {
                        this.loadingCities = true;
                        this.cityList = await WilayahService.getRegencies(found.id);
                        this.loadingCities = false;

                        if (this.kota) {
                            const foundCity = this.cityList.find(c => c.name.toLowerCase() === this.kota.toLowerCase());
                            if (foundCity) {
                                this.loadingDistricts = true;
                                this.districtList = await WilayahService.getDistricts(foundCity.id);
                                this.loadingDistricts = false;

                                if (this.kecamatan) {
                                    const foundDist = this.districtList.find(d => d.name.toLowerCase() === this.kecamatan.toLowerCase());
                                    if (foundDist) {
                                        this.loadingVillages = true;
                                        this.villageList = await WilayahService.getVillages(foundDist.id);
                                        this.loadingVillages = false;
                                    }
                                }
                            }
                        }
                    }
                }
            },

            get selectedKlasifikasi() {
                return this.klasifikasiItems.find(item => item.id === this.klasifikasiSelected);
            },
            get filteredKlasifikasi() {
                if (!this.klasifikasiSearch) return this.klasifikasiItems;
                const query = this.klasifikasiSearch.toLowerCase();
                return this.klasifikasiItems.filter(item => item.label.toLowerCase().includes(query));
            },
            get filteredCountries() {
                if (!this.countrySearch) return this.countries;
                const query = this.countrySearch.toLowerCase();
                return this.countries.filter(country => country.toLowerCase().includes(query));
            },
            get filteredProvinces() {
                if (!this.provinceSearch) return this.provinceList;
                const query = this.provinceSearch.toLowerCase();
                return this.provinceList.filter(p => p.name.toLowerCase().includes(query));
            },
            get filteredCities() {
                if (!this.citySearch) return this.cityList;
                const query = this.citySearch.toLowerCase();
                return this.cityList.filter(c => c.name.toLowerCase().includes(query));
            },
            get filteredDistricts() {
                if (!this.districtSearch) return this.districtList;
                const query = this.districtSearch.toLowerCase();
                return this.districtList.filter(d => d.name.toLowerCase().includes(query));
            },
            get filteredVillages() {
                if (!this.villageSearch) return this.villageList;
                const query = this.villageSearch.toLowerCase();
                return this.villageList.filter(v => v.name.toLowerCase().includes(query));
            },

            async selectProvince(item) {
                if (!item) {
                    this.provinsi = '';
                    this.kota = '';
                    this.kecamatan = '';
                    this.kelurahan = '';
                    this.cityList = [];
                    this.districtList = [];
                    this.villageList = [];
                } else {
                    this.provinsi = item.name;
                    this.kota = '';
                    this.kecamatan = '';
                    this.kelurahan = '';
                    this.districtList = [];
                    this.villageList = [];
                    this.loadingCities = true;
                    this.cityList = await WilayahService.getRegencies(item.id);
                    this.loadingCities = false;
                }
                this.provinceOpen = false;
                this.provinceSearch = '';
            },

            async selectCity(item) {
                if (!item) {
                    this.kota = '';
                    this.kecamatan = '';
                    this.kelurahan = '';
                    this.districtList = [];
                    this.villageList = [];
                } else {
                    this.kota = item.name;
                    this.kecamatan = '';
                    this.kelurahan = '';
                    this.villageList = [];
                    this.loadingDistricts = true;
                    this.districtList = await WilayahService.getDistricts(item.id);
                    this.loadingDistricts = false;
                }
                this.cityOpen = false;
                this.citySearch = '';
            },

            async selectDistrict(item) {
                if (!item) {
                    this.kecamatan = '';
                    this.kelurahan = '';
                    this.villageList = [];
                } else {
                    this.kecamatan = item.name;
                    this.kelurahan = '';
                    this.loadingVillages = true;
                    this.villageList = await WilayahService.getVillages(item.id);
                    this.loadingVillages = false;
                }
                this.districtOpen = false;
                this.districtSearch = '';
            },

            selectVillage(item) {
                this.kelurahan = item ? item.name : '';
                this.villageOpen = false;
                this.villageSearch = '';
            }
        };
    };
})();
</script>
@endsection

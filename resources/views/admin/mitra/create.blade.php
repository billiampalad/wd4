@extends('admin.dashboard')

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
                <span>Tambah Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake-simple"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Tambah Mitra Baru</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Isi formulir lengkap sesuai tampilan modal tambah mitra.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="uc-layout mitra-form-layout">
        <div class="uc-form-col">
            <div class="card uc-form-card mitra-page-card">
                <div class="mitra-create-header mitra-page-form-header">
                    <div class="mitra-create-header-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="mitra-create-header-text">
                        <h3 class="mitra-create-title">Tambah Mitra Baru</h3>
                        <p class="mitra-create-subtitle">Lengkapi informasi instansi mitra kerjasama</p>
                    </div>
                </div>

                <form action="{{ route('mitra.store') }}" method="POST" x-data="adminMitraForm({
                    klasifikasiItems: {{ Js::from($mitraKlasifikasiItems) }},
                    selectedKlasifikasi: {{ Js::from((string) old('id_klasifikasi', '')) }},
                    kategori: {{ Js::from(old('kategori', '')) }},
                    negara: {{ Js::from(old('negara', 'Indonesia')) }}
                })">
                    @csrf
                    <div class="mitra-create-content mitra-page-form-content">
                        <div class="mc-group mitra-create-section">
                            <label class="mc-label">Klasifikasi Mitra</label>
                            <input type="hidden" name="id_klasifikasi" :value="klasifikasiSelected">
                            <div class="alpine-dropdown" @click.outside="klasifikasiOpen = false; klasifikasiSearch = ''">
                                <div class="ad-trigger no-icon" :class="{'active': klasifikasiOpen}"
                                    @click="klasifikasiOpen = !klasifikasiOpen; $nextTick(() => { if (klasifikasiOpen) $refs.mkSearch.focus() })">
                                    <div class="mitra-create-trigger-content">
                                        <div class="mitra-create-field-icon">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <span x-show="!selectedKlasifikasi" class="mitra-create-placeholder">- Pilih Klasifikasi -</span>
                                        <span x-show="selectedKlasifikasi" x-text="selectedKlasifikasi ? selectedKlasifikasi.label : ''" class="mitra-create-selected"></span>
                                    </div>
                                    <i class="fas fa-chevron-down mitra-create-chevron" :class="{'is-open': klasifikasiOpen}"></i>
                                </div>

                                <div class="ad-menu mitra-create-menu is-scrollable" x-show="klasifikasiOpen" x-transition>
                                    <div class="mitra-create-search-wrap">
                                        <div class="mitra-create-search">
                                            <i class="fas fa-search"></i>
                                            <input x-ref="mkSearch" x-model="klasifikasiSearch" type="text" placeholder="Cari klasifikasi..." @click.stop>
                                        </div>
                                    </div>
                                    <div class="mitra-create-menu-list">
                                        <template x-for="item in filteredKlasifikasi" :key="item.id">
                                            <div class="ad-item mitra-create-check-row" :class="{'selected': klasifikasiSelected === item.id}"
                                                @click="klasifikasiSelected = item.id; klasifikasiOpen = false; klasifikasiSearch = ''">
                                                <div class="mitra-create-check" :class="{'is-selected': klasifikasiSelected === item.id}">
                                                    <i class="fas fa-check" x-show="klasifikasiSelected === item.id"></i>
                                                </div>
                                                <span x-text="item.label" class="mitra-create-item-text"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredKlasifikasi.length === 0" class="mitra-create-empty">Tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>
                            @error('id_klasifikasi')
                            <span class="mitra-create-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mc-grid-2 mitra-create-row">
                            <div class="mc-group">
                                <label class="mc-label">Nama Instansi / Mitra <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <input type="text" name="nama_mitra" required placeholder="Masukkan nama instansi/mitra" class="mc-input no-icon @error('nama_mitra') uc-input-error @enderror" value="{{ old('nama_mitra') }}">
                                </div>
                                @error('nama_mitra')
                                <span class="mitra-create-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mc-group" x-data="{ katOpen: false }">
                                <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                                <input type="hidden" name="kategori" :value="kategori">
                                <div class="alpine-dropdown" @click.outside="katOpen = false">
                                    <div class="ad-trigger no-icon" :class="{'active': katOpen}" @click="katOpen = !katOpen">
                                        <span x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '- Pilih Kategori -')" class="mitra-create-item-text"></span>
                                        <i class="fas fa-chevron-down mitra-create-chevron is-small" :class="{'is-open': katOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-create-menu" x-show="katOpen" x-transition>
                                        <div class="ad-item" :class="{'selected': kategori === 'nasional'}" @click="kategori = 'nasional'; negara = 'Indonesia'; katOpen = false">Nasional</div>
                                        <div class="ad-item" :class="{'selected': kategori === 'internasional'}" @click="kategori = 'internasional'; if (negara === 'Indonesia') negara = ''; katOpen = false">Internasional</div>
                                    </div>
                                </div>
                                @error('kategori')
                                <span class="mitra-create-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <input type="hidden" name="negara" :value="kategori === 'internasional' ? negara : 'Indonesia'">
                        <div x-show="kategori === 'internasional'" x-transition class="mitra-create-row">
                            <div class="mc-group">
                                <label class="mc-label"><i class="fas fa-globe-americas mitra-create-label-icon"></i>Negara</label>
                                <div class="alpine-dropdown" @click.outside="countryOpen = false; countrySearch = ''">
                                    <div class="ad-trigger" :class="{'active': countryOpen}" @click="countryOpen = !countryOpen; $nextTick(() => { if (countryOpen) $refs.mkCountrySearch.focus() })">
                                        <div class="mitra-create-trigger-content is-compact">
                                            <i class="fas fa-flag mitra-create-muted-icon"></i>
                                            <span x-show="!negara" class="mitra-create-placeholder">- Pilih Negara -</span>
                                            <span x-show="negara" x-text="negara" class="mitra-create-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-create-chevron is-small" :class="{'is-open': countryOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-create-menu is-scrollable" x-show="countryOpen" x-transition>
                                        <div class="mitra-create-search-wrap">
                                            <div class="mitra-create-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkCountrySearch" x-model="countrySearch" type="text" placeholder="Cari negara..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-create-menu-list is-country">
                                            <template x-for="country in filteredCountries" :key="country">
                                                <div class="ad-item" :class="{'selected': negara === country}" @click="negara = country; countryOpen = false; countrySearch = ''" x-text="country"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                @error('negara')
                                <span class="mitra-create-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mc-group mitra-create-row">
                            <label class="mc-label">Alamat</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-map-marker-alt mc-icon-left mitra-create-textarea-icon"></i>
                                <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap mitra..." class="mc-input mitra-create-textarea @error('alamat') uc-input-error @enderror">{{ old('alamat') }}</textarea>
                            </div>
                            @error('alamat')
                            <span class="mitra-create-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">Nomor Telepon</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-phone mc-icon-left"></i>
                                    <input type="text" name="telp" placeholder="Contoh: 021-12345678" class="mc-input @error('telp') uc-input-error @enderror" value="{{ old('telp') }}">
                                </div>
                                @error('telp')
                                <span class="mitra-create-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Website</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-globe mc-icon-left"></i>
                                    <input type="text" name="website" placeholder="https://www.example.com" class="mc-input @error('website') uc-input-error @enderror" value="{{ old('website') }}">
                                </div>
                                @error('website')
                                <span class="mitra-create-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mitra-create-footer mitra-page-form-footer">
                        <a href="{{ route('mitra.index') }}" class="mitra-create-btn mitra-create-btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="mitra-create-btn mitra-create-btn-primary">
                            <span class="mitra-create-btn-content"><i class="fas fa-save"></i> Simpan Mitra</span>
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
    window.adminMitraCountries = window.adminMitraCountries || ['Afghanistan','Albania','Algeria','Andorra','Angola','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahrain','Bangladesh','Belgium','Brazil','Brunei','Cambodia','Canada','China','Denmark','Egypt','Finland','France','Germany','India','Indonesia','Italy','Japan','Malaysia','Netherlands','New Zealand','Philippines','Russia','Saudi Arabia','Singapore','South Korea','Spain','Thailand','United Arab Emirates','United Kingdom','United States','Vietnam'];
    window.adminMitraForm = window.adminMitraForm || function (config) {
        return {
            kategori: config.kategori || '',
            negara: config.negara || 'Indonesia',
            klasifikasiOpen: false,
            klasifikasiSearch: '',
            klasifikasiSelected: config.selectedKlasifikasi || '',
            klasifikasiItems: config.klasifikasiItems || [],
            countryOpen: false,
            countrySearch: '',
            countries: window.adminMitraCountries,
            get selectedKlasifikasi() {
                return this.klasifikasiItems.find((item) => item.id === this.klasifikasiSelected);
            },
            get filteredKlasifikasi() {
                if (!this.klasifikasiSearch) return this.klasifikasiItems;
                const query = this.klasifikasiSearch.toLowerCase();
                return this.klasifikasiItems.filter((item) => item.label.toLowerCase().includes(query));
            },
            get filteredCountries() {
                if (!this.countrySearch) return this.countries;
                const query = this.countrySearch.toLowerCase();
                return this.countries.filter((country) => country.toLowerCase().includes(query));
            }
        };
    };
</script>
@endsection

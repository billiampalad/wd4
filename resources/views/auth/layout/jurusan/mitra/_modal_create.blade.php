@php
    $modalKlasifikasi = \App\Models\Klasifikasi::orderBy('nama', 'asc')->get();
    $modalKlasifikasiItems = $modalKlasifikasi->map(fn($klas) => [
        'id' => (string) $klas->id,
        'label' => $klas->nama,
    ])->values();
@endphp

<div id="mitraModal" class="mitra-create-modal" hidden>
    <div id="mitraModalBackdrop" class="mitra-create-backdrop"></div>

    <div id="mitraModalBox" class="mitra-create-box">
        <div class="mitra-create-header">
            <div class="mitra-create-header-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="mitra-create-header-text">
                <h3 class="mitra-create-title">Tambah Mitra Baru</h3>
                <p class="mitra-create-subtitle">Lengkapi informasi instansi mitra kerjasama</p>
            </div>
            <button data-mitra-modal-close type="button" class="mitra-create-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mitra-create-body" data-store-url="{{ route('jurusan.mitra.store') }}"
            data-klasifikasi-items='@json($modalKlasifikasiItems)' x-data="createMitraModalFromElement($el)"
            @reset-mitra-create-data.window="resetForm()">
            <form id="mitraModalForm" @submit.prevent="submitMitra()">
                <div class="mitra-create-content">
                    <div class="mc-group mitra-create-section">
                        <label class="mc-label">Klasifikasi Mitra</label>
                        <div class="alpine-dropdown" @click.outside="klasifikasiOpen = false; klasifikasiSearch = ''">
                            <div class="ad-trigger no-icon" :class="{'active': klasifikasiOpen}"
                                @click="klasifikasiOpen = !klasifikasiOpen; $nextTick(() => { if(klasifikasiOpen) $refs.mkSearch.focus() })">
                                <div class="mitra-create-trigger-content">
                                    <div class="mitra-create-field-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <span x-show="!selectedKlasifikasi" class="mitra-create-placeholder">- Pilih
                                        Klasifikasi -</span>
                                    <span x-show="selectedKlasifikasi"
                                        x-text="selectedKlasifikasi ? selectedKlasifikasi.label : ''"
                                        class="mitra-create-selected"></span>
                                </div>
                                <i class="fas fa-chevron-down mitra-create-chevron"
                                    :class="{'is-open': klasifikasiOpen}"></i>
                            </div>

                            <div class="ad-menu mitra-create-menu is-scrollable" x-show="klasifikasiOpen" x-transition>
                                <div class="mitra-create-search-wrap">
                                    <div class="mitra-create-search">
                                        <i class="fas fa-search"></i>
                                        <input x-ref="mkSearch" x-model="klasifikasiSearch" type="text"
                                            placeholder="Cari klasifikasi..." @click.stop>
                                    </div>
                                </div>
                                <div class="mitra-create-menu-list">
                                    <template x-for="item in filteredKlasifikasi" :key="item.id">
                                        <div class="ad-item mitra-create-check-row"
                                            :class="{'selected': klasifikasiSelected === item.id}"
                                            @click="klasifikasiSelected = item.id; klasifikasiOpen = false; klasifikasiSearch = ''">
                                            <div class="mitra-create-check"
                                                :class="{'is-selected': klasifikasiSelected === item.id}">
                                                <i class="fas fa-check" x-show="klasifikasiSelected === item.id"></i>
                                            </div>
                                            <span x-text="item.label" class="mitra-create-item-text"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredKlasifikasi.length === 0" class="mitra-create-empty">
                                        Tidak ditemukan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mc-grid-2 mitra-create-row">
                        <div class="mc-group">
                            <label class="mc-label">Nama Instansi / Mitra <span class="mc-req">*</span></label>
                            <div class="mc-input-wrap">
                                <input type="text" name="nama_mitra" required placeholder="Masukkan nama instansi/mitra"
                                    class="mc-input no-icon">
                            </div>
                            <template x-if="errors.nama_mitra">
                                <span class="mitra-create-error">
                                    <i class="fas fa-circle-exclamation"></i> <span
                                        x-text="errors.nama_mitra[0]"></span>
                                </span>
                            </template>
                        </div>

                        <div class="mc-group" x-data="{ katOpen: false }">
                            <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                            <div class="alpine-dropdown" @click.outside="katOpen = false">
                                <div class="ad-trigger no-icon" :class="{'active': katOpen}"
                                    @click="katOpen = !katOpen">
                                    <span
                                        x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '- Pilih Kategori -')"
                                        class="mitra-create-item-text"></span>
                                    <i class="fas fa-chevron-down mitra-create-chevron is-small"
                                        :class="{'is-open': katOpen}"></i>
                                </div>
                                <div class="ad-menu mitra-create-menu" x-show="katOpen" x-transition>
                                    <div class="ad-item" :class="{'selected': kategori === 'nasional'}"
                                        @click="kategori = 'nasional'; negara = 'Indonesia'; katOpen = false">Nasional
                                    </div>
                                    <div class="ad-item" :class="{'selected': kategori === 'internasional'}"
                                        @click="kategori = 'internasional'; negara = ''; katOpen = false">Internasional
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="kategori === 'nasional'" x-transition class="mitra-create-row">
                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-map-marked-alt mitra-create-label-icon"></i>Provinsi
                                </label>
                                <div class="alpine-dropdown" @click.outside="provinceOpen = false; provinceSearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': provinceOpen}"
                                        @click="provinceOpen = !provinceOpen; $nextTick(() => { if(provinceOpen) $refs.mkProvinceSearch.focus() })">
                                        <div class="mitra-create-trigger-content is-compact">
                                            <i class="fas fa-map-pin mitra-create-muted-icon"></i>
                                            <span x-show="!provinsi" class="mitra-create-placeholder">- Pilih Provinsi
                                                -</span>
                                            <span x-show="provinsi" x-text="provinsi"
                                                class="mitra-create-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-create-chevron is-small"
                                            :class="{'is-open': provinceOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-create-menu is-scrollable" x-show="provinceOpen"
                                        x-transition>
                                        <div class="mitra-create-search-wrap">
                                            <div class="mitra-create-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkProvinceSearch" x-model="provinceSearch" type="text"
                                                    placeholder="Cari provinsi..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-create-menu-list is-country">
                                            <div class="ad-item" :class="{'selected': !provinsi}"
                                                @click="selectProvince(null)">- Pilih Provinsi -</div>
                                            <template x-for="item in filteredProvinces" :key="item.id || item.name">
                                                <div class="ad-item" :class="{'selected': provinsi === item.name}"
                                                    @click="selectProvince(item)"
                                                    x-text="item.name"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-city mitra-create-label-icon"></i>Kota / Kabupaten
                                </label>
                                <div class="alpine-dropdown" @click.outside="cityOpen = false; citySearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': cityOpen, 'disabled': !provinsi}"
                                        @click="if(!provinsi) return; cityOpen = !cityOpen; $nextTick(() => { if(cityOpen) $refs.mkCitySearch.focus() })">
                                        <div class="mitra-create-trigger-content is-compact">
                                            <i class="fas fa-city mitra-create-muted-icon"></i>
                                            <span x-show="!kota && !provinsi" class="mitra-create-placeholder">- Pilih Provinsi Dahulu -</span>
                                            <span x-show="!kota && provinsi" class="mitra-create-placeholder">- Pilih Kota / Kabupaten -</span>
                                            <span x-show="kota" x-text="kota" class="mitra-create-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-create-chevron is-small"
                                            :class="{'is-open': cityOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-create-menu is-scrollable" x-show="cityOpen"
                                        x-transition>
                                        <div class="mitra-create-search-wrap">
                                            <div class="mitra-create-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkCitySearch" x-model="citySearch" type="text"
                                                    placeholder="Cari kota/kabupaten..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-create-menu-list is-country">
                                            <div class="ad-item" :class="{'selected': !kota}"
                                                @click="selectCity(null)">- Pilih Kota / Kabupaten -</div>
                                            <div x-show="loadingCities" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                <i class="fas fa-spinner fa-spin me-1"></i> Memuat data...
                                            </div>
                                            <template x-for="item in filteredCities" :key="item.id || item.name">
                                                <div class="ad-item" :class="{'selected': kota === item.name}"
                                                    @click="selectCity(item)"
                                                    x-text="item.name"></div>
                                            </template>
                                            <div x-show="!loadingCities && filteredCities.length === 0 && citySearch" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                Tidak ada kota/kabupaten ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mc-grid-2" style="margin-top: 12px;">
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-map-location-dot mitra-create-label-icon"></i>Kecamatan
                                </label>
                                <div class="alpine-dropdown" @click.outside="districtOpen = false; districtSearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': districtOpen, 'disabled': !kota}"
                                        @click="if(!kota) return; districtOpen = !districtOpen; $nextTick(() => { if(districtOpen) $refs.mkDistrictSearch.focus() })">
                                        <div class="mitra-create-trigger-content is-compact">
                                            <i class="fas fa-map-location-dot mitra-create-muted-icon"></i>
                                            <span x-show="!kecamatan && !kota" class="mitra-create-placeholder">- Pilih Kota Dahulu -</span>
                                            <span x-show="!kecamatan && kota" class="mitra-create-placeholder">- Pilih Kecamatan (Opsional) -</span>
                                            <span x-show="kecamatan" x-text="kecamatan" class="mitra-create-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-create-chevron is-small"
                                            :class="{'is-open': districtOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-create-menu is-scrollable" x-show="districtOpen"
                                        x-transition>
                                        <div class="mitra-create-search-wrap">
                                            <div class="mitra-create-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkDistrictSearch" x-model="districtSearch" type="text"
                                                    placeholder="Cari kecamatan..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-create-menu-list is-country">
                                            <div class="ad-item" :class="{'selected': !kecamatan}"
                                                @click="selectDistrict(null)">- Pilih Kecamatan -</div>
                                            <div x-show="loadingDistricts" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                <i class="fas fa-spinner fa-spin me-1"></i> Memuat data...
                                            </div>
                                            <template x-for="item in filteredDistricts" :key="item.id || item.name">
                                                <div class="ad-item" :class="{'selected': kecamatan === item.name}"
                                                    @click="selectDistrict(item)"
                                                    x-text="item.name"></div>
                                            </template>
                                            <div x-show="!loadingDistricts && filteredDistricts.length === 0 && districtSearch" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                Tidak ada kecamatan ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-signs-post mitra-create-label-icon"></i>Kelurahan / Desa
                                </label>
                                <div class="alpine-dropdown" @click.outside="villageOpen = false; villageSearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': villageOpen, 'disabled': !kecamatan}"
                                        @click="if(!kecamatan) return; villageOpen = !villageOpen; $nextTick(() => { if(villageOpen) $refs.mkVillageSearch.focus() })">
                                        <div class="mitra-create-trigger-content is-compact">
                                            <i class="fas fa-signs-post mitra-create-muted-icon"></i>
                                            <span x-show="!kelurahan && !kecamatan" class="mitra-create-placeholder">- Pilih Kecamatan Dahulu -</span>
                                            <span x-show="!kelurahan && kecamatan" class="mitra-create-placeholder">- Pilih Kelurahan (Opsional) -</span>
                                            <span x-show="kelurahan" x-text="kelurahan" class="mitra-create-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-create-chevron is-small"
                                            :class="{'is-open': villageOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-create-menu is-scrollable" x-show="villageOpen"
                                        x-transition>
                                        <div class="mitra-create-search-wrap">
                                            <div class="mitra-create-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkVillageSearch" x-model="villageSearch" type="text"
                                                    placeholder="Cari kelurahan/desa..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-create-menu-list is-country">
                                            <div class="ad-item" :class="{'selected': !kelurahan}"
                                                @click="selectVillage(null)">- Pilih Kelurahan / Desa -</div>
                                            <div x-show="loadingVillages" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                <i class="fas fa-spinner fa-spin me-1"></i> Memuat data...
                                            </div>
                                            <template x-for="item in filteredVillages" :key="item.id || item.name">
                                                <div class="ad-item" :class="{'selected': kelurahan === item.name}"
                                                    @click="selectVillage(item)"
                                                    x-text="item.name"></div>
                                            </template>
                                            <div x-show="!loadingVillages && filteredVillages.length === 0 && villageSearch" class="ad-item" style="color: #94a3b8; font-style: italic;">
                                                Tidak ada kelurahan/desa ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="kategori === 'internasional'" x-transition class="mitra-create-row">
                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-globe-americas mitra-create-label-icon"></i>Negara
                                </label>
                                <div class="alpine-dropdown" @click.outside="countryOpen = false; countrySearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': countryOpen}"
                                        @click="countryOpen = !countryOpen; $nextTick(() => { if(countryOpen) $refs.mkCountrySearch.focus() })">
                                        <div class="mitra-create-trigger-content is-compact">
                                            <i class="fas fa-flag mitra-create-muted-icon"></i>
                                            <span x-show="!negara" class="mitra-create-placeholder">- Pilih Negara
                                                -</span>
                                            <span x-show="negara" x-text="negara"
                                                class="mitra-create-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-create-chevron is-small"
                                            :class="{'is-open': countryOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-create-menu is-scrollable" x-show="countryOpen"
                                        x-transition>
                                        <div class="mitra-create-search-wrap">
                                            <div class="mitra-create-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkCountrySearch" x-model="countrySearch" type="text"
                                                    placeholder="Cari negara..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-create-menu-list is-country">
                                            <template x-for="country in filteredCountries" :key="country">
                                                <div class="ad-item" :class="{'selected': negara === country}"
                                                    @click="negara = country; countryOpen = false; countrySearch = ''"
                                                    x-text="country"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-city mitra-create-label-icon"></i>City / Kota
                                </label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-city mc-icon-left"></i>
                                    <input type="text" x-model="kotaInternasional" placeholder="Contoh: Tokyo / Munich"
                                        class="mc-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mc-group mitra-create-row">
                        <label class="mc-label">Alamat</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-map-marker-alt mc-icon-left mitra-create-textarea-icon"></i>
                            <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap mitra..."
                                class="mc-input mitra-create-textarea"></textarea>
                        </div>
                    </div>

                    <div class="mc-grid-2">
                        <div class="mc-group">
                            <label class="mc-label">Nomor Telepon</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-phone mc-icon-left"></i>
                                <input type="text" name="telp" placeholder="Contoh: 021-12345678" class="mc-input">
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Website</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-globe mc-icon-left"></i>
                                <input type="text" name="website" placeholder="https://www.example.com"
                                    class="mc-input">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mitra-create-footer">
                    <button type="button" data-mitra-modal-close class="mitra-create-btn mitra-create-btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" :disabled="submitting" class="mitra-create-btn mitra-create-btn-primary"
                        :class="{'is-submitting': submitting}">
                        <template x-if="!submitting">
                            <span class="mitra-create-btn-content"><i class="fas fa-save"></i> Simpan Mitra</span>
                        </template>
                        <template x-if="submitting">
                            <span class="mitra-create-btn-content"><x-loading class="is-inline" text="Menyimpan..."
                                    :size="18" /></span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
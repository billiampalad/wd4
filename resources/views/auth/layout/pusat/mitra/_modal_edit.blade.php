@php
    $modalKlasifikasi = \App\Models\Klasifikasi::orderBy('nama', 'asc')->get();
    $modalKlasifikasiItems = $modalKlasifikasi->map(fn($klas) => [
        'id' => (string) $klas->id,
        'label' => $klas->nama,
    ])->values();
@endphp

<div id="mitraEditModal" class="mitra-edit-modal" hidden>
    <div id="mitraEditModalBackdrop" class="mitra-edit-backdrop"></div>

    <div id="mitraEditModalBox" class="mitra-edit-box">
        <div class="mitra-edit-header">
            <div class="mitra-edit-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div class="mitra-edit-header-text">
                <h3 class="mitra-edit-title">Edit Data Mitra</h3>
                <p class="mitra-edit-subtitle">Perbarui informasi instansi mitra kerjasama</p>
            </div>
            <button data-mitra-edit-modal-close type="button" class="mitra-edit-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mitra-edit-body" data-base-url="{{ url('pusat/mitra') }}"
            data-klasifikasi-items='@json($modalKlasifikasiItems)' x-data="createMitraEditModalFromElement($el)"
            @set-mitra-edit-data.window="setEditData($event.detail)">
            <form id="mitraEditModalForm" @submit.prevent="submitMitra()">
                <div class="mitra-edit-content">
                    <div class="mc-group mitra-edit-section">
                        <label class="mc-label">Klasifikasi Mitra</label>
                        <div class="alpine-dropdown" @click.outside="klasifikasiOpen = false; klasifikasiSearch = ''">
                            <div class="ad-trigger no-icon" :class="{'active': klasifikasiOpen}"
                                @click="klasifikasiOpen = !klasifikasiOpen; $nextTick(() => { if(klasifikasiOpen) $refs.mkeSearch.focus() })">
                                <div class="mitra-edit-trigger-content">
                                    <div class="mitra-edit-field-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <span x-show="!selectedKlasifikasi" class="mitra-edit-placeholder">- Pilih
                                        Klasifikasi -</span>
                                    <span x-show="selectedKlasifikasi"
                                        x-text="selectedKlasifikasi ? selectedKlasifikasi.label : ''"
                                        class="mitra-edit-selected"></span>
                                </div>
                                <i class="fas fa-chevron-down mitra-edit-chevron"
                                    :class="{'is-open': klasifikasiOpen}"></i>
                            </div>

                            <div class="ad-menu mitra-edit-menu is-scrollable" x-show="klasifikasiOpen" x-transition>
                                <div class="mitra-edit-search-wrap">
                                    <div class="mitra-edit-search">
                                        <i class="fas fa-search"></i>
                                        <input x-ref="mkeSearch" x-model="klasifikasiSearch" type="text"
                                            placeholder="Cari klasifikasi..." @click.stop>
                                    </div>
                                </div>
                                <div class="mitra-edit-menu-list">
                                    <template x-for="item in filteredKlasifikasi" :key="item.id">
                                        <div class="ad-item mitra-edit-check-row"
                                            :class="{'selected': klasifikasiSelected === item.id}"
                                            @click="klasifikasiSelected = item.id; klasifikasiOpen = false; klasifikasiSearch = ''">
                                            <div class="mitra-edit-check"
                                                :class="{'is-selected': klasifikasiSelected === item.id}">
                                                <i class="fas fa-check" x-show="klasifikasiSelected === item.id"></i>
                                            </div>
                                            <span x-text="item.label" class="mitra-edit-item-text"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredKlasifikasi.length === 0" class="mitra-edit-empty">
                                        Tidak ditemukan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mc-grid-2 mitra-edit-row">
                        <div class="mc-group">
                            <label class="mc-label">Nama Instansi / Mitra <span class="mc-req">*</span></label>
                            <div class="mc-input-wrap">
                                <input type="text" x-model="nama_mitra" required
                                    placeholder="Masukkan nama instansi/mitra" class="mc-input no-icon">
                            </div>
                            <template x-if="errors.nama_mitra">
                                <span class="mitra-edit-error">
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
                                        class="mitra-edit-item-text"></span>
                                    <i class="fas fa-chevron-down mitra-edit-chevron is-small"
                                        :class="{'is-open': katOpen}"></i>
                                </div>
                                <div class="ad-menu mitra-edit-menu" x-show="katOpen" x-transition>
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

                    <div x-show="kategori === 'nasional'" x-transition class="mitra-edit-row">
                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-map-marked-alt mitra-edit-label-icon"></i>Provinsi
                                </label>
                                <div class="alpine-dropdown" @click.outside="provinceOpen = false; provinceSearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': provinceOpen}"
                                        @click="provinceOpen = !provinceOpen; $nextTick(() => { if(provinceOpen) $refs.mkeProvinceSearch.focus() })">
                                        <div class="mitra-edit-trigger-content is-compact">
                                            <i class="fas fa-map-pin mitra-edit-muted-icon"></i>
                                            <span x-show="!provinsi" class="mitra-edit-placeholder">- Pilih Provinsi
                                                -</span>
                                            <span x-show="provinsi" x-text="provinsi"
                                                class="mitra-edit-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-edit-chevron is-small"
                                            :class="{'is-open': provinceOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-edit-menu is-scrollable" x-show="provinceOpen"
                                        x-transition>
                                        <div class="mitra-edit-search-wrap">
                                            <div class="mitra-edit-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkeProvinceSearch" x-model="provinceSearch" type="text"
                                                    placeholder="Cari provinsi..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-edit-menu-list is-country">
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
                                    <i class="fas fa-city mitra-edit-label-icon"></i>Kota / Kabupaten
                                </label>
                                <div class="alpine-dropdown" @click.outside="cityOpen = false; citySearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': cityOpen, 'disabled': !provinsi}"
                                        @click="if(!provinsi) return; cityOpen = !cityOpen; $nextTick(() => { if(cityOpen) $refs.mkeCitySearch.focus() })">
                                        <div class="mitra-edit-trigger-content is-compact">
                                            <i class="fas fa-city mitra-edit-muted-icon"></i>
                                            <span x-show="!kota && !provinsi" class="mitra-edit-placeholder">- Pilih Provinsi Dahulu -</span>
                                            <span x-show="!kota && provinsi" class="mitra-edit-placeholder">- Pilih Kota / Kabupaten -</span>
                                            <span x-show="kota" x-text="kota" class="mitra-edit-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-edit-chevron is-small"
                                            :class="{'is-open': cityOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-edit-menu is-scrollable" x-show="cityOpen"
                                        x-transition>
                                        <div class="mitra-edit-search-wrap">
                                            <div class="mitra-edit-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkeCitySearch" x-model="citySearch" type="text"
                                                    placeholder="Cari kota/kabupaten..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-edit-menu-list is-country">
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
                                    <i class="fas fa-map-location-dot mitra-edit-label-icon"></i>Kecamatan
                                </label>
                                <div class="alpine-dropdown" @click.outside="districtOpen = false; districtSearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': districtOpen, 'disabled': !kota}"
                                        @click="if(!kota) return; districtOpen = !districtOpen; $nextTick(() => { if(districtOpen) $refs.mkeDistrictSearch.focus() })">
                                        <div class="mitra-edit-trigger-content is-compact">
                                            <i class="fas fa-map-location-dot mitra-edit-muted-icon"></i>
                                            <span x-show="!kecamatan && !kota" class="mitra-edit-placeholder">- Pilih Kota Dahulu -</span>
                                            <span x-show="!kecamatan && kota" class="mitra-edit-placeholder">- Pilih Kecamatan (Opsional) -</span>
                                            <span x-show="kecamatan" x-text="kecamatan" class="mitra-edit-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-edit-chevron is-small"
                                            :class="{'is-open': districtOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-edit-menu is-scrollable" x-show="districtOpen"
                                        x-transition>
                                        <div class="mitra-edit-search-wrap">
                                            <div class="mitra-edit-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkeDistrictSearch" x-model="districtSearch" type="text"
                                                    placeholder="Cari kecamatan..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-edit-menu-list is-country">
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
                                    <i class="fas fa-signs-post mitra-edit-label-icon"></i>Kelurahan / Desa
                                </label>
                                <div class="alpine-dropdown" @click.outside="villageOpen = false; villageSearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': villageOpen, 'disabled': !kecamatan}"
                                        @click="if(!kecamatan) return; villageOpen = !villageOpen; $nextTick(() => { if(villageOpen) $refs.mkeVillageSearch.focus() })">
                                        <div class="mitra-edit-trigger-content is-compact">
                                            <i class="fas fa-signs-post mitra-edit-muted-icon"></i>
                                            <span x-show="!kelurahan && !kecamatan" class="mitra-edit-placeholder">- Pilih Kecamatan Dahulu -</span>
                                            <span x-show="!kelurahan && kecamatan" class="mitra-edit-placeholder">- Pilih Kelurahan (Opsional) -</span>
                                            <span x-show="kelurahan" x-text="kelurahan" class="mitra-edit-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-edit-chevron is-small"
                                            :class="{'is-open': villageOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-edit-menu is-scrollable" x-show="villageOpen"
                                        x-transition>
                                        <div class="mitra-edit-search-wrap">
                                            <div class="mitra-edit-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkeVillageSearch" x-model="villageSearch" type="text"
                                                    placeholder="Cari kelurahan/desa..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-edit-menu-list is-country">
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

                    <div x-show="kategori === 'internasional'" x-transition class="mitra-edit-row">
                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">
                                    <i class="fas fa-globe-americas mitra-edit-label-icon"></i>Negara
                                </label>
                                <div class="alpine-dropdown" @click.outside="countryOpen = false; countrySearch = ''">
                                    <div class="ad-trigger no-icon" :class="{'active': countryOpen}"
                                        @click="countryOpen = !countryOpen; $nextTick(() => { if(countryOpen) $refs.mkeCountrySearch.focus() })">
                                        <div class="mitra-edit-trigger-content is-compact">
                                            <i class="fas fa-flag mitra-edit-muted-icon"></i>
                                            <span x-show="!negara" class="mitra-edit-placeholder">- Pilih Negara
                                                -</span>
                                            <span x-show="negara" x-text="negara"
                                                class="mitra-edit-selected is-normal"></span>
                                        </div>
                                        <i class="fas fa-chevron-down mitra-edit-chevron is-small"
                                            :class="{'is-open': countryOpen}"></i>
                                    </div>
                                    <div class="ad-menu mitra-edit-menu is-scrollable" x-show="countryOpen"
                                        x-transition>
                                        <div class="mitra-edit-search-wrap">
                                            <div class="mitra-edit-search">
                                                <i class="fas fa-search"></i>
                                                <input x-ref="mkeCountrySearch" x-model="countrySearch" type="text"
                                                    placeholder="Cari negara..." @click.stop>
                                            </div>
                                        </div>
                                        <div class="mitra-edit-menu-list is-country">
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
                                    <i class="fas fa-city mitra-edit-label-icon"></i>City / Kota
                                </label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-city mc-icon-left"></i>
                                    <input type="text" x-model="kotaInternasional" placeholder="Contoh: Tokyo / Munich"
                                        class="mc-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mc-group mitra-edit-row">
                        <label class="mc-label">Alamat</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-map-marker-alt mc-icon-left mitra-edit-textarea-icon"></i>
                            <textarea x-model="alamat" rows="2" placeholder="Masukkan alamat lengkap mitra..."
                                class="mc-input mitra-edit-textarea"></textarea>
                        </div>
                    </div>

                    <div class="mc-grid-2">
                        <div class="mc-group">
                            <label class="mc-label">Nomor Telepon</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-phone mc-icon-left"></i>
                                <input type="text" x-model="telp" placeholder="Contoh: 021-12345678" class="mc-input">
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Website</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-globe mc-icon-left"></i>
                                <input type="text" x-model="website" placeholder="https://www.example.com"
                                    class="mc-input">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mitra-edit-footer">
                    <button type="button" data-mitra-edit-modal-close class="mitra-edit-btn mitra-edit-btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" @click="submitMitra()" :disabled="submitting" class="mitra-edit-btn mitra-edit-btn-primary"
                        :class="{'is-submitting': submitting}">
                        <template x-if="!submitting">
                            <span class="mitra-edit-btn-content"><i class="fas fa-save"></i> Perbarui Mitra</span>
                        </template>
                        <template x-if="submitting">
                            <span class="mitra-edit-btn-content"><x-loading class="is-inline" text="Menyimpan..."
                                    :size="18" /></span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
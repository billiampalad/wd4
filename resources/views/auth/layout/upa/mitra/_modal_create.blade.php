@php
    $modalKlasifikasi = \App\Models\Klasifikasi::orderBy('nama', 'asc')->get();
    $modalKlasifikasiItems = $modalKlasifikasi->map(fn ($klas) => [
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

        <div class="mitra-create-body"
            data-store-url="{{ route('upa.mitra.store') }}"
            data-klasifikasi-items='@json($modalKlasifikasiItems)'
            x-data="createMitraModalFromElement($el)">
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
                                    <span x-show="!selectedKlasifikasi" class="mitra-create-placeholder">- Pilih Klasifikasi -</span>
                                    <span x-show="selectedKlasifikasi" x-text="selectedKlasifikasi ? selectedKlasifikasi.label : ''"
                                        class="mitra-create-selected"></span>
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
                                <input type="text" name="nama_mitra" required placeholder="Masukkan nama instansi/mitra" class="mc-input no-icon">
                            </div>
                            <template x-if="errors.nama_mitra">
                                <span class="mitra-create-error">
                                    <i class="fas fa-circle-exclamation"></i> <span x-text="errors.nama_mitra[0]"></span>
                                </span>
                            </template>
                        </div>

                        <div class="mc-group" x-data="{ katOpen: false }">
                            <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                            <div class="alpine-dropdown" @click.outside="katOpen = false">
                                <div class="ad-trigger no-icon" :class="{'active': katOpen}" @click="katOpen = !katOpen">
                                    <span x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '- Pilih Kategori -')"
                                        class="mitra-create-item-text"></span>
                                    <i class="fas fa-chevron-down mitra-create-chevron is-small" :class="{'is-open': katOpen}"></i>
                                </div>
                                <div class="ad-menu mitra-create-menu" x-show="katOpen" x-transition>
                                    <div class="ad-item" :class="{'selected': kategori === 'nasional'}"
                                        @click="kategori = 'nasional'; negara = 'Indonesia'; katOpen = false">Nasional</div>
                                    <div class="ad-item" :class="{'selected': kategori === 'internasional'}"
                                        @click="kategori = 'internasional'; negara = ''; katOpen = false">Internasional</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="kategori === 'internasional'" x-transition class="mitra-create-row">
                        <div class="mc-group">
                            <label class="mc-label">
                                <i class="fas fa-globe-americas mitra-create-label-icon"></i>Negara
                            </label>
                            <div class="alpine-dropdown" @click.outside="countryOpen = false; countrySearch = ''">
                                <div class="ad-trigger" :class="{'active': countryOpen}"
                                    @click="countryOpen = !countryOpen; $nextTick(() => { if(countryOpen) $refs.mkCountrySearch.focus() })">
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
                                            <div class="ad-item" :class="{'selected': negara === country}"
                                                @click="negara = country; countryOpen = false; countrySearch = ''"
                                                x-text="country"></div>
                                        </template>
                                    </div>
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
                                <input type="text" name="website" placeholder="https://www.example.com" class="mc-input">
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
                            <span class="mitra-create-btn-content"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $modalKlasifikasi = \App\Models\Klasifikasi::orderBy('nama', 'asc')->get();
    $modalKlasifikasiItems = $modalKlasifikasi->map(fn ($klas) => [
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

        <div class="mitra-edit-body"
            data-base-url="{{ url('unit/mitra') }}"
            data-klasifikasi-items='@json($modalKlasifikasiItems)'
            x-data="createMitraEditModalFromElement($el)"
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
                                    <span x-show="!selectedKlasifikasi" class="mitra-edit-placeholder">- Pilih Klasifikasi -</span>
                                    <span x-show="selectedKlasifikasi" x-text="selectedKlasifikasi ? selectedKlasifikasi.label : ''"
                                        class="mitra-edit-selected"></span>
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
                                <input type="text" x-model="nama_mitra" required placeholder="Masukkan nama instansi/mitra" class="mc-input no-icon">
                            </div>
                            <template x-if="errors.nama_mitra">
                                <span class="mitra-edit-error">
                                    <i class="fas fa-circle-exclamation"></i> <span x-text="errors.nama_mitra[0]"></span>
                                </span>
                            </template>
                        </div>

                        <div class="mc-group" x-data="{ katOpen: false }">
                            <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                            <div class="alpine-dropdown" @click.outside="katOpen = false">
                                <div class="ad-trigger no-icon" :class="{'active': katOpen}" @click="katOpen = !katOpen">
                                    <span x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '- Pilih Kategori -')"
                                        class="mitra-edit-item-text"></span>
                                    <i class="fas fa-chevron-down mitra-edit-chevron is-small" :class="{'is-open': katOpen}"></i>
                                </div>
                                <div class="ad-menu mitra-edit-menu" x-show="katOpen" x-transition>
                                    <div class="ad-item" :class="{'selected': kategori === 'nasional'}"
                                        @click="kategori = 'nasional'; negara = 'Indonesia'; katOpen = false">Nasional</div>
                                    <div class="ad-item" :class="{'selected': kategori === 'internasional'}"
                                        @click="kategori = 'internasional'; negara = ''; katOpen = false">Internasional</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="kategori === 'internasional'" x-transition class="mitra-edit-row">
                        <div class="mc-group">
                            <label class="mc-label">
                                <i class="fas fa-globe-americas mitra-edit-label-icon"></i>Negara
                            </label>
                            <div class="alpine-dropdown" @click.outside="countryOpen = false; countrySearch = ''">
                                <div class="ad-trigger" :class="{'active': countryOpen}"
                                    @click="countryOpen = !countryOpen; $nextTick(() => { if(countryOpen) $refs.mkeCountrySearch.focus() })">
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
                                                @click="negara = country; countryOpen = false; countrySearch = ''"
                                                x-text="country"></div>
                                        </template>
                                    </div>
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
                                <input type="text" x-model="website" placeholder="https://www.example.com" class="mc-input">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mitra-edit-footer">
                    <button type="button" data-mitra-edit-modal-close class="mitra-edit-btn mitra-edit-btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" :disabled="submitting" class="mitra-edit-btn mitra-edit-btn-primary"
                        :class="{'is-submitting': submitting}">
                        <template x-if="!submitting">
                            <span class="mitra-edit-btn-content"><i class="fas fa-save"></i> Perbarui Mitra</span>
                        </template>
                        <template x-if="submitting">
                            <span class="mitra-edit-btn-content"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

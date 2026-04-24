<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">Kerjasama</span>
            <span class="sep">/</span>
            <a href="{{ route('unit.mitra') }}" style="color: inherit; text-decoration: none;">Mitra</a>
            <span class="sep">/</span>
            <span class="current">Tambah Mitra</span>
        </div>
        <h2 id="pageTitle">Tambah Mitra Baru</h2>
        <p id="pageDesc">Silakan lengkapi data instansi atau mitra baru di bawah ini.</p>
    </div>

    <div class="modern-card">
        <form action="{{ route('unit.mitra.store') }}" method="POST">
            @csrf
            <div class="mc-body" style="padding: 30px;">
                <div class="mc-grid-2">
                    {{-- Klasifikasi (Alpine.js Dropdown) --}}
                    <div class="mc-group" x-data="{
                        open: false,
                        search: '',
                        selected: '{{ old('id_klasifikasi', '') }}',
                        items: [
                            @foreach($klasifikasi ?? [] as $klas)
                                { id: '{{ $klas->id }}', label: '{{ $klas->nama }}' },
                            @endforeach
                        ],
                        get selectedItem() {
                            return this.items.find(i => i.id === this.selected);
                        },
                        get filteredItems() {
                            if (!this.search) return this.items;
                            const q = this.search.toLowerCase();
                            return this.items.filter(i => i.label.toLowerCase().includes(q));
                        }
                    }">
                        <label class="mc-label">Klasifikasi Mitra <span class="mc-req">*</span></label>
                        <input type="hidden" name="id_klasifikasi" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false; search = ''">
                            <div class="ad-trigger no-icon" :class="{'active': open}"
                                @click="open = !open; $nextTick(() => { if(open) $refs.klasifikasiSearch.focus() })">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                    <div
                                        style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79, 70, 229, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <span x-show="!selectedItem" style="color: #9ca3af; font-size: 13px;">— Pilih
                                        Klasifikasi —</span>
                                    <span x-show="selectedItem" x-text="selectedItem ? selectedItem.label : ''"
                                        style="font-weight: 600; font-size: 13px; color: var(--text);"></span>
                                </div>
                                <i class="fas fa-chevron-down"
                                    style="font-size: 11px; color: #9ca3af; transition: 0.3s; flex-shrink: 0;"
                                    :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition
                                style="max-height: 280px; display: flex; flex-direction: column;">
                                <div
                                    style="padding: 8px 12px; border-bottom: 1px solid var(--border); background: var(--surface); position: sticky; top: 0; z-index: 2;">
                                    <div
                                        style="display: flex; align-items: center; gap: 8px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px;">
                                        <i class="fas fa-search" style="font-size: 12px; color: #9ca3af;"></i>
                                        <input x-ref="klasifikasiSearch" x-model="search" type="text"
                                            placeholder="Cari klasifikasi..."
                                            style="border: none; outline: none; background: transparent; font-size: 13px; color: var(--text); width: 100%; font-family: inherit;"
                                            @click.stop>
                                        <button x-show="search" @click.stop="search = ''" type="button"
                                            style="background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; font-size: 11px;">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <div style="overflow-y: auto; flex: 1;">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <div class="ad-item" :class="{'selected': selected === item.id}"
                                            @click="selected = item.id; open = false; search = ''"
                                            style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                :style="selected === item.id ? 'background: var(--accent); border-color: var(--accent);' : ''">
                                                <i class="fas fa-check" style="font-size: 10px; color: #fff;"
                                                    x-show="selected === item.id"></i>
                                            </div>
                                            <span x-text="item.label"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredItems.length === 0"
                                        style="padding: 12px 16px; text-align: center; color: #9ca3af; font-size: 12px;">
                                        Tidak ada hasil ditemukan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('id_klasifikasi')
                            <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;"><i
                                    class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Mitra Section --}}
                <div id="mitraContainer"
                    x-data="{ kategori: '{{ old('kategori', '') }}', negara: '{{ old('negara', 'Indonesia') }}' }">
                    <div class="mitra-card"
                        style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-top: 16px; margin-bottom: 16px; position: relative;">
                        <div class="mc-grid-2">
                            {{-- Nama Mitra --}}
                            <div class="mc-group">
                                <label class="mc-label">Nama Instansi / Mitra <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <input type="text" name="nama_mitra" value="{{ old('nama_mitra') }}" required
                                        placeholder="Masukkan nama instansi/mitra" class="mc-input no-icon">
                                </div>
                                @error('nama_mitra')
                                    <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;"><i
                                            class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Kategori Mitra (Alpine Dropdown) --}}
                            <div class="mc-group" x-data="{ open: false }">
                                <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                                <input type="hidden" name="kategori" :value="kategori" required>
                                <div class="alpine-dropdown" @click.outside="open = false">
                                    <div class="ad-trigger no-icon" :class="{'active': open}" @click="open = !open">
                                        <span
                                            x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '— Pilih Kategori —')"></span>
                                        <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                            :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>
                                    <div class="ad-menu" x-show="open" x-transition>
                                        <div class="ad-item" :class="{'selected': kategori === 'nasional'}"
                                            @click="kategori = 'nasional'; negara = 'Indonesia'; open = false">
                                            Nasional</div>
                                        <div class="ad-item" :class="{'selected': kategori === 'internasional'}"
                                            @click="kategori = 'internasional'; negara = ''; open = false">
                                            Internasional</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Negara Dropdown (hanya muncul saat Internasional) --}}
                        <div x-show="kategori === 'internasional'" x-transition>
                            <div class="mc-group" style="margin-top: 16px;" x-data="countryPicker()"
                                x-init="$watch('kategori', v => { if(v !== 'internasional') selected = 'Indonesia' }); selected = negara">
                                <label class="mc-label"><i class="fas fa-globe-americas"
                                        style="color: var(--accent); margin-right: 6px;"></i>Negara</label>
                                <input type="hidden" name="negara" :value="selected" required>
                                <div class="alpine-dropdown" @click.outside="open = false; search = ''">
                                    <div class="ad-trigger" :class="{'active': open}"
                                        @click="open = !open; $nextTick(() => { if(open) $refs.countrySearch.focus() })">
                                        <div
                                            style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                            <i class="fas fa-flag"
                                                style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                            <span x-show="!selected" style="color: #9ca3af;">— Pilih Negara —</span>
                                            <span x-show="selected" x-text="selected" style="font-weight: 500;"></span>
                                        </div>
                                        <i class="fas fa-chevron-down"
                                            style="font-size: 10px; transition: 0.3s; flex-shrink: 0;"
                                            :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>
                                    <div class="ad-menu" x-show="open" x-transition
                                        style="max-height: 280px; overflow: hidden; display: flex; flex-direction: column;">
                                        {{-- Search Input --}}
                                        <div
                                            style="padding: 8px 12px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                            <div
                                                style="display: flex; align-items: center; gap: 8px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px;">
                                                <i class="fas fa-search" style="font-size: 12px; color: #9ca3af;"></i>
                                                <input x-ref="countrySearch" x-model="search" type="text"
                                                    placeholder="Cari negara..."
                                                    style="border: none; outline: none; background: transparent; font-size: 13px; color: var(--text); width: 100%; font-family: inherit;"
                                                    @click.stop>
                                                <button x-show="search" @click.stop="search = ''" type="button"
                                                    style="background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; font-size: 11px;">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- Country List --}}
                                        <div style="overflow-y: auto; max-height: 220px; flex: 1;">
                                            <template x-for="country in filteredCountries" :key="country">
                                                <div class="ad-item" :class="{'selected': selected === country}"
                                                    @click="selected = country; negara = country; open = false; search = ''"
                                                    x-text="country"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="negara" :value="negara" x-show="kategori !== 'internasional'">
                    </div>
                </div>

                @error('nama_mitra')
                    <span class="text-danger" style="margin-top: 8px; display: block; font-size: 11px;"><i
                            class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                @enderror

                {{-- Alamat --}}
                <div class="mc-group" style="margin-bottom: 16px;">
                    <label class="mc-label">Alamat</label>
                    <div class="mc-input-wrap">
                        <i class="fas fa-map-marker-alt mc-icon-left" style="top: 14px;"></i>
                        <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap mitra..."
                            class="mc-input @error('alamat') border-danger @enderror"
                            style="resize: vertical; min-height: 70px;">{{ old('alamat') }}</textarea>
                    </div>
                    @error('alamat')
                        <span class="text-danger"
                            style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mc-grid-2">

                    {{-- Telepon --}}
                    <div class="mc-group">
                        <label class="mc-label">Nomor Telepon</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-phone mc-icon-left"></i>
                            <input type="text" name="telp" value="{{ old('telp') }}" placeholder="Contoh: 021-12345678"
                                class="mc-input @error('telp') border-danger @enderror" />
                        </div>
                        @error('telp')
                            <span class="text-danger"
                                style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Website --}}
                    <div class="mc-group">
                        <label class="mc-label">Website</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-globe mc-icon-left"></i>
                            <input type="text" name="website" value="{{ old('website') }}"
                                placeholder="Contoh: https://www.example.com"
                                class="mc-input @error('website') border-danger @enderror" />
                        </div>
                        @error('website')
                            <span class="text-danger"
                                style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mc-footer"
                style="display: flex; justify-content: space-between; align-items: center; padding: 20px 30px; background: var(--surface2); border-top: 1px solid var(--border);">
                <a href="{{ route('unit.mitra') }}" class="rfc-btn rfc-btn-danger" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="rfc-btn rfc-btn-primary">
                    <i class="fas fa-save"></i> Simpan Mitra
                </button>
            </div>
        </form>
    </div>
</main>
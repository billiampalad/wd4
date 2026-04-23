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
                    {{-- Nama Mitra --}}
                    <div class="mc-group">
                        <label class="mc-label">Nama Instansi / Mitra <span class="mc-req">*</span></label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-building mc-icon-left"></i>
                            <input type="text" name="nama_mitra" value="{{ old('nama_mitra') }}" required placeholder="Contoh: PT. Teknologi Maju Bersama" class="mc-input @error('nama_mitra') border-danger @enderror" />
                        </div>
                        @error('nama_mitra')
                            <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Klasifikasi --}}
                    <div class="mc-group">
                        <label class="mc-label">Klasifikasi</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-tag mc-icon-left"></i>
                            <select name="id_klasifikasi" class="mc-input @error('id_klasifikasi') border-danger @enderror">
                                <option value="">-- Pilih Klasifikasi --</option>
                                @forelse($klasifikasi ?? [] as $klas)
                                    <option value="{{ $klas->id }}" {{ old('id_klasifikasi') == $klas->id ? 'selected' : '' }}>{{ $klas->nama }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        @error('id_klasifikasi')
                            <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="mc-group" style="grid-column: 1 / -1;">
                        <label class="mc-label">Alamat</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-map-marker-alt mc-icon-left" style="top: 14px;"></i>
                            <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap mitra..." class="mc-input" style="resize: vertical; min-height: 70px;">{{ old('alamat') }}</textarea>
                        </div>
                        @error('alamat')
                            <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Kategori Mitra --}}
                    <div class="mc-group" x-data="{ 
                        open: false, 
                        selected: '{{ old('kategori', 'nasional') }}',
                        items: [
                            { id: 'nasional', label: 'Nasional', icon: 'fa-flag', color: '#3b82f6' },
                            { id: 'internasional', label: 'Internasional', icon: 'fa-globe-americas', color: '#8b5cf6' }
                        ],
                        get selectedItem() {
                            return this.items.find(i => i.id === this.selected);
                        }
                    }">
                        <label class="mc-label">Kategori Mitra <span class="mc-req">*</span></label>
                        <input type="hidden" name="kategori" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div :style="'width: 32px; height: 32px; border-radius: 8px; background:' + selectedItem.color + '20; color:' + selectedItem.color + '; display: flex; align-items: center; justify-content: center; font-size: 14px;'">
                                        <i class="fas" :class="selectedItem.icon"></i>
                                    </div>
                                    <span x-text="selectedItem.label" style="font-weight: 600; font-size: 13px; color: var(--text);"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 11px; color: #9ca3af; transition: 0.3s;" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div @click="selected = item.id; open = false" class="ad-item" :class="{'selected': selected === item.id}">
                                        <div :style="'width: 30px; height: 30px; border-radius: 8px; background:' + item.color + '20; color:' + item.color + '; display: flex; align-items: center; justify-content: center; font-size: 13px;'">
                                            <i class="fas" :class="item.icon"></i>
                                        </div>
                                        <span x-text="item.label" style="font-weight: 600; font-size: 13px; color: var(--text);"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Negara --}}
                    <div class="mc-group" x-data="countryPicker()" x-init="selected = '{{ old('negara', 'Indonesia') }}'">
                        <label class="mc-label">Negara <span class="mc-req">*</span></label>
                        <input type="hidden" name="negara" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false; search = ''">
                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open; $nextTick(() => { if(open) $refs.countrySearch.focus() })">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--accent2); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                        <i class="fas fa-flag"></i>
                                    </div>
                                    <span x-text="selected" style="font-weight: 600; font-size: 13px; color: var(--text);"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 11px; color: #9ca3af; transition: 0.3s;" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition style="max-height: 280px; display: flex; flex-direction: column;">
                                <div style="padding: 8px 12px; border-bottom: 1px solid var(--border); background: var(--surface); position: sticky; top: 0; z-index: 2;">
                                    <div style="display: flex; align-items: center; gap: 8px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px;">
                                        <i class="fas fa-search" style="font-size: 12px; color: #9ca3af;"></i>
                                        <input x-ref="countrySearch" x-model="search" type="text" placeholder="Cari negara..." style="border: none; outline: none; background: transparent; font-size: 13px; color: var(--text); width: 100%;" @click.stop>
                                    </div>
                                </div>
                                <div style="overflow-y: auto; flex: 1;">
                                    <template x-for="country in filteredCountries" :key="country">
                                        <div class="ad-item" :class="{'selected': selected === country}" @click="selected = country; open = false; search = ''" x-text="country"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Telepon --}}
                    <div class="mc-group">
                        <label class="mc-label">Nomor Telepon</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-phone mc-icon-left"></i>
                            <input type="text" name="telp" value="{{ old('telp') }}" placeholder="Contoh: 021-12345678" class="mc-input @error('telp') border-danger @enderror" />
                        </div>
                        @error('telp')
                            <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Website --}}
                    <div class="mc-group">
                        <label class="mc-label">Website</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-globe mc-icon-left"></i>
                            <input type="text" name="website" value="{{ old('website') }}" placeholder="Contoh: https://www.example.com" class="mc-input @error('website') border-danger @enderror" />
                        </div>
                        @error('website')
                            <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mc-footer" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 30px; background: var(--surface2); border-top: 1px solid var(--border);">
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
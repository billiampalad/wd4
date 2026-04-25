<!-- Main Content -->
<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <a href="{{ route('unit.dkerjasama') }}" style="color: inherit; text-decoration: none;">Data Kerjasama</a>
            <span class="sep">/</span>
            <span class="current">Tambah Data</span>
        </div>
        <h2 id="pageTitle">Tambah Data Kerjasama Unit</h2>
        <p id="pageDesc">Isi formulir untuk menambahkan kegiatan kerjasama baru (Unit).</p>
    </div>

    @if(session('error'))
        <div
            style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="font-size: 16px; color: #ef4444;"></i>
            {{ session('error') }}
        </div>
    @endif

    <div style="width: 100%; max-width: 1200px; margin: 0 auto;">
        <div class="modern-card">
            <div class="mc-header">
                <div class="mc-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="mc-title">
                    <h3>Formulir Kerjasama Baru</h3>
                    <p>Silakan lengkapi data kegiatan kerjasama di bawah ini.</p>
                </div>
            </div>

            <form action="{{ route('unit.kerjasama.store') }}" method="POST">
                @csrf
                <div class="mc-body">
                    <div class="mc-grid-2">
                        {{-- Dokumen Kerjasama (Alpine Interactive) --}}
                        <div style="grid-column: 1 / -1;" class="mc-group" x-data="{ 
                            open: false, 
                            selected: '{{ old('jenis_dokumen', 'MoU') }}',
                            items: [
                                { id: 'MoU', label: 'Memorandum of Understanding', short: 'MoU', icon: 'fa-file-signature', color: '#4f46e5' },
                                { id: 'MoA', label: 'Memorandum of Agreement', short: 'MoA', icon: 'fa-file-contract', color: '#059669' },
                                { id: 'IA', label: 'Implementation Arrangement', short: 'IA', icon: 'fa-file-invoice', color: '#d97706' }
                            ],
                            get selectedItem() {
                                return this.items.find(i => i.id === this.selected);
                            }
                        }">
                            <label class="mc-label">Dokumen Kerjasama <span class="mc-req">*</span></label>
                            <input type="hidden" name="jenis_dokumen" :value="selected">

                            <div class="mc-grid-2" style="gap: 16px;">
                                {{-- Left: Type Dropdown --}}
                                <div class="alpine-dropdown" @click.outside="open = false" style="position: relative;">
                                    <div class="ad-trigger" :class="{'active': open}" @click="open = !open"
                                        style="height: 48px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.3s;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div
                                                :style="'width: 32px; height: 32px; border-radius: 8px; background:' + selectedItem.color + '20; color:' + selectedItem.color + '; display: flex; align-items: center; justify-content: center; font-size: 14px;'">
                                                <i class="fas" :class="selectedItem.icon"></i>
                                            </div>
                                            <div style="display: flex; flex-direction: column; line-height: 1.2;">
                                                <span x-text="selectedItem.short"
                                                    style="font-weight: 700; font-size: 13px; color: var(--text);"></span>
                                                <span x-text="selectedItem.label"
                                                    style="font-size: 11px; color: var(--text-sub);"></span>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-down"
                                            style="font-size: 11px; color: #9ca3af; transition: 0.3s;"
                                            :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>

                                    <div class="ad-menu" x-show="open"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        style="position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); z-index: 50; padding: 6px; display: flex; flex-direction: column; gap: 4px;">
                                        <template x-for="item in items" :key="item.id">
                                            <div @click="selected = item.id; open = false" class="ad-item"
                                                :class="{'selected': selected === item.id}"
                                                style="padding: 10px 12px; border-radius: 8px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: all 0.2s;"
                                                onmouseover="this.style.background='var(--surface2)'"
                                                onmouseout="if(!this.classList.contains('selected')) this.style.background='transparent'">
                                                <div
                                                    :style="'width: 30px; height: 30px; border-radius: 8px; background:' + item.color + '20; color:' + item.color + '; display: flex; align-items: center; justify-content: center; font-size: 13px;'">
                                                    <i class="fas" :class="item.icon"></i>
                                                </div>
                                                <div style="display: flex; flex-direction: column; line-height: 1.2;">
                                                    <span x-text="item.short"
                                                        style="font-weight: 700; font-size: 13px; color: var(--text);"></span>
                                                    <span x-text="item.label"
                                                        style="font-size: 11px; color: var(--text-sub);"></span>
                                                </div>
                                                <i class="fas fa-check" x-show="selected === item.id"
                                                    style="margin-left: auto; font-size: 11px; color: var(--accent);"></i>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Right: Number Input --}}
                                <div class="mc-input-wrap">
                                    <i class="fas fa-hashtag mc-icon-left"></i>
                                    <input type="text" name="nomor_mou" value="{{ old('nomor_mou') }}"
                                        placeholder="Masukkan nomor dokumen..." class="mc-input"
                                        style="height: 48px;" />
                                </div>
                            </div>
                            @error('jenis_dokumen')
                                <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;"><i
                                        class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Nama Kegiatan --}}
                        <div style="grid-column: 1 / -1;" class="mc-group">
                            <label class="mc-label">Judul Kerjasama<span class="mc-req">*</span></label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-file-lines mc-icon-left"></i>
                                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" required
                                    placeholder="Contoh: Pelatihan Web Development Bersama Industri"
                                    class="mc-input @error('nama_kegiatan') border-danger @enderror" />
                            </div>
                            @error('nama_kegiatan')
                                <span class="text-danger" style="font-size: 11px; margin-top: 4px;"><i
                                        class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div style="grid-column: 1 / -1;" class="mc-group">
                            <label class="mc-label">Deskripsi</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-comment-dots mc-icon-left" style="top: 14px;"></i>
                                <textarea name="dok_keterangan" rows="3"
                                    placeholder="Ringkasan singkat terkait cakupan atau kegiatan kerja sama"
                                    class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('dok_keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ SECTION 2: Penggiat Kerja Sama (Separate Card) ═══ --}}
                <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; margin-top: 24px; overflow: hidden;">
                    {{-- Card Header --}}
                    <div x-data="{ showPenggiat: true, showPihak1: true, showPihak2: false }">
                        <div @click="showPenggiat = !showPenggiat"
                            style="display: flex; align-items: center; gap: 14px; padding: 20px 24px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.04), rgba(5,150,105,0.04));">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #059669); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text);">Penggiat Kerja Sama</h4>
                                <p style="margin: 2px 0 0; font-size: 12px; color: var(--text-sub);">Data pihak-pihak yang terlibat dalam kerja sama</p>
                            </div>
                            <i class="fas fa-chevron-down"
                                style="font-size: 12px; color: var(--text-sub); transition: transform 0.3s ease;"
                                :style="showPenggiat ? 'transform: rotate(180deg)' : ''"></i>
                        </div>

                        {{-- Card Body --}}
                        <div x-show="showPenggiat" x-collapse.duration.300ms
                            style="padding: 20px 24px;">

                            {{-- ── Pihak Ke-1 ── --}}
                            <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 16px; overflow: hidden;">
                                {{-- Pihak 1 Header --}}
                                <div @click="showPihak1 = !showPihak1"
                                    style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                    onmouseover="this.style.background='var(--surface)'"
                                    onmouseout="this.style.background='transparent'">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.12); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 700; font-size: 13px; color: var(--text);">Pihak Ke-1</span>
                                        <span style="font-size: 11px; color: var(--text-sub); margin-left: 8px;">Instansi Penyelenggara</span>
                                    </div>
                                    <i class="fas fa-chevron-down"
                                        style="font-size: 11px; color: #9ca3af; transition: transform 0.3s ease;"
                                        :style="showPihak1 ? 'transform: rotate(180deg)' : ''"></i>
                                </div>

                                {{-- Pihak 1 Content --}}
                                <div x-show="showPihak1" x-collapse.duration.300ms
                                    style="padding: 0 20px 20px 20px;">
                                    {{-- Nama Instansi & Alamat --}}
                                    <div class="mc-grid-2">
                                        <div class="mc-group">
                                            <label class="mc-label">Nama Instansi <span class="mc-req">*</span></label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-building mc-icon-left"></i>
                                                <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}"
                                                    placeholder="Masukkan nama instansi" class="mc-input" required />
                                            </div>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Alamat</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-map-marker-alt mc-icon-left"></i>
                                                <input type="text" name="alamat_instansi" value="{{ old('alamat_instansi') }}"
                                                    placeholder="Masukkan alamat instansi" class="mc-input" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Penandatangan --}}
                                    <div class="mc-grid-2" style="margin-top: 10px;">
                                        <div class="mc-group">
                                            <label class="mc-label">Nama Penandatangan</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-pen-nib mc-icon-left"></i>
                                                <input type="text" name="nama_penandatangan" value="{{ old('nama_penandatangan') }}"
                                                    placeholder="Nama penandatangan" class="mc-input" />
                                            </div>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Jabatan Penandatangan</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-id-badge mc-icon-left"></i>
                                                <input type="text" name="jabatan_penandatangan"
                                                    value="{{ old('jabatan_penandatangan') }}" placeholder="Jabatan penandatangan"
                                                    class="mc-input" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Penanggung Jawab --}}
                                    <div class="mc-grid-2" style="margin-top: 10px;">
                                        <div class="mc-group">
                                            <label class="mc-label">Nama Penanggung Jawab</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-user-tie mc-icon-left"></i>
                                                <input type="text" name="nama_penanggung_jawab"
                                                    value="{{ old('nama_penanggung_jawab') }}" placeholder="Nama penanggung jawab"
                                                    class="mc-input" />
                                            </div>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Jabatan Penanggung Jawab</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-id-badge mc-icon-left"></i>
                                                <input type="text" name="jabatan_penanggung_jawab"
                                                    value="{{ old('jabatan_penanggung_jawab') }}" placeholder="Jabatan penanggung jawab"
                                                    class="mc-input" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Pihak Ke-2 ── --}}
                            <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
                                {{-- Pihak 2 Header --}}
                                <div @click="showPihak2 = !showPihak2"
                                    style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                    onmouseover="this.style.background='var(--surface)'"
                                    onmouseout="this.style.background='transparent'">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(5,150,105,0.12); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 700; font-size: 13px; color: var(--text);">Pihak Ke-2</span>
                                        <span style="font-size: 11px; color: var(--text-sub); margin-left: 8px;">Mitra Kerja Sama</span>
                                    </div>
                                    <i class="fas fa-chevron-down"
                                        style="font-size: 11px; color: #9ca3af; transition: transform 0.3s ease;"
                                        :style="showPihak2 ? 'transform: rotate(180deg)' : ''"></i>
                                </div>

                                {{-- Pihak 2 Content --}}
                                <div x-show="showPihak2" x-collapse.duration.300ms
                                    style="padding: 0 20px 20px 20px;">
                                    {{-- Nama Mitra & Alamat --}}
                                    <div class="mc-grid-2">
                                        <div class="mc-group">
                                            <label class="mc-label">Nama Mitra <span class="mc-req">*</span></label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-building mc-icon-left"></i>
                                                <input type="text" name="nama_mitra" value="{{ old('nama_mitra') }}"
                                                    placeholder="Masukkan nama mitra" class="mc-input" required />
                                            </div>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Alamat</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-map-marker-alt mc-icon-left"></i>
                                                <input type="text" name="alamat_instansi" value="{{ old('alamat_instansi') }}"
                                                    placeholder="Masukkan alamat instansi" class="mc-input" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Penandatangan --}}
                                    <div class="mc-grid-2" style="margin-top: 10px;">
                                        <div class="mc-group">
                                            <label class="mc-label">Nama Penandatangan</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-pen-nib mc-icon-left"></i>
                                                <input type="text" name="nama_penandatangan" value="{{ old('nama_penandatangan') }}"
                                                    placeholder="Nama penandatangan" class="mc-input" />
                                            </div>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Jabatan Penandatangan</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-id-badge mc-icon-left"></i>
                                                <input type="text" name="jabatan_penandatangan"
                                                    value="{{ old('jabatan_penandatangan') }}" placeholder="Jabatan penandatangan"
                                                    class="mc-input" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Penanggung Jawab --}}
                                    <div class="mc-grid-2" style="margin-top: 10px;">
                                        <div class="mc-group">
                                            <label class="mc-label">Nama Penanggung Jawab</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-user-tie mc-icon-left"></i>
                                                <input type="text" name="nama_penanggung_jawab"
                                                    value="{{ old('nama_penanggung_jawab') }}" placeholder="Nama penanggung jawab"
                                                    class="mc-input" />
                                            </div>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Jabatan Penanggung Jawab</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-id-badge mc-icon-left"></i>
                                                <input type="text" name="jabatan_penanggung_jawab"
                                                    value="{{ old('jabatan_penanggung_jawab') }}" placeholder="Jabatan penanggung jawab"
                                                    class="mc-input" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('mitra_nama')
                        <span class="text-danger" style="margin: 12px 24px; display: block; font-size: 11px;"><i
                                class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Continue mc-body for remaining sections --}}
                <div class="mc-body">

                    {{-- ═══ SECTION 4: Bentuk Kegiatan ═══ --}}
                    <div class="mc-section-title">
                        <span class="mc-section-num"><i class="fas fa-users"></i></span>
                        <span>Bentuk Kegiatan</span>
                    </div>

                    {{-- Jenis Kerjasama (Alpine Multi-Select) --}}
                    <div class="mc-group" x-data="{ 
                            open: false, 
                            selected: {{ json_encode(old('id_jenis', [])) }},
                            items: [
                                @foreach($jenisKerjasama as $jenis)
                                    { id: {{ $jenis->id }}, label: '{{ $jenis->nama_kerjasama }}' },
                                @endforeach
                            ],
                            toggle(id) {
                                const idx = this.selected.indexOf(id);
                                if (idx > -1) { this.selected.splice(idx, 1); }
                                else { this.selected.push(id); }
                            },
                            isSelected(id) { return this.selected.includes(id); },
                            get selectedLabels() {
                                return this.items.filter(i => this.selected.includes(i.id)).map(i => i.label);
                            }
                        }">
                        <label class="mc-label">Jenis Kerjasama <span class="mc-req">*</span></label>
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="id_jenis[]" :value="id">
                        </template>
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger no-icon" :class="{'active': open}" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                    <i class="fas fa-handshake"
                                        style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                    <span x-show="selected.length === 0" style="color: #9ca3af;">— Pilih Jenis
                                        —</span>
                                    <div x-show="selected.length > 0" style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        <template x-for="label in selectedLabels" :key="label">
                                            <span class="tag tag-purple" style="font-size: 10px; padding: 2px 8px;"
                                                x-text="label"></span>
                                        </template>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down"
                                    style="font-size: 10px; transition: 0.3s; flex-shrink: 0;"
                                    :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{'selected': isSelected(item.id)}"
                                        @click="toggle(item.id)" style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                            :style="isSelected(item.id) ? 'background: var(--accent); border-color: var(--accent);' : ''">
                                            <i class="fas fa-check" style="font-size: 10px; color: #fff;"
                                                x-show="isSelected(item.id)"></i>
                                        </div>
                                        <span x-text="item.label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ SECTION 5: Permasalahan & Solusi ═══ --}}
                    <div class="mc-section-title">
                        <span class="mc-section-num">05</span>
                        <span>Permasalahan & Solusi</span>
                    </div>

                    <div class="mc-grid-1">
                        <div class="mc-group">
                            <label class="mc-label">Kendala yang dihadapi</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-exclamation-triangle mc-icon-left" style="top: 14px;"></i>
                                <textarea name="masalah_kendala" rows="3"
                                    placeholder="Jelaskan kendala atau permasalahan..." class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('masalah_kendala') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Solusi</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-check-circle mc-icon-left" style="top: 14px;"></i>
                                <textarea name="masalah_solusi" rows="3"
                                    placeholder="Upaya yang dilakukan untuk mengatasi kendala..." class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('masalah_solusi') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Rekomendasi</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-lightbulb mc-icon-left" style="top: 14px;"></i>
                                <textarea name="masalah_rekomendasi" rows="3"
                                    placeholder="Berikan rekomendasi perbaikan..." class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('masalah_rekomendasi') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ SECTION 6: Dokumentasi ═══ --}}
                    <div class="mc-section-title">
                        <span class="mc-section-num">06</span>
                        <span>Dokumentasi <span
                                style="font-weight: 400; font-size: 12px; color: var(--text-sub); margin-left: 4px;">(Opsional)</span></span>
                    </div>

                    <div class="mc-grid-1">
                        <div class="mc-group">
                            <label class="mc-label">Link Google Drive</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-link mc-icon-left"></i>
                                <input type="text" name="dok_link_drive" value="{{ old('dok_link_drive') }}"
                                    placeholder="https://drive.google.com/..." class="mc-input" />
                            </div>
                        </div>
                    </div>

                    <div class="mc-section-title">
                        <span class="mc-section-num"><i class="fas fa-users"></i></span>
                        <span>Form Tambahan</span>
                    </div>

                    <div class="mc-grid-2">
                        <div class="mc-group" style="grid-column: 1 / -1;">
                            <label class="mc-label">Deskripsi Pelaksanaan <span class="mc-req">*</span></label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-cogs mc-icon-left" style="top: 14px;"></i>
                                <textarea name="pelaksanaan_deskripsi" rows="3" required
                                    placeholder="Deskripsi pelaksanaan kegiatan..." class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('pelaksanaan_deskripsi') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Cakupan</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-layer-group mc-icon-left"></i>
                                <input type="text" name="pelaksanaan_cakupan" value="{{ old('pelaksanaan_cakupan') }}"
                                    placeholder="Cakupan kegiatan" class="mc-input" />
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Jumlah Peserta</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-users mc-icon-left"></i>
                                <input type="number" name="pelaksanaan_peserta" value="{{ old('pelaksanaan_peserta') }}"
                                    placeholder="0" min="0" class="mc-input" />
                            </div>
                        </div>
                        <div class="mc-group" style="grid-column: 1 / -1;">
                            <label class="mc-label">Sumber Daya</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-tools mc-icon-left" style="top: 14px;"></i>
                                <textarea name="pelaksanaan_sumber_daya" rows="2"
                                    placeholder="Sumber daya yang digunakan..." class="mc-input"
                                    style="resize: vertical; min-height: 80px;">{{ old('pelaksanaan_sumber_daya') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Periode Mulai (Alpine Datepicker) --}}
                    <div class="mc-group" x-data="datepicker('{{ old('periode_mulai') }}')">
                        <label class="mc-label">Periode Mulai</label>
                        <div class="alpine-datepicker" @click.outside="show = false">
                            <div class="adp-input-wrap">
                                <i class="fas fa-calendar-day mc-icon-left"></i>
                                <input type="text" name="periode_mulai" x-model="formattedDate" readonly
                                    @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()"
                                            x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i
                                                class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i
                                                class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>

                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{'selected': month === index}"
                                            @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..."
                                            style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                            @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{'selected': year === y}"
                                            @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>

                                <div class="adp-grid">
                                    <template x-for="day in dayNames">
                                        <div class="adp-day-name" x-text="day"></div>
                                    </template>
                                    <template x-for="blankday in blanks">
                                        <div class="adp-day empty"></div>
                                    </template>
                                    <template x-for="date in days">
                                        <div class="adp-day"
                                            :class="{'today': isToday(date), 'selected': isSelected(date)}"
                                            @click="selectDate(date)" x-text="date"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Periode Selesai (Alpine Datepicker) --}}
                    <div class="mc-group" x-data="datepicker('{{ old('periode_selesai') }}')">
                        <label class="mc-label">Periode Selesai</label>
                        <div class="alpine-datepicker" @click.outside="show = false">
                            <div class="adp-input-wrap">
                                <i class="fas fa-calendar-check mc-icon-left"></i>
                                <input type="text" name="periode_selesai" x-model="formattedDate" readonly
                                    @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                            </div>
                            <div class="adp-calendar" x-show="show" x-transition>
                                <div class="adp-header">
                                    <div style="display: flex; gap: 4px;">
                                        <span class="adp-month" @click="toggleMonthPicker()"
                                            x-text="monthNames[month]"></span>
                                        <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                    </div>
                                    <div class="adp-nav">
                                        <div class="adp-nav-btn" @click="prevMonth()"><i
                                                class="fas fa-chevron-left"></i></div>
                                        <div class="adp-nav-btn" @click="nextMonth()"><i
                                                class="fas fa-chevron-right"></i></div>
                                    </div>
                                </div>

                                <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                    <template x-for="(mName, index) in monthNames">
                                        <div class="adp-picker-item" :class="{'selected': month === index}"
                                            @click="selectMonth(index)" x-text="mName"></div>
                                    </template>
                                </div>
                                <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                    <div style="grid-column: span 4; padding: 4px;">
                                        <input type="text" x-model="yearSearch" placeholder="Cari tahun..."
                                            style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                            @click.stop>
                                    </div>
                                    <template x-for="y in filteredYears">
                                        <div class="adp-picker-item" :class="{'selected': year === y}"
                                            @click="selectYear(y)" x-text="y"></div>
                                    </template>
                                </div>

                                <div class="adp-grid">
                                    <template x-for="day in dayNames">
                                        <div class="adp-day-name" x-text="day"></div>
                                    </template>
                                    <template x-for="blankday in blanks">
                                        <div class="adp-day empty"></div>
                                    </template>
                                    <template x-for="date in days">
                                        <div class="adp-day"
                                            :class="{'today': isToday(date), 'selected': isSelected(date)}"
                                            @click="selectDate(date)" x-text="date"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mc-grid-2">
                        <div class="mc-group">
                            <label class="mc-label">Tujuan Kegiatan <span class="mc-req">*</span></label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-bullseye mc-icon-left" style="top: 14px;"></i>
                                <textarea name="tujuan" rows="3" required
                                    placeholder="Meningkatkan kompetensi praktis mahasiswa..." class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('tujuan') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Sasaran Kegiatan <span class="mc-req">*</span></label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-crosshairs mc-icon-left" style="top: 14px;"></i>
                                <textarea name="sasaran" rows="3" required
                                    placeholder="Mahasiswa D3 Teknik Informatika Semester 5..." class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('sasaran') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mc-grid-2">
                        <div class="mc-group">
                            <label class="mc-label">Hasil Langsung (Output)</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-chart-line mc-icon-left" style="top: 14px;"></i>
                                <textarea name="hasil_langsung" rows="3" placeholder="Hasil langsung kegiatan..."
                                    class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('hasil_langsung') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Dampak (Outcome)</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-impact-gradient mc-icon-left" style="top: 14px;"></i>
                                <textarea name="hasil_dampak" rows="3" placeholder="Dampak kegiatan..." class="mc-input"
                                    style="resize: vertical; min-height: 100px;">{{ old('hasil_dampak') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Manfaat Mahasiswa</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-user-graduate mc-icon-left" style="top: 14px;"></i>
                                <textarea name="hasil_manfaat_mahasiswa" rows="2"
                                    placeholder="Manfaat bagi mahasiswa..." class="mc-input"
                                    style="resize: vertical; min-height: 80px;">{{ old('hasil_manfaat_mahasiswa') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Manfaat Polimdo</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-university mc-icon-left" style="top: 14px;"></i>
                                <textarea name="hasil_manfaat_polimdo" rows="2" placeholder="Manfaat bagi Polimdo..."
                                    class="mc-input"
                                    style="resize: vertical; min-height: 80px;">{{ old('hasil_manfaat_polimdo') }}</textarea>
                            </div>
                        </div>
                        <div class="mc-group" style="grid-column: 1 / -1;">
                            <label class="mc-label">Manfaat Mitra</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-handshake-angle mc-icon-left" style="top: 14px;"></i>
                                <textarea name="hasil_manfaat_mitra" rows="2" placeholder="Manfaat bagi mitra..."
                                    class="mc-input"
                                    style="resize: vertical; min-height: 80px;">{{ old('hasil_manfaat_mitra') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mc-footer"
                    style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <a href="{{ route('unit.dkerjasama') }}" class="rfc-btn rfc-btn-danger"
                        style="text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <div style="display: flex; gap: 12px;">
                        <button type="submit" name="action" value="draft" class="rfc-btn"
                            style="background: var(--surface); color: var(--text); border: 1px solid var(--border);">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="rfc-btn rfc-btn-primary">
                            <i class="fas fa-paper-plane"></i> Simpan & Kirim ke Pimpinan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function mitraManager(initial = null) {
        return {
            mitras: initial || [{ id: Date.now(), kategori: '', negara: '' }]
        };
    }

    function countryPicker() {
        return {
            open: false,
            search: '',
            selected: '',
            countries: [
                'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria',
                'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan',
                'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia',
                'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica',
                'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'DR Congo', 'East Timor',
                'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland',
                'France', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea',
                'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq',
                'Ireland', 'Israel', 'Italy', 'Ivory Coast', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati',
                'Kosovo', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein',
                'Lithuania', 'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania',
                'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar',
                'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia',
                'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines',
                'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines',
                'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore',
                'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan',
                'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Togo', 'Tonga',
                'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates',
                'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
            ],
            get filteredCountries() {
                if (!this.search) return this.countries;
                const q = this.search.toLowerCase();
                return this.countries.filter(c => c.toLowerCase().includes(q));
            }
        };
    }
</script>
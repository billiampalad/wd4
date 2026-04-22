<!-- Main Content -->
<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <a href="{{ route('unit.dkerjasama') }}" style="color: inherit; text-decoration: none;">Data Kerjasama</a>
            <span class="sep">/</span>
            <span class="current">Edit Data</span>
        </div>
        <h2 id="pageTitle">Edit Data Kerjasama</h2>
        <p id="pageDesc">Perbarui informasi kegiatan kerjasama.</p>
    </div>

    @if(session('error'))
    <div style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-exclamation-circle" style="font-size: 16px; color: #ef4444;"></i>
        {{ session('error') }}
    </div>
    @endif

    <div style="width: 100%; max-width: 1200px; margin: 0 auto;">
        <div class="uc-form-col" style="flex: 1;">
            <div class="modern-card">
                <div class="mc-header">
                    <div class="mc-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="mc-title">
                        <h3>Edit Data Kerjasama</h3>
                        <p>Pastikan data yang diisi telah valid.</p>
                    </div>
                </div>

                <form action="{{ route('unit.kerjasama.update', $kegiatan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mc-body">
                        @php
                        $firstMitra = $kegiatan->mitras->first();
                        $mitraInit = [
                        'nama_mitra' => $firstMitra->nama_mitra ?? '',
                        'kategori' => $firstMitra->kategori ?? '',
                        'negara' => $firstMitra->negara ?? 'Indonesia'
                        ];
                        @endphp

                        <div id="mitraContainer" x-data="{ kategori: '{{ old('mitra_kategori.0', $mitraInit['kategori']) }}', negara: '{{ old('mitra_negara.0', $mitraInit['negara']) }}' }">
                            <div class="mitra-card" style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 16px; position: relative;">
                                <div class="mc-grid-2">
                                    <div class="mc-group">
                                        <label class="mc-label">Nama Mitra <span class="mc-req">*</span></label>
                                        <div class="mc-input-wrap">
                                            <input type="text" name="mitra_nama[]" value="{{ old('mitra_nama.0', $mitraInit['nama_mitra']) }}" required placeholder="Masukkan nama instansi/mitra" class="mc-input no-icon">
                                        </div>
                                    </div>
                                    {{-- Kategori Mitra (Alpine Dropdown) --}}
                                    <div class="mc-group" x-data="{ open: false }">
                                        <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                                        <input type="hidden" name="mitra_kategori[]" :value="kategori" required>
                                        <div class="alpine-dropdown" @click.outside="open = false">
                                            <div class="ad-trigger no-icon" :class="{'active': open}" @click="open = !open">
                                                <span x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '— Pilih Kategori —')"></span>
                                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                            </div>
                                            <div class="ad-menu" x-show="open" x-transition>
                                                <div class="ad-item" :class="{'selected': kategori === 'nasional'}"
                                                    @click="kategori = 'nasional'; negara = 'Indonesia'; open = false">Nasional</div>
                                                <div class="ad-item" :class="{'selected': kategori === 'internasional'}"
                                                    @click="kategori = 'internasional'; negara = ''; open = false">Internasional</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Negara Dropdown (hanya muncul saat Internasional) --}}
                                <div x-show="kategori === 'internasional'" x-transition>
                                    <div class="mc-group" style="margin-top: 16px;" x-data="countryPicker()" x-init="$watch('kategori', v => { if(v !== 'internasional') selected = 'Indonesia' }); selected = negara">
                                        <label class="mc-label"><i class="fas fa-globe-americas" style="color: var(--accent); margin-right: 6px;"></i>Negara <span class="mc-req">*</span></label>
                                        <input type="hidden" name="mitra_negara[]" :value="selected" required>
                                        <div class="alpine-dropdown" @click.outside="open = false; search = ''">
                                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open; $nextTick(() => { if(open) $refs.countrySearch.focus() })">
                                                <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                    <i class="fas fa-flag" style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                                    <span x-show="!selected" style="color: #9ca3af;">— Pilih Negara —</span>
                                                    <span x-show="selected" x-text="selected" style="font-weight: 500;"></span>
                                                </div>
                                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s; flex-shrink: 0;" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                            </div>
                                            <div class="ad-menu" x-show="open" x-transition style="max-height: 280px; overflow: hidden; display: flex; flex-direction: column;">
                                                {{-- Search Input --}}
                                                <div style="padding: 8px 12px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                                    <div style="display: flex; align-items: center; gap: 8px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px;">
                                                        <i class="fas fa-search" style="font-size: 12px; color: #9ca3af;"></i>
                                                        <input x-ref="countrySearch" x-model="search" type="text" placeholder="Cari negara..."
                                                            style="border: none; outline: none; background: transparent; font-size: 13px; color: var(--text); width: 100%; font-family: inherit;"
                                                            @click.stop>
                                                        <button x-show="search" @click.stop="search = ''" type="button" style="background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; font-size: 11px;">
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
                                <input type="hidden" name="mitra_negara[]" :value="negara" x-show="kategori !== 'internasional'">
                            </div>
                        </div>

                        <div class="mc-grid-2">
                            {{-- Nama Kegiatan --}}
                            <div style="grid-column: 1 / -1;" class="mc-group">
                                <label class="mc-label">Nama Kegiatan <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-file-lines mc-icon-left"></i>
                                    <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required placeholder="Masukkan nama kegiatan"
                                        class="mc-input @error('nama_kegiatan') border-danger @enderror" />
                                </div>
                                @error('nama_kegiatan')
                                <span class="text-danger" style="font-size: 11px; margin-top: 4px;"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Jenis Kerjasama (Alpine Multi-Select) --}}
                            <div class="mc-group" x-data="{ 
                                open: false, 
                                selected: {{ json_encode(old('id_jenis', $kegiatan->jenisKerjasama->pluck('id')->toArray())) }},
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
                                    <div class="ad-trigger" :class="{'active': open}" @click="open = !open">
                                        <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                            <i class="fas fa-handshake" style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                            <span x-show="selected.length === 0" style="color: #9ca3af;">— Pilih Jenis —</span>
                                            <div x-show="selected.length > 0" style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                <template x-for="label in selectedLabels" :key="label">
                                                    <span class="tag tag-purple" style="font-size: 10px; padding: 2px 8px;" x-text="label"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s; flex-shrink: 0;" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>
                                    <div class="ad-menu" x-show="open" x-transition>
                                        <template x-for="item in items" :key="item.id">
                                            <div class="ad-item" :class="{'selected': isSelected(item.id)}"
                                                @click="toggle(item.id)"
                                                style="display: flex; align-items: center; gap: 10px;">
                                                <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                    :style="isSelected(item.id) ? 'background: var(--accent); border-color: var(--accent);' : ''">
                                                    <i class="fas fa-check" style="font-size: 10px; color: #fff;" x-show="isSelected(item.id)"></i>
                                                </div>
                                                <span x-text="item.label"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Penanggung Jawab --}}
                            <div class="mc-group">
                                <label class="mc-label">Penanggung Jawab (Opsional)</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-user-tie mc-icon-left"></i>
                                    <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $kegiatan->penanggung_jawab) }}" placeholder="Nama Koordinator/PJ"
                                        class="mc-input" />
                                </div>
                            </div>

                            {{-- Periode Mulai (Alpine Datepicker) --}}
                            <div class="mc-group" x-data="datepicker('{{ old('periode_mulai', $kegiatan->periode_mulai?->format('Y-m-d')) }}')">
                                <label class="mc-label">Periode Mulai</label>
                                <div class="alpine-datepicker" @click.outside="show = false">
                                    <div class="adp-input-wrap">
                                        <i class="fas fa-calendar-day mc-icon-left"></i>
                                        <input type="text" name="periode_mulai" x-model="formattedDate" readonly @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                                    </div>
                                    <div class="adp-calendar" x-show="show" x-transition>
                                        <div class="adp-header">
                                            <div style="display: flex; gap: 4px;">
                                                <span class="adp-month" @click="toggleMonthPicker()" x-text="monthNames[month]"></span>
                                                <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                            </div>
                                            <div class="adp-nav">
                                                <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                                <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                            </div>
                                        </div>

                                        <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                            <template x-for="(mName, index) in monthNames">
                                                <div class="adp-picker-item" :class="{'selected': month === index}" @click="selectMonth(index)" x-text="mName"></div>
                                            </template>
                                        </div>
                                        <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                            <div style="grid-column: span 4; padding: 4px;">
                                                <input type="text" x-model="yearSearch" placeholder="Cari tahun..." style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);" @click.stop>
                                            </div>
                                            <template x-for="y in filteredYears">
                                                <div class="adp-picker-item" :class="{'selected': year === y}" @click="selectYear(y)" x-text="y"></div>
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
                            <div class="mc-group" x-data="datepicker('{{ old('periode_selesai', $kegiatan->periode_selesai?->format('Y-m-d')) }}')">
                                <label class="mc-label">Periode Selesai</label>
                                <div class="alpine-datepicker" @click.outside="show = false">
                                    <div class="adp-input-wrap">
                                        <i class="fas fa-calendar-check mc-icon-left"></i>
                                        <input type="text" name="periode_selesai" x-model="formattedDate" readonly @click="show = !show" placeholder="Pilih Tanggal" class="adp-input">
                                    </div>
                                    <div class="adp-calendar" x-show="show" x-transition>
                                        <div class="adp-header">
                                            <div style="display: flex; gap: 4px;">
                                                <span class="adp-month" @click="toggleMonthPicker()" x-text="monthNames[month]"></span>
                                                <span class="adp-month" @click="toggleYearPicker()" x-text="year"></span>
                                            </div>
                                            <div class="adp-nav">
                                                <div class="adp-nav-btn" @click="prevMonth()"><i class="fas fa-chevron-left"></i></div>
                                                <div class="adp-nav-btn" @click="nextMonth()"><i class="fas fa-chevron-right"></i></div>
                                            </div>
                                        </div>

                                        <div class="adp-month-picker" x-show="showMonthPicker" x-transition>
                                            <template x-for="(mName, index) in monthNames">
                                                <div class="adp-picker-item" :class="{'selected': month === index}" @click="selectMonth(index)" x-text="mName"></div>
                                            </template>
                                        </div>
                                        <div class="adp-year-picker" x-show="showYearPicker" x-transition>
                                            <div style="grid-column: span 4; padding: 4px;">
                                                <input type="text" x-model="yearSearch" placeholder="Cari tahun..." style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);" @click.stop>
                                            </div>
                                            <template x-for="y in filteredYears">
                                                <div class="adp-picker-item" :class="{'selected': year === y}" @click="selectYear(y)" x-text="y"></div>
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

                            {{-- Dokumen Kerjasama (Alpine Interactive) --}}
                            <div style="grid-column: 1 / -1;" class="mc-group" x-data="{ 
                                open: false, 
                                selected: '{{ old('jenis_dokumen', $kegiatan->jenis_dokumen ?? 'MoU') }}',
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
                                        <div class="ad-trigger" :class="{'active': open}" @click="open = !open" style="height: 48px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.3s;">
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div :style="'width: 32px; height: 32px; border-radius: 8px; background:' + selectedItem.color + '20; color:' + selectedItem.color + '; display: flex; align-items: center; justify-content: center; font-size: 14px;'">
                                                    <i class="fas" :class="selectedItem.icon"></i>
                                                </div>
                                                <div style="display: flex; flex-direction: column; line-height: 1.2;">
                                                    <span x-text="selectedItem.short" style="font-weight: 700; font-size: 13px; color: var(--text);"></span>
                                                    <span x-text="selectedItem.label" style="font-size: 11px; color: var(--text-sub);"></span>
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-down" style="font-size: 11px; color: #9ca3af; transition: 0.3s;" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                                        </div>

                                        <div class="ad-menu" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); z-index: 50; padding: 6px; display: flex; flex-direction: column; gap: 4px;">
                                            <template x-for="item in items" :key="item.id">
                                                <div @click="selected = item.id; open = false" 
                                                     class="ad-item" 
                                                     :class="{'selected': selected === item.id}"
                                                     style="padding: 10px 12px; border-radius: 8px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: all 0.2s;"
                                                     onmouseover="this.style.background='var(--surface2)'" 
                                                     onmouseout="if(!this.classList.contains('selected')) this.style.background='transparent'">
                                                    <div :style="'width: 30px; height: 30px; border-radius: 8px; background:' + item.color + '20; color:' + item.color + '; display: flex; align-items: center; justify-content: center; font-size: 13px;'">
                                                        <i class="fas" :class="item.icon"></i>
                                                    </div>
                                                    <div style="display: flex; flex-direction: column; line-height: 1.2;">
                                                        <span x-text="item.short" style="font-weight: 700; font-size: 13px; color: var(--text);"></span>
                                                        <span x-text="item.label" style="font-size: 11px; color: var(--text-sub);"></span>
                                                    </div>
                                                    <i class="fas fa-check" x-show="selected === item.id" style="margin-left: auto; font-size: 11px; color: var(--accent);"></i>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Right: Number Input --}}
                                    <div class="mc-input-wrap">
                                        <i class="fas fa-hashtag mc-icon-left"></i>
                                        <input type="text" name="nomor_mou" value="{{ old('nomor_mou', $kegiatan->nomor_mou) }}" placeholder="Masukkan nomor dokumen..." class="mc-input" style="height: 48px;" />
                                    </div>
                                </div>
                                @error('jenis_dokumen')
                                    <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- ═══ SECTION 2: Tujuan & Sasaran ═══ --}}
                        <div class="mc-section-title">
                            <span class="mc-section-num">02</span>
                            <span>Tujuan & Sasaran</span>
                        </div>

                        @php $tujuan = $kegiatan->tujuans->first(); @endphp
                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">Tujuan Kegiatan <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-bullseye mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="tujuan" rows="3" required placeholder="Meningkatkan kompetensi praktis mahasiswa..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('tujuan', $tujuan->tujuan ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Sasaran Kegiatan <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-crosshairs mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="sasaran" rows="3" required placeholder="Mahasiswa D3 Teknik Informatika Semester 5..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('sasaran', $tujuan->sasaran ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ SECTION 3: Pelaksanaan ═══ --}}
                        <div class="mc-section-title">
                            <span class="mc-section-num">03</span>
                            <span>Pelaksanaan Kegiatan</span>
                        </div>

                        @php $pelaksanaan = $kegiatan->pelaksanaans->first(); @endphp
                        <div class="mc-grid-2">
                            <div class="mc-group" style="grid-column: 1 / -1;">
                                <label class="mc-label">Deskripsi Pelaksanaan <span class="mc-req">*</span></label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-cogs mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="pelaksanaan_deskripsi" rows="3" required placeholder="Deskripsi pelaksanaan kegiatan..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('pelaksanaan_deskripsi', $pelaksanaan->deskripsi ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Cakupan</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-layer-group mc-icon-left"></i>
                                    <input type="text" name="pelaksanaan_cakupan" value="{{ old('pelaksanaan_cakupan', $pelaksanaan->cakupan ?? '') }}" placeholder="Cakupan kegiatan" class="mc-input" />
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Jumlah Peserta</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-users mc-icon-left"></i>
                                    <input type="number" name="pelaksanaan_peserta" value="{{ old('pelaksanaan_peserta', $pelaksanaan->jumlah_peserta ?? '') }}" placeholder="0" min="0" class="mc-input" />
                                </div>
                            </div>
                            <div class="mc-group" style="grid-column: 1 / -1;">
                                <label class="mc-label">Sumber Daya</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-tools mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="pelaksanaan_sumber_daya" rows="2" placeholder="Sumber daya yang digunakan..." class="mc-input" style="resize: vertical; min-height: 80px;">{{ old('pelaksanaan_sumber_daya', $pelaksanaan->sumber_daya ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ SECTION 4: Hasil & Capaian ═══ --}}
                        <div class="mc-section-title">
                            <span class="mc-section-num">04</span>
                            <span>Hasil & Capaian</span>
                        </div>

                        @php $hasil = $kegiatan->hasils->first(); @endphp
                        <div class="mc-grid-2">
                            <div class="mc-group">
                                <label class="mc-label">Hasil Langsung</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-chart-line mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="hasil_langsung" rows="3" placeholder="Hasil langsung kegiatan..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('hasil_langsung', $hasil->hasil_langsung ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Dampak</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-impact-gradient mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="hasil_dampak" rows="3" placeholder="Dampak kegiatan..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('hasil_dampak', $hasil->dampak ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Manfaat Mahasiswa</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-user-graduate mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="hasil_manfaat_mahasiswa" rows="2" placeholder="Manfaat bagi mahasiswa..." class="mc-input" style="resize: vertical; min-height: 80px;">{{ old('hasil_manfaat_mahasiswa', $hasil->manfaat_mahasiswa ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Manfaat Polimdo</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-university mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="hasil_manfaat_polimdo" rows="2" placeholder="Manfaat bagi Polimdo..." class="mc-input" style="resize: vertical; min-height: 80px;">{{ old('hasil_manfaat_polimdo', $hasil->manfaat_polimdo ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group" style="grid-column: 1 / -1;">
                                <label class="mc-label">Manfaat Mitra</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-handshake-angle mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="hasil_manfaat_mitra" rows="2" placeholder="Manfaat bagi mitra..." class="mc-input" style="resize: vertical; min-height: 80px;">{{ old('hasil_manfaat_mitra', $hasil->manfaat_mitra ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ SECTION 5: Permasalahan & Solusi ═══ --}}
                        <div class="mc-section-title">
                            <span class="mc-section-num">05</span>
                            <span>Permasalahan & Solusi</span>
                        </div>

                        @php $masalah = $kegiatan->permasalahanSolusis->first(); @endphp
                        <div class="mc-grid-1">
                            <div class="mc-group">
                                <label class="mc-label">Kendala / Permasalahan</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-exclamation-triangle mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="masalah_kendala" rows="3" placeholder="Jelaskan kendala atau permasalahan..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('masalah_kendala', $masalah->kendala ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Solusi</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-check-circle mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="masalah_solusi" rows="3" placeholder="Jelaskan solusi yang diterapkan..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('masalah_solusi', $masalah->solusi ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Rekomendasi</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-lightbulb mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="masalah_rekomendasi" rows="3" placeholder="Berikan rekomendasi perbaikan..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('masalah_rekomendasi', $masalah->rekomendasi ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ SECTION 6: Dokumentasi ═══ --}}
                        <div class="mc-section-title">
                            <span class="mc-section-num">06</span>
                            <span>Dokumentasi <span style="font-weight: 400; font-size: 12px; color: var(--text-sub); margin-left: 4px;">(Opsional)</span></span>
                        </div>

                        @php $firstDok = $kegiatan->dokumentasis->first(); @endphp

                        <div class="mc-grid-1">
                            <div class="mc-group">
                                <label class="mc-label">Link Google Drive</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-link mc-icon-left"></i>
                                    <input type="text" name="dok_link_drive" value="{{ old('dok_link_drive', $firstDok->link_drive ?? '') }}" placeholder="https://drive.google.com/..." class="mc-input" />
                                </div>
                            </div>
                            <div class="mc-group">
                                <label class="mc-label">Keterangan</label>
                                <div class="mc-input-wrap">
                                    <i class="fas fa-comment-dots mc-icon-left" style="top: 14px;"></i>
                                    <textarea name="dok_keterangan" rows="3" placeholder="Tambahkan catatan atau keterangan dokumentasi jika diperlukan..." class="mc-input" style="resize: vertical; min-height: 100px;">{{ old('dok_keterangan', $firstDok->keterangan ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mc-footer" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <a href="{{ route('unit.dkerjasama') }}" class="rfc-btn rfc-btn-danger" style="text-decoration: none;">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <div style="display: flex; gap: 12px;">
                            <button type="submit" name="action" value="draft" class="rfc-btn" style="background: var(--surface); color: var(--text); border: 1px solid var(--border);">
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
    </div>
</main>

<script>
    function mitraManager(initial = null) {
        return {
            mitras: initial || [{
                id: Date.now(),
                nama_mitra: '',
                kategori: '',
                negara: ''
            }]
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
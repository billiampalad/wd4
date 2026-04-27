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
                {{-- ═══ TWO-COLUMN TOP LAYOUT: Masa Berlaku (Left) + Form Utama (Right) ═══ --}}
                <div style="display: grid; grid-template-columns: 340px 1fr; gap: 24px; padding: 24px;">

                    {{-- ══ LEFT COLUMN: Masa Berlaku (Sticky) ══ --}}
                    <div style="position: sticky; top: 24px; align-self: start;">
                        <div
                            style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible;">
                            <div
                                x-data="{ showMasaBerlaku: true, statusOpen: false, statusValue: '{{ old('status_kerjasama', '') }}' }">
                                {{-- Card Header --}}
                                <div @click="showMasaBerlaku = !showMasaBerlaku"
                                    style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(16,185,129,0.06), rgba(5,150,105,0.04)); border-radius: 16px 16px 0 0; transition: background 0.2s;">
                                    <div
                                        style="width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 3px 8px rgba(5,150,105,0.25);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: -0.01em;">
                                            Masa Berlaku</h4>
                                    </div>
                                    <i class="fas fa-chevron-down"
                                        style="font-size: 10px; color: var(--text-sub); transition: transform 0.3s ease;"
                                        :style="showMasaBerlaku ? 'transform: rotate(180deg)' : ''"></i>
                                </div>

                                {{-- Card Body --}}
                                <div x-show="showMasaBerlaku" x-collapse.duration.300ms style="padding: 18px;">

                                    {{-- ── Status Kerjasama ── --}}
                                    <div style="margin-bottom: 20px;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                            <div
                                                style="width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg, #059669, #10b981);">
                                            </div>
                                            <span
                                                style="font-weight: 700; font-size: 13px; color: var(--text); letter-spacing: 0.02em;">Status
                                                Kerjasama</span>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Status <span class="mc-req">*</span></label>
                                            <input type="hidden" name="status_kerjasama" :value="statusValue">
                                            <div class="alpine-dropdown" @click.outside="statusOpen = false"
                                                style="position: relative;">
                                                <div class="ad-trigger no-icon" :class="{'active': statusOpen}"
                                                    @click="statusOpen = !statusOpen" style="min-height: 42px;">
                                                    <div
                                                        style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                        <i class="fas fa-signal"
                                                            style="font-size: 13px; flex-shrink: 0;"
                                                            :style="statusValue === 'Aktif' ? 'color: #10b981;' : statusValue === 'Dalam Perpanjangan' ? 'color: #f59e0b;' : statusValue === 'Kadarluarsa' ? 'color: #ef4444;' : statusValue === 'Tidak Aktif' ? 'color: #6b7280;' : 'color: #9ca3af;'"></i>
                                                        <span x-show="!statusValue"
                                                            style="color: #9ca3af; font-size: 13px;">— Pilih Status
                                                            —</span>
                                                        <span x-show="statusValue"
                                                            style="font-size: 13px; font-weight: 500;"
                                                            :style="statusValue === 'Aktif' ? 'color: #10b981;' : statusValue === 'Dalam Perpanjangan' ? 'color: #f59e0b;' : statusValue === 'Kadarluarsa' ? 'color: #ef4444;' : 'color: #6b7280;'"
                                                            x-text="statusValue"></span>
                                                    </div>
                                                    <i class="fas fa-chevron-down"
                                                        style="font-size: 9px; transition: 0.3s; flex-shrink: 0; color: #9ca3af;"
                                                        :style="statusOpen ? 'transform: rotate(180deg)' : ''"></i>
                                                </div>
                                                <div class="ad-menu" x-show="statusOpen" x-transition
                                                    style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 130;">
                                                    <div class="ad-item" :class="{'selected': statusValue === 'Aktif'}"
                                                        @click="statusValue = 'Aktif'; statusOpen = false"
                                                        style="display: flex; align-items: center; gap: 10px; padding: 10px 14px;">
                                                        <div
                                                            style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; flex-shrink: 0;">
                                                        </div>
                                                        <span style="font-size: 13px;">Aktif</span>
                                                    </div>
                                                    <div class="ad-item"
                                                        :class="{'selected': statusValue === 'Dalam Perpanjangan'}"
                                                        @click="statusValue = 'Dalam Perpanjangan'; statusOpen = false"
                                                        style="display: flex; align-items: center; gap: 10px; padding: 10px 14px;">
                                                        <div
                                                            style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; flex-shrink: 0;">
                                                        </div>
                                                        <span style="font-size: 13px;">Dalam Perpanjangan</span>
                                                    </div>
                                                    <div class="ad-item"
                                                        :class="{'selected': statusValue === 'Kadarluarsa'}"
                                                        @click="statusValue = 'Kadarluarsa'; statusOpen = false"
                                                        style="display: flex; align-items: center; gap: 10px; padding: 10px 14px;">
                                                        <div
                                                            style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; flex-shrink: 0;">
                                                        </div>
                                                        <span style="font-size: 13px;">Kadarluarsa</span>
                                                    </div>
                                                    <div class="ad-item"
                                                        :class="{'selected': statusValue === 'Tidak Aktif'}"
                                                        @click="statusValue = 'Tidak Aktif'; statusOpen = false"
                                                        style="display: flex; align-items: center; gap: 10px; padding: 10px 14px;">
                                                        <div
                                                            style="width: 8px; height: 8px; border-radius: 50%; background: #6b7280; flex-shrink: 0;">
                                                        </div>
                                                        <span style="font-size: 13px;">Tidak Aktif</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Divider --}}
                                    <div
                                        style="height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); margin: 20px 0;">
                                    </div>

                                    {{-- ── Periode Kerjasama ── --}}
                                    <div style="margin-bottom: 20px;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                            <div
                                                style="width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg, #4f46e5, #7c3aed);">
                                            </div>
                                            <span
                                                style="font-weight: 700; font-size: 13px; color: var(--text); letter-spacing: 0.02em;">Periode
                                                Kerjasama</span>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                            {{-- Periode Mulai --}}
                                            <div class="mc-group" x-data="datepicker('{{ old('periode_mulai') }}')">
                                                <label class="mc-label">Tanggal Mulai</label>
                                                <div class="alpine-datepicker" @click.outside="show = false">
                                                    <div class="adp-input-wrap">
                                                        <i class="fas fa-calendar-day mc-icon-left"></i>
                                                        <input type="text" name="periode_mulai" x-model="formattedDate"
                                                            readonly @click="show = !show" placeholder="Pilih Tanggal"
                                                            class="adp-input">
                                                    </div>
                                                    <div class="adp-calendar" x-show="show" x-transition>
                                                        <div class="adp-header">
                                                            <div style="display: flex; gap: 4px;">
                                                                <span class="adp-month" @click="toggleMonthPicker()"
                                                                    x-text="monthNames[month]"></span>
                                                                <span class="adp-month" @click="toggleYearPicker()"
                                                                    x-text="year"></span>
                                                            </div>
                                                            <div class="adp-nav">
                                                                <div class="adp-nav-btn" @click="prevMonth()"><i
                                                                        class="fas fa-chevron-left"></i></div>
                                                                <div class="adp-nav-btn" @click="nextMonth()"><i
                                                                        class="fas fa-chevron-right"></i></div>
                                                            </div>
                                                        </div>
                                                        <div class="adp-month-picker" x-show="showMonthPicker"
                                                            x-transition>
                                                            <template x-for="(mName, index) in monthNames">
                                                                <div class="adp-picker-item"
                                                                    :class="{'selected': month === index}"
                                                                    @click="selectMonth(index)" x-text="mName"></div>
                                                            </template>
                                                        </div>
                                                        <div class="adp-year-picker" x-show="showYearPicker"
                                                            x-transition>
                                                            <div style="grid-column: span 4; padding: 4px;">
                                                                <input type="text" x-model="yearSearch"
                                                                    placeholder="Cari tahun..."
                                                                    style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                                                    @click.stop>
                                                            </div>
                                                            <template x-for="y in filteredYears">
                                                                <div class="adp-picker-item"
                                                                    :class="{'selected': year === y}"
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

                                            {{-- Periode Selesai --}}
                                            <div class="mc-group" x-data="datepicker('{{ old('periode_selesai') }}')">
                                                <label class="mc-label">Tanggal Selesai</label>
                                                <div class="alpine-datepicker" @click.outside="show = false">
                                                    <div class="adp-input-wrap">
                                                        <i class="fas fa-calendar-check mc-icon-left"></i>
                                                        <input type="text" name="periode_selesai"
                                                            x-model="formattedDate" readonly @click="show = !show"
                                                            placeholder="Pilih Tanggal" class="adp-input">
                                                    </div>
                                                    <div class="adp-calendar" x-show="show" x-transition>
                                                        <div class="adp-header">
                                                            <div style="display: flex; gap: 4px;">
                                                                <span class="adp-month" @click="toggleMonthPicker()"
                                                                    x-text="monthNames[month]"></span>
                                                                <span class="adp-month" @click="toggleYearPicker()"
                                                                    x-text="year"></span>
                                                            </div>
                                                            <div class="adp-nav">
                                                                <div class="adp-nav-btn" @click="prevMonth()"><i
                                                                        class="fas fa-chevron-left"></i></div>
                                                                <div class="adp-nav-btn" @click="nextMonth()"><i
                                                                        class="fas fa-chevron-right"></i></div>
                                                            </div>
                                                        </div>
                                                        <div class="adp-month-picker" x-show="showMonthPicker"
                                                            x-transition>
                                                            <template x-for="(mName, index) in monthNames">
                                                                <div class="adp-picker-item"
                                                                    :class="{'selected': month === index}"
                                                                    @click="selectMonth(index)" x-text="mName"></div>
                                                            </template>
                                                        </div>
                                                        <div class="adp-year-picker" x-show="showYearPicker"
                                                            x-transition>
                                                            <div style="grid-column: span 4; padding: 4px;">
                                                                <input type="text" x-model="yearSearch"
                                                                    placeholder="Cari tahun..."
                                                                    style="width: 100%; padding: 6px; font-size: 11px; border: 1px solid var(--border); border-radius: 4px; background: var(--surface2); color: var(--text);"
                                                                    @click.stop>
                                                            </div>
                                                            <template x-for="y in filteredYears">
                                                                <div class="adp-picker-item"
                                                                    :class="{'selected': year === y}"
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
                                        </div>
                                    </div>

                                    {{-- Divider --}}
                                    <div
                                        style="height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); margin: 20px 0;">
                                    </div>

                                    {{-- ── Dokumentasi ── --}}
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                            <div
                                                style="width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg, #d97706, #f59e0b);">
                                            </div>
                                            <span
                                                style="font-weight: 700; font-size: 13px; color: var(--text); letter-spacing: 0.02em;">Dokumentasi</span>
                                            <span
                                                style="font-weight: 400; font-size: 11px; color: var(--text-sub); margin-left: 2px;">(Opsional)</span>
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Link Google Drive</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-link mc-icon-left"></i>
                                                <input type="text" name="dok_link_drive"
                                                    value="{{ old('dok_link_drive') }}"
                                                    placeholder="https://drive.google.com/..." class="mc-input" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ══ RIGHT COLUMN: Form Utama ══ --}}
                    <div>
                        <div class="mc-body" style="padding: 0;">
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
                                        <div class="alpine-dropdown" @click.outside="open = false"
                                            style="position: relative;">
                                            <div class="ad-trigger" :class="{'active': open}" @click="open = !open"
                                                style="height: 48px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; background: var(--surface); border: 1.5px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.3s;">
                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                    <div
                                                        :style="'width: 32px; height: 32px; border-radius: 8px; background:' + selectedItem.color + '20; color:' + selectedItem.color + '; display: flex; align-items: center; justify-content: center; font-size: 14px;'">
                                                        <i class="fas" :class="selectedItem.icon"></i>
                                                    </div>
                                                    <div
                                                        style="display: flex; flex-direction: column; line-height: 1.2;">
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
                                                        <div
                                                            style="display: flex; flex-direction: column; line-height: 1.2;">
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
                                        <span class="text-danger"
                                            style="font-size: 11px; margin-top: 4px; display: block;"><i
                                                class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Nama Kegiatan --}}
                                <div style="grid-column: 1 / -1;" class="mc-group">
                                    <label class="mc-label">Judul Kerjasama<span class="mc-req">*</span></label>
                                    <div class="mc-input-wrap">
                                        <i class="fas fa-file-lines mc-icon-left"></i>
                                        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}"
                                            required placeholder="Contoh: Pelatihan Web Development Bersama Industri"
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
                    </div> {{-- End RIGHT COLUMN --}}
                </div> {{-- End TWO-COLUMN TOP LAYOUT --}}

                {{-- ═══ TWO-COLUMN LAYOUT: Penggiat & Bentuk Kegiatan ═══ --}}
                <div style="display: flex; flex-direction: column; gap: 24px; margin-top: 24px; padding: 0 24px;">

                    {{-- ═══ COLUMN 1: Penggiat Kerja Sama ═══ --}}
                    <div
                        style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden;">
                        {{-- Card Header --}}
                        <div x-data="{
                            showPenggiat: true,
                            showPihak1: true,
                            showPihak2: true,
                            showPenandatangan1: true,
                            showPJ1: false,
                            penggiatList: [{ id: Date.now(), showPenandatangan: true, showPJ: false, mitraId: '', mitraOpen: false }],
                            nextId() { return Date.now() + Math.random(); },
                            addPenggiat() {
                                this.penggiatList.push({ id: this.nextId(), showPenandatangan: true, showPJ: false, mitraId: '', mitraOpen: false });
                            },
                            removePenggiat(idx) {
                                if (this.penggiatList.length > 1) this.penggiatList.splice(idx, 1);
                            },
                            mitraItems: [
                                @foreach($mitras as $m)
                                    { id: {{ $m->id }}, nama: '{{ addslashes($m->nama_mitra) }}' },
                                @endforeach
                            ]
                        }">
                            <div @click="showPenggiat = !showPenggiat"
                                style="display: flex; align-items: center; gap: 14px; padding: 20px 24px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.04), rgba(5,150,105,0.04));">
                                <div
                                    style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #059669); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text);">
                                        Penggiat Kerja Sama</h4>
                                    <p style="margin: 2px 0 0; font-size: 12px; color: var(--text-sub);">Data
                                        pihak-pihak yang terlibat dalam kerja sama</p>
                                </div>
                                <i class="fas fa-chevron-down"
                                    style="font-size: 12px; color: var(--text-sub); transition: transform 0.3s ease;"
                                    :style="showPenggiat ? 'transform: rotate(180deg)' : ''"></i>
                            </div>

                            {{-- Card Body --}}
                            <div x-show="showPenggiat" x-collapse.duration.300ms style="padding: 20px 24px;">

                                {{-- ══ Pihak Ke-1 ══ --}}
                                <div
                                    style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 16px; overflow: hidden;">
                                    {{-- Pihak 1 Header --}}
                                    <div @click="showPihak1 = !showPihak1"
                                        style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                        onmouseover="this.style.background='var(--surface)'"
                                        onmouseout="this.style.background='transparent'">
                                        <div
                                            style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.12); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <span style="font-weight: 700; font-size: 13px; color: var(--text);">Pihak
                                                Ke-1</span>
                                            <span
                                                style="font-size: 11px; color: var(--text-sub); margin-left: 8px;">Instansi
                                                Penyelenggara</span>
                                        </div>
                                        <i class="fas fa-chevron-down"
                                            style="font-size: 11px; color: #9ca3af; transition: transform 0.3s ease;"
                                            :style="showPihak1 ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>

                                    {{-- Pihak 1 Content --}}
                                    <div x-show="showPihak1" x-collapse.duration.300ms
                                        style="padding: 0 20px 20px 20px;">
                                        {{-- Nama Instansi (full width) --}}
                                        <div class="mc-group">
                                            <label class="mc-label">Nama Pelaksana<span class="mc-req">*</span></label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-building mc-icon-left"></i>
                                                <input type="text" name="nama_instansi"
                                                    value="{{ old('nama_instansi') }}"
                                                    placeholder="Masukkan nama pelaksana" class="mc-input" required />
                                            </div>
                                        </div>

                                        {{-- Penandatangan (Collapsible) --}}
                                        <div
                                            style="margin-top: 14px; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: var(--surface);">
                                            <div @click="showPenandatangan1 = !showPenandatangan1"
                                                style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                                onmouseover="this.style.background='var(--surface2)'"
                                                onmouseout="this.style.background='var(--surface)'">
                                                <i class="fas fa-pen-nib" style="font-size: 12px; color: #4f46e5;"></i>
                                                <div style="flex: 1;">
                                                    <span
                                                        style="font-weight: 600; font-size: 12px; color: var(--text);">Penandatangan</span>
                                                    <span
                                                        style="font-size: 10px; color: var(--text-sub); margin-left: 6px;">Pejabat
                                                        yang menandatangani dokumen</span>
                                                </div>
                                                <i class="fas fa-chevron-down"
                                                    style="font-size: 9px; color: #9ca3af; transition: transform 0.3s;"
                                                    :style="showPenandatangan1 ? 'transform: rotate(180deg)' : ''"></i>
                                            </div>
                                            <div x-show="showPenandatangan1" x-collapse.duration.200ms
                                                style="padding: 0 14px 14px 14px;">
                                                <div class="mc-grid-2">
                                                    <div class="mc-group">
                                                        <label class="mc-label">Nama</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-user mc-icon-left"></i>
                                                            <input type="text" name="nama_penandatangan"
                                                                value="{{ old('nama_penandatangan') }}"
                                                                placeholder="Nama penandatangan" class="mc-input" />
                                                        </div>
                                                    </div>
                                                    <div class="mc-group">
                                                        <label class="mc-label">Jabatan</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-id-badge mc-icon-left"></i>
                                                            <input type="text" name="jabatan_penandatangan"
                                                                value="{{ old('jabatan_penandatangan') }}"
                                                                placeholder="Jabatan penandatangan" class="mc-input" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Penanggung Jawab (Collapsible) --}}
                                        <div
                                            style="margin-top: 10px; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: var(--surface);">
                                            <div @click="showPJ1 = !showPJ1"
                                                style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                                onmouseover="this.style.background='var(--surface2)'"
                                                onmouseout="this.style.background='var(--surface)'">
                                                <i class="fas fa-user-tie" style="font-size: 12px; color: #059669;"></i>
                                                <div style="flex: 1;">
                                                    <span
                                                        style="font-weight: 600; font-size: 12px; color: var(--text);">Penanggung
                                                        Jawab</span>
                                                    <span
                                                        style="font-size: 10px; color: var(--text-sub); margin-left: 6px;">Jika
                                                        ada</span>
                                                </div>
                                                <i class="fas fa-chevron-down"
                                                    style="font-size: 9px; color: #9ca3af; transition: transform 0.3s;"
                                                    :style="showPJ1 ? 'transform: rotate(180deg)' : ''"></i>
                                            </div>
                                            <div x-show="showPJ1" x-collapse.duration.200ms
                                                style="padding: 0 14px 14px 14px;">
                                                <div class="mc-grid-2">
                                                    <div class="mc-group">
                                                        <label class="mc-label">Nama</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-user mc-icon-left"></i>
                                                            <input type="text" name="nama_penanggung_jawab"
                                                                value="{{ old('nama_penanggung_jawab') }}"
                                                                placeholder="Nama penanggung jawab" class="mc-input" />
                                                        </div>
                                                    </div>
                                                    <div class="mc-group">
                                                        <label class="mc-label">Jabatan</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-id-badge mc-icon-left"></i>
                                                            <input type="text" name="jabatan_penanggung_jawab"
                                                                value="{{ old('jabatan_penanggung_jawab') }}"
                                                                placeholder="Jabatan penanggung jawab"
                                                                class="mc-input" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- ══ Pihak Ke-2 (Dynamic Penggiat) ══ --}}
                                <div
                                    style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; overflow: visible;">
                                    {{-- Pihak 2 Header --}}
                                    <div @click="showPihak2 = !showPihak2"
                                        style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                        onmouseover="this.style.background='var(--surface)'"
                                        onmouseout="this.style.background='transparent'">
                                        <div
                                            style="width: 32px; height: 32px; border-radius: 8px; background: rgba(5,150,105,0.12); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                            <i class="fas fa-handshake"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <span style="font-weight: 700; font-size: 13px; color: var(--text);">Pihak
                                                Ke-2</span>
                                            <span
                                                style="font-size: 11px; color: var(--text-sub); margin-left: 8px;">Mitra
                                                Kerja Sama</span>
                                        </div>
                                        <span x-show="penggiatList.length > 0"
                                            style="background: var(--accent); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; margin-right: 8px;"
                                            x-text="penggiatList.length"></span>
                                        <i class="fas fa-chevron-down"
                                            style="font-size: 11px; color: #9ca3af; transition: transform 0.3s ease;"
                                            :style="showPihak2 ? 'transform: rotate(180deg)' : ''"></i>
                                    </div>

                                    {{-- Pihak 2 Content --}}
                                    <div x-show="showPihak2" x-collapse.duration.300ms
                                        style="padding: 0 20px 20px 20px;">

                                        {{-- Dynamic Penggiat Entries --}}
                                        <template x-for="(pg, idx) in penggiatList" :key="pg.id">
                                            <div style="border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-bottom: 12px; background: var(--surface); position: relative;"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                x-transition:enter-end="opacity-100 transform translate-y-0">

                                                {{-- Penggiat Number Badge + Remove --}}
                                                <div
                                                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                        <span
                                                            style="background: linear-gradient(135deg, #059669, #10b981); color: #fff; font-size: 10px; font-weight: 700; width: 22px; height: 22px; border-radius: 6px; display: flex; align-items: center; justify-content: center;"
                                                            x-text="idx + 1"></span>
                                                        <span
                                                            style="font-weight: 600; font-size: 12px; color: var(--text);">Penggiat
                                                            <span x-text="idx + 1"></span></span>
                                                    </div>
                                                    <button type="button" x-show="penggiatList.length > 1"
                                                        @click="removePenggiat(idx)"
                                                        style="background: rgba(239,68,68,0.1); border: none; color: #ef4444; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; transition: all 0.2s;"
                                                        onmouseover="this.style.background='rgba(239,68,68,0.2)'"
                                                        onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>

                                                {{-- Nama Mitra (Dropdown from DB + Add New Link) --}}
                                                <div class="mc-group" style="margin-bottom: 10px;">
                                                    <label class="mc-label">Nama Mitra <span
                                                            class="mc-req">*</span></label>
                                                    <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                        <div style="flex: 1; position: relative;"
                                                            class="alpine-dropdown"
                                                            @click.outside="pg.mitraOpen = false">
                                                            <input type="hidden" name="mitra_nama[]"
                                                                :value="pg.mitraId ? mitraItems.find(m => m.id == pg.mitraId)?.nama : ''">
                                                            <div class="ad-trigger no-icon"
                                                                :class="{'active': pg.mitraOpen}"
                                                                @click="pg.mitraOpen = !pg.mitraOpen"
                                                                style="min-height: 40px;">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                                    <i class="fas fa-building"
                                                                        style="color: #9ca3af; font-size: 12px; flex-shrink: 0;"></i>
                                                                    <span x-show="!pg.mitraId"
                                                                        style="color: #9ca3af; font-size: 12px;">— Pilih
                                                                        Mitra —</span>
                                                                    <span x-show="pg.mitraId"
                                                                        style="font-size: 12px; color: var(--text);"
                                                                        x-text="mitraItems.find(m => m.id == pg.mitraId)?.nama || ''"></span>
                                                                </div>
                                                                <i class="fas fa-chevron-down"
                                                                    style="font-size: 9px; transition: 0.3s; flex-shrink: 0; color: #9ca3af;"
                                                                    :style="pg.mitraOpen ? 'transform: rotate(180deg)' : ''"></i>
                                                            </div>
                                                            <div class="ad-menu" x-show="pg.mitraOpen" x-transition
                                                                style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 130; max-height: 180px; overflow-y: auto;">
                                                                <template x-for="mitra in mitraItems" :key="mitra.id">
                                                                    <div class="ad-item"
                                                                        :class="{'selected': pg.mitraId == mitra.id}"
                                                                        @click="pg.mitraId = mitra.id; pg.mitraOpen = false"
                                                                        style="font-size: 12px; padding: 8px 12px;">
                                                                        <span x-text="mitra.nama"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        {{-- Add New Mitra Button --}}
                                                        <a href="{{ route('unit.mitra.create') }}"
                                                            style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 8px rgba(5,150,105,0.3);"
                                                            onmouseover="this.style.transform='scale(1.08)'"
                                                            onmouseout="this.style.transform='scale(1)'"
                                                            title="Tambah Mitra Baru">
                                                            <i class="fas fa-plus"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                {{-- Penandatangan (Collapsible) --}}
                                                <div
                                                    style="margin-top: 10px; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: var(--surface2);">
                                                    <div @click="pg.showPenandatangan = !pg.showPenandatangan"
                                                        style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                                        onmouseover="this.style.background='var(--surface)'"
                                                        onmouseout="this.style.background='var(--surface2)'">
                                                        <i class="fas fa-pen-nib"
                                                            style="font-size: 12px; color: #4f46e5;"></i>
                                                        <div style="flex: 1;">
                                                            <span
                                                                style="font-weight: 600; font-size: 12px; color: var(--text);">Penandatangan</span>
                                                            <span
                                                                style="font-size: 10px; color: var(--text-sub); margin-left: 6px;">Pejabat
                                                                yang menandatangani dokumen</span>
                                                        </div>
                                                        <i class="fas fa-chevron-down"
                                                            style="font-size: 9px; color: #9ca3af; transition: transform 0.3s;"
                                                            :style="pg.showPenandatangan ? 'transform: rotate(180deg)' : ''"></i>
                                                    </div>
                                                    <div x-show="pg.showPenandatangan" x-collapse.duration.200ms
                                                        style="padding: 0 14px 14px 14px;">
                                                        <div class="mc-grid-2">
                                                            <div class="mc-group">
                                                                <label class="mc-label">Nama</label>
                                                                <div class="mc-input-wrap">
                                                                    <i class="fas fa-user mc-icon-left"></i>
                                                                    <input type="text"
                                                                        :name="'penggiat[' + idx + '][nama_penandatangan]'"
                                                                        placeholder="Nama penandatangan"
                                                                        class="mc-input" />
                                                                </div>
                                                            </div>
                                                            <div class="mc-group">
                                                                <label class="mc-label">Jabatan</label>
                                                                <div class="mc-input-wrap">
                                                                    <i class="fas fa-id-badge mc-icon-left"></i>
                                                                    <input type="text"
                                                                        :name="'penggiat[' + idx + '][jabatan_penandatangan]'"
                                                                        placeholder="Jabatan penandatangan"
                                                                        class="mc-input" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Penanggung Jawab (Collapsible) --}}
                                                <div
                                                    style="margin-top: 8px; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: var(--surface2);">
                                                    <div @click="pg.showPJ = !pg.showPJ"
                                                        style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; cursor: pointer; user-select: none; transition: background 0.2s;"
                                                        onmouseover="this.style.background='var(--surface)'"
                                                        onmouseout="this.style.background='var(--surface2)'">
                                                        <i class="fas fa-user-tie"
                                                            style="font-size: 12px; color: #059669;"></i>
                                                        <div style="flex: 1;">
                                                            <span
                                                                style="font-weight: 600; font-size: 12px; color: var(--text);">Penanggung
                                                                Jawab</span>
                                                            <span
                                                                style="font-size: 10px; color: var(--text-sub); margin-left: 6px;">Jika
                                                                ada</span>
                                                        </div>
                                                        <i class="fas fa-chevron-down"
                                                            style="font-size: 9px; color: #9ca3af; transition: transform 0.3s;"
                                                            :style="pg.showPJ ? 'transform: rotate(180deg)' : ''"></i>
                                                    </div>
                                                    <div x-show="pg.showPJ" x-collapse.duration.200ms
                                                        style="padding: 0 14px 14px 14px;">
                                                        <div class="mc-grid-2">
                                                            <div class="mc-group">
                                                                <label class="mc-label">Nama</label>
                                                                <div class="mc-input-wrap">
                                                                    <i class="fas fa-user mc-icon-left"></i>
                                                                    <input type="text"
                                                                        :name="'penggiat[' + idx + '][nama_pj]'"
                                                                        placeholder="Nama penanggung jawab"
                                                                        class="mc-input" />
                                                                </div>
                                                            </div>
                                                            <div class="mc-group">
                                                                <label class="mc-label">Jabatan</label>
                                                                <div class="mc-input-wrap">
                                                                    <i class="fas fa-id-badge mc-icon-left"></i>
                                                                    <input type="text"
                                                                        :name="'penggiat[' + idx + '][jabatan_pj]'"
                                                                        placeholder="Jabatan penanggung jawab"
                                                                        class="mc-input" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Tambah Penggiat Button --}}
                                        <button type="button" @click="addPenggiat()"
                                            style="width: 100%; padding: 12px; border: 2px dashed var(--border); border-radius: 10px; background: transparent; color: var(--accent); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;"
                                            onmouseover="this.style.borderColor='var(--accent)'; this.style.background='rgba(79,70,229,0.04)'"
                                            onmouseout="this.style.borderColor='var(--border)'; this.style.background='transparent'">
                                            <i class="fas fa-plus-circle"></i>
                                            Tambah Penggiat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('mitra_nama')
                            <span class="text-danger" style="margin: 12px 24px; display: block; font-size: 11px;"><i
                                    class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ═══ COLUMN 2: Bentuk Kegiatan ═══ --}}
                    <div
                        style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible;">
                        <div x-data="{ showBentuk: true }">
                            {{-- Card Header --}}
                            <div @click="showBentuk = !showBentuk"
                                style="display: flex; align-items: center; gap: 14px; padding: 20px 24px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(217,119,6,0.04), rgba(245,158,11,0.04)); border-radius: 16px 16px 0 0;">
                                <div
                                    style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text);">Bentuk
                                        Kegiatan</h4>
                                    <p style="margin: 2px 0 0; font-size: 12px; color: var(--text-sub);">Pilih jenis
                                        kerjasama yang sesuai</p>
                                </div>
                                <i class="fas fa-chevron-down"
                                    style="font-size: 12px; color: var(--text-sub); transition: transform 0.3s ease;"
                                    :style="showBentuk ? 'transform: rotate(180deg)' : ''"></i>
                            </div>

                            {{-- Card Body --}}
                            <div x-show="showBentuk" x-collapse.duration.300ms style="padding: 20px 24px;">

                                {{-- Jenis Kerjasama (Alpine Multi-Select with Dynamic Forms) --}}
                                <div x-data="{ 
                                    open: false, 
                                    selected: {{ json_encode(old('id_jenis', [])) }},
                                    items: [
                                        @foreach($jenisKerjasama as $jenis)
                                            { id: {{ $jenis->id }}, label: '{{ $jenis->nama_kerjasama }}' },
                                        @endforeach
                                    ],
                                    formData: {},
                                    sasaranOpen: {},
                                    sasaranOptions: [
                                        'Meningkatnya Kualitas Lulusan Perguruan Tinggi',
                                        'Meningkatnya Inovasi Perguruan Tinggi Dalam Rangka Meningkatkan Mutu Pendidikan',
                                        'Meningkatnya Kualitas Dosen Pendidikan Tinggi',
                                        'Meningkatkan Kualitas Kurikulum dan Pembelajaran',
                                        'Meningkatnya Program Studi yang Berkualitas'
                                    ],
                                    toggle(id) {
                                        const idx = this.selected.indexOf(id);
                                        if (idx > -1) {
                                            this.selected.splice(idx, 1);
                                            delete this.formData[id];
                                            delete this.sasaranOpen[id];
                                        } else {
                                            this.selected.push(id);
                                            this.formData[id] = { nilai_kontrak: '', volume: '', satuan_volume: '', keterangan: '', sasaran: '', indikator_kinerja: '' };
                                            this.sasaranOpen[id] = false;
                                        }
                                    },
                                    isSelected(id) { return this.selected.includes(id); },
                                    getLabel(id) { const item = this.items.find(i => i.id === id); return item ? item.label : ''; },
                                    get selectedLabels() {
                                        return this.items.filter(i => this.selected.includes(i.id)).map(i => i.label);
                                    },
                                    init() {
                                        this.selected.forEach(id => {
                                            if (!this.formData[id]) {
                                                this.formData[id] = { nilai_kontrak: '', volume: '', satuan_volume: '', keterangan: '', sasaran: '', indikator_kinerja: '' };
                                                this.sasaranOpen[id] = false;
                                            }
                                        });
                                    }
                                }">
                                    {{-- Dropdown Selector --}}
                                    <div class="mc-group">
                                        <label class="mc-label">Jenis Kerjasama <span class="mc-req">*</span></label>
                                        <template x-for="id in selected" :key="id">
                                            <input type="hidden" name="id_jenis[]" :value="id">
                                        </template>
                                        <div class="alpine-dropdown" @click.outside="open = false">
                                            <div class="ad-trigger no-icon" :class="{'active': open}"
                                                @click="open = !open">
                                                <div
                                                    style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                                    <i class="fas fa-handshake"
                                                        style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                                    <span x-show="selected.length === 0" style="color: #9ca3af;">— Pilih
                                                        Jenis
                                                        —</span>
                                                    <div x-show="selected.length > 0"
                                                        style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                        <template x-for="label in selectedLabels" :key="label">
                                                            <span class="tag tag-purple"
                                                                style="font-size: 10px; padding: 2px 8px;"
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
                                                        @click="toggle(item.id); open = false"
                                                        style="display: flex; align-items: center; gap: 10px;">
                                                        <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                            :style="isSelected(item.id) ? 'background: var(--accent); border-color: var(--accent);' : ''">
                                                            <i class="fas fa-check"
                                                                style="font-size: 10px; color: #fff;"
                                                                x-show="isSelected(item.id)"></i>
                                                        </div>
                                                        <span x-text="item.label"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Dynamic Sub-Forms per Selected Item --}}
                                    <template x-for="id in selected" :key="'form-' + id">
                                        <div x-show="isSelected(id)"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                                            x-transition:enter-end="opacity-100 transform translate-y-0"
                                            style="margin-top: 16px; background: var(--surface2); border: 1px solid var(--border); border-radius: 14px; overflow: visible;">

                                            {{-- Sub-Form Header --}}
                                            <div
                                                style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.05), rgba(124,58,237,0.03)); border-radius: 14px 14px 0 0;">
                                                <div
                                                    style="width: 28px; height: 28px; border-radius: 8px; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <div style="flex: 1; min-width: 0;">
                                                    <span style="font-weight: 700; font-size: 13px; color: var(--text);"
                                                        x-text="getLabel(id)"></span>
                                                    <span
                                                        style="font-size: 11px; color: var(--text-sub); display: block; margin-top: 1px;">Detail
                                                        kegiatan kerjasama</span>
                                                </div>
                                            </div>

                                            {{-- Sub-Form Body --}}
                                            <div style="padding: 18px;">
                                                {{-- Row 1: Nilai Kontrak --}}
                                                <div class="mc-group" style="margin-bottom: 14px;">
                                                    <label class="mc-label">Nilai Kontrak <span
                                                            style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Nominal
                                                            nilai kontrak proposal)</span></label>
                                                    <div class="mc-input-wrap">
                                                        <i class="fas fa-money-bill-wave mc-icon-left"></i>
                                                        <input type="text"
                                                            :name="'jenis_detail[' + id + '][nilai_kontrak]'"
                                                            x-model="formData[id].nilai_kontrak" placeholder="Rp 0"
                                                            class="mc-input" @input="
                                                            let v = $event.target.value.replace(/[^0-9]/g, '');
                                                            formData[id].nilai_kontrak = v ? 'Rp ' + Number(v).toLocaleString('id-ID') : '';
                                                        " />
                                                    </div>
                                                </div>

                                                {{-- Row 2: Luaran (Volume + Satuan Volume) --}}
                                                <div style="margin-bottom: 14px;">
                                                    <label class="mc-label"
                                                        style="margin-bottom: 8px; display: block;">Luaran</label>
                                                    <div
                                                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                                        <div class="mc-group">
                                                            <div class="mc-input-wrap">
                                                                <i class="fas fa-chart-bar mc-icon-left"></i>
                                                                <input type="text"
                                                                    :name="'jenis_detail[' + id + '][volume]'"
                                                                    x-model="formData[id].volume" placeholder="Volume"
                                                                    class="mc-input" />
                                                            </div>
                                                        </div>
                                                        <div class="mc-group">
                                                            <div class="mc-input-wrap">
                                                                <i class="fas fa-at mc-icon-left"></i>
                                                                <input type="text"
                                                                    :name="'jenis_detail[' + id + '][satuan_volume]'"
                                                                    x-model="formData[id].satuan_volume"
                                                                    placeholder="Satuan Volume" class="mc-input" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 3: Keterangan --}}
                                                <div class="mc-group" style="margin-bottom: 14px;">
                                                    <label class="mc-label">Keterangan <span
                                                            style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Ringkasan
                                                            luaran dari kegiatan)</span></label>
                                                    <div class="mc-input-wrap">
                                                        <i class="fas fa-align-left mc-icon-left"
                                                            style="top: 14px;"></i>
                                                        <textarea :name="'jenis_detail[' + id + '][keterangan]'"
                                                            x-model="formData[id].keterangan" rows="2"
                                                            placeholder="Jelaskan ringkasan luaran kegiatan..."
                                                            class="mc-input"
                                                            style="resize: vertical; min-height: 70px;"></textarea>
                                                    </div>
                                                </div>

                                                {{-- Row 4: Sasaran (Custom Dropdown) + Indikator Kinerja --}}
                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                                    {{-- Sasaran Dropdown --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Sasaran</label>
                                                        <div class="alpine-dropdown"
                                                            @click.outside="sasaranOpen[id] = false"
                                                            style="position: relative;">
                                                            <input type="hidden"
                                                                :name="'jenis_detail[' + id + '][sasaran]'"
                                                                x-model="formData[id].sasaran">
                                                            <div class="ad-trigger no-icon"
                                                                :class="{'active': sasaranOpen[id]}"
                                                                @click="sasaranOpen[id] = !sasaranOpen[id]">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                                    <i class="fas fa-crosshairs"
                                                                        style="color: #9ca3af; font-size: 12px; flex-shrink: 0;"></i>
                                                                    <span x-show="!formData[id].sasaran"
                                                                        style="color: #9ca3af; font-size: 12px;">— Pilih
                                                                        Sasaran —</span>
                                                                    <span x-show="formData[id].sasaran"
                                                                        style="font-size: 12px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                                        x-text="formData[id].sasaran"></span>
                                                                </div>
                                                                <i class="fas fa-chevron-down"
                                                                    style="font-size: 9px; transition: 0.3s; flex-shrink: 0; color: #9ca3af;"
                                                                    :style="sasaranOpen[id] ? 'transform: rotate(180deg)' : ''"></i>
                                                            </div>
                                                            <div class="ad-menu" x-show="sasaranOpen[id]" x-transition
                                                                style="position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 120; max-height: 200px; overflow-y: auto;">
                                                                <template x-for="(opt, oi) in sasaranOptions" :key="oi">
                                                                    <div class="ad-item"
                                                                        :class="{'selected': formData[id].sasaran === opt}"
                                                                        @click="formData[id].sasaran = opt; sasaranOpen[id] = false"
                                                                        style="font-size: 12px; padding: 8px 12px;">
                                                                        <span x-text="opt"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Indikator Kinerja --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Indikator Kinerja</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-tachometer-alt mc-icon-left"></i>
                                                            <input type="text"
                                                                :name="'jenis_detail[' + id + '][indikator_kinerja]'"
                                                                x-model="formData[id].indikator_kinerja"
                                                                placeholder="Indikator pencapaian..."
                                                                class="mc-input" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> {{-- End Stacked Layout --}}

                {{-- Continue mc-body for remaining sections --}}
                <div class="mc-body">

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
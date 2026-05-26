@php
    $pksNumberInputs = collect((array) old('pks_numbers', $kegiatan->pksNumbers->pluck('number')->all()))
        ->map(fn ($number) => trim((string) $number))
        ->values()
        ->all();
    $pksNumberInputs = !empty($pksNumberInputs) ? $pksNumberInputs : [''];
    $allowedTipePelaksana = $allowedTipePelaksana ?? null;
    $allPelaksanaOptions = [
        'jurusan' => ['v' => 'jurusan', 'icon' => 'fas fa-microchip', 'label' => 'Jurusan', 'color' => '#4f46e5'],
        'upa' => ['v' => 'upa', 'icon' => 'fas fa-building-columns', 'label' => 'UPA', 'color' => '#0891b2'],
        'pusat' => ['v' => 'pusat', 'icon' => 'fas fa-landmark', 'label' => 'Pusat', 'color' => '#7c3aed'],
    ];
    $pelaksanaOptions = $allowedTipePelaksana && isset($allPelaksanaOptions[$allowedTipePelaksana])
        ? [$allPelaksanaOptions[$allowedTipePelaksana]]
        : array_values($allPelaksanaOptions);
@endphp

<!-- Main Content -->
<main id="mainContent" class="dk-page">
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('pusat.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('pusat.dashboard') }}" style="text-decoration: none; color: inherit;">
                    <span class="current">Beranda</span>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('pusat.dkerjasama') }}" style="text-decoration: none; color: inherit;">
                    <span class="current">Daftar Kerjasama</span>
                </a>
                <span class="sep">/</span>
                <span class="current">Edit Data</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <span class="dk-eyebrow">Repositori Unit</span>
                    <h2 id="pageTitle">Edit Data Kerjasama</h2>
                    <p id="pageDesc">Perbarui informasi kegiatan kerjasama.</p>
                </div>
            </div>
        </div>
    </section>

    @if(session('error'))
    <div class="dk-alert dk-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="card um-card dk-card" style="overflow: visible;">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-edit"></i></span>
                <span>
                    <strong>Edit Data Kerjasama</strong>
                    <small>Pastikan data yang diisi telah valid.</small>
                </span>
            </div>
        </div>

        <div class="card-body dk-card-body" style="padding: 0;">
            <form action="{{ route('pusat.kerjasama.update', $kegiatan->id) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- ═══ TWO-COLUMN TOP LAYOUT: Masa Berlaku (Left) + Form Utama (Right) ═══ --}}
                <div style="display: grid; grid-template-columns: 340px 1fr; gap: 24px; padding: 24px;">

                    {{-- ══ LEFT COLUMN: Masa Berlaku (Sticky) ══ --}}
                    <div style="position: sticky; top: 24px; align-self: start;">
                        <div
                            style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible;">
                            <div
                                x-data="{ 
                                    showMasaBerlaku: true, 
                                    statusOpen: false, 
                                    statusValue: '{{ 
                                        match(strtolower(old('status', $kegiatan->status ?? ''))) {
                                            'aktif' => 'Aktif',
                                            'dalam perpanjangan' => 'Dalam Perpanjangan',
                                            'kadarluarsa' => 'Kadarluarsa',
                                            'tidak aktif' => 'Tidak Aktif',
                                            default => old('status', $kegiatan->status ?? '')
                                        }
                                    }}' 
                                }">
                                {{-- Card Header --}}
                                <div @click="showMasaBerlaku = !showMasaBerlaku"
                                    style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(16,185,129,0.06), rgba(5,150,105,0.04)); border-radius: 16px 16px 0 0; transition: background 0.2s;">
                                    <div
                                        style="width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 3px 8px rgba(5,150,105,0.25);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4
                                            style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: -0.01em;">
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
                                            <input type="hidden" name="status" :value="statusValue">
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
                                            <div class="mc-group"
                                                x-data="datepicker('{{ old('start_date', $kegiatan->start_date ? $kegiatan->start_date->format('Y-m-d') : '') }}')">
                                                <label class="mc-label">Tanggal Mulai</label>
                                                <div class="alpine-datepicker" @click.outside="show = false">
                                                    <div class="adp-input-wrap">
                                                        <i class="fas fa-calendar-day mc-icon-left"></i>
                                                        <input type="text" name="start_date" x-model="formattedDate"
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
                                            <div class="mc-group"
                                                x-data="datepicker('{{ old('end_date', $kegiatan->end_date ? $kegiatan->end_date->format('Y-m-d') : '') }}')">
                                                <label class="mc-label">Tanggal Selesai</label>
                                                <div class="alpine-datepicker" @click.outside="show = false">
                                                    <div class="adp-input-wrap">
                                                        <i class="fas fa-calendar-check mc-icon-left"></i>
                                                        <input type="text" name="end_date" x-model="formattedDate"
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
                                        </div>
                                        <div class="mc-group">
                                            <label class="mc-label">Link Google Drive</label>
                                            <div class="mc-input-wrap">
                                                <i class="fas fa-link mc-icon-left"></i>
                                                <input type="text" name="document_link"
                                                    value="{{ old('document_link', $kegiatan->document_link ?? '') }}"
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
                            selected: '{{ old('jenis', $kegiatan->jenis ?? 'MoU (Memorandum of Understanding)') }}',
                            items: [
                                { id: 'MoU (Memorandum of Understanding)', label: 'Memorandum of Understanding', short: 'MoU', icon: 'fa-file-signature', color: '#4f46e5' },
                                { id: 'MoA (Memorandum of Agreement)', label: 'Memorandum of Agreement', short: 'MoA', icon: 'fa-file-contract', color: '#059669' },
                                { id: 'IA (Implementation Agreement)', label: 'Implementation Agreement', short: 'IA', icon: 'fa-file-invoice', color: '#d97706' }
                            ],
                            get selectedItem() {
                                return this.items.find(i => i.id === this.selected);
                            },
                            selectType(id) {
                                                this.selected = id;
                                                this.$dispatch('jenis-dokumen-changed', { value: id });
                                                // Reset tipe pelaksana jika pindah ke MoU
                                                if (id.includes('MoU')) {
                                                    window.dispatchEvent(new CustomEvent('reset-tipe-pelaksana'));
                                                }
                                            }
                        }" x-init="$dispatch('jenis-dokumen-changed', { value: selected })">
                                    <label class="mc-label">Dokumen Kerjasama <span class="mc-req">*</span></label>
                                    <input type="hidden" name="jenis" :value="selected">

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
                                                    <div @click="selectType(item.id); open = false" class="ad-item"
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
                                            <input type="text" name="doc_number"
                                                value="{{ old('doc_number', $kegiatan->doc_number ?? '') }}"
                                                placeholder="Masukkan nomor dokumen..." class="mc-input"
                                                style="height: 48px;" />
                                        </div>
                                    </div>

                                    {{-- Nomor PKS --}}
                                    <div style="margin-top: 12px;" x-data="pksNumberFields(@js($pksNumberInputs))">
                                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <label class="mc-label" style="margin: 0;">Nomor PKS</label>
                                            <button type="button" class="rfc-btn rfc-btn-primary" @click="add()"
                                                style="padding: 8px 12px; font-size: 12px;">
                                                <i class="fas fa-plus"></i> Tambah PKS
                                            </button>
                                        </div>
                                        <template x-for="(number, index) in numbers" :key="index">
                                            <div class="mc-input-wrap" style="margin-top: 8px;">
                                                <i class="fas fa-file-contract mc-icon-left"></i>
                                                <input type="text" name="pks_numbers[]" x-model="numbers[index]"
                                                    placeholder="Masukkan nomor PKS..." class="mc-input @if($errors->has('pks_numbers.*')) is-invalid @endif"
                                                    style="height: 48px; padding-right: 48px;" />
                                                <button type="button" @click="remove(index)" x-show="numbers.length > 1"
                                                    title="Hapus nomor PKS"
                                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 30px; height: 30px; border: 0; border-radius: 8px; background: rgba(239,68,68,.1); color: #ef4444; cursor: pointer;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </template>
                                        @if($errors->has('pks_numbers.*'))
                                        <span class="text-danger" style="font-size: 11px; margin-top: 4px; display: block;"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('pks_numbers.*') }}</span>
                                        @endif
                                    </div>

                                    @error('jenis')
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
                                        <input type="text" name="title"
                                            value="{{ old('title', $kegiatan->title ?? '') }}" required
                                            placeholder="Contoh: Pelatihan Web Development Bersama Industri"
                                            class="mc-input @error('title') border-danger @enderror" />
                                    </div>
                                    @error('title')
                                    <span class="text-danger" style="font-size: 11px; margin-top: 4px;"><i
                                            class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                </div>

                                <div style="grid-column: 1 / -1;" class="mc-group">
                                    <label class="mc-label">Deskripsi</label>
                                    <div class="mc-input-wrap">
                                        <i class="fas fa-comment-dots mc-icon-left" style="top: 14px;"></i>
                                        <textarea name="description" rows="3"
                                            placeholder="Ringkasan singkat terkait cakupan atau kegiatan kerja sama"
                                            class="mc-input"
                                            style="resize: vertical; min-height: 100px;">{{ old('description', $kegiatan->description ?? '') }}</textarea>
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
                        style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible;">
                        {{-- Card Header --}}
                        <div x-data="{
                            showPenggiat: true,
                            showPihak1: true,
                            showPihak2: true,
                            showPenandatangan1: {{ $kegiatan->penandatanganInternal ? 'true' : 'false' }},
                            showPJ1: {{ $kegiatan->pjInternal ? 'true' : 'false' }},
                            penggiatList: [
                                @if($kegiatan->mitra_id)
                                    { 
                                        id: Date.now(), 
                                        showPenandatangan: {{ $kegiatan->penandatanganMitra ? 'true' : 'false' }}, 
                                        showPJ: {{ $kegiatan->pjMitra ? 'true' : 'false' }}, 
                                        mitraId: '{{ $kegiatan->mitra_id }}', 
                                        mitraOpen: false,
                                        nama_penandatangan: '{{ addslashes($kegiatan->penandatanganMitra?->nama ?? '') }}',
                                        jabatan_penandatangan: '{{ addslashes($kegiatan->penandatanganMitra?->jabatan ?? '') }}',
                                        nama_pj: '{{ addslashes($kegiatan->pjMitra?->nama ?? '') }}',
                                        jabatan_pj: '{{ addslashes($kegiatan->pjMitra?->jabatan ?? '') }}'
                                    }
                                @else
                                    { id: Date.now(), showPenandatangan: false, showPJ: false, mitraId: '', mitraOpen: false, nama_penandatangan: '', jabatan_penandatangan: '', nama_pj: '', jabatan_pj: '' }
                                @endif
                            ],
                            nextId() { return Date.now() + Math.random(); },
                            addPenggiat() {
                                this.penggiatList.push({ 
                                    id: this.nextId(), 
                                    showPenandatangan: false, 
                                    showPJ: false, 
                                    mitraId: '', 
                                    mitraOpen: false,
                                    nama_penandatangan: '',
                                    jabatan_penandatangan: '',
                                    nama_pj: '',
                                    jabatan_pj: ''
                                });
                            },
                            removePenggiat(idx) {
                                if (this.penggiatList.length > 1) this.penggiatList.splice(idx, 1);
                            },
                            mitraItems: [
                                @foreach($mitras as $m)
                                    { id: {{ $m->id }}, nama: '{{ addslashes($m->nama_mitra) }}' },
                                @endforeach
                            ]
                        }"
                            @mitra-added.window="mitraItems.push($event.detail)">
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
                                    style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 16px; overflow: visible;">
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
                                        <div x-data="{
                                            jenisDokumen: '{{ old('jenis_dokumen', $kegiatan->jenis) }}',
                                            tipePelaksana: '{{ old('tipe_pelaksana', $kegiatan->tipe_pelaksana ?? ($kegiatan->jurusans->count() > 0 ? 'jurusan' : ($kegiatan->upas->count() > 0 ? 'upa' : ($kegiatan->pusats->count() > 0 ? 'pusat' : '')))) }}',
                                            pelaksanaOptions: {{ \Illuminate\Support\Js::from($pelaksanaOptions) }},
                                            init() {
                                                if (this.pelaksanaOptions.length === 1 && !this.tipePelaksana) {
                                                    this.tipePelaksana = this.pelaksanaOptions[0].v;
                                                }
                                                this.ensureSinglePelaksanaSelected();
                                            },
                                            ensureSinglePelaksanaSelected() {
                                                if (this.tipePelaksana === 'jurusan' && this.jurusanItems.length === 1 && this.selectedJurusans.length === 0) {
                                                    this.selectedJurusans = [this.jurusanItems[0].id];
                                                }
                                                if (this.tipePelaksana === 'upa' && this.upaItems.length === 1 && this.selectedUpas.length === 0) {
                                                    this.selectedUpas = [this.upaItems[0].id];
                                                }
                                                if (this.tipePelaksana === 'pusat' && this.pusatItems.length === 1 && this.selectedPusats.length === 0) {
                                                    this.selectedPusats = [this.pusatItems[0].id];
                                                }
                                            },

                                            {{-- Jurusan multi-select --}}
                                            jurusanOpen: false,
                                            selectedJurusans: [{{ $kegiatan->jurusans->pluck('id')->join(',') }}],
                                            jurusanItems: [
                                                @foreach($jurusans ?? [] as $jur)
                                                    { id: {{ $jur->id }}, nama: '{{ addslashes($jur->nama_jurusan) }}' },
                                                @endforeach
                                            ],
                                            toggleJurusan(id) {
                                                if (this.selectedJurusans.includes(id)) {
                                                    this.selectedJurusans = this.selectedJurusans.filter(i => i !== id);
                                                } else {
                                                    this.selectedJurusans.push(id);
                                                }
                                                this.selectedProdis = this.selectedProdis.filter(pid => {
                                                    const p = this.prodiItems.find(x => x.id === pid);
                                                    return p && this.selectedJurusans.includes(p.jurusan_id);
                                                });
                                            },
                                            getJurusanName(id) { return this.jurusanItems.find(j => j.id === id)?.nama ?? ''; },

                                            {{-- Prodi data (used by nested x-data scopes) --}}
                                            selectedProdis: [{{ $kegiatan->prodis->pluck('id')->join(',') }}],
                                            prodiItems: [
                                                @foreach($prodis ?? [] as $p)
                                                    { id: {{ $p->id }}, jurusan_id: {{ $p->jurusan_id }}, nama: '{{ addslashes($p->nama_prodi) }}', jenjang: '{{ $p->jenjang }}' },
                                                @endforeach
                                            ],
                                            toggleProdi(id) {
                                                if (this.selectedProdis.includes(id)) {
                                                    this.selectedProdis = this.selectedProdis.filter(i => i !== id);
                                                } else {
                                                    this.selectedProdis.push(id);
                                                }
                                            },
                                            getProdiName(id) {
                                                const p = this.prodiItems.find(x => x.id === id);
                                                return p ? `${p.nama} (${p.jenjang})` : '';
                                            },

                                            {{-- UPA multi-select --}}
                                            upaOpen: false,
                                            selectedUpas: [{{ $kegiatan->upas->pluck('id')->join(',') }}],
                                            upaItems: [
                                                @foreach($upas ?? [] as $u)
                                                    { id: {{ $u->id }}, nama: '{{ addslashes($u->nama_upa) }}' },
                                                @endforeach
                                            ],
                                            toggleUpa(id) {
                                                if (this.selectedUpas.includes(id)) { this.selectedUpas = this.selectedUpas.filter(i => i !== id); }
                                                else { this.selectedUpas.push(id); }
                                            },
                                            getUpaName(id) { return this.upaItems.find(u => u.id === id)?.nama ?? ''; },

                                            {{-- Pusat multi-select --}}
                                            pusatOpen: false,
                                            selectedPusats: [{{ $kegiatan->pusats->pluck('id')->join(',') }}],
                                            pusatItems: [
                                                @foreach($pusats ?? [] as $ps)
                                                    { id: {{ $ps->id }}, nama: '{{ addslashes($ps->nama_pusat) }}' },
                                                @endforeach
                                            ],
                                            togglePusat(id) {
                                                if (this.selectedPusats.includes(id)) { this.selectedPusats = this.selectedPusats.filter(i => i !== id); }
                                                else { this.selectedPusats.push(id); }
                                            },
                                            getPusatName(id) { return this.pusatItems.find(p => p.id === id)?.nama ?? ''; },
                                        }" @jenis-dokumen-changed.window="jenisDokumen = $event.detail.value; if (pelaksanaOptions.length === 1 && !tipePelaksana) tipePelaksana = pelaksanaOptions[0].v; ensureSinglePelaksanaSelected()"
                                            @reset-tipe-pelaksana.window="tipePelaksana = pelaksanaOptions.length === 1 ? pelaksanaOptions[0].v : ''; ensureSinglePelaksanaSelected()">
                                            {{-- Nama Instansi (Always shown) --}}
                                            <div>
                                                <div class="mc-group">
                                                    <label class="mc-label"><i class="fas fa-university"
                                                            style="margin-top:15px; color:#4f46e5;"></i> Nama
                                                        Instansi</label>
                                                    <div class="mc-input-wrap">
                                                        <i class="fas fa-building mc-icon-left"></i>
                                                        <input type="text" name="nama_instansi"
                                                            value="{{ old('nama_instansi', 'Politeknik Negeri Manado') }}"
                                                            class="mc-input" readonly
                                                            style="background: var(--surface2); color: var(--text); font-weight: 600;" />
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tipe Pelaksana --}}
                                            <div>
                                                {{-- Tipe Pelaksana Selector --}}
                                                <div style="margin-top: 15px;" class="mc-group">
                                                    <label class="mc-label">Tipe Pelaksana <span
                                                            class="mc-req">*</span></label>
                                                    <div
                                                        :style="`display: grid; grid-template-columns: repeat(${pelaksanaOptions.length}, 1fr); gap: 8px;`">
                                                        <template x-for="opt in pelaksanaOptions" :key="opt.v">
                                                            <button type="button" @click="tipePelaksana = opt.v"
                                                                :style="`display:flex; align-items:center; justify-content:center; gap:8px; padding:10px 12px; border-radius:10px; font-size:12px; font-weight:600; cursor:pointer; transition: all 0.25s ease; border: 2px solid ${tipePelaksana === opt.v ? opt.color : 'var(--border)'}; background: ${tipePelaksana === opt.v ? opt.color + '12' : 'var(--surface)'}; color: ${tipePelaksana === opt.v ? opt.color : 'var(--text-sub)'};`">
                                                                <i :class="opt.icon" style="font-size: 13px;"></i>
                                                                <span x-text="opt.label"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                    <input type="hidden" name="tipe_pelaksana" :value="tipePelaksana">
                                                </div>

                                                {{-- ══ Jurusan Sub-form (Jenis Kerjasama Style) ══ --}}
                                                <div x-show="tipePelaksana === 'jurusan'" x-collapse.duration.300ms
                                                    style="margin-top: 12px;">

                                                    {{-- Jurusan Dropdown Selector --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label"><i class="fas fa-microchip"
                                                                style="margin-right:5px; color:#4f46e5;"></i> Pilih
                                                            Jurusan</label>
                                                        <template x-for="jId in selectedJurusans" :key="'hj'+jId">
                                                            <input type="hidden" name="pelaksana_jurusan_ids[]"
                                                                :value="jId">
                                                        </template>
                                                        <div class="alpine-dropdown"
                                                            @click.outside="jurusanOpen = false">
                                                            <div class="ad-trigger no-icon"
                                                                :class="{'active': jurusanOpen}"
                                                                @click="jurusanOpen = !jurusanOpen">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                                                    <i class="fas fa-microchip"
                                                                        style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                                                    <span x-show="selectedJurusans.length === 0"
                                                                        style="color: #9ca3af;">— Pilih Jurusan —</span>
                                                                    <div x-show="selectedJurusans.length > 0"
                                                                        style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                                        <template x-for="jId in selectedJurusans"
                                                                            :key="'tag'+jId">
                                                                            <span class="tag tag-purple"
                                                                                style="font-size: 10px; padding: 2px 8px;"
                                                                                x-text="getJurusanName(jId)"></span>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <i class="fas fa-chevron-down"
                                                                    style="font-size: 10px; transition: 0.3s; flex-shrink: 0;"
                                                                    :style="jurusanOpen ? 'transform: rotate(180deg)' : ''"></i>
                                                            </div>
                                                            <div class="ad-menu" x-show="jurusanOpen" x-transition>
                                                                <template x-for="j in jurusanItems" :key="j.id">
                                                                    <div class="ad-item"
                                                                        :class="{'selected': selectedJurusans.includes(j.id)}"
                                                                        @click="toggleJurusan(j.id)"
                                                                        style="display: flex; align-items: center; gap: 10px;">
                                                                        <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                                            :style="selectedJurusans.includes(j.id) ? 'background: #4f46e5; border-color: #4f46e5;' : ''">
                                                                            <i class="fas fa-check"
                                                                                style="font-size: 10px; color: #fff;"
                                                                                x-show="selectedJurusans.includes(j.id)"></i>
                                                                        </div>
                                                                        <span x-text="j.nama"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Dynamic Sub-Forms: Prodi per Selected Jurusan --}}
                                                    <template x-for="jId in selectedJurusans" :key="'jform-' + jId">
                                                        <div x-transition:enter="transition ease-out duration-300"
                                                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                            x-transition:enter-end="opacity-100 transform translate-y-0"
                                                            style="margin-top: 16px; background: var(--surface2); border: 1px solid var(--border); border-radius: 14px; overflow: visible;">

                                                            {{-- Sub-Form Header --}}
                                                            <div
                                                                style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.05), rgba(124,58,237,0.03)); border-radius: 14px 14px 0 0;">
                                                                <div
                                                                    style="width: 28px; height: 28px; border-radius: 8px; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                                                    <i class="fas fa-microchip"></i>
                                                                </div>
                                                                <div style="flex: 1; min-width: 0;">
                                                                    <span
                                                                        style="font-weight: 700; font-size: 13px; color: var(--text);"
                                                                        x-text="getJurusanName(jId)"></span>
                                                                    <span
                                                                        style="font-size: 11px; color: var(--text-sub); display: block; margin-top: 1px;">Pilih
                                                                        program studi pada jurusan ini</span>
                                                                </div>
                                                                <button type="button" @click="toggleJurusan(jId)"
                                                                    style="width: 26px; height: 26px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface); color: var(--text-sub); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 11px;"
                                                                    onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444'; this.style.background='rgba(239,68,68,0.06)'"
                                                                    onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-sub)'; this.style.background='var(--surface)'"
                                                                    title="Hapus jurusan ini">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>

                                                            {{-- Sub-Form Body: Prodi Multi-Select --}}
                                                            <div style="padding: 18px;" x-data="{
                                                            prodiDropOpen: false,
                                                            prodiSearchQ: '',
                                                            get availableProdis() {
                                                                const q = this.prodiSearchQ.toLowerCase();
                                                                return $data.prodiItems.filter(p =>
                                                                    p.jurusan_id === jId &&
                                                                    !$data.selectedProdis.includes(p.id) &&
                                                                    (!q || p.nama.toLowerCase().includes(q))
                                                                );
                                                            },
                                                            get selectedInJurusan() {
                                                                return $data.selectedProdis.filter(pid => {
                                                                    const p = $data.prodiItems.find(x => x.id === pid);
                                                                    return p && p.jurusan_id === jId;
                                                                });
                                                            }
                                                        }">
                                                                <div class="mc-group"
                                                                    style="position: relative; margin-bottom: 0;"
                                                                    @click.outside="prodiDropOpen = false">
                                                                    <label class="mc-label" style="margin-bottom: 8px;">
                                                                        <i class="fas fa-graduation-cap"
                                                                            style="margin-right:5px; color:#059669;"></i>
                                                                        Program Studi
                                                                    </label>

                                                                    {{-- Selected Prodi Tags --}}
                                                                    <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:8px;"
                                                                        x-show="selectedInpusat.length > 0">
                                                                        <template x-for="pId in selectedInJurusan"
                                                                            :key="'ptag'+pId">
                                                                            <span
                                                                                style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:600; background:linear-gradient(135deg, rgba(5,150,105,0.12), rgba(5,150,105,0.06)); color:#059669; border:1px solid rgba(5,150,105,0.2);">
                                                                                <span
                                                                                    x-text="$data.getProdiName(pId)"></span>
                                                                                <i class="fas fa-times"
                                                                                    style="font-size:9px; cursor:pointer; opacity:0.7;"
                                                                                    @click="$data.toggleProdi(pId)"
                                                                                    @click.stop></i>
                                                                            </span>
                                                                        </template>
                                                                    </div>

                                                                    {{-- Hidden inputs for prodis --}}
                                                                    <template x-for="pId in selectedInJurusan"
                                                                        :key="'hp'+pId">
                                                                        <input type="hidden"
                                                                            name="pelaksana_prodi_ids[]" :value="pId">
                                                                    </template>

                                                                    {{-- Prodi Dropdown --}}
                                                                    <div class="alpine-dropdown"
                                                                        @click.outside="prodiDropOpen = false">
                                                                        <div class="ad-trigger no-icon"
                                                                            :class="{'active': prodiDropOpen}"
                                                                            @click="prodiDropOpen = !prodiDropOpen">
                                                                            <div
                                                                                style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                                                                <i class="fas fa-graduation-cap"
                                                                                    style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                                                                <span
                                                                                    style="color: #9ca3af; font-size: 12px;">—
                                                                                    Pilih Program Studi —</span>
                                                                            </div>
                                                                            <i class="fas fa-chevron-down"
                                                                                style="font-size: 10px; transition: 0.3s; flex-shrink: 0;"
                                                                                :style="prodiDropOpen ? 'transform: rotate(180deg)' : ''"></i>
                                                                        </div>
                                                                        <div class="ad-menu" x-show="prodiDropOpen"
                                                                            x-transition
                                                                            style="max-height: 220px; overflow-y: auto;">
                                                                            {{-- Search inside dropdown --}}
                                                                            <div
                                                                                style="padding: 8px 12px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                                                                <div
                                                                                    style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 6px;">
                                                                                    <i class="fas fa-search"
                                                                                        style="font-size: 11px; color: #9ca3af;"></i>
                                                                                    <input type="text"
                                                                                        x-model="prodiSearchQ"
                                                                                        placeholder="Cari prodi..."
                                                                                        style="border: none; outline: none; background: transparent; font-size: 12px; color: var(--text); width: 100%; font-family: inherit;"
                                                                                        @click.stop>
                                                                                </div>
                                                                            </div>
                                                                            <template x-for="p in availableProdis"
                                                                                :key="p.id">
                                                                                <div class="ad-item"
                                                                                    @click="$data.toggleProdi(p.id)"
                                                                                    style="display: flex; align-items: center; gap: 10px;">
                                                                                    <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                                                        :style="$data.selectedProdis.includes(p.id) ? 'background: #059669; border-color: #059669;' : ''">
                                                                                        <i class="fas fa-check"
                                                                                            style="font-size: 10px; color: #fff;"
                                                                                            x-show="$data.selectedProdis.includes(p.id)"></i>
                                                                                    </div>
                                                                                    <span x-text="p.nama"
                                                                                        style="flex: 1;"></span>
                                                                                    <span
                                                                                        style="font-size:10px; padding:1px 6px; border-radius:4px; background:rgba(5,150,105,0.1); color:#059669; font-weight:600;"
                                                                                        x-text="p.jenjang"></span>
                                                                                </div>
                                                                            </template>
                                                                            <div x-show="availableProdis.length === 0"
                                                                                style="padding:12px 14px; font-size:12px; color:var(--text-sub); text-align:center;">
                                                                                <i class="fas fa-info-circle"
                                                                                    style="margin-right:4px; opacity:0.5;"></i>
                                                                                Tidak ada prodi tersedia
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>

                                                {{-- ══ UPA Sub-form (Checkbox Style) ══ --}}
                                                <div x-show="tipePelaksana === 'upa'" x-collapse.duration.300ms
                                                    style="margin-top: 12px;">
                                                    <div class="mc-group">
                                                        <label class="mc-label"><i class="fas fa-building-columns"
                                                                style="margin-right:5px; color:#0891b2;"></i> Pilih
                                                            UPA</label>
                                                        <template x-for="uId in selectedUpas" :key="'hu'+uId">
                                                            <input type="hidden" name="pelaksana_upa_ids[]"
                                                                :value="uId">
                                                        </template>
                                                        <div class="alpine-dropdown" @click.outside="upaOpen = false">
                                                            <div class="ad-trigger no-icon" :class="{'active': upaOpen}"
                                                                @click="upaOpen = !upaOpen">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                                                    <i class="fas fa-building-columns"
                                                                        style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                                                    <span x-show="selectedUpas.length === 0"
                                                                        style="color: #9ca3af;">— Pilih UPA —</span>
                                                                    <div x-show="selectedUpas.length > 0"
                                                                        style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                                        <template x-for="uId in selectedUpas"
                                                                            :key="'utag'+uId">
                                                                            <span class="tag tag-purple"
                                                                                style="font-size: 10px; padding: 2px 8px; background: rgba(8,145,178,0.12); color: #0891b2; border: 1px solid rgba(8,145,178,0.2);"
                                                                                x-text="getUpaName(uId)"></span>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <i class="fas fa-chevron-down"
                                                                    style="font-size: 10px; transition: 0.3s; flex-shrink: 0;"
                                                                    :style="upaOpen ? 'transform: rotate(180deg)' : ''"></i>
                                                            </div>
                                                            <div class="ad-menu" x-show="upaOpen" x-transition>
                                                                <template x-for="u in upaItems" :key="u.id">
                                                                    <div class="ad-item"
                                                                        :class="{'selected': selectedUpas.includes(u.id)}"
                                                                        @click="toggleUpa(u.id)"
                                                                        style="display: flex; align-items: center; gap: 10px;">
                                                                        <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                                            :style="selectedUpas.includes(u.id) ? 'background: #0891b2; border-color: #0891b2;' : ''">
                                                                            <i class="fas fa-check"
                                                                                style="font-size: 10px; color: #fff;"
                                                                                x-show="selectedUpas.includes(u.id)"></i>
                                                                        </div>
                                                                        <span x-text="u.nama"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ══ Pusat Sub-form (Checkbox Style) ══ --}}
                                                <div x-show="tipePelaksana === 'pusat'" x-collapse.duration.300ms
                                                    style="margin-top: 12px;">
                                                    <div class="mc-group">
                                                        <label class="mc-label"><i class="fas fa-landmark"
                                                                style="margin-right:5px; color:#7c3aed;"></i> Pilih
                                                            Pusat</label>
                                                        <template x-for="psId in selectedPusats" :key="'hps'+psId">
                                                            <input type="hidden" name="pelaksana_pusat_ids[]"
                                                                :value="psId">
                                                        </template>
                                                        <div class="alpine-dropdown" @click.outside="pusatOpen = false">
                                                            <div class="ad-trigger no-icon"
                                                                :class="{'active': pusatOpen}"
                                                                @click="pusatOpen = !pusatOpen">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                                                    <i class="fas fa-landmark"
                                                                        style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                                                    <span x-show="selectedPusats.length === 0"
                                                                        style="color: #9ca3af;">— Pilih Pusat —</span>
                                                                    <div x-show="selectedPusats.length > 0"
                                                                        style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                                        <template x-for="psId in selectedPusats"
                                                                            :key="'pstag'+psId">
                                                                            <span class="tag tag-purple"
                                                                                style="font-size: 10px; padding: 2px 8px; background: rgba(124,58,237,0.12); color: #7c3aed; border: 1px solid rgba(124,58,237,0.2);"
                                                                                x-text="getPusatName(psId)"></span>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <i class="fas fa-chevron-down"
                                                                    style="font-size: 10px; transition: 0.3s; flex-shrink: 0;"
                                                                    :style="pusatOpen ? 'transform: rotate(180deg)' : ''"></i>
                                                            </div>
                                                            <div class="ad-menu" x-show="pusatOpen" x-transition>
                                                                <template x-for="ps in pusatItems" :key="ps.id">
                                                                    <div class="ad-item"
                                                                        :class="{'selected': selectedPusats.includes(ps.id)}"
                                                                        @click="togglePusat(ps.id)"
                                                                        style="display: flex; align-items: center; gap: 10px;">
                                                                        <div style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                                            :style="selectedPusats.includes(ps.id) ? 'background: #7c3aed; border-color: #7c3aed;' : ''">
                                                                            <i class="fas fa-check"
                                                                                style="font-size: 10px; color: #fff;"
                                                                                x-show="selectedPusats.includes(ps.id)"></i>
                                                                        </div>
                                                                        <span x-text="ps.nama"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Penandatangan (Collapsible) --}}
                                        <div
                                            style="margin-top: 14px; border: 1px solid var(--border); border-radius: 10px; overflow: visible; background: var(--surface);">
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
                                                style="padding: 10px 14px 14px 14px;">
                                                <div class="mc-grid-2">
                                                    <div class="mc-group">
                                                        <label class="mc-label">Nama</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-user mc-icon-left"></i>
                                                            <input type="text" name="nama_penandatangan"
                                                                value="{{ old('nama_penandatangan', $kegiatan->penandatanganInternal?->nama ?? '') }}"
                                                                placeholder="Nama penandatangan" class="mc-input" />
                                                        </div>
                                                    </div>
                                                    <div class="mc-group">
                                                        <label class="mc-label">Jabatan</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-id-badge mc-icon-left"></i>
                                                            <input type="text" name="jabatan_penandatangan"
                                                                value="{{ old('jabatan_penandatangan', $kegiatan->penandatanganInternal?->jabatan ?? '') }}"
                                                                placeholder="Jabatan penandatangan" class="mc-input" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Penanggung Jawab (Collapsible) --}}
                                        <div
                                            style="margin-top: 10px; border: 1px solid var(--border); border-radius: 10px; overflow: visible; background: var(--surface);">
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
                                                style="padding: 10px 14px 14px 14px;">
                                                <div class="mc-grid-2">
                                                    <div class="mc-group">
                                                        <label class="mc-label">Nama</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-user mc-icon-left"></i>
                                                            <input type="text" name="nama_penanggung_jawab"
                                                                value="{{ old('nama_penanggung_jawab', $kegiatan->pjInternal?->nama ?? '') }}"
                                                                placeholder="Nama penanggung jawab" class="mc-input" />
                                                        </div>
                                                    </div>
                                                    <div class="mc-group">
                                                        <label class="mc-label">Jabatan</label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-id-badge mc-icon-left"></i>
                                                            <input type="text" name="jabatan_penanggung_jawab"
                                                                value="{{ old('jabatan_penanggung_jawab', $kegiatan->pjInternal?->jabatan ?? '') }}"
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
                                                            <input type="hidden" name="penggiat_mitra_ids[]" :value="pg.mitraId">
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
                                                        <a href="{{ route('pusat.mitra.create') }}"
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
                                                    style="margin-top: 10px; border: 1px solid var(--border); border-radius: 10px; overflow: visible; background: var(--surface2);">
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
                                                        style="padding: 10px 14px 14px 14px;">
                                                        <div class="mc-grid-2">
                                                            <div class="mc-group">
                                                                <label class="mc-label">Nama</label>
                                                                <div class="mc-input-wrap">
                                                                    <i class="fas fa-user mc-icon-left"></i>
                                                                    <input type="text"
                                                                        :name="'penggiat[' + idx + '][nama_penandatangan]'"
                                                                        x-model="pg.nama_penandatangan"
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
                                                                        x-model="pg.jabatan_penandatangan"
                                                                        placeholder="Jabatan penandatangan"
                                                                        class="mc-input" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Penanggung Jawab (Collapsible) --}}
                                                <div
                                                    style="margin-top: 8px; border: 1px solid var(--border); border-radius: 10px; overflow: visible; background: var(--surface2);">
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
                                                        style="padding: 10px 14px 14px 14px;">
                                                        <div class="mc-grid-2">
                                                            <div class="mc-group">
                                                                <label class="mc-label">Nama</label>
                                                                <div class="mc-input-wrap">
                                                                    <i class="fas fa-user mc-icon-left"></i>
                                                                    <input type="text"
                                                                        :name="'penggiat[' + idx + '][nama_pj]'"
                                                                        x-model="pg.nama_pj"
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
                                                                        x-model="pg.jabatan_pj"
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
                        <div x-data="{ showBentuk: false }">
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
                            <div x-show="showBentuk" x-collapse.duration.300ms style="padding: 20px 24px; overflow: visible;">

                                {{-- Jenis Kerjasama (Alpine Multi-Select with Dynamic Forms) --}}
                                <div x-data="{ 
                                    open: false, 
                                    selected: {{ json_encode(old('id_jenis', $kegiatan->details->pluck('jenis_kerjasama_id')->toArray())) }},
                                    items: [
                                        @foreach($jenisKerjasama as $jenis)
                                            { id: {{ $jenis->id }}, label: '{{ $jenis->nama_kerjasama }}' },
                                        @endforeach
                                    ],
                                    formData: {
                                        @foreach($kegiatan->details as $detail)
                                            {{ $detail->jenis_kerjasama_id }}: {
                                                nilai_kontrak: 'Rp {{ number_format($detail->nilai_kontrak, 0, ",", ".") }}',
                                                income: '{{ addslashes($detail->income) }}',
                                                volume: '{{ $detail->volume_luaran }}',
                                                satuan_volume: '{{ addslashes($detail->satuan_luaran) }}',
                                                keterangan: '{{ addslashes($detail->keterangan) }}',
                                                tujuan: '{{ addslashes($detail->tujuan) }}',
                                                sasaran_id: '{{ $detail->sasaran_id }}',
                                                indikator_kinerja: '{{ addslashes($detail->indikator_kinerja) }}',
                                                output: '{{ addslashes($detail->output) }}',
                                                outcome: '{{ addslashes($detail->outcome) }}'
                                            },
                                        @endforeach
                                    },
                                    sasaranOpen: {},
                                    sasaranOptions: [
                                        @foreach($sasarans as $sas)
                                            { id: {{ $sas->id }}, deskripsi: '{{ addslashes($sas->deskripsi) }}' },
                                        @endforeach
                                    ],
                                    toggle(id) {
                                        const idx = this.selected.indexOf(id);
                                        if (idx > -1) {
                                            this.selected.splice(idx, 1);
                                            delete this.formData[id];
                                            delete this.sasaranOpen[id];
                                        } else {
                                            this.selected.push(id);
                                            this.formData[id] = { nilai_kontrak: '', income: '', volume: '', satuan_volume: '', keterangan: '', tujuan: '', sasaran_id: '', indikator_kinerja: '', output: '', outcome: '' };
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
                                                this.formData[id] = { nilai_kontrak: '', income: '', volume: '', satuan_volume: '', keterangan: '', tujuan: '', sasaran_id: '', indikator_kinerja: '', output: '', outcome: '' };
                                                this.sasaranOpen[id] = false;
                                            }
                                        });
                                    }
                                }">
                                    {{-- Dropdown Selector --}}
                                    <div class="mc-group">
                                        <label class="mc-label">Bentuk Kegiatan Kerjasama (Ruang Lingkup)<span
                                                class="mc-req">*</span></label>
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
                                                        Bentuk Kegiatan Kerjasama
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
                                                {{-- Row 1: Nilai Kontrak + Income --}}
                                                <div
                                                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                                                    {{-- Nilai Kontrak --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Nilai Kontrak <span
                                                                style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Total
                                                                Pendapatan)</span></label>
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

                                                    {{-- Income --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Income <span
                                                                style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Deskripsi
                                                                pendapatan)</span></label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-coins mc-icon-left" style="top: 14px;"></i>
                                                            <textarea :name="'jenis_detail[' + id + '][income]'"
                                                                x-model="formData[id].income" rows="2"
                                                                placeholder="Deskripsi pendapatan dari kegiatan..."
                                                                class="mc-input"
                                                                style="resize: vertical; min-height: 70px;"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 2: Output & Outcome --}}
                                                <div
                                                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                                                    {{-- Output --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Output <span
                                                                style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Hasil
                                                                langsung kegiatan)</span></label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-box-open mc-icon-left"
                                                                style="top: 14px;"></i>
                                                            <textarea :name="'jenis_detail[' + id + '][output]'"
                                                                x-model="formData[id].output" rows="2"
                                                                placeholder="Jelaskan output / hasil langsung kegiatan..."
                                                                class="mc-input"
                                                                style="resize: vertical; min-height: 70px;"></textarea>
                                                        </div>
                                                    </div>

                                                    {{-- Outcome --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Outcome <span
                                                                style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Dampak
                                                                / manfaat kegiatan)</span></label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-seedling mc-icon-left"
                                                                style="top: 14px;"></i>
                                                            <textarea :name="'jenis_detail[' + id + '][outcome]'"
                                                                x-model="formData[id].outcome" rows="2"
                                                                placeholder="Jelaskan outcome / dampak manfaat kegiatan..."
                                                                class="mc-input"
                                                                style="resize: vertical; min-height: 70px;"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 3: Luaran (Volume + Satuan Volume) --}}
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

                                                {{-- Row 4: Keterangan + Tujuan --}}
                                                <div
                                                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                                                    {{-- Keterangan --}}
                                                    <div class="mc-group">
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

                                                    {{-- Tujuan --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Tujuan <span
                                                                style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Tujuan
                                                                dari kegiatan)</span></label>
                                                        <div class="mc-input-wrap">
                                                            <i class="fas fa-bullseye mc-icon-left"
                                                                style="top: 14px;"></i>
                                                            <textarea :name="'jenis_detail[' + id + '][tujuan]'"
                                                                x-model="formData[id].tujuan" rows="2"
                                                                placeholder="Jelaskan tujuan dari kegiatan kerjasama..."
                                                                class="mc-input"
                                                                style="resize: vertical; min-height: 70px;"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 5: Sasaran (Custom Dropdown) + Indikator Kinerja --}}
                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                                    {{-- Sasaran Dropdown --}}
                                                    <div class="mc-group">
                                                        <label class="mc-label">Sasaran</label>
                                                        <div class="alpine-dropdown"
                                                            @click.outside="sasaranOpen[id] = false"
                                                            style="position: relative;">
                                                            <input type="hidden"
                                                                :name="'jenis_detail[' + id + '][sasaran_id]'"
                                                                x-model="formData[id].sasaran_id">
                                                            <div class="ad-trigger no-icon"
                                                                :class="{'active': sasaranOpen[id]}"
                                                                @click="sasaranOpen[id] = !sasaranOpen[id]">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                                    <i class="fas fa-crosshairs"
                                                                        style="color: #9ca3af; font-size: 12px; flex-shrink: 0;"></i>
                                                                    <span x-show="!formData[id].sasaran_id"
                                                                        style="color: #9ca3af; font-size: 12px;">— Pilih
                                                                        Sasaran —</span>
                                                                    <span x-show="formData[id].sasaran_id"
                                                                        style="font-size: 12px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                                        x-text="sasaranOptions.find(o => o.id == formData[id].sasaran_id)?.deskripsi || ''"></span>
                                                                </div>
                                                                <i class="fas fa-chevron-down"
                                                                    style="font-size: 9px; transition: 0.3s; flex-shrink: 0; color: #9ca3af;"
                                                                    :style="sasaranOpen[id] ? 'transform: rotate(180deg)' : ''"></i>
                                                            </div>
                                                            <div class="ad-menu" x-show="sasaranOpen[id]" x-transition
                                                                style="position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 120; max-height: 200px; overflow-y: auto;">
                                                                <template x-for="opt in sasaranOptions" :key="opt.id">
                                                                    <div class="ad-item"
                                                                        :class="{'selected': formData[id].sasaran_id == opt.id}"
                                                                        @click="formData[id].sasaran_id = opt.id; sasaranOpen[id] = false"
                                                                        style="font-size: 12px; padding: 8px 12px;">
                                                                        <span x-text="opt.deskripsi"></span>
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
                </div>
                {{-- Footer --}}
                <div class="mc-footer"
                    style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <a href="{{ route('pusat.dkerjasama') }}" class="rfc-btn rfc-btn-danger"
                        style="text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="rfc-btn rfc-btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function pksNumberFields(initialNumbers = ['']) {
        return {
            numbers: initialNumbers.length ? initialNumbers : [''],
            add() {
                this.numbers.push('');
            },
            remove(index) {
                if (this.numbers.length > 1) {
                    this.numbers.splice(index, 1);
                }
            }
        };
    }

    function mitraManager(initial = null) {
        return {
            mitras: initial || [{
                id: Date.now(),
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

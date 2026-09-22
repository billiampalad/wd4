@props([
    'id' => null,
    'title' => 'Filter Data',
    'subtitle' => 'Saring dan sesuaikan tampilan data sesuai kebutuhan Anda',
    'icon' => 'fas fa-sliders-h',
    'action' => null, // Form action URL (jika null atau '#', akan memfilter client-side live)
    'method' => 'GET',
    'collapsible' => true,
    'collapsed' => false,
    'target' => null, // CSS selector data target (misal: '.um-table tbody tr.um-row')
    'emptyTarget' => null, // CSS selector status data kosong (misal: '#mitraSearchEmptyRow')
    'countTarget' => null, // CSS selector label jumlah data (misal: '#resultCount')
    'autoApply' => false, // Jika true, filter langsung diaplikasikan saat ada perubahan input
    'showActiveChips' => true, // Menampilkan bar chip filter yang sedang aktif
    'showReset' => true, // Menampilkan tombol Reset
    'showApply' => true, // Menampilkan tombol Terapkan Filter
    'applyText' => 'Terapkan Filter',
    'resetText' => 'Reset Filter',
    'exportPdf' => null, // URL cetak PDF
    'exportExcel' => null, // URL ekspor Excel
    'previewUrl' => null, // URL preview dokumen
    'presets' => [], // Array preset tombol [ ['label' => 'Semua', 'value' => 'all', 'icon' => '...', 'count' => 10], ... ]
    'presetName' => 'status', // Nama parameter/field yang dikontrol oleh presets
    'activePreset' => 'all', // Preset awal yang aktif
    'badge' => null, // Teks badge opsional di sebelah judul
])

@php
    $id = $id ?? ('customFilter_' . uniqid());
    $isCollapsed = filter_var($collapsed, FILTER_VALIDATE_BOOLEAN);
@endphp

<div 
    {{ $attributes->class([
        'custom-filter-wrapper',
        'is-collapsed' => $isCollapsed,
        'has-presets' => !empty($presets),
    ]) }}
    id="{{ $id }}"
    data-custom-filter
    data-filter-id="{{ $id }}"
    @if($target) data-filter-target="{{ $target }}" @endif
    @if($emptyTarget) data-filter-empty="{{ $emptyTarget }}" @endif
    @if($countTarget) data-filter-count="{{ $countTarget }}" @endif
    @if($autoApply) data-filter-auto-apply="true" @endif
    @if($exportPdf) data-pdf-url="{{ $exportPdf }}" @endif
    @if($exportExcel) data-excel-url="{{ $exportExcel }}" @endif
    @if($previewUrl) data-preview-url="{{ $previewUrl }}" @endif
>
    <div class="custom-filter-card">
        {{-- ═══ 1. PANEL HEADER ═══ --}}
        <div class="custom-filter-header" @if($collapsible) data-filter-toggle role="button" tabindex="0" aria-expanded="{{ $isCollapsed ? 'false' : 'true' }}" title="Klik untuk membuka/menutup panel filter" @endif>
            <div class="custom-filter-title-group">
                <div class="custom-filter-icon-box">
                    <i class="{{ $icon }}"></i>
                </div>
                <div class="custom-filter-text">
                    <div class="custom-filter-heading-row">
                        <h3 class="custom-filter-title">{{ $title }}</h3>
                        @if($badge)
                            <span class="custom-filter-badge">{{ $badge }}</span>
                        @endif
                        <span class="custom-filter-active-count" style="display: none;">
                            <span class="count-num">0</span> Aktif
                        </span>
                    </div>
                    @if($subtitle)
                        <p class="custom-filter-subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            <div class="custom-filter-header-actions">
                @if(isset($headerActions))
                    {{ $headerActions }}
                @endif

                @if($collapsible)
                    <button type="button" class="custom-filter-collapse-btn" aria-label="Buka/Tutup Panel Filter">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                @endif
            </div>
        </div>

        {{-- ═══ 2. QUICK PRESET PILLS ROW (OPSIONAL) ═══ --}}
        @if(!empty($presets))
            <div class="custom-filter-presets-bar">
                <div class="custom-filter-presets-label">
                    <i class="fas fa-bolt"></i> <span>Filter Cepat:</span>
                </div>
                <div class="custom-filter-presets-list" data-preset-group="{{ $presetName }}">
                    @foreach($presets as $preset)
                        @php
                            $val = is_array($preset) ? ($preset['value'] ?? '') : $preset;
                            $lbl = is_array($preset) ? ($preset['label'] ?? $val) : $preset;
                            $ic = is_array($preset) ? ($preset['icon'] ?? null) : null;
                            $cnt = is_array($preset) ? ($preset['count'] ?? null) : null;
                            $isActive = (string)request($presetName, $activePreset) === (string)$val;
                        @endphp
                        <button 
                            type="button" 
                            class="custom-filter-preset-pill {{ $isActive ? 'is-active' : '' }}" 
                            data-preset-value="{{ $val }}"
                            data-preset-name="{{ $presetName }}"
                        >
                            @if($ic) <i class="{{ $ic }}"></i> @endif
                            <span>{{ $lbl }}</span>
                            @if(!is_null($cnt))
                                <span class="preset-badge">{{ $cnt }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ═══ 3. PANEL BODY & FORM ═══ --}}
        <div class="custom-filter-body-wrapper" @if($isCollapsed) style="display: none;" @endif>
            <form 
                action="{{ $action ?? '#' }}" 
                method="{{ strtoupper($method) === 'POST' ? 'POST' : 'GET' }}"
                class="custom-filter-form"
                id="{{ $id }}_form"
            >
                @if(strtoupper($method) === 'POST')
                    @csrf
                @endif

                {{-- Hidden input sinkronisasi preset cepat --}}
                @if(!empty($presets))
                    <input type="hidden" name="{{ $presetName }}" value="{{ request($presetName, $activePreset) }}" class="custom-filter-preset-input">
                @endif

                {{-- Grid Input Filter --}}
                <div class="custom-filter-grid">
                    {{ $slot }}
                </div>

                {{-- Dynamic Active Tags Bar --}}
                @if($showActiveChips)
                    <div class="custom-filter-active-tags-bar" style="display: none;">
                        <div class="active-tags-label">
                            <i class="fas fa-tags"></i> <span>Filter Diterapkan:</span>
                        </div>
                        <div class="active-tags-container"></div>
                        <button type="button" class="btn-clear-all-tags" title="Hapus semua filter aktif">
                            <i class="fas fa-rotate-left"></i> Reset
                        </button>
                    </div>
                @endif

                {{-- ═══ 4. FOOTER ACTIONS ═══ --}}
                <div class="custom-filter-footer">
                    <div class="custom-filter-footer-left">
                        @if(isset($footerLeft))
                            {{ $footerLeft }}
                        @endif
                    </div>

                    <div class="custom-filter-footer-right">
                        @if($showReset)
                            <button type="button" class="custom-filter-btn custom-filter-btn-reset" data-filter-reset>
                                <i class="fas fa-rotate-left"></i>
                                <span>{{ $resetText }}</span>
                            </button>
                        @endif

                        @if($exportPdf)
                            <button type="button" class="custom-filter-btn custom-filter-btn-pdf" data-filter-export="pdf" title="Cetak / Unduh berkas format PDF">
                                <i class="fas fa-file-pdf"></i>
                                <span>Cetak PDF</span>
                            </button>
                        @endif

                        @if($exportExcel)
                            <button type="button" class="custom-filter-btn custom-filter-btn-excel" data-filter-export="excel" title="Ekspor data ke spreadsheet Excel">
                                <i class="fas fa-file-excel"></i>
                                <span>Ekspor Excel</span>
                            </button>
                        @endif

                        @if(isset($extraButtons))
                            {{ $extraButtons }}
                        @endif

                        @if($showApply)
                            <button type="submit" class="custom-filter-btn custom-filter-btn-primary" data-filter-apply>
                                <i class="fas fa-magnifying-glass"></i>
                                <span>{{ $applyText }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

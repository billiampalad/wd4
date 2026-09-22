@props([
    'id' => null,
    'title' => 'Filter Data',
    'subtitle' => 'Saring dan sesuaikan tampilan data sesuai kebutuhan Anda',
    'icon' => 'fas fa-sliders-h',
    'variant' => 'panel', // 'panel' (card expandable), 'bar' (inline toolbar), 'chips' (quick filter bar), 'compact'
    'action' => null, // Form action URL (if null, acts as client-side live filter form)
    'method' => 'GET',
    'collapsible' => true,
    'collapsed' => false,
    'target' => null, // Selector for client-side live filtering (e.g. '.dk-table tbody tr')
    'emptyTarget' => null, // Selector for empty state (e.g. '#filterEmptyState')
    'countTarget' => null, // Selector for count indicator
    'autoApply' => false, // If true, applying filters immediately on change (for client filtering)
    'showActiveChips' => true, // Display active filter tags/chips bar
    'showReset' => true, // Display Reset button
    'showApply' => true, // Display Apply/Tampilkan button
    'applyText' => 'Terapkan Filter',
    'resetText' => 'Reset Filter',
    'exportPdf' => null, // PDF export URL
    'exportExcel' => null, // Excel export URL
    'previewUrl' => null, // Preview URL
    'presets' => [], // Array of preset buttons [ ['label' => 'Semua', 'value' => 'all'], ... ]
    'presetName' => 'status', // Field name that presets control
    'activePreset' => 'all',
    'badge' => null, // Optional badge count or text in header
])

@php
    $id = $id ?? ('customFilter_' . uniqid());
    $hasForm = true; // Component always renders a structured form wrapper for consistency
    $isCollapsed = filter_var($collapsed, FILTER_VALIDATE_BOOLEAN);
@endphp

<div 
    {{ $attributes->class([
        'custom-filter-wrapper',
        'custom-filter-' . $variant,
        'is-collapsed' => $isCollapsed,
        'has-presets' => !empty($presets),
    ]) }}
    id="{{ $id }}"
    data-custom-filter
    data-filter-id="{{ $id }}"
    data-variant="{{ $variant }}"
    @if($target) data-filter-target="{{ $target }}" @endif
    @if($emptyTarget) data-filter-empty="{{ $emptyTarget }}" @endif
    @if($countTarget) data-filter-count="{{ $countTarget }}" @endif
    @if($autoApply) data-filter-auto-apply="true" @endif
    @if($exportPdf) data-pdf-url="{{ $exportPdf }}" @endif
    @if($exportExcel) data-excel-url="{{ $exportExcel }}" @endif
    @if($previewUrl) data-preview-url="{{ $previewUrl }}" @endif
>
    @if($variant === 'panel')
        {{-- ============================================================= --}}
        {{-- VARIANT: PANEL (Card with Gradient Header & Smooth Collapse) --}}
        {{-- ============================================================= --}}
        <div class="custom-filter-card">
            {{-- Panel Header --}}
            <div class="custom-filter-header" @if($collapsible) data-filter-toggle role="button" tabindex="0" aria-expanded="{{ $isCollapsed ? 'false' : 'true' }}" title="Klik untuk membuka/menutup filter" @endif>
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
                        <button type="button" class="custom-filter-collapse-btn" aria-label="Toggle Filter Panel">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Quick Filter Chips / Presets Row (Optional) --}}
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

            {{-- Panel Body / Form --}}
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

                    {{-- Hidden input for quick preset if applicable --}}
                    @if(!empty($presets))
                        <input type="hidden" name="{{ $presetName }}" value="{{ request($presetName, $activePreset) }}" class="custom-filter-preset-input">
                    @endif

                    {{-- Filter Grid / Fields Container --}}
                    <div class="custom-filter-grid">
                        {{ $slot }}
                    </div>

                    {{-- Dynamic Active Filter Tags / Chips Bar --}}
                    @if($showActiveChips)
                        <div class="custom-filter-active-tags-bar" style="display: none;">
                            <div class="active-tags-label">
                                <i class="fas fa-tags"></i> <span>Filter Diterapkan:</span>
                            </div>
                            <div class="active-tags-container"></div>
                            <button type="button" class="btn-clear-all-tags" title="Hapus semua filter">
                                <i class="fas fa-rotate-left"></i> Reset
                            </button>
                        </div>
                    @endif

                    {{-- Panel Footer Actions --}}
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
                                <button type="button" class="custom-filter-btn custom-filter-btn-pdf" data-filter-export="pdf" title="Cetak / Unduh dokumen format PDF">
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

    @elseif($variant === 'bar')
        {{-- ============================================================= --}}
        {{-- VARIANT: BAR / TOOLBAR (Horizontal Sleek Modern Filter Bar)   --}}
        {{-- ============================================================= --}}
        <div class="custom-filter-bar-container">
            <form 
                action="{{ $action ?? '#' }}" 
                method="{{ strtoupper($method) === 'POST' ? 'POST' : 'GET' }}"
                class="custom-filter-bar-form"
                id="{{ $id }}_form"
            >
                @if(strtoupper($method) === 'POST')
                    @csrf
                @endif

                <div class="custom-filter-bar-content">
                    <div class="custom-filter-bar-lead">
                        <span class="custom-filter-bar-icon"><i class="{{ $icon }}"></i></span>
                        <span class="custom-filter-bar-label">{{ $title }}</span>
                    </div>

                    <div class="custom-filter-bar-fields">
                        {{ $slot }}
                    </div>

                    <div class="custom-filter-bar-actions">
                        @if($showReset)
                            <button type="button" class="custom-filter-btn custom-filter-btn-reset custom-filter-btn-icon-only" data-filter-reset title="{{ $resetText }}">
                                <i class="fas fa-rotate-left"></i>
                            </button>
                        @endif

                        @if($showApply)
                            <button type="submit" class="custom-filter-btn custom-filter-btn-primary" data-filter-apply>
                                <i class="fas fa-filter"></i>
                                <span>{{ $applyText }}</span>
                            </button>
                        @endif

                        @if(isset($extraButtons))
                            {{ $extraButtons }}
                        @endif
                    </div>
                </div>

                @if($showActiveChips)
                    <div class="custom-filter-active-tags-bar custom-filter-bar-tags" style="display: none;">
                        <div class="active-tags-label">
                            <i class="fas fa-tags"></i> <span>Filter Aktif:</span>
                        </div>
                        <div class="active-tags-container"></div>
                        <button type="button" class="btn-clear-all-tags" title="Hapus semua filter">
                            <i class="fas fa-times"></i> Hapus Semua
                        </button>
                    </div>
                @endif
            </form>
        </div>

    @elseif($variant === 'chips')
        {{-- ============================================================= --}}
        {{-- VARIANT: CHIPS (Quick Filter Pill Bar for Status / Category)  --}}
        {{-- ============================================================= --}}
        <div class="custom-filter-chips-wrapper">
            <div class="custom-filter-chips-header">
                @if($icon)<i class="{{ $icon }} custom-filter-chips-lead-icon"></i>@endif
                <span class="custom-filter-chips-title">{{ $title }}</span>
            </div>
            <div class="custom-filter-chips-list" data-preset-group="{{ $presetName }}">
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
                        class="custom-filter-chip-item {{ $isActive ? 'is-active' : '' }}" 
                        data-preset-value="{{ $val }}"
                        data-preset-name="{{ $presetName }}"
                    >
                        @if($ic) <i class="{{ $ic }}"></i> @endif
                        <span>{{ $lbl }}</span>
                        @if(!is_null($cnt))
                            <span class="chip-count">{{ $cnt }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

    @else
        {{-- ============================================================= --}}
        {{-- VARIANT: COMPACT / CUSTOM SLOTTED WRAPPER                    --}}
        {{-- ============================================================= --}}
        <div class="custom-filter-compact-box">
            <form action="{{ $action ?? '#' }}" method="{{ strtoupper($method) === 'POST' ? 'POST' : 'GET' }}" id="{{ $id }}_form">
                @if(strtoupper($method) === 'POST') @csrf @endif
                <div class="custom-filter-compact-inner">
                    {{ $slot }}
                </div>
            </form>
        </div>
    @endif
</div>

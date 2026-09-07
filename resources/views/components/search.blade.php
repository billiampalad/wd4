@props([
    'id' => null,
    'name' => 'search',
    'value' => null,
    'placeholder' => 'Cari data...',
    'size' => 'md', // sm, md, lg
    'variant' => 'default', // default, filled, glass, pill, outline, table
    'width' => '200px', // default width 200px
    'target' => null, // CSS selector untuk live filter (misal: '.um-table tbody tr')
    'emptyTarget' => null, // Selector / ID pesan data kosong (misal: '#userSearchEmptyRow')
    'querySpan' => null, // Selector / ID elemen teks kata kunci (misal: '#userSearchQueryText')
    'countTarget' => null, // Selector / ID elemen jumlah hasil ditemukan
    'clearable' => true,
    'debounce' => 200,
    'action' => null,
    'method' => 'GET',
    'icon' => 'fas fa-search',
    'button' => false,
    'buttonText' => 'Cari',
    'buttonIcon' => null,
    'autofocus' => false,
    'autocomplete' => 'off',
    'disabled' => false,
])

@php
    $id = $id ?? ('customSearch_' . uniqid());
    $value = $value ?? request($name, request('q', ''));
    $hasForm = !empty($action);
@endphp

@if($hasForm)
<form 
    action="{{ $action }}" 
    method="{{ strtoupper($method) === 'POST' ? 'POST' : 'GET' }}" 
    class="custom-search-form"
    role="search"
>
    @if(strtoupper($method) === 'POST')
        @csrf
    @endif
@endif

<div 
    {{ $attributes->class([
        'custom-search-wrapper',
        'custom-search-' . $size,
        'custom-search-' . $variant,
        'has-value' => !empty($value),
        'has-button' => !empty($button),
    ]) }}
    style="width: {{ $width }};"
    data-custom-search
    data-search-id="{{ $id }}"
    @if($target) data-search-target="{{ $target }}" @endif
    @if($emptyTarget) data-search-empty="{{ $emptyTarget }}" @endif
    @if($querySpan) data-search-query-span="{{ $querySpan }}" @endif
    @if($countTarget) data-search-count-target="{{ $countTarget }}" @endif
    @if($debounce) data-search-debounce="{{ $debounce }}" @endif
>
    <div class="custom-search-inner">
        {{-- Search Icon --}}
        <span class="custom-search-icon" aria-hidden="true">
            <i class="{{ $icon }}"></i>
        </span>

        {{-- Search Input --}}
        <input 
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            class="custom-search-input"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            @if($autofocus) autofocus @endif
            @if($disabled) disabled @endif
            aria-label="{{ $placeholder }}"
        />

        {{-- Loading Spinner Indicator --}}
        <span class="custom-search-spinner" aria-hidden="true">
            <i class="fas fa-circle-notch fa-spin"></i>
        </span>

        {{-- Clear Button --}}
        @if($clearable)
            <button 
                type="button" 
                class="custom-search-clear" 
                title="Hapus pencarian"
                aria-label="Hapus pencarian"
                style="{{ empty($value) ? 'display: none;' : '' }}"
            >
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>

    {{-- Optional Submit Button --}}
    @if($button)
        <button type="{{ $hasForm ? 'submit' : 'button' }}" class="custom-search-btn">
            @if($buttonIcon)
                <i class="{{ $buttonIcon }}"></i>
            @endif
            <span>{{ $buttonText }}</span>
        </button>
    @endif

    {{-- Optional Addon / Filter Slot --}}
    @if(isset($slot) && $slot->isNotEmpty())
        <div class="custom-search-addon">
            {{ $slot }}
        </div>
    @endif
</div>

@if($hasForm)
</form>
@endif

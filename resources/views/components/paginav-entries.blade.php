@props([
    'id' => null,
    'target' => null,           // CSS selector target baris tabel (misal: '.um-table tbody tr.um-row')
    'perPage' => 10,            // Default rows per page
    'options' => [5, 10, 25, 50, 100], // Pilihan rows per page
    'labelBefore' => 'Tampilkan:',
    'labelAfter' => 'data',
    'size' => 'md',             // sm, md, lg
    'direction' => 'down',      // down (buka ke bawah jika di header), up (buka ke atas)
])

@php
    $id = $id ?? ('paginav_entries_' . uniqid());
@endphp

<div 
    {{ $attributes->class([
        'paginav-entries-wrapper',
        'paginav-entries-' . $size,
    ]) }}
    id="{{ $id }}"
    data-paginav-entries
    @if($target) data-paginav-target="{{ $target }}" @endif
    x-data="{ 
        open: false, 
        selected: {{ $perPage }},
        select(val) {
            this.selected = val;
            this.open = false;
            const selectEl = $refs.hiddenSelect;
            if (selectEl) {
                selectEl.value = val;
                selectEl.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }"
>
    @if($labelBefore)
        <span class="paginav-entries-label">{{ $labelBefore }}</span>
    @endif

    <div class="paginav-dropdown-custom" @click.outside="open = false">
        {{-- Alpine Trigger Button --}}
        <button 
            type="button" 
            class="paginav-dropdown-trigger" 
            @click="open = !open"
            :aria-expanded="open.toString()" 
            aria-haspopup="listbox"
        >
            <span class="paginav-dropdown-value" x-text="selected"></span>
            <i class="fas fa-chevron-down paginav-dropdown-arrow" :class="{ 'is-open': open }" aria-hidden="true"></i>
        </button>

        {{-- Alpine Floating Dropdown Menu --}}
        <div 
            class="paginav-dropdown-menu menu-{{ $direction }}" 
            x-show="open" 
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 transform scale-95 {{ $direction === 'down' ? '-translate-y-1' : 'translate-y-1' }}"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 {{ $direction === 'down' ? '-translate-y-1' : 'translate-y-1' }}" 
            role="listbox"
        >
            @foreach($options as $opt)
                <button 
                    type="button" 
                    class="paginav-dropdown-item" 
                    :class="{ 'is-active': selected == {{ $opt }} }"
                    @click="select({{ $opt }})" 
                    role="option" 
                    :aria-selected="(selected == {{ $opt }}).toString()"
                >
                    <span class="paginav-dropdown-item-text">{{ $opt }} baris</span>
                    <i class="fas fa-check paginav-dropdown-check" x-show="selected == {{ $opt }}" aria-hidden="true"></i>
                </button>
            @endforeach
        </div>

        {{-- Hidden Native Select for standard JS event listeners --}}
        <select 
            id="{{ $id }}_select" 
            x-ref="hiddenSelect" 
            class="paginav-perpage-select"
            style="position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0;"
            aria-hidden="true"
        >
            @foreach($options as $opt)
                <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>
                    {{ $opt }}
                </option>
            @endforeach
        </select>
    </div>

    @if($labelAfter)
        <span class="paginav-entries-label">{{ $labelAfter }}</span>
    @endif
</div>

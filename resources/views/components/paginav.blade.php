@props([
    'id' => null,
    'target' => null,           // CSS selector untuk target elemen (contoh: '.um-table tbody tr.um-row')
    'paginator' => null,        // Laravel LengthAwarePaginator (opsional jika server-side)
    'perPage' => 10,            // Default item per page
    'perPageOptions' => [5, 10, 25, 50, 100], // Pilihan rows per page
    'showInfo' => true,         // Tampilkan status "Menampilkan X-Y dari Z data"
    'showPerPage' => false,     // Default false jika entries sudah dipasang di atas tabel
    'showJump' => false,        // Tampilkan input lompat ke halaman tertentu
    'align' => 'between',       // between, center, start, end
    'size' => 'md',             // sm, md, lg
    'variant' => 'default',     // default, glass, rounded, minimal
    'emptyNotice' => 'Tidak ada data untuk ditampilkan',
    'alpine' => false,          // Mode integrasi Alpine.js (true jika dikontrol oleh Alpine state parent)
])

@php
    $id = $id ?? ('paginav_' . uniqid());
    $isServerSide = !empty($paginator) && method_exists($paginator, 'hasPages');

    // Fallback data jika server-side
    $total = $isServerSide ? $paginator->total() : 0;
    $currentPage = $isServerSide ? $paginator->currentPage() : 1;
    $lastPage = $isServerSide ? $paginator->lastPage() : 1;
    $from = $isServerSide ? ($paginator->firstItem() ?? 0) : 0;
    $to = $isServerSide ? ($paginator->lastItem() ?? 0) : 0;
    $currentPerPage = $isServerSide ? $paginator->perPage() : $perPage;
@endphp

<div {{ $attributes->class([
    'custom-paginav-wrapper',
    'paginav-' . $align,
    'paginav-' . $size,
    'paginav-' . $variant,
    'is-serverside' => $isServerSide,
    'is-clientside' => !$isServerSide,
    'is-alpine' => $alpine,
]) }} id="{{ $id }}"
    @if(!$alpine) data-custom-paginav @endif
    @if($alpine) x-show="totalFiltered > 0" x-cloak @endif
    @if($target) data-paginav-target="{{ $target }}" @endif 
    data-paginav-per-page="{{ $currentPerPage }}"
    data-paginav-current-page="{{ $currentPage }}" 
    data-paginav-last-page="{{ $lastPage }}"
    data-paginav-total="{{ $total }}" 
    data-paginav-serverside="{{ $isServerSide ? 'true' : 'false' }}" 
    role="navigation"
    aria-label="Navigasi Paginasi Halaman"
>
    {{-- Bagian Kiri: Info Jumlah Data & Rows Per Page --}}
    <div class="paginav-meta-section">
        @if($showInfo)
            <div class="paginav-info" aria-live="polite">
                <div class="paginav-info-badge">
                    <i class="fas fa-layer-group paginav-info-icon" aria-hidden="true"></i>
                    <span class="paginav-info-text">
                        Menampilkan
                        <span class="paginav-badge-highlight">
                            @if($alpine)
                                <span class="paginav-count-from" x-text="startEntry">{{ $from }}</span>–<span class="paginav-count-to" x-text="endEntry">{{ $to }}</span>
                            @else
                                <span class="paginav-count-from">{{ $from }}</span>–<span class="paginav-count-to">{{ $to }}</span>
                            @endif
                        </span>
                        dari
                        <span class="paginav-badge-highlight total">
                            @if($alpine)
                                <span class="paginav-count-total" x-text="totalFiltered">{{ $total }}</span>
                            @else
                                <span class="paginav-count-total">{{ $total }}</span>
                            @endif
                        </span>
                        data
                    </span>
                </div>
            </div>
        @endif

        @if($showPerPage && count($perPageOptions) > 1)
            <div 
                class="paginav-perpage-wrap" 
                x-data="{ 
                    open: false, 
                    selected: {{ $currentPerPage }},
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
                <span class="paginav-perpage-label">Tampilkan:</span>
                <div class="paginav-dropdown-custom" @click.outside="open = false">
                    {{-- Alpine Trigger Button --}}
                    <button type="button" class="paginav-dropdown-trigger" @click="open = !open"
                        :aria-expanded="open.toString()" aria-haspopup="listbox">
                        <span class="paginav-dropdown-value" x-text="selected"></span>
                        <i class="fas fa-chevron-down paginav-dropdown-arrow" :class="{ 'is-open': open }" aria-hidden="true"></i>
                    </button>

                    {{-- Alpine Floating Dropdown Menu (Membuka ke atas jika di footer) --}}
                    <div class="paginav-dropdown-menu menu-up" x-show="open" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 transform scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 transform scale-95 translate-y-1" 
                        role="listbox"
                    >
                        @foreach($perPageOptions as $opt)
                            <button type="button" class="paginav-dropdown-item" :class="{ 'is-active': selected == {{ $opt }} }"
                                @click="select({{ $opt }})" role="option" :aria-selected="(selected == {{ $opt }}).toString()">
                                <span class="paginav-dropdown-item-text">{{ $opt }} baris</span>
                                <i class="fas fa-check paginav-dropdown-check" x-show="selected == {{ $opt }}" aria-hidden="true"></i>
                            </button>
                        @endforeach
                    </div>

                    {{-- Hidden Native Select for standard JS listener fallback --}}
                    <select id="{{ $id }}_perpage" x-ref="hiddenSelect" class="paginav-perpage-select"
                        style="position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0;"
                        aria-hidden="true"
                    >
                        @foreach($perPageOptions as $opt)
                            <option value="{{ $opt }}" {{ $currentPerPage == $opt ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <span class="paginav-perpage-label">data</span>
            </div>
        @endif
    </div>

    {{-- Bagian Kanan: Tombol Navigasi Halaman --}}
    <div class="paginav-controls-section">
        {{-- Tombol First / Awal --}}
        <button type="button" class="paginav-btn paginav-btn-first" title="Halaman Pertama" aria-label="Halaman Pertama"
            data-page-action="first" 
            @if($alpine)
                @click="goToPage(1)" :disabled="currentPage === 1"
            @else
                {{ $currentPage <= 1 ? 'disabled' : '' }}
            @endif
        >
            <i class="fas fa-angles-left" aria-hidden="true"></i>
        </button>

        {{-- Tombol Previous / Sebelumnya --}}
        <button type="button" class="paginav-btn paginav-btn-prev" title="Halaman Sebelumnya"
            aria-label="Halaman Sebelumnya" data-page-action="prev" 
            @if($alpine)
                @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
            @else
                {{ $currentPage <= 1 ? 'disabled' : '' }}
            @endif
        >
            <i class="fas fa-chevron-left" aria-hidden="true"></i>
            <span class="paginav-btn-label">Prev</span>
        </button>

        {{-- Container Nomor Halaman Dinamis (Smart Truncation Ellipsis) --}}
        <div class="paginav-pages-container" role="list">
            @if($alpine)
                <template x-for="page in pageNumbers()" :key="page">
                    <button type="button" class="paginav-page-item" :class="{ 'is-active': page === currentPage }"
                        @click="goToPage(page)" x-text="page"></button>
                </template>
            @elseif($isServerSide)
                {{-- Server-side render link Laravel --}}
                @for($p = 1; $p <= $lastPage; $p++)
                    @if($p == 1 || $p == $lastPage || ($p >= $currentPage - 1 && $p <= $currentPage + 1))
                        <a href="{{ $paginator->url($p) }}" class="paginav-page-item {{ $p == $currentPage ? 'is-active' : '' }}"
                            aria-current="{{ $p == $currentPage ? 'page' : 'false' }}">
                            {{ $p }}
                        </a>
                    @elseif($p == $currentPage - 2 || $p == $currentPage + 2)
                        <span class="paginav-ellipsis" aria-hidden="true">&hellip;</span>
                    @endif
                @endfor
            @else
                {{-- Client-side placeholder (Di-generate dan di-manage oleh paginav.js) --}}
                <button type="button" class="paginav-page-item is-active" data-page="1">1</button>
            @endif
        </div>

        {{-- Tombol Next / Selanjutnya --}}
        <button type="button" class="paginav-btn paginav-btn-next" title="Halaman Selanjutnya"
            aria-label="Halaman Selanjutnya" data-page-action="next" 
            @if($alpine)
                @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
            @else
                {{ $currentPage >= $lastPage ? 'disabled' : '' }}
            @endif
        >
            <span class="paginav-btn-label">Next</span>
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </button>

        {{-- Tombol Last / Terakhir --}}
        <button type="button" class="paginav-btn paginav-btn-last" title="Halaman Terakhir"
            aria-label="Halaman Terakhir" data-page-action="last" 
            @if($alpine)
                @click="goToPage(totalPages)" :disabled="currentPage === totalPages"
            @else
                {{ $currentPage >= $lastPage ? 'disabled' : '' }}
            @endif
        >
            <i class="fas fa-angles-right" aria-hidden="true"></i>
        </button>

        {{-- Opsi Jump to Page (jika diaktifkan) --}}
        @if($showJump)
            <div class="paginav-jump-wrap">
                <span class="paginav-jump-label">Lompat:</span>
                <input type="number" min="1" max="{{ $lastPage }}" class="paginav-jump-input" placeholder="#"
                    aria-label="Lompat ke halaman" />
            </div>
        @endif
    </div>
</div>
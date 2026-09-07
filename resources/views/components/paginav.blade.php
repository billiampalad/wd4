@props([
    'id' => null,
    'target' => null,           // CSS selector untuk target elemen (contoh: '.um-table tbody tr.um-row')
    'paginator' => null,        // Laravel LengthAwarePaginator (opsional jika server-side)
    'perPage' => 10,            // Default item per page
    'perPageOptions' => [5, 10, 25, 50, 100], // Pilihan rows per page
    'showInfo' => true,         // Tampilkan status "Menampilkan X-Y dari Z data"
    'showPerPage' => true,      // Tampilkan dropdown pilihan rows per page
    'showJump' => false,        // Tampilkan input lompat ke halaman tertentu
    'align' => 'between',       // between, center, start, end
    'size' => 'md',             // sm, md, lg
    'variant' => 'default',     // default, glass, rounded, minimal
    'emptyNotice' => 'Tidak ada data untuk ditampilkan',
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

<div 
    {{ $attributes->class([
        'custom-paginav-wrapper',
        'paginav-' . $align,
        'paginav-' . $size,
        'paginav-' . $variant,
        'is-serverside' => $isServerSide,
        'is-clientside' => !$isServerSide,
    ]) }}
    id="{{ $id }}"
    data-custom-paginav
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
                <span class="paginav-info-text">
                    Menampilkan 
                    <strong class="paginav-count-from">{{ $from }}</strong>–<strong class="paginav-count-to">{{ $to }}</strong> 
                    dari 
                    <strong class="paginav-count-total">{{ $total }}</strong> data
                </span>
            </div>
        @endif

        @if($showPerPage && count($perPageOptions) > 1)
            <div class="paginav-perpage-wrap">
                <label for="{{ $id }}_perpage" class="paginav-perpage-label">Baris:</label>
                <div class="paginav-select-custom">
                    <select id="{{ $id }}_perpage" class="paginav-perpage-select" aria-label="Jumlah baris per halaman">
                        @foreach($perPageOptions as $opt)
                            <option value="{{ $opt }}" {{ $currentPerPage == $opt ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down paginav-select-arrow" aria-hidden="true"></i>
                </div>
            </div>
        @endif
    </div>

    {{-- Bagian Kanan: Tombol Navigasi Halaman --}}
    <div class="paginav-controls-section">
        {{-- Tombol First / Awal --}}
        <button 
            type="button" 
            class="paginav-btn paginav-btn-first" 
            title="Halaman Pertama" 
            aria-label="Halaman Pertama"
            data-page-action="first"
            {{ $currentPage <= 1 ? 'disabled' : '' }}
        >
            <i class="fas fa-angles-left" aria-hidden="true"></i>
        </button>

        {{-- Tombol Previous / Sebelumnya --}}
        <button 
            type="button" 
            class="paginav-btn paginav-btn-prev" 
            title="Halaman Sebelumnya" 
            aria-label="Halaman Sebelumnya"
            data-page-action="prev"
            {{ $currentPage <= 1 ? 'disabled' : '' }}
        >
            <i class="fas fa-chevron-left" aria-hidden="true"></i>
            <span class="paginav-btn-label">Prev</span>
        </button>

        {{-- Container Nomor Halaman Dinamis --}}
        <div class="paginav-pages-container" role="list">
            @if($isServerSide)
                {{-- Server-side render link Laravel --}}
                @for($p = 1; $p <= $lastPage; $p++)
                    @if($p == 1 || $p == $lastPage || ($p >= $currentPage - 1 && $p <= $currentPage + 1))
                        <a 
                            href="{{ $paginator->url($p) }}" 
                            class="paginav-page-item {{ $p == $currentPage ? 'is-active' : '' }}"
                            aria-current="{{ $p == $currentPage ? 'page' : 'false' }}"
                        >
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
        <button 
            type="button" 
            class="paginav-btn paginav-btn-next" 
            title="Halaman Selanjutnya" 
            aria-label="Halaman Selanjutnya"
            data-page-action="next"
            {{ $currentPage >= $lastPage ? 'disabled' : '' }}
        >
            <span class="paginav-btn-label">Next</span>
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </button>

        {{-- Tombol Last / Terakhir --}}
        <button 
            type="button" 
            class="paginav-btn paginav-btn-last" 
            title="Halaman Terakhir" 
            aria-label="Halaman Terakhir"
            data-page-action="last"
            {{ $currentPage >= $lastPage ? 'disabled' : '' }}
        >
            <i class="fas fa-angles-right" aria-hidden="true"></i>
        </button>

        {{-- Opsi Jump to Page (jika diaktifkan) --}}
        @if($showJump)
            <div class="paginav-jump-wrap">
                <span class="paginav-jump-label">Lompat:</span>
                <input 
                    type="number" 
                    min="1" 
                    max="{{ $lastPage }}" 
                    class="paginav-jump-input" 
                    placeholder="#"
                    aria-label="Lompat ke halaman"
                />
            </div>
        @endif
    </div>
</div>

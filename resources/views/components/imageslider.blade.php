@props([
    'images' => null,
])

@if(!empty($images) && count($images) > 0)
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/auth/imageslider.css') }}">
    @endpush

    <!-- ═══════════════════════════════════════════════════════════════
       KOMPONEN SLIDER IMAGE SHOWCASE
       5-Card Step Showcase Slider (Frameless Images with Ambient Glow)
       ═══════════════════════════════════════════════════════════════ -->
    <section class="showcase-section" id="campusShowcase">

        <!-- Header Aksen Elegan & Kontrol Navigasi -->
        <div class="showcase-header">
            <div class="showcase-header-left">
                <div class="showcase-pill-badge">
                    <span class="badge-live-pulse"></span>
                    <span>Partnership & Award Showcase</span>
                </div>
                <h2 class="showcase-main-title">
                    Dokumentasi & <span class="title-gradient-accent">Penghargaan Kerjasama</span>
                </h2>
                <p class="showcase-subtitle">
                    Pencapaian prestasi, sertifikat kemitraan strategis, dan momentum kolaborasi institusi Politeknik Negeri Manado.
                </p>
            </div>

            <div class="showcase-header-right">
                <div class="slider-counter-pill" id="sliderCounterPill">
                    <i class="fa-solid fa-layer-group" style="color:var(--primary); font-size:12px;"></i>
                    <span id="sliderCurrentNum">01</span> / <span id="sliderTotalNum">{{ str_pad(count($images), 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <button type="button" class="slider-nav-btn" id="btnPrevSlide" aria-label="Slide Sebelumnya" title="Slide Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" class="slider-nav-btn" id="btnNextSlide" aria-label="Slide Selanjutnya" title="Slide Selanjutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="showcase-container">
            <div class="carousel-outer-wrap" id="stepViewport">
                <div class="carousel-step-track" id="stepTrack">
                    @foreach($images as $img)
                        <div class="gallery-card-frame">
                            <img src="{{ asset($img->image ?? $img['image'] ?? $img->image_path ?? $img) }}"
                                alt="{{ $img->judul ?? $img->title ?? $img['judul'] ?? $img['title'] ?? 'Dokumentasi Penghargaan Kerjasama' }}"
                                class="gallery-img-inner" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ LIGHTBOX MODAL (PURE IMAGE + SIDE ARROWS & COUNTER) ═══ -->
    <div class="lightbox-modal" id="lightboxModal" role="dialog" aria-modal="true" aria-label="Pratinjau Gambar">
        <!-- Floating Info Header -->
        <div class="lightbox-top-bar">
            <span class="lightbox-counter-badge" id="lightboxCounter">01 / {{ str_pad(count($images), 2, '0', STR_PAD_LEFT) }}</span>
            <span style="opacity:0.4;">•</span>
            <span id="lightboxCaption">Dokumentasi Penghargaan Kerjasama</span>
        </div>

        <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Tutup Pratinjau">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <button type="button" class="lightbox-nav-arrow lightbox-arrow-prev" id="lightboxPrev" aria-label="Gambar Sebelumnya">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="lightbox-content">
            <img src="" alt="Pratinjau Gambar" class="lightbox-img" id="lightboxImg">
        </div>

        <button type="button" class="lightbox-nav-arrow lightbox-arrow-next" id="lightboxNext" aria-label="Gambar Selanjutnya">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    @push('scripts')
        <script src="{{ asset('js/auth/imageslider.js') }}"></script>
    @endpush
@endif
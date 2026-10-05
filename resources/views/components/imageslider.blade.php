@props([
    'images' => null,
])

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
                Pencapaian prestasi, sertifikat kemitraan strategis, dan momentum kolaborasi institusi Politeknik Negeri
                Manado.
            </p>
        </div>

        <div class="showcase-header-right">
            <div class="slider-counter-pill" id="sliderCounterPill">
                <i class="fa-solid fa-layer-group" style="color:var(--primary); font-size:12px;"></i>
                <span id="sliderCurrentNum">01</span> / <span id="sliderTotalNum">08</span>
            </div>
            <button type="button" class="slider-nav-btn" id="btnPrevSlide" aria-label="Slide Sebelumnya"
                title="Slide Sebelumnya">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="slider-nav-btn" id="btnNextSlide" aria-label="Slide Selanjutnya"
                title="Slide Selanjutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="showcase-container">
        <div class="carousel-outer-wrap" id="stepViewport">
            <div class="carousel-step-track" id="stepTrack">

                @if(!empty($images) && count($images) > 0)
                    @foreach($images as $img)
                        <div class="gallery-card-frame">
                            <img src="{{ asset($img->image ?? $img['image'] ?? $img) }}"
                                alt="{{ $img->title ?? $img['judul'] ?? 'Dokumentasi Penghargaan Kerjasama' }}"
                                class="gallery-img-inner" loading="lazy">
                        </div>
                    @endforeach
                @else
                    <!-- Default Curated Showcase Images -->
                    <div class="gallery-card-frame">
                        <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?q=80&w=800&auto=format&fit=crop"
                            alt="Polimart Teaching Factory" class="gallery-img-inner" loading="lazy">
                    </div>

                    <div class="gallery-card-frame">
                        <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=800&auto=format&fit=crop"
                            alt="TEFA Bar & Cafe" class="gallery-img-inner" loading="lazy">
                    </div>

                    <div class="gallery-card-frame">
                        <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=800&auto=format&fit=crop"
                            alt="Lounge & Co-Working Space" class="gallery-img-inner" loading="lazy">
                    </div>

                    <div class="gallery-card-frame">
                        <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?q=80&w=800&auto=format&fit=crop"
                            alt="Asrama Mahasiswa Kampus" class="gallery-img-inner" loading="lazy">
                    </div>

                    <div class="gallery-card-frame">
                        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=800&auto=format&fit=crop"
                            alt="GOR Indoor Polimdo" class="gallery-img-inner" loading="lazy">
                    </div>

                    <div class="gallery-card-frame">
                        <img src="https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=800&auto=format&fit=crop"
                            alt="Auditorium & Theater" class="gallery-img-inner" loading="lazy">
                    </div>

                    <div class="gallery-card-frame">
                        <img src="{{ asset('img/gedung.jpeg') }}" alt="Gedung Kuliah Terpadu (GKT)"
                            class="gallery-img-inner" loading="lazy">
                    </div>

                    <div class="gallery-card-frame">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=800&auto=format&fit=crop"
                            alt="Galeri Investasi & Lab Modern" class="gallery-img-inner" loading="lazy">
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

<!-- ═══ LIGHTBOX MODAL (PURE IMAGE + SIDE ARROWS & COUNTER) ═══ -->
<div class="lightbox-modal" id="lightboxModal" role="dialog" aria-modal="true" aria-label="Pratinjau Gambar">
    <!-- Floating Info Header -->
    <div class="lightbox-top-bar">
        <span class="lightbox-counter-badge" id="lightboxCounter">01 / 08</span>
        <span style="opacity:0.4;">•</span>
        <span id="lightboxCaption">Dokumentasi Penghargaan Kerjasama</span>
    </div>

    <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Tutup Pratinjau">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <button type="button" class="lightbox-nav-arrow lightbox-arrow-prev" id="lightboxPrev"
        aria-label="Gambar Sebelumnya">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div class="lightbox-content">
        <img src="" alt="Pratinjau Gambar" class="lightbox-img" id="lightboxImg">
    </div>

    <button type="button" class="lightbox-nav-arrow lightbox-arrow-next" id="lightboxNext"
        aria-label="Gambar Selanjutnya">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>

@push('scripts')
    <script src="{{ asset('js/auth/imageslider.js') }}"></script>
@endpush
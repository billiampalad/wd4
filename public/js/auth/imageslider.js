/**
 * ═══════════════════════════════════════════════════════════════
 * 5-CARD STEP SHOWCASE SLIDER & LIGHTBOX ENGINE
 * Infinite Seamless Step Sliding (+1 Per Step) with Symmetrical Spacing
 * ═══════════════════════════════════════════════════════════════
 */

document.addEventListener('DOMContentLoaded', () => {
    const viewport = document.getElementById('stepViewport');
    const track = document.getElementById('stepTrack');

    if (!viewport || !track) return;

    // Header Elements
    const sliderCurrentNum = document.getElementById('sliderCurrentNum');
    const sliderTotalNum = document.getElementById('sliderTotalNum');
    const btnPrevSlide = document.getElementById('btnPrevSlide');
    const btnNextSlide = document.getElementById('btnNextSlide');

    // Lightbox Elements
    const lightbox = document.getElementById('lightboxModal');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const lightboxCounter = document.getElementById('lightboxCounter');
    const lightboxCaption = document.getElementById('lightboxCaption');

    // 1. GATHER UNIQUE IMAGE LIST & METADATA FOR LIGHTBOX
    const originalCards = Array.from(track.children);
    const originalCount = originalCards.length;
    if (originalCount === 0) return;

    const itemsData = originalCards.map(card => {
        const img = card.querySelector('img');
        return {
            src: img ? img.src : '',
            alt: img ? (img.alt || 'Penghargaan Kerjasama') : 'Penghargaan Kerjasama'
        };
    });

    if (sliderTotalNum) {
        sliderTotalNum.textContent = String(originalCount).padStart(2, '0');
    }

    // 2. CLONE ITEMS TO ENABLE SEAMLESS INFINITE STEP SLIDING
    originalCards.forEach(card => {
        const clone = card.cloneNode(true);
        track.appendChild(clone);
    });

    const GAP = 18; // 18px jarak celah antar kartu & margin tepi
    const STEP_DURATION = 3500; // 3.5 Detik per interval slide
    let currentIndex = 0;
    let stepInterval = null;
    let isHovered = false;
    let isPausedManually = false;

    // 3. KALKULASI UKURAN KARTU SIMETRIS
    function calculateCardWidth() {
        const containerWidth = viewport.clientWidth || window.innerWidth;
        let visibleCount = 5;
        if (window.innerWidth <= 640) visibleCount = 2;
        else if (window.innerWidth <= 1024) visibleCount = 3;
        else visibleCount = 5;

        const totalSpacing = (visibleCount + 1) * GAP;
        const cardWidth = (containerWidth - totalSpacing) / visibleCount;

        document.documentElement.style.setProperty('--card-item-width', `${cardWidth}px`);
        return cardWidth + GAP;
    }

    let stepWidth = calculateCardWidth();
    window.addEventListener('resize', () => {
        stepWidth = calculateCardWidth();
        track.style.transition = 'none';
        track.style.transform = `translate3d(${-currentIndex * stepWidth}px, 0, 0)`;
    });

    function updateCounter() {
        if (!sliderCurrentNum) return;
        const activeIndex = (currentIndex % originalCount) + 1;
        sliderCurrentNum.textContent = String(activeIndex).padStart(2, '0');
    }

    // 4. STEP-BY-STEP SLIDE ENGINE
    function slideNext() {
        currentIndex++;
        track.style.transition = 'transform 0.65s cubic-bezier(0.25, 1, 0.5, 1)';
        track.style.transform = `translate3d(${-currentIndex * stepWidth}px, 0, 0)`;
        updateCounter();

        // Ketika sudah bergeser sejauh total kartu asli, reset ke index 0 secara invisible
        if (currentIndex >= originalCount) {
            setTimeout(() => {
                track.style.transition = 'none';
                currentIndex = 0;
                track.style.transform = `translate3d(0px, 0, 0)`;
                updateCounter();
            }, 650);
        }
    }

    function slidePrev() {
        if (currentIndex <= 0) {
            track.style.transition = 'none';
            currentIndex = originalCount;
            track.style.transform = `translate3d(${-currentIndex * stepWidth}px, 0, 0)`;
        }
        setTimeout(() => {
            currentIndex--;
            track.style.transition = 'transform 0.65s cubic-bezier(0.25, 1, 0.5, 1)';
            track.style.transform = `translate3d(${-currentIndex * stepWidth}px, 0, 0)`;
            updateCounter();
        }, 20);
    }

    function startStepTimer() {
        clearInterval(stepInterval);
        if (isPausedManually) return;

        stepInterval = setInterval(() => {
            if (!isHovered && !isPausedManually) {
                slideNext();
            }
        }, STEP_DURATION);
    }

    startStepTimer();

    // Header Nav Buttons
    if (btnNextSlide) {
        btnNextSlide.addEventListener('click', () => {
            slideNext();
            startStepTimer();
        });
    }

    if (btnPrevSlide) {
        btnPrevSlide.addEventListener('click', () => {
            slidePrev();
            startStepTimer();
        });
    }

    // Hover Pause
    viewport.addEventListener('mouseenter', () => { isHovered = true; });
    viewport.addEventListener('mouseleave', () => { isHovered = false; });

    // 5. LIGHTBOX LOGIC
    let currentLightboxIndex = 0;

    function showLightboxImage(index) {
        if (!lightbox || !lightboxImg) return;
        if (index < 0) index = itemsData.length - 1;
        if (index >= itemsData.length) index = 0;
        currentLightboxIndex = index;

        lightboxImg.style.opacity = '0.3';
        lightboxImg.style.transform = 'scale(0.98)';

        const item = itemsData[currentLightboxIndex];
        if (lightboxCounter) {
            lightboxCounter.textContent = `${String(currentLightboxIndex + 1).padStart(2, '0')} / ${String(itemsData.length).padStart(2, '0')}`;
        }
        if (lightboxCaption) {
            lightboxCaption.textContent = item.alt;
        }

        setTimeout(() => {
            lightboxImg.src = item.src;
            lightboxImg.alt = item.alt;
            lightboxImg.style.opacity = '1';
            lightboxImg.style.transform = 'scale(1)';
        }, 100);
    }

    // Click Card to Open Lightbox
    track.addEventListener('click', (e) => {
        const card = e.target.closest('.gallery-card-frame');
        if (!card || !lightbox) return;
        const img = card.querySelector('img');
        if (img) {
            const foundIndex = itemsData.findIndex(item => item.src === img.src);
            showLightboxImage(foundIndex !== -1 ? foundIndex : 0);
            lightbox.classList.add('open');
        }
    });

    // Lightbox Prev & Next
    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', (e) => {
            e.stopPropagation();
            showLightboxImage(currentLightboxIndex - 1);
        });
    }

    if (lightboxNext) {
        lightboxNext.addEventListener('click', (e) => {
            e.stopPropagation();
            showLightboxImage(currentLightboxIndex + 1);
        });
    }

    function closeLightbox() {
        if (lightbox) lightbox.classList.remove('open');
    }

    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightbox) {
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }

    // Keyboard Navigation (Arrow Keys & Escape)
    document.addEventListener('keydown', (e) => {
        if (!lightbox || !lightbox.classList.contains('open')) return;

        if (e.key === 'ArrowLeft') {
            showLightboxImage(currentLightboxIndex - 1);
        } else if (e.key === 'ArrowRight') {
            showLightboxImage(currentLightboxIndex + 1);
        } else if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // Expose dynamic helper jika dibutuhkan oleh modul upload
    window.ShowcaseSlider = {
        slideNext,
        slidePrev,
        calculateCardWidth,
        itemsData
    };
});

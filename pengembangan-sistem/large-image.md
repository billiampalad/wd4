# Desain & Arsitektur Fitur Large Image Showcase Slider (Image-Only Database)

Dokumen ini merinci konsep desain visual, arsitektur teknis, dan implementasi fitur **Large Image Showcase Slider** yang ditempatkan pada bagian bawah halaman utama ([welcome.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/welcome.blade.php)) sebelum footer, di mana database **hanya menyimpan atribut gambar (`image`)** yang diunggah oleh akun **Humas** melalui dropdown sidebar ([unit.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/unit.blade.php)).

---

## 1. Konsep & Struktur Visual Halaman Welcome

### 1.1 Posisi Hierarki Halaman
Bagian visual showcase ini ditempatkan tepat di atas *Footer* (setelah section Data Kerjasama), berfungsi sebagai *Visual Focal Point* untuk menampilkan dokumentasi fasilitas, kampus, kegiatan, atau proyek secara sinematik:

```text
               HERO & STATS
                     │
                     ▼
           VISUALISASI & PETA MITRA
                     │
                     ▼
             DATA KERJASAMA
                     │
                     ▼
  ┌───────────────────────────────────────────────┐
  │         LARGE IMAGE SHOWCASE SLIDER           │
  │   - Full / Wide Focal Large Image Display     │
  │   - Cinematic Scale & Fade Transition         │
  │   - Dynamic Image Indicators / Thumbnail Bar  │
  │   - Auto-Play Timer with Progress Bar         │
  └───────────────────────────────────────────────┘
                     │
                     ▼
                  FOOTER
```

---

## 2. Karakter Desain & Pengalaman Visual (Cinematic Image Showcase)

### 2.1 Visual Focal Point (Clean & Modern)
Karena data hanya berfokus pada gambar, desain dibuat bersih (*clean minimalism*), elegan, dan memusatkan seluruh perhatian pengunjung pada kualitas visual gambar:

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│  [ DOKUMENTASI & FASILITAS KAMPUS ]                           01 / 04       │
│                                                                             │
│  ┌───────────────────────────────────────────────────────────────────────┐  │
│  │                                                                       │  │
│  │                                                                       │  │
│  │                                                                       │  │
│  │                       LARGE FOCAL IMAGE                               │  │
│  │                  (Cinematic Scale & Transition)                       │  │
│  │                                                                       │  │
│  │                                                                       │  │
│  │ ────────────────────────────────────────────────────────────────────  │  │
│  │ Progress Auto-Play Bar                                                │  │
│  └───────────────────────────────────────────────────────────────────────┘  │
│                                                                             │
│  [ PREV ]   (● 01)   (○ 02)   (○ 03)   (○ 04)   [ NEXT ]                    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Efek Transisi Gambar
1. **Gambar Keluar (Exit):**
   - `opacity: 1 → 0`
   - `transform: scale(1) → scale(1.05)`
2. **Gambar Masuk (Enter):**
   - `opacity: 0 → 1`
   - `transform: scale(1.05) → scale(1)`
   - `transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1)`
3. **Progress Bar Otomatis:**
   - Garis animasi linier (misal 5 detik) di bawah frame gambar sebelum beralih ke slide berikutnya.

---

## 3. Skema Database & Model (Hanya Atribut Image)

Sesuai kebutuhan, tabel database dirancang sangat sederhana dan hanya menyimpan path file gambar yang diunggah.

### 3.1 Migration: `create_showcase_images_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('showcase_images', function (Blueprint $table) {
            $table->id();
            $table->string('image'); // Menyimpan path file gambar (e.g. "uploads/showcase/gkt.jpg")
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showcase_images');
    }
};
```

### 3.2 Model: `App\Models\ShowcaseImage.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowcaseImage extends Model
{
    protected $table = 'showcase_images';

    protected $fillable = [
        'image',
    ];
}
```

---

## 4. Integrasi Menu Sidebar untuk Akun Humas

Pada file [unit.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/unit.blade.php), menu pengelolaan Showcase ditambahkan ke dalam dropdown **Kerjasama** agar staf Humas/Unit dapat langsung mengelola galeri gambar.

### 4.1 Modifikasi Sidebar [unit.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/unit.blade.php#L380-L403)
Tambahkan item submenu **"Upload Gambar Beranda"**:

```blade
<div class="submenu {{ $isDataKerjasamaActive ? 'open' : '' }}" id="kerjasamaSub">
    <div class="submenu-inner">
        <a class="submenu-item {{ request()->routeIs('unit.dkerjasama', 'unit.kerjasama.*') ? 'active' : '' }}"
            href="{{ route('unit.dkerjasama') }}">
            <span class="submenu-dot"></span><span>Repositori</span>
        </a>
        <a class="submenu-item {{ request()->routeIs('unit.pengajuan_perpanjangan', 'unit.pengajuan_perpanjangan.*') ? 'active' : '' }}"
            href="{{ route('unit.pengajuan_perpanjangan') }}">
            <span class="submenu-dot"></span><span>Pengajuan Perpanjangan</span>
        </a>
        <a class="submenu-item {{ request()->routeIs('unit.mitra', 'unit.mitra.*') ? 'active' : '' }}"
            href="{{ route('unit.mitra') }}">
            <span class="submenu-dot"></span><span>Mitra</span>
        </a>
        <a class="submenu-item {{ request()->routeIs('unit.form', 'unit.form.*') ? 'active' : '' }}"
            href="{{ route('unit.form') }}">
            <span class="submenu-dot"></span><span>Form Laporan</span>
        </a>
        {{-- Item Menu Pengelolaan Gambar Showcase --}}
        <a class="submenu-item {{ request()->routeIs('unit.showcase.*') ? 'active' : '' }}"
            href="{{ route('unit.showcase.index') }}">
            <span class="submenu-dot"></span><span>Galeri Beranda</span>
        </a>
    </div>
</div>
```

---

## 5. Modul Pengelolaan Humas (Controller & View)

### 5.1 Controller: `App\Http\Controllers\Unit\ShowcaseController.php`
```php
namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\ShowcaseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ShowcaseController extends Controller
{
    public function index()
    {
        $images = ShowcaseImage::latest()->get();
        return view('unit.showcase.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // Maks 5MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/showcase'), $filename);

            ShowcaseImage::create([
                'image' => 'uploads/showcase/' . $filename,
            ]);
        }

        return redirect()->back()->with('success', 'Gambar berhasil diupload dan otomatis tampil di halaman utama!');
    }

    public function destroy($id)
    {
        $showcase = ShowcaseImage::findOrFail($id);

        // Hapus file fisik dari public path jika ada
        if (File::exists(public_path($showcase->image))) {
            File::delete(public_path($showcase->image));
        }

        $showcase->delete();

        return redirect()->back()->with('success', 'Gambar berhasil dihapus.');
    }
}
```

### 5.2 Rute Web (`routes/web.php`)
```php
Route::middleware(['auth'])->prefix('unit')->name('unit.')->group(function () {
    Route::get('/showcase', [App\Http\Controllers\Unit\ShowcaseController::class, 'index'])->name('showcase.index');
    Route::post('/showcase', [App\Http\Controllers\Unit\ShowcaseController::class, 'store'])->name('showcase.store');
    Route::delete('/showcase/{id}', [App\Http\Controllers\Unit\ShowcaseController::class, 'destroy'])->name('showcase.destroy');
});
```

---

## 6. Implementasi Komponen di `welcome.blade.php`

### 6.1 Backend Delivery (Controller Halaman Welcome)
Pada controller yang menampilkan halaman welcome (misal `WelcomeController` atau route closure):
```php
$showcases = \App\Models\ShowcaseImage::latest()->get();

return view('auth.welcome', compact('...', 'showcases'));
```

### 6.2 Struktur Blade di [welcome.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/welcome.blade.php) (Sebelum `<footer>`)

```blade
@if(isset($showcases) && $showcases->count() > 0)
<!-- ═══ LARGE IMAGE SHOWCASE SLIDER SECTION ═════════════════════ -->
<section class="showcase-section" id="image-showcase" aria-label="Galeri Gambar Kampus">
    <div class="showcase-container">
        <!-- Header & Counter -->
        <div class="showcase-header">
            <div class="showcase-badge">
                <span class="showcase-badge-dot"></span>
                <span>GALERI & DOKUMENTASI VISUAL</span>
            </div>
            <div class="showcase-counter" id="showcaseCounter">
                <span class="curr-idx">01</span> / <span class="total-idx">{{ sprintf('%02d', $showcases->count()) }}</span>
            </div>
        </div>

        <!-- Large Image Stage -->
        <div class="showcase-viewport">
            @foreach($showcases as $index => $item)
                <div class="showcase-slide {{ $index === 0 ? 'is-active' : '' }}" 
                     data-index="{{ $index }}"
                     style="background-image: url('{{ asset($item->image) }}');">
                    <div class="showcase-overlay"></div>
                </div>
            @endforeach

            <!-- Auto-play Progress Line -->
            <div class="showcase-progress-bar">
                <div class="showcase-progress-fill" id="showcaseProgress"></div>
            </div>

            <!-- Floating Arrow Controls -->
            <button type="button" class="showcase-arrow showcase-arrow-prev" id="showcasePrev" aria-label="Gambar Sebelumnya">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </button>
            <button type="button" class="showcase-arrow showcase-arrow-next" id="showcaseNext" aria-label="Gambar Selanjutnya">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Dot / Number Indicators -->
        <div class="showcase-nav-dots" role="tablist">
            @foreach($showcases as $index => $item)
                <button type="button" 
                        class="showcase-dot {{ $index === 0 ? 'is-active' : '' }}" 
                        data-target-index="{{ $index }}"
                        role="tab"
                        aria-label="Slide {{ $index + 1 }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    <span class="dot-num">{{ sprintf('%02d', $index + 1) }}</span>
                </button>
            @endforeach
        </div>
    </div>
</section>
@endif
```

---

## 7. Desain CSS Responsif & Dark Mode

```css
/* Container & Section */
.showcase-section {
    padding: 70px 24px 80px;
    background: linear-gradient(180deg, var(--bg-surface, #ffffff) 0%, var(--bg-alt, #f8fafc) 100%);
    position: relative;
    overflow: hidden;
}

[data-theme="dark"] .showcase-section {
    background: linear-gradient(180deg, #091a2f 0%, #060e18 100%);
}

.showcase-container {
    max-width: 1240px;
    margin: 0 auto;
}

/* Header */
.showcase-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.showcase-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #0284c7;
}

.showcase-badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #0284c7;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.2);
}

.showcase-counter {
    font-family: 'DM Sans', sans-serif;
    font-weight: 700;
    color: var(--ink-faint, #64748b);
}

.showcase-counter .curr-idx {
    font-size: 1.4rem;
    color: #0284c7;
}

/* Large Viewport Frame */
.showcase-viewport {
    position: relative;
    width: 100%;
    height: 520px;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 45px -15px rgba(0, 0, 0, 0.12);
    border: 1px solid var(--border-color, rgba(226, 232, 240, 0.8));
    background-color: #0b1320;
}

@media (max-width: 768px) {
    .showcase-viewport {
        height: 320px;
        border-radius: 16px;
    }
}

/* Slide Layers */
.showcase-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transform: scale(1.05);
    transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
    pointer-events: none;
}

.showcase-slide.is-active {
    opacity: 1;
    transform: scale(1);
    pointer-events: auto;
    z-index: 2;
}

.showcase-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.3) 100%);
}

/* Progress Bar */
.showcase-progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    z-index: 10;
}

.showcase-progress-fill {
    height: 100%;
    width: 0%;
    background: #0284c7;
}

/* Floating Arrows */
.showcase-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    color: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 15;
    transition: all 0.25s ease;
}

.showcase-arrow:hover {
    background: #ffffff;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.showcase-arrow-prev { left: 20px; }
.showcase-arrow-next { right: 20px; }

/* Navigation Indicators */
.showcase-nav-dots {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 24px;
}

.showcase-dot {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 16px;
    border-radius: 999px;
    background: var(--card-bg, #ffffff);
    border: 1px solid var(--border-color, #e2e8f0);
    color: var(--ink-faint, #64748b);
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
}

.showcase-dot.is-active {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
    transform: translateY(-2px);
}
```

---

## 8. Logika Interaksi JavaScript (Auto-Play & Kontrol)

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const section = document.getElementById('image-showcase');
    if (!section) return;

    const slides = section.querySelectorAll('.showcase-slide');
    const dots = section.querySelectorAll('.showcase-dot');
    const counterEl = section.querySelector('.curr-idx');
    const progressFill = document.getElementById('showcaseProgress');
    const btnPrev = document.getElementById('showcasePrev');
    const btnNext = document.getElementById('showcaseNext');

    let currentIndex = 0;
    const totalSlides = slides.length;
    const DURATION = 5000; // 5 detik per slide
    let timer = null;

    function goToSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        currentIndex = index;

        // 1. Toggle Slide Active
        slides.forEach((slide, idx) => {
            slide.classList.toggle('is-active', idx === currentIndex);
        });

        // 2. Toggle Dot Active
        dots.forEach((dot, idx) => {
            const isActive = idx === currentIndex;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        // 3. Update Counter
        if (counterEl) {
            counterEl.textContent = String(currentIndex + 1).padStart(2, '0');
        }

        resetProgressAnimation();
    }

    function resetProgressAnimation() {
        if (progressFill) {
            progressFill.style.transition = 'none';
            progressFill.style.width = '0%';
            requestAnimationFrame(() => {
                progressFill.style.transition = `width ${DURATION}ms linear`;
                progressFill.style.width = '100%';
            });
        }
    }

    function startAutoPlay() {
        clearInterval(timer);
        resetProgressAnimation();
        timer = setInterval(() => {
            goToSlide(currentIndex + 1);
        }, DURATION);
    }

    // Event listener tombol Next & Prev
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            goToSlide(currentIndex - 1);
            startAutoPlay();
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', () => {
            goToSlide(currentIndex + 1);
            startAutoPlay();
        });
    }

    // Event listener dot navigasi
    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            goToSlide(idx);
            startAutoPlay();
        });
    });

    // Jalankan auto-play saat pertama load
    if (totalSlides > 1) {
        startAutoPlay();
    }
});
```

---

## 9. Rangkuman Perubahan (Image-Only)

1. **Struktur Tabel Database Murni Gambar:**
   - Tabel `showcase_images` hanya memiliki field `id`, `image`, dan `timestamps`.
2. **Form Upload Humas Sangat Simpel:**
   - Staf Humas hanya perlu memilih file foto lalu klik **Upload**, tanpa perlu mengisi formulir panjang (judul, kategori, teks, dsb).
3. **Tampilan Welcome Tetap Mewah & Sinematik:**
   - Menghasilkan slider gambar berukuran besar (*Large Image Focal Point*) yang bersih, modern, dan responsif dengan transisi halus.

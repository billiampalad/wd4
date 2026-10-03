# Desain & Arsitektur Fitur Facility & Campus Image Carousel Slider (Image-Only Database)

Dokumen ini merinci konsep desain visual, arsitektur teknis, dan implementasi fitur **Facility & Campus Image Carousel Slider** yang ditempatkan pada bagian bawah halaman utama ([welcome.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/welcome.blade.php)) sebelum footer.

Desain visual mengadopsi format **Horizontal Card Frame Strip (Squircle / Rounded Card)** dengan bingkai lembut bernuansa *soft beige/cream frame*, yang menampilkan deretan fasilitas kampus (seperti Polimart, TEFA Bar & Cafe, Lounge, Asrama, GOR, Theater/Auditorium, GKT) secara dinamis dari database `showcase_images` yang dikelola oleh staf **Humas** melalui sidebar ([unit.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/unit.blade.php)).

---

## 1. Karakter Visual (Sesuai Referensi)

```text
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│  DOKUMENTASI & FASILITAS KAMPUS                                               [ ◄ Prev ]  [ Next ► ]    │
│  "Eksplorasi Fasilitas & Lingkungan Polimdo"                                                            │
│                                                                                                         │
│   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐          │
│   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   ...    │
│   │ │ POLIMART │ │   │ │ TEFA BAR │ │   │ │  LOUNGE  │ │   │ │  ASRAMA  │ │   │ │   GOR    │ │          │
│   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │          │
│   └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘          │
│                                                                                                         │
│  [━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━] 8 Fasilitas │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 1.1 Spesifikasi Kartu Gambar
- **Frame Kartu:** Bentuk rounded (*squircle*) dengan padding bingkai `7px`, `border-radius: 22px`, dan warna bingkai *warm soft beige* (`#ebdcc0` dengan border `#dfcfb0`) pada mode terang, serta *dark slate frame* pada mode gelap.
- **Gambar Internal:** `object-fit: cover` dengan `border-radius: 16px`, responsif dan tetap tajam.
- **Interaksi:**
  - Efek hover kartu: micro-elevation `translateY(-6px)` dan pembesaran halus `scale(1.03)`.
  - Fitur klik gambar: Membuka **Lightbox Modal Pop-up** untuk melihat foto resolusi penuh.
  - Track Carousel yang bisa di-drag mouse / touch-swipe di HP, dilengkapi tombol panah navigasi kiri/kanan dan progress bar scroll indikator.
  - Auto-scroll halus berkala yang otomatis jeda saat kursor diarahkan ke area slider.

---

## 2. Skema Database & Model (Image-Only)

### 2.1 Migration: `create_showcase_images_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('showcase_images', function (Blueprint $table) {
            $table->id();
            $table->string('image'); // Menyimpan path file gambar (e.g. "uploads/showcase/xxx.jpg")
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showcase_images');
    }
};
```

### 2.2 Model: `App\Models\ShowcaseImage.php`
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

## 3. Integrasi Menu Sidebar untuk Akun Humas

Pada [unit.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/unit.blade.php#L380-L403), tambahkan submenu **"Galeri Fasilitas"**:

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
        {{-- Item Menu Pengelolaan Gambar Showcase Humas --}}
        <a class="submenu-item {{ request()->routeIs('unit.showcase.*') ? 'active' : '' }}"
            href="{{ route('unit.showcase.index') }}">
            <span class="submenu-dot"></span><span>Galeri Fasilitas</span>
        </a>
    </div>
</div>
```

---

## 4. Modul Controller Humas & Rute

### 4.1 Controller: `App\Http\Controllers\Unit\ShowcaseController.php`
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
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/showcase'), $filename);

            ShowcaseImage::create([
                'image' => 'uploads/showcase/' . $filename,
            ]);
        }

        return redirect()->back()->with('success', 'Foto fasilitas berhasil diupload!');
    }

    public function destroy($id)
    {
        $showcase = ShowcaseImage::findOrFail($id);

        if (File::exists(public_path($showcase->image))) {
            File::delete(public_path($showcase->image));
        }

        $showcase->delete();

        return redirect()->back()->with('success', 'Foto fasilitas berhasil dihapus.');
    }
}
```

---

## 5. Implementasi di `welcome.blade.php` (Sebelum `<footer>`)

```blade
@if(isset($showcases) && $showcases->count() > 0)
<!-- ═══ FACILITY & CAMPUS IMAGE CAROUSEL SECTION ═══════════════ -->
<section class="showcase-section" id="campusShowcase" aria-label="Galeri Fasilitas Kampus">
    <div class="showcase-container">
        <!-- Header -->
        <div class="showcase-header">
            <div>
                <div class="showcase-eyebrow">
                    <span class="eyebrow-pulse"></span>
                    <span>DOKUMENTASI & FASILITAS KAMPUS</span>
                </div>
                <h2 class="showcase-title-main">Dokumentasi Fasilitas Polimdo</h2>
            </div>
            <div class="showcase-nav-btns">
                <button type="button" class="nav-btn-circle" id="btnSlidePrev" aria-label="Geser ke Kiri">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" class="nav-btn-circle" id="btnSlideNext" aria-label="Geser ke Kanan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
        </div>

        <!-- Carousel Track Wrapper -->
        <div class="carousel-outer-wrap">
            <div class="carousel-track" id="carouselTrack">
                @foreach($showcases as $item)
                    <div class="gallery-card-frame" onclick="openLightbox('{{ asset($item->image) }}')">
                        <img src="{{ asset($item->image) }}" alt="Fasilitas Polimdo" class="gallery-img-inner" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bottom Track Progress -->
        <div class="carousel-bottom-bar">
            <div class="track-progress-container">
                <div class="track-progress-fill" id="trackProgressFill"></div>
            </div>
            <div class="slide-counter-hint">
                <span>{{ $showcases->count() }} Fasilitas Terdaftar</span>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal Fullscreen -->
<div class="lightbox-modal" id="lightboxModal" onclick="closeLightbox(event)">
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <button type="button" class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <img src="" alt="Fasilitas Polimdo" class="lightbox-img" id="lightboxImg">
    </div>
</div>
@endif
```

---

## 6. Preview Halaman

Tampilan preview live interaktif dapat dibuka pada:
[preview-large-image.html](file:///c:/laragon/www/wd4/public/preview-large-image.html) atau melalui URL `http://localhost/wd4/public/preview-large-image.html`.

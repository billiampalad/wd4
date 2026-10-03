# Desain & Arsitektur Fitur Infinite Seamless Facility Showcase Slider (Image-Only Database)

Dokumen ini merinci konsep desain visual, arsitektur teknis, dan implementasi fitur **Facility & Campus Image Infinite Carousel Slider** yang ditempatkan pada bagian bawah halaman utama ([welcome.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/welcome.blade.php)) sebelum footer.

---

## 1. Karakter Desain & Pengalaman Visual (Ultra-Premium & Infinite Seamless)

### 1.1 Estetika Kartu Mewah (*Luxury Squircle Frame*)
- **Frame Finishing:** Menggunakan *Warm Champagne / Soft Cream Gradient* (`linear-gradient(145deg, #fbf7ee 0%, #ece1cb 100%)`) dengan sentuhan *subtle glossy rim* (`border: 2px solid rgba(217, 198, 165, 0.85)`), *inner highlight shadow*, dan *ambient floating shadow*.
- **Mode Gelap (Dark Mode):** Berubah menjadi *Obsidian Slate Metallic Frame* dengan tepi *glow* biru/cyan saat di-hover.
- **Micro-Interaction Hover:** Ketika mouse melintas, kartu naik halus `translateY(-8px) scale(1.04)`, bayangan meluas (*deep ambient glow*), dan gambar di dalamnya memperbesar halus `scale(1.05)`.
- **Edge Fade Masking:** Ujung kiri dan kanan viewport slider menggunakan CSS `mask-image: linear-gradient(to right, transparent 0%, black 4%, black 96%, transparent 100%)` untuk memberikan efek kartu muncul dan menghilang secara elegan dan natural.

---

### 1.2 Animasi Infinite Loop Tanpa Reset (*No-Jump Continuous Marquee*)
- **Mekanisme:** Slider berjalan terus-menerus (*continuous smooth drift*) menggunakan `requestAnimationFrame` (60/120 FPS).
- **Seamless Looping:** Ketika set kartu pertama selesai lewat, posisi offset di-wrap secara invisible (`position -= singleSetWidth`), sehingga transisi dari gambar terakhir ke gambar pertama terjadi **100% mulus tanpa jeda, tanpa loncatan, dan tanpa kembali ke awal**.
- **User Control:**
  - Otomatis jeda (*pause*) ketika kursor mouse berada di atas kartu (*hover*).
  - Mendukung tarikan mouse manual (*drag-to-scroll*) di desktop dan usap jari (*touch swipe*) di layar smartphone.

---

### 1.3 Lightbox Modal Murni Foto & Navigasi Panah Samping
- **Tampilan Bersih (Clean Pure Image):** Tidak memiliki border, background warna, ataupun border-radius (*border-radius: 0; border: none; background: transparent;*). Gambar tampil murni mengambang di atas latar belakang gelap transparan (*blurred backdrop*).
- **Navigasi Panah Sisi Kiri & Kanan:** Dilengkapi tombol panah melayang di samping kiri (`◄`) dan kanan (`►`) untuk berpindah antar foto secara berurutan.
- **Kontrol Keyboard:** Mendukung tombol panah kiri/kanan keyboard serta tombol `Escape` untuk menutup lightbox.

```text
┌───────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                                                                                           │
│   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐            │
│   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   ════►    │
│   │ │ POLIMART │ │   │ │ TEFA BAR │ │   │ │  LOUNGE  │ │   │ │  ASRAMA  │ │   │ │   GOR    │ │  (Infinite  │
│   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │   Continuous│
│   └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘    Loop)   │
│                                                                                                           │
└───────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

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
<!-- ═══ INFINITE SEAMLESS CAMPUS IMAGE SHOWCASE SECTION ════════ -->
<section class="showcase-section" id="campusShowcase" aria-label="Galeri Fasilitas Kampus">
    <div class="showcase-container">
        <div class="carousel-outer-wrap" id="marqueeViewport">
            <div class="carousel-marquee-track" id="marqueeTrack">
                @foreach($showcases as $item)
                    <div class="gallery-card-frame">
                        <img src="{{ asset($item->image) }}" alt="Fasilitas Polimdo" class="gallery-img-inner" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal Fullscreen (Pure Image + Side Arrows) -->
<div class="lightbox-modal" id="lightboxModal">
    <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Tutup">&times;</button>
    <button type="button" class="lightbox-nav-arrow lightbox-arrow-prev" id="lightboxPrev" aria-label="Sebelumnya">&#10094;</button>
    
    <div class="lightbox-content">
        <img src="" alt="Fasilitas Polimdo" class="lightbox-img" id="lightboxImg">
    </div>

    <button type="button" class="lightbox-nav-arrow lightbox-arrow-next" id="lightboxNext" aria-label="Selanjutnya">&#10095;</button>
</div>
@endif
```

---

## 6. Live Preview File

File preview interaktif lengkap dapat diakses pada:
[preview-large-image.html](file:///c:/laragon/www/wd4/public/preview-large-image.html) atau melalui URL `http://localhost/wd4/public/preview-large-image.html`.

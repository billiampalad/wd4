# Desain & Arsitektur Fitur 5-Card Step Facility Showcase Slider (Image-Only Database)

Dokumen ini merinci konsep desain visual, arsitektur teknis, dan implementasi fitur **Facility & Campus Image 5-Card Step Carousel Slider** yang ditempatkan pada bagian bawah halaman utama ([welcome.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/welcome.blade.php)) sebelum footer.

---

## 1. Karakter Desain & Pengalaman Visual (Glowing Inset Blue Shadow & Step Slide)

### 1.1 Efek Shadow Biru Menyala (*Ramping & Presisi di Tepi Frame*)
- **Glowing Inset Shadow Mengelilingi Slider:** Seluruh area viewport slider dikelilingi oleh efek *inner blue neon glow* yang ramping dan presisi di tepi (`box-shadow: inset 0 0 6px 1px rgba(2, 132, 199, 0.35);`) tanpa memanjang terlalu jauh ke bagian tengah gambar.
- **Mode Gelap (Dark Mode):** Berubah menjadi pendaran *electric cyan/blue neon* ramping (`box-shadow: inset 0 0 8px 1px rgba(56, 189, 248, 0.45);`) yang futuristik dan bersih.
- **Murni Gambar Tanpa Border:** Kartu foto tampil murni sebagai foto beresolusi tinggi dengan sudut melengkung halus (`border-radius: 10px`) tanpa garis border tepi (`border: none`).
- **Jarak Tepi Kiri & Kanan Simetris:** Jarak margin luar kiri (`18px`) dan kanan (`18px`) sama persis dengan jarak celah antar kartu (`gap: 18px`).
- **Kalkulasi Presisi 5 Kartu:**
  $$\text{Lebar Kartu} = \frac{\text{Lebar Kontainer} - (\text{Jarak } 18\text{px} \times 6)}{5}$$

---

### 1.2 Mekanisme Animasi Bergeser Bertambah 1 (*Step-by-Step Slide*)
- **Pergeseran Teratur (+1 Foto per Interval):** Slider diam sejenak (3.5 detik), lalu bergeser secara halus (*smooth cubic-bezier transition*) sejauh 1 lebar kartu untuk memunculkan foto berikutnya.
- **Infinite Looping Mulus:** Menggunakan teknik *invisible wrap reset* sehingga saat mencapai foto terakhir, urutan berikutnya kembali menyambung ke awal secara natural tanpa merusak alur visual.
- **Hover Lift & Z-Index:** Ketika mouse melintas di atas salah satu kartu, kartu tersebut naik halus (`translateY(-8px)`) dengan `z-index: 20` dan pendaran cahaya lembut (*soft ambient glow*).
- **Lightbox Modal Murni Foto:** Klik pada kartu membuka tampilan foto murni tanpa border/radius dengan tombol panah navigasi samping kiri (`◄`) dan kanan (`►`) serta dukungan keyboard.

```text
┌───────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ ░░░░░░░░░░░░░░░░░░░░░░░░░ ( Glowing Blue Inset Shadow ) ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ │
│ ░                                                                                                       ░ │
│ ░ │◄─ 18px ─►│                                                                 │◄─ 18px ─►│             ░ │
│ ░ ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐          ░ │
│ ░ │              │   │              │   │              │   │              │   │              │ ──► +1   ░ │
│ ░ │      01      │   │      02      │   │      03      │   │      04      │   │      05      │ (Step    ░ │
│ ░ │   POLIMART   │18px  TEFA BAR    │18px   LOUNGE     │18px   ASRAMA     │18px    GOR       │  Slide)  ░ │
│ ░ │              │   │              │   │              │   │              │   │              │          ░ │
│ ░ └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘          ░ │
│ ░                                                                                                       ░ │
│ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ │
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
<!-- ═══ 5-CARD STEP FACILITY IMAGE SHOWCASE SECTION (ULTRA PREMIUM ACCENTS) ════════════ -->
<section class="showcase-section" id="campusShowcase" aria-label="Galeri Fasilitas Kampus">
    <!-- Header Aksen Elegan & Kontrol Navigasi -->
    <div class="showcase-header">
        <div class="showcase-header-left">
            <div class="showcase-pill-badge">
                <span class="badge-live-pulse"></span>
                <span>Campus & Facilities Showcase</span>
            </div>
            <h2 class="showcase-main-title">
                Eksplorasi Lingkungan & <span class="title-gradient-accent">Fasilitas Unggulan</span>
            </h2>
            <p class="showcase-subtitle">
                Jelajahi berbagai sarana modern dan infrastruktur pendukung pembelajaran di Politeknik Negeri Manado.
            </p>
        </div>

        <div class="showcase-header-right">
            <div class="slider-counter-pill" id="sliderCounterPill">
                <i class="fa-solid fa-layer-group" style="color:var(--primary); font-size:12px;"></i>
                <span id="sliderCurrentNum">01</span> / <span id="sliderTotalNum">{{ str_pad($showcases->count(), 2, '0', STR_PAD_LEFT) }}</span>
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
                @foreach($showcases as $item)
                    <div class="gallery-card-frame">
                        <img src="{{ asset($item->image) }}" alt="Fasilitas Polimdo" class="gallery-img-inner" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal Fullscreen (Pure Image + Side Arrows & Counter) -->
<div class="lightbox-modal" id="lightboxModal">
    <div class="lightbox-top-bar">
        <span class="lightbox-counter-badge" id="lightboxCounter">01 / 08</span>
        <span style="opacity:0.4;">•</span>
        <span id="lightboxCaption">Fasilitas Kampus</span>
    </div>

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

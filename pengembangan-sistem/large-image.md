# Desain & Arsitektur Fitur 5-Card Step Facility Showcase Slider (Image-Only Database)

Dokumen ini merinci konsep desain visual, arsitektur teknis, dan implementasi fitur **Facility & Campus Image 5-Card Step Carousel Slider** yang ditempatkan pada bagian bawah halaman utama ([welcome.blade.php](file:///c:/laragon/www/wd4/resources/views/auth/welcome.blade.php)) sebelum footer.

---

## 1. Karakter Desain & Pengalaman Visual (Full-Width 5-Card Display & Step Slide)

### 1.1 Tampilan Lebar Penuh (*Full-Width Unobstructed View*)
- **100% Lebar Penuh Layar (*Full Width*):** Container `showcase-container` diatur `width: 100%; max-width: 100%; margin: 0; padding: 0;` tanpa batasan padding atau margin samping.
- **Tepat 5 Kartu Fit Sempurna:** Lebar tiap kartu dihitung secara presisi memenuhi 100% lebar layar `(viewportWidth - (gap * 4)) / 5`, sehingga 5 foto tampil utuh dari ujung kiri monitor hingga ujung kanan monitor tanpa ada kartu yang terhalang atau mengintip separuh.
- **Tanpa Masking / Side Shadow:** Seluruh efek *gradient mask / side shadow* pada sisi kiri dan kanan telah dihapus sepenuhnya (`mask-image: none; -webkit-mask-image: none;`).
- **Frame Finishing:** Menggunakan *Warm Beige Frame* (`#ebdcc0` dengan border `#dfcfb0`) pada mode terang dan *Obsidian Slate Frame* pada mode gelap.

---

### 1.2 Mekanisme Animasi Bergeser Bertambah 1 (*Step-by-Step Slide*)
- **Pergeseran Teratur (+1 Foto per Interval):** Slider diam sejenak (3.5 detik), lalu bergeser secara halus (*smooth cubic-bezier transition*) sejauh 1 lebar kartu untuk memunculkan foto berikutnya.
- **Infinite Looping Mulus:** Menggunakan teknik *invisible wrap reset* sehingga saat mencapai foto terakhir, urutan berikutnya kembali menyambung ke awal secara natural tanpa merusak alur visual.
- **Hover Lift & Z-Index:** Ketika mouse melintas di atas salah satu kartu, kartu tersebut naik halus (`translateY(-8px)`) dengan `z-index: 20` sehingga tampak mengambang bebas.
- **Lightbox Modal Murni Foto:** Klik pada kartu membuka tampilan foto murni tanpa border/radius dengan tombol panah navigasi samping kiri (`◄`) dan kanan (`►`) serta dukungan keyboard.

```text
┌───────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                                                                                           │
│   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐            │
│   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   │ ┌──────────┐ │   ───► +1  │
│   │ │    01    │ │   │ │    02    │ │   │ │    03    │ │   │ │    04    │ │   │ │    05    │ │   (Step    │
│   │ │ POLIMART │ │   │ │ TEFA BAR │ │   │ │  LOUNGE  │ │   │ │  ASRAMA  │ │   │ │   GOR    │ │    Slide)  │
│   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │   │ └──────────┘ │            │
│   └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘   └──────────────┘            │
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
<!-- ═══ 5-CARD STEP FACILITY IMAGE SHOWCASE SECTION (FULL WIDTH) ════════════ -->
<section class="showcase-section" id="campusShowcase" aria-label="Galeri Fasilitas Kampus">
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

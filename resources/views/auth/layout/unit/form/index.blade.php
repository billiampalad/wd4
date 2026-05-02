<!-- Main Content -->
<main id="mainContent" class="dk-page">
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('unit.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('unit.dkerjasama') }}" style="text-decoration: none; color: inherit;">
                    <span>Kerjasama</span>
                </a>
                <span class="sep">/</span>
                <span class="current">Form Laporan</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff;">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div>
                    <span class="dk-eyebrow">Dokumentasi Kerjasama</span>
                    <h2 id="pageTitle">Form Laporan</h2>
                    <p id="pageDesc">
                        Upload dan kelola dokumen laporan kerjasama Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div style="width: 100%;">

        {{-- ═══ Glassmorphism Header Bar ═══ --}}
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px 28px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); width: 100%;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <span style="display: block; font-weight: 800; font-size: 15px; color: var(--text); letter-spacing: -0.01em;">Template Laporan</span>
                    <span style="display: block; font-size: 11px; color: var(--text-sub); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pusat Unduhan Format</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ asset('templates/Laporan Pelaksanaan Kerjasama.docx') }}" download="Laporan Pelaksanaan Kerjasama.docx"
                    class="dk-primary-btn"
                    style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; padding: 10px 20px; border-radius: 12px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; border: none; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25); text-decoration: none; cursor: pointer; position: relative; z-index: 10;">
                    <i class="fas fa-download"></i>
                    Unduh Format
                </a>
            </div>
        </div>

        {{-- ═══ Grid Content Area ═══ --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px; padding-bottom: 60px;">

            {{-- Modern Unique File Card --}}
            <div style="position: relative; width: 100%; max-width: 400px;">
                <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative;"
                    onmouseover="this.style.transform='translateY(-8px)'; this.style.borderColor='#4f46e5'; this.style.boxShadow='0 20px 40px rgba(79, 70, 229, 0.1)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='var(--border)'; this.style.boxShadow='none';">

                    {{-- Decorative Background Element --}}
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.05), rgba(124, 58, 237, 0.05)); border-radius: 50%; z-index: 0;"></div>

                    {{-- Preview Area with 3D Effect (Clickable to Preview) --}}
                    <a href="{{ url('templates/Laporan Pelaksanaan Kerjasama.docx') }}" target="_blank"
                        title="Klik untuk buka file"
                        style="text-decoration: none; display: flex; height: 200px; background: var(--surface2); align-items: center; justify-content: center; position: relative; overflow: hidden; cursor: pointer; z-index: 2;">
                        {{-- Floating Document --}}
                        <div style="position: relative; width: 100px; height: 130px; background: var(--surface); border-radius: 8px; box-shadow: 10px 10px 30px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center; transform: rotate(-3deg); transition: 0.4s; border: 1px solid var(--border);">
                            <div style="width: 50px; height: 50px; border-radius: 12px; background: #2b579a; color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 10px; box-shadow: 0 4px 10px rgba(43,87,154,0.3);">
                                <i class="fas fa-file-word"></i>
                            </div>
                            <div style="width: 60%; height: 4px; background: var(--border); border-radius: 2px; margin-bottom: 6px; opacity: 0.8;"></div>
                            <div style="width: 40%; height: 4px; background: var(--border); border-radius: 2px; opacity: 0.8;"></div>

                            {{-- Fold Corner --}}
                            <div style="position: absolute; top: 0; right: 0; width: 25px; height: 25px; background: linear-gradient(135deg, var(--surface2) 50%, rgba(0,0,0,0.1) 50%); border-bottom-left-radius: 8px; border-left: 1px solid var(--border); border-bottom: 1px solid var(--border);"></div>
                        </div>
                    </a>

                    {{-- Info Area (Non-clickable) --}}
                    <div style="padding: 24px; position: relative; z-index: 1; cursor: default;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                            <span style="padding: 4px 10px; border-radius: 6px; background: rgba(43, 87, 154, 0.1); color: #2b579a; font-size: 10px; font-weight: 800; text-transform: uppercase;">Docx</span>
                            <span style="font-size: 11px; color: var(--text-sub); font-weight: 600;">Template Laporan Kerjasama</span>
                        </div>
                        <h3 style="margin: 0 0 6px; font-size: 16px; font-weight: 700; color: var(--text); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            Laporan Pelaksanaan Kerjasama
                        </h3>
                        <p style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.6;">
                            Ditambahkan pada {{ date('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif
</main>
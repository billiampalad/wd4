@php
    $showcaseList = $showcases ?? collect();
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/auth/unit/uploudimageslider.css') }}" data-turbo-track="reload">

<style>
    .showcase-admin-page {
        padding: 24px 30px 60px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .showcase-grid-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 24px;
    }

    .showcase-admin-card {
        background: var(--bg-surface, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    [data-theme="dark"] .showcase-admin-card {
        background: #0c1524;
        border-color: #1e293b;
    }

    .showcase-admin-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        border-color: var(--primary, #0284c7);
    }

    .showcase-card-thumb-wrap {
        width: 100%;
        aspect-ratio: 16 / 10;
        background: #0f172a;
        position: relative;
        overflow: hidden;
    }

    .showcase-card-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .showcase-admin-card:hover .showcase-card-thumb {
        transform: scale(1.05);
    }

    .showcase-card-body {
        padding: 14px 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .showcase-card-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--ink, #0f172a);
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .showcase-card-meta {
        font-size: 11.5px;
        color: var(--ink-muted, #475569);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .showcase-card-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid var(--border, #e2e8f0);
    }

    .btn-delete-showcase {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.2);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-delete-showcase:hover {
        background: #ef4444;
        color: #ffffff;
    }

    .badge-status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<main id="mainContent" class="sk-page showcase-admin-page">
    <!-- Topbar / Breadcrumb Header -->
    <section class="ud-topbar" style="margin-bottom: 24px;">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Kerjasama</span>
                <span>/</span>
                <span>Galeri Showcase</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-images"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Galeri Foto Penghargaan Kerjasama</h2>
                    <p class="ud-subtitle">
                        Kelola dan unggah foto dokumentasi penghargaan, sertifikat prestasi, dan kemitraan kampus yang tampil pada carousel utama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Komponen Dropzone Upload Humas -->
    <x-uploudimageslider :action="route('unit.showcase.store')" name="image" :multiple="true" />

    <!-- Daftar Foto Tersimpan di Database -->
    <section style="margin-top: 36px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h4 style="font-size: 16px; font-weight: 800; color: var(--ink, #0f172a); display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-photo-film" style="color: var(--primary, #0284c7);"></i>
                Foto Aktif di Database ({{ $showcaseList->count() }})
            </h4>
        </div>

        @if($showcaseList->count() > 0)
            <div class="showcase-grid-list">
                @foreach($showcaseList as $item)
                    <div class="showcase-admin-card">
                        <div class="showcase-card-thumb-wrap">
                            <img src="{{ asset($item->image_path) }}" alt="{{ $item->judul }}" class="showcase-card-thumb" loading="lazy">
                        </div>
                        <div class="showcase-card-body">
                            <div>
                                <div class="showcase-card-title" title="{{ $item->judul }}">{{ $item->judul ?: 'Foto Penghargaan Kerjasama' }}</div>
                                <div class="showcase-card-meta">
                                    <i class="fa-regular fa-clock"></i> {{ $item->created_at ? $item->created_at->diffForHumans() : 'Tersimpan' }}
                                </div>
                            </div>
                            <div class="showcase-card-actions">
                                <span class="badge-status-active">
                                    <i class="fa-solid fa-circle-check"></i> Aktif di Slider
                                </span>
                                <form action="{{ route('unit.showcase.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini dari galeri?');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-showcase" title="Hapus Foto">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px 20px; background: var(--bg-surface-alt, #f1f5f9); border: 1px dashed var(--border, #e2e8f0); border-radius: 10px; color: var(--ink-muted, #475569);">
                <i class="fa-solid fa-image" style="font-size: 32px; opacity: 0.4; margin-bottom: 10px;"></i>
                <p style="font-size: 13.5px; font-weight: 600;">Belum ada foto yang diunggah ke database.</p>
                <p style="font-size: 12px; opacity: 0.8;">Slider depan saat ini menampilkan gambar kurasi bawaan sistem.</p>
            </div>
        @endif
    </section>
</main>

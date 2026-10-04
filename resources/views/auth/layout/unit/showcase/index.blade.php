@php
    $showcaseList = $showcases ?? collect();
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<!-- Main Content -->
<main id="mainContent" class="dk-page" x-data="{
    showUploadModal: false,
    showImageModal: false,
    activeImageUrl: '',
    activeImageTitle: '',
    previewSrc: null,
    fileName: '',
    openPreview(url, title) {
        this.activeImageUrl = url;
        this.activeImageTitle = title || 'Dokumentasi Foto Showcase';
        this.showImageModal = true;
    },
    handleFileChange(e) {
        const file = e.target.files[0];
        if (file) {
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => {
                this.previewSrc = ev.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            this.previewSrc = null;
            this.fileName = '';
        }
    }
}">
    <!-- Topbar Header -->
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Galeri</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-images"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Galeri Foto Showcase</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Kelola dokumentasi foto penghargaan, sertifikat prestasi, dan kemitraan kampus yang tampil pada
                        carousel utama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Card Table -->
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header"
            style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                <span>
                    <strong>Daftar Foto Showcase</strong>
                    <small id="dshowcaseCount">{{ $showcaseList->count() }} foto tersimpan</small>
                </span>
            </div>

            <div class="dk-card-tools" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <x-paginav-entries target="#previewBody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />

                <button type="button" @click="showUploadModal = true" class="dk-primary-btn">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Foto</span>
                </button>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num" style="width: 50px;">#</th>
                            <th class="um-th" style="width: 100px;">Pratinjau</th>
                            <th class="um-th dk-th-title" style="min-width: 250px;">Judul Foto</th>
                            <th class="um-th" style="min-width: 220px;">Lokasi Penyimpanan (Path)</th>
                            <th class="um-th" style="width: 180px; white-space: nowrap;">Waktu Unggah</th>
                            <th class="um-th um-th-aksi" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="previewBody">
                        @forelse($showcaseList as $item)
                            @php
                                $imgSrc = asset($item->image_path);
                                $formattedDate = $item->created_at ? $item->created_at->copy()->timezone('Asia/Makassar')->format('d M Y, H:i') : '-';
                            @endphp
                            <tr class="um-row dk-row" data-row-id="{{ $item->id }}">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>

                                <td class="um-td" style="vertical-align: middle;">
                                    <div @click="openPreview('{{ $imgSrc }}', '{{ addslashes($item->judul ?: 'Foto Penghargaan Kerjasama') }}')"
                                        title="Klik untuk memperbesar foto"
                                        style="width: 72px; height: 48px; border-radius: 8px; overflow: hidden; background: #0f172a; position: relative; cursor: pointer; border: 1px solid var(--border); box-shadow: 0 2px 6px rgba(0,0,0,0.06); display: inline-flex; align-items: center; justify-content: center;">
                                        <img src="{{ $imgSrc }}" alt="{{ $item->judul }}"
                                            style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                    </div>
                                </td>

                                <td class="um-td dk-title-cell" style="vertical-align: middle;">
                                    <div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">
                                        <span class="dk-doc-title"
                                            style="font-weight: 700; line-height: 1.4; color: var(--text);">
                                            {{ $item->judul ?: 'Foto Penghargaan Kerjasama' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="um-td" style="vertical-align: middle;">
                                    <span
                                        style="display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; color: var(--text-sub); background: var(--surface2); padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border); max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                        title="{{ $item->image_path }}">
                                        <i class="fas fa-file-image" style="color: #0284c7;"></i>
                                        <span>{{ $item->image_path }}</span>
                                    </span>
                                </td>

                                <td class="um-td" style="white-space: nowrap; vertical-align: middle;">
                                    <div class="dk-date-range-compact">
                                        <span class="date-val"><i class="far fa-clock"
                                                style="margin-right: 4px; color: var(--text-sub);"></i>{{ $formattedDate }}</span>
                                    </div>
                                </td>

                                <td class="um-td um-td-aksi" style="vertical-align: middle;">
                                    <div class="um-actions dk-actions-compact">
                                        <button type="button" class="dk-action-btn view"
                                            @click="openPreview('{{ $imgSrc }}', '{{ addslashes($item->judul ?: 'Foto Penghargaan Kerjasama') }}')"
                                            title="Pratinjau Foto">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <form action="{{ route('unit.showcase.destroy', $item->id) }}" method="POST"
                                            class="dk-delete-form" style="display: inline;"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto showcase ini dari galeri?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dk-action-btn delete" title="Hapus Foto">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-images"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada foto showcase diunggah</p>
                                        <p class="um-empty-sub">Unggah foto dokumentasi penghargaan atau kemitraan pertama
                                            untuk mengisi slider.</p>
                                        <button type="button" @click="showUploadModal = true" class="dk-empty-btn">
                                            <i class="fas fa-plus"></i>
                                            Tambah Foto
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-paginav id="showcaseTablePaginav" target="#previewBody tr.um-row" :perPage="10"
                class="um-paginav dk-paginav" />
        </div>
    </div>

    <!-- ══ MODAL TAMBAH / UNGGAH FOTO SHOWCASE ══ -->
    <div x-show="showUploadModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="modal-overlay"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
        @click.self="showUploadModal = false" x-cloak>

        <div class="modal-card" x-show="showUploadModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            style="background: var(--surface); border-radius: 20px; width: 100%; max-width: 520px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--border);">

            <div
                style="padding: 20px 28px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, var(--surface), var(--surface2)); display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div
                        style="width: 38px; height: 38px; border-radius: 10px; background: rgba(2,132,199,0.12); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 16.5px; font-weight: 800; color: var(--text);">Tambah Foto
                            Showcase</h3>
                        <p style="margin: 0; font-size: 11.5px; color: var(--text-sub);">Unggah foto penghargaan &
                            kemitraan kampus</p>
                    </div>
                </div>
                <button type="button" @click="showUploadModal = false"
                    style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 6px; font-size: 15px;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-sub)'">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('unit.showcase.store') }}" method="POST" enctype="multipart/form-data"
                style="padding: 24px 28px;">
                @csrf
                <div style="margin-bottom: 18px;">
                    <label
                        style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                        Judul / Keterangan Foto <span
                            style="font-size: 11px; font-weight: normal; color: var(--text-sub);">(Opsional)</span>
                    </label>
                    <input type="text" name="judul" placeholder="Contoh: MoU Bersama PT Telekomunikasi Indonesia"
                        style="width: 100%; padding: 10px 14px; font-size: 13px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface2); color: var(--text); outline: none; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#0284c7'" onblur="this.style.borderColor='var(--border)'">
                </div>

                <div style="margin-bottom: 22px;">
                    <label
                        style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px;">
                        Pilih File Gambar <span style="color:#ef4444;">*</span>
                    </label>

                    <label for="showcaseFileInput"
                        style="border: 2px dashed var(--border); border-radius: 12px; padding: 24px 16px; text-align: center; background: var(--surface2); cursor: pointer; display: block;">
                        <input type="file" id="showcaseFileInput" name="image" accept="image/*" required
                            style="display:none;" @change="handleFileChange($event)">

                        <template x-if="!previewSrc">
                            <div>
                                <i class="fas fa-images"
                                    style="font-size: 28px; color: #0284c7; margin-bottom: 8px; display: block;"></i>
                                <span
                                    style="font-size: 13px; font-weight: 700; color: var(--text); display: block;">Klik
                                    untuk memilih file gambar</span>
                                <span
                                    style="font-size: 11.5px; color: var(--text-sub); display: block; margin-top: 4px;">Mendukung
                                    JPG, PNG, WEBP, AVIF (Maks. 5 MB)</span>
                            </div>
                        </template>

                        <template x-if="previewSrc">
                            <div>
                                <img :src="previewSrc" alt="Pratinjau Unggahan"
                                    style="max-height: 140px; border-radius: 8px; object-fit: contain; margin: 10px auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: block;">
                                <div style="font-size: 12px; font-weight: 600; color: #0284c7; margin-top: 4px;"
                                    x-text="fileName"></div>
                                <span style="font-size: 11px; color: var(--text-sub); display: block;">Klik untuk
                                    mengganti gambar</span>
                            </div>
                        </template>
                    </label>
                </div>

                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                    <button type="button" @click="showUploadModal = false; previewSrc = null; fileName = ''"
                        style="padding: 9px 18px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface2); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer;">
                        Batal
                    </button>
                    <button type="submit" class="dk-primary-btn" style="padding: 9px 20px; font-size: 13px;">
                        <i class="fas fa-upload"></i>
                        <span>Unggah Foto</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══ MODAL PRATINJAU GAMBAR (LIGHTBOX) ══ -->
    <div x-show="showImageModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="modal-overlay"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px;"
        @click.self="showImageModal = false" x-cloak>

        <div
            style="width: 100%; max-width: 900px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; color: #ffffff;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-image" style="color: #38bdf8;"></i>
                <span style="font-size: 15px; font-weight: 700;" x-text="activeImageTitle"></span>
            </div>
            <button type="button" @click="showImageModal = false"
                style="background: rgba(255,255,255,0.15); border: none; color: #ffffff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;"
                onmouseover="this.style.background='rgba(239,68,68,0.8)'"
                onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div
            style="max-width: 900px; max-height: 80vh; border-radius: 12px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7); background: #0b1120;">
            <img :src="activeImageUrl" :alt="activeImageTitle"
                style="width: 100%; height: auto; max-height: 80vh; object-fit: contain; display: block;">
        </div>
    </div>
</main>
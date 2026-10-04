@props([
    'action' => null,
    'name' => 'image',
    'multiple' => true,
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/unit/uploudimageslider.css') }}">
@endpush

<!-- ═══ HUMAS UPLOAD IMAGE SLIDER COMPONENT ═══ -->
<section class="humas-admin-showcase">
    <div class="humas-header-row">
        <div class="humas-title-group">
            <div class="humas-icon-badge">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div class="humas-text-meta">
                <h3>
                    Unggah Foto Penghargaan Kerjasama
                </h3>
                <p>
                    Mengunggah dan memperbarui foto penghargaan serta pencapaian kerjasama kampus.
                </p>
            </div>
        </div>
    </div>

    @if($action)
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="humasUploadForm">
            @csrf
    @endif

    <input type="file" id="humasFileInput" name="{{ $name }}{{ $multiple ? '[]' : '' }}" accept="image/*" {{ $multiple ? 'multiple' : '' }} style="display:none;">

    <div class="humas-dropzone-box" id="humasDropzone">
        <div class="dropzone-icon-stack">
            <i class="fa-solid fa-images main-ico"></i>
            <div class="sub-ico-badge">
                <i class="fa-solid fa-plus"></i>
            </div>
        </div>

        <h4>Tarik & Lepas Foto Penghargaan Kerjasama di Sini</h4>
        <p>
            Unggah dokumentasi foto penghargaan, sertifikat prestasi, dan kemitraan kampus beresolusi tinggi. Foto yang diunggah akan otomatis disinkronkan ke carousel slider 5-kartu.
        </p>

        <button type="button" class="btn-browse-file" id="btnBrowseFile">
            <i class="fa-solid fa-folder-open"></i>
            <span>Pilih Foto dari Komputer</span>
        </button>

        <div class="dropzone-specs">
            <span class="spec-item">
                <i class="fa-regular fa-file-image"></i> Format: JPG, PNG, WEBP, AVIF
            </span>
            <span class="spec-item">
                <i class="fa-solid fa-weight-scale"></i> Ukuran Maks: 5 MB / file
            </span>
            <span class="spec-item">
                <i class="fa-solid fa-expand"></i> Resolusi Rekomendasi: 1920x1080 (16:9 / 4:3)
            </span>
        </div>
    </div>

    @if($action)
        </form>
    @endif
</section>

<!-- ═══ TOAST FEEDBACK NOTIFICATION ═══ -->
<div class="humas-toast" id="humasToast" role="alert" aria-live="polite">
    <div class="humas-toast-icon">
        <i class="fa-solid fa-check"></i>
    </div>
    <span id="humasToastMsg">Foto penghargaan kerjasama berhasil diunggah & dimasukkan ke slider!</span>
</div>

@push('scripts')
    <script src="{{ asset('js/auth/unit/uploudimageslider.js') }}"></script>
@endpush

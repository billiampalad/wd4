@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-microchip"></i>
            <span class="sep">/</span>
            <a href="{{ route('jurusan.index') }}" style="color: inherit; text-decoration: none;">Jurusan</a>
            <span class="sep">/</span>
            <span class="current">Tambah Jurusan</span>
        </div>
        <h2 id="pageTitle">Tambah Jurusan</h2>
        <p id="pageDesc">Isi formulir untuk menambahkan jurusan baru.</p>
    </div>

    <div class="card uc-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header uc-form-header">
            <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Jurusan Baru</div>
        </div>

        <form action="{{ route('jurusan.store') }}" method="POST">
            @csrf
            <div class="card-body uc-body">
                <div class="uc-section-label">
                    <span class="uc-section-num">01</span>
                    <span>Informasi Jurusan</span>
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="nama_jurusan">
                        <i class="fas fa-microchip uc-label-icon"></i>
                        Nama Jurusan
                        <span class="uc-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_jurusan" name="nama_jurusan"
                        class="uc-input @error('nama_jurusan') uc-input-error @enderror"
                        placeholder="Contoh: Istitusi dsb"
                        value="{{ old('nama_jurusan') }}"
                        required
                    >
                    @error('nama_jurusan')  
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer uc-footer">
                <a href="{{ route('jurusan.index') }}" class="uc-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="uc-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan Jurusan
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
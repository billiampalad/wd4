@extends('admin.dashboard')

@section('content')
<main class="main-content admin-dashboard">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <a href="{{ route('jurusan.index') }}" class="ud-breadcrumb-link">Jurusan</a>
                <span>/</span>
                <span>Edit Jurusan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Edit Jurusan</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Ubah data jurusan yang sudah ada.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <div class="card ue-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header ue-form-header">
            <div class="card-title"><i class="fas fa-edit"></i> Formulir Edit Jurusan</div>
            <span class="ue-edit-badge">
                <i class="fas fa-circle" style="font-size:7px;"></i> Mode Edit
            </span>
        </div>

        <form action="{{ route('jurusan.update', $jurusan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body ue-body">
                <div class="ue-section-label">
                    <span class="ue-section-num">01</span>
                    <span>Informasi Jurusan</span> 
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="kode_jurusan">
                        <i class="fas fa-barcode ue-label-icon"></i>
                        Kode Jurusan
                    </label>
                    <input
                        type="text" id="kode_jurusan" name="kode_jurusan"
                        class="ue-input @error('kode_jurusan') ue-input-error @enderror"
                        value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}"
                        placeholder="Contoh: JUR01"
                        maxlength="20"
                    >
                    @error('kode_jurusan')
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="nama_jurusan">
                        <i class="fas fa-microchip ue-label-icon"></i>
                        Nama Jurusan
                        <span class="ue-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_jurusan" name="nama_jurusan"
                        class="ue-input @error('nama_jurusan') ue-input-error @enderror"    
                        value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}"
                        placeholder="Ubah nama jurusan"
                        required
                        maxlength="150"
                    >
                    @error('nama_jurusan')   
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer ue-footer">
                <a href="{{ route('jurusan.index') }}" class="ue-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="ue-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
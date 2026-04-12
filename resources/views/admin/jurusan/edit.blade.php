@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-microchip"></i>
            <span class="sep">/</span>
            <a href="{{ route('jurusan.index') }}" style="color: inherit; text-decoration: none;">Jurusan</a>
            <span class="sep">/</span>
            <span class="current">Edit Jurusan</span>
        </div>
        <h2 id="pageTitle">Edit Jurusan</h2>
        <p id="pageDesc">Ubah data jurusan yang sudah ada.</p>
    </div>
    
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
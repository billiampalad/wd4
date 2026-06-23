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
                <a href="{{ route('pusat.index') }}" class="ud-breadcrumb-link">Pusat</a>
                <span>/</span>
                <span>Edit Pusat</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Edit Pusat</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Ubah data Pusat yang sudah ada.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <div class="card ue-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header ue-form-header">
            <div class="card-title"><i class="fas fa-edit"></i> Formulir Edit Pusat</div>
            <span class="ue-edit-badge">
                <i class="fas fa-circle" style="font-size:7px;"></i> Mode Edit
            </span>
        </div>

        <form action="{{ route('pusat.update', $pusat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body ue-body">
                <div class="ue-section-label">
                    <span class="ue-section-num">01</span>
                    <span>Informasi Pusat</span> 
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="nama_pusat">
                        <i class="fas fa-landmark ue-label-icon"></i>
                        Nama Pusat
                        <span class="ue-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_pusat" name="nama_pusat"
                        class="ue-input @error('nama_pusat') ue-input-error @enderror"    
                        value="{{ old('nama_pusat', $pusat->nama_pusat) }}"
                        placeholder="Ubah nama Pusat"
                        required
                        maxlength="150"
                    >
                    @error('nama_pusat')   
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer ue-footer">
                <a href="{{ route('pusat.index') }}" class="ue-btn-cancel">
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

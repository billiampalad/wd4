@extends('admin.dashboard')

@section('content')
<main class="main-content admin-dashboard">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('upelaksana.index') }}" class="ud-breadcrumb-link">Unit Pelaksana</a>
                <span>/</span>
                <span>Edit Unit</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Edit Unit</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Ubah data unit pelaksana yang sudah ada.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card ue-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header ue-form-header">
            <div class="card-title"><i class="fas fa-edit"></i> Formulir Edit Unit</div>
            <span class="ue-edit-badge">
                <i class="fas fa-circle" style="font-size:7px;"></i> Mode Edit
            </span>
        </div>

        <form action="{{ route('upelaksana.update', $upelaksana->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body ue-body">
                <div class="ue-section-label">
                    <span class="ue-section-num">01</span>
                    <span>Informasi Unit</span> 
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="nama_unit_pelaksana">
                        <i class="fas fa-tag ue-label-icon"></i>
                        Nama Unit Pelaksana
                        <span class="ue-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_unit_pelaksana" name="nama_unit_pelaksana"
                        class="ue-input @error('nama_unit_pelaksana') ue-input-error @enderror"
                        value="{{ old('nama_unit_pelaksana', $upelaksana->nama_unit_pelaksana) }}"
                        placeholder="Ubah nama unit pelaksana"
                        required
                    >
                    @error('nama_unit_pelaksana')   
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer ue-footer">
                <a href="{{ route('upelaksana.index') }}" class="ue-btn-cancel">
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
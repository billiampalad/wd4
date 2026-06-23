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
                <a href="{{ route('upelaksana.index') }}" class="ud-breadcrumb-link">Unit Pelaksana</a>
                <span>/</span>
                <span>Tambah Unit</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-circle-plus"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Tambah Unit Pelaksana</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Isi formulir untuk menambahkan unit pelaksana baru.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card uc-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header uc-form-header">
            <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Unit Pelaksana Baru</div>
        </div>

        <form action="{{ route('upelaksana.store') }}" method="POST">
            @csrf
            <div class="card-body uc-body">
                <div class="uc-section-label">
                    <span class="uc-section-num">01</span>
                    <span>Informasi Unit</span>
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="nama_unit">
                        <i class="fas fa-tag uc-label-icon"></i>
                        Nama Unit
                        <span class="uc-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_unit_pelaksana" name="nama_unit_pelaksana"
                        class="uc-input @error('nama_unit_pelaksana') uc-input-error @enderror"
                        placeholder="Contoh: Istitusi dsb"
                        value="{{ old('nama_unit_pelaksana') }}"
                        required
                    >
                    @error('nama_unit_pelaksana')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer uc-footer">
                <a href="{{ route('upelaksana.index') }}" class="uc-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="uc-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan Unit
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
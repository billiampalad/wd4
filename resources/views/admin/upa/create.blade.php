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
                <a href="{{ route('upa.index') }}" class="ud-breadcrumb-link">UPA</a>
                <span>/</span>
                <span>Tambah UPA</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-circle-plus"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Tambah UPA</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Isi formulir untuk menambahkan UPA baru.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card uc-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header uc-form-header">
            <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir UPA Baru</div>
        </div>

        <form action="{{ route('upa.store') }}" method="POST">
            @csrf
            <div class="card-body uc-body">
                <div class="uc-section-label">
                    <span class="uc-section-num">01</span>
                    <span>Informasi UPA</span>
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="nama_upa">
                        <i class="fas fa-building-columns uc-label-icon"></i>
                        Nama UPA
                        <span class="uc-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_upa" name="nama_upa"
                        class="uc-input @error('nama_upa') uc-input-error @enderror"
                        placeholder="Masukkan nama UPA"
                        value="{{ old('nama_upa') }}"
                        required
                        maxlength="150"
                    >
                    @error('nama_upa')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer uc-footer">
                <a href="{{ route('upa.index') }}" class="uc-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="uc-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan UPA
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-landmark"></i>
            <span class="sep">/</span>
            <a href="{{ route('pusat.index') }}" style="color: inherit; text-decoration: none;">Pusat</a>
            <span class="sep">/</span>
            <span class="current">Tambah Pusat</span>
        </div>
        <h2 id="pageTitle">Tambah Pusat</h2>
        <p id="pageDesc">Isi formulir untuk menambahkan Pusat baru.</p>
    </div>

    <div class="card uc-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header uc-form-header">
            <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Pusat Baru</div>
        </div>

        <form action="{{ route('pusat.store') }}" method="POST">
            @csrf
            <div class="card-body uc-body">
                <div class="uc-section-label">
                    <span class="uc-section-num">01</span>
                    <span>Informasi Pusat</span>
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="nama_pusat">
                        <i class="fas fa-landmark uc-label-icon"></i>
                        Nama Pusat
                        <span class="uc-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_pusat" name="nama_pusat"
                        class="uc-input @error('nama_pusat') uc-input-error @enderror"
                        placeholder="Masukkan nama Pusat"
                        value="{{ old('nama_pusat') }}"
                        required
                        maxlength="150"
                    >
                    @error('nama_pusat')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer uc-footer">
                <a href="{{ route('pusat.index') }}" class="uc-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="uc-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan Pusat
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

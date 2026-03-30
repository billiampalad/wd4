@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-tags"></i>
            <span class="sep">/</span>
            <a href="{{ route('jkerjasama.index') }}" style="color: inherit; text-decoration: none;">Jenis Kerjasama</a>
            <span class="sep">/</span>
            <span class="current">Tambah Jenis</span>
        </div>
        <h2 id="pageTitle">Tambah Jenis Kerjasama</h2>
        <p id="pageDesc">Isi formulir untuk menambahkan jenis kerjasama baru.</p>
    </div>

    <div class="card uc-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header uc-form-header">
            <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Jenis Kerjasama Baru</div>
        </div>

        <form action="{{ route('jkerjasama.store') }}" method="POST">
            @csrf
            <div class="card-body uc-body">
                <div class="uc-section-label">
                    <span class="uc-section-num">01</span>
                    <span>Informasi Jenis</span>
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="nama_kerjasama">
                        <i class="fas fa-tag uc-label-icon"></i>
                        Nama Jenis Kerjasama
                        <span class="uc-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_kerjasama" name="nama_kerjasama"
                        class="uc-input @error('nama_kerjasama') uc-input-error @enderror"
                        placeholder="Contoh: Magang, Penelitian, dsb"
                        value="{{ old('nama_kerjasama') }}"
                        required
                    >
                    @error('nama_kerjasama')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer uc-footer">
                <a href="{{ route('jkerjasama.index') }}" class="uc-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="uc-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan Jenis
                </button>
            </div>
        </form>
    </div>
</main>
@endsection


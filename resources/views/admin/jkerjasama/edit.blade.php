@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-tags"></i>
            <span class="sep">/</span>
            <a href="{{ route('jkerjasama.index') }}" style="color: inherit; text-decoration: none;">Jenis Kerjasama</a>
            <span class="sep">/</span>
            <span class="current">Edit Jenis</span>
        </div>
        <h2 id="pageTitle">Edit Jenis Kerjasama</h2>
        <p id="pageDesc">Ubah data jenis kerjasama yang sudah ada.</p>
    </div>

    <div class="card ue-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header ue-form-header">
            <div class="card-title"><i class="fas fa-edit"></i> Formulir Edit Jenis Kerjasama</div>
            <span class="ue-edit-badge">
                <i class="fas fa-circle" style="font-size:7px;"></i> Mode Edit
            </span>
        </div>

        <form action="{{ route('jkerjasama.update', $jkerjasama->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body ue-body">
                <div class="ue-section-label">
                    <span class="ue-section-num">01</span>
                    <span>Informasi Jenis</span>
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="nama_kerjasama">
                        <i class="fas fa-tag ue-label-icon"></i>
                        Nama Jenis Kerjasama
                        <span class="ue-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_kerjasama" name="nama_kerjasama"
                        class="ue-input @error('nama_kerjasama') ue-input-error @enderror"
                        value="{{ old('nama_kerjasama', $jkerjasama->nama_kerjasama) }}"
                        placeholder="Ubah nama jenis kerjasama"
                        required
                    >
                    @error('nama_kerjasama')
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer ue-footer">
                <a href="{{ route('jkerjasama.index') }}" class="ue-btn-cancel">
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


@extends('admin.dashboard')

@section('content')
<main class="main-content admin-dashboard jurusan-form-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <a href="{{ route('jurusan.index') }}" class="ud-breadcrumb-link">Jurusan</a>
                <span>/</span>
                <span>Tambah Jurusan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-circle-plus"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Tambah Jurusan</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Isi formulir untuk menambahkan jurusan baru.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="uc-layout jurusan-form-layout">
        <div class="uc-form-col">
            <div class="card uc-form-card jurusan-form-card">
                <div class="card-header uc-form-header jurusan-form-header">
                    <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Jurusan Baru</div>
                </div>

                <form action="{{ route('jurusan.store') }}" method="POST">
                    @csrf
                    <div class="card-body uc-body jurusan-form-body">
                        <div class="uc-section-label">
                            <span class="uc-section-num">01</span>
                            <span>Informasi Jurusan</span>
                        </div>

                        <div class="uc-grid-2 jurusan-form-grid">
                            <div class="uc-form-group">
                                <label class="uc-label" for="kode_jurusan">
                                    <i class="fas fa-barcode uc-label-icon"></i>
                                    Kode Jurusan
                                </label>
                                <input
                                    type="text"
                                    id="kode_jurusan"
                                    name="kode_jurusan"
                                    class="uc-input @error('kode_jurusan') uc-input-error @enderror"
                                    placeholder="Contoh: JUR01"
                                    value="{{ old('kode_jurusan') }}"
                                    maxlength="20"
                                >
                                @error('kode_jurusan')
                                    <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="uc-form-group">
                                <label class="uc-label" for="nama_jurusan">
                                    <i class="fas fa-microchip uc-label-icon"></i>
                                    Nama Jurusan
                                    <span class="uc-required">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_jurusan"
                                    name="nama_jurusan"
                                    class="uc-input @error('nama_jurusan') uc-input-error @enderror"
                                    placeholder="Contoh: Teknik Elektro"
                                    value="{{ old('nama_jurusan') }}"
                                    required
                                    maxlength="150"
                                >
                                @error('nama_jurusan')
                                    <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer uc-footer jurusan-form-footer">
                        <a href="{{ route('jurusan.index') }}" class="uc-btn-cancel">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="uc-btn-submit">
                            <i class="fas fa-floppy-disk"></i> Simpan Jurusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

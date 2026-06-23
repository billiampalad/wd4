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
                <a href="{{ route('prodi.index') }}" class="ud-breadcrumb-link">Program Studi</a>
                <span>/</span>
                <span>Tambah Prodi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-circle-plus"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Tambah Program Studi</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Isi formulir untuk menambahkan program studi baru.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card uc-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header uc-form-header">
            <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Prodi Baru</div>
        </div>

        <form action="{{ route('prodi.store') }}" method="POST">
            @csrf
            <div class="card-body uc-body">
                <div class="uc-section-label">
                    <span class="uc-section-num">01</span>
                    <span>Informasi Program Studi</span>
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="jurusan_id">
                        <i class="fas fa-microchip uc-label-icon"></i>
                        Jurusan
                        <span class="uc-required">*</span>
                    </label>
                    <select id="jurusan_id" name="jurusan_id"
                        class="uc-input @error('jurusan_id') uc-input-error @enderror"
                        required>
                        <option value="">— Pilih Jurusan —</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}{{ $jurusan->kode_jurusan ? ' ('.$jurusan->kode_jurusan.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('jurusan_id')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="kode_prodi">
                        <i class="fas fa-barcode uc-label-icon"></i>
                        Kode Prodi
                    </label>
                    <input
                        type="text" id="kode_prodi" name="kode_prodi"
                        class="uc-input @error('kode_prodi') uc-input-error @enderror"
                        placeholder="Contoh: TI01"
                        value="{{ old('kode_prodi') }}"
                        maxlength="20"
                    >
                    @error('kode_prodi')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="nama_prodi">
                        <i class="fas fa-graduation-cap uc-label-icon"></i>
                        Nama Program Studi
                        <span class="uc-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_prodi" name="nama_prodi"
                        class="uc-input @error('nama_prodi') uc-input-error @enderror"
                        placeholder="Contoh: Teknik Informatika"
                        value="{{ old('nama_prodi') }}"
                        required
                        maxlength="150"
                    >
                    @error('nama_prodi')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="uc-form-group">
                    <label class="uc-label" for="jenjang">
                        <i class="fas fa-layer-group uc-label-icon"></i>
                        Jenjang
                        <span class="uc-required">*</span>
                    </label>
                    <select id="jenjang" name="jenjang"
                        class="uc-input @error('jenjang') uc-input-error @enderror"
                        required>
                        @foreach(['D3', 'D4', 'S1', 'S2'] as $j)
                            <option value="{{ $j }}" {{ old('jenjang', 'D4') == $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenjang')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer uc-footer">
                <a href="{{ route('prodi.index') }}" class="uc-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="uc-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan Prodi
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-handshake"></i>
            <span class="sep">/</span>
            <a href="{{ route('mitra.index') }}" style="color: inherit; text-decoration: none;">Master Data</a>
            <span class="sep">/</span>
            <span class="current">Tambah Mitra</span>
        </div>
        <h2 id="pageTitle">Tambah Mitra Baru</h2>
        <p id="pageDesc">Isi formulir untuk menambahkan mitra kerjasama baru.</p>
    </div>

    <div class="uc-layout">
        <div class="uc-form-col" style="flex: 1;">
            <div class="card uc-form-card">
                <div class="card-header uc-form-header">
                    <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Mitra Baru</div>
                </div>

                <form action="{{ route('mitra.store') }}" method="POST">
                    @csrf
                    <div class="card-body uc-body">
                        <div class="uc-section-label">
                            <span class="uc-section-num">01</span>
                            <span>Informasi Mitra</span>
                        </div>

                        <div class="uc-grid-2">
                            <div class="uc-form-group">
                                <label class="uc-label" for="nama_mitra">
                                    <i class="fas fa-handshake uc-label-icon"></i>
                                    Nama Mitra
                                    <span class="uc-required">*</span>
                                </label>
                                <input
                                    type="text" id="nama_mitra" name="nama_mitra"
                                    class="uc-input @error('nama_mitra') uc-input-error @enderror"
                                    placeholder="Masukkan nama mitra"
                                    value="{{ old('nama_mitra') }}"
                                    required
                                />
                                @error('nama_mitra')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="uc-form-group">
                                <label class="uc-label" for="negara">
                                    <i class="fas fa-globe uc-label-icon"></i>
                                    Negara
                                </label>
                                <input
                                    type="text" id="negara" name="negara"
                                    class="uc-input @error('negara') uc-input-error @enderror"
                                    placeholder="Contoh: Indonesia"
                                    value="{{ old('negara', 'Indonesia') }}"
                                />
                                @error('negara')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="uc-grid-2" style="margin-top: 15px;">
                            <div class="uc-form-group">
                                <label class="uc-label" for="kategori">
                                    <i class="fas fa-tags uc-label-icon"></i>
                                    Kategori
                                    <span class="uc-required">*</span>
                                </label>
                                <select
                                    id="kategori" name="kategori"
                                    class="uc-input uc-select @error('kategori') uc-input-error @enderror"
                                    required
                                >
                                    <option value="" disabled selected>— Pilih Kategori —</option>
                                    <option value="nasional" {{ old('kategori') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                                    <option value="internasional" {{ old('kategori') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                                </select>
                                @error('kategori')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer uc-footer">
                        <a href="{{ route('mitra.index') }}" class="uc-btn-cancel">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="uc-btn-submit">
                            <i class="fas fa-floppy-disk"></i> Simpan Mitra
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

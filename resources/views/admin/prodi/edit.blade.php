@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-graduation-cap"></i>
            <span class="sep">/</span>
            <a href="{{ route('prodi.index') }}" style="color: inherit; text-decoration: none;">Program Studi</a>
            <span class="sep">/</span>
            <span class="current">Edit Prodi</span>
        </div>
        <h2 id="pageTitle">Edit Program Studi</h2>
        <p id="pageDesc">Ubah data program studi yang sudah ada.</p>
    </div>
    
    <div class="card ue-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header ue-form-header">
            <div class="card-title"><i class="fas fa-edit"></i> Formulir Edit Prodi</div>
            <span class="ue-edit-badge">
                <i class="fas fa-circle" style="font-size:7px;"></i> Mode Edit
            </span>
        </div>

        <form action="{{ route('prodi.update', $prodi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body ue-body">
                <div class="ue-section-label">
                    <span class="ue-section-num">01</span>
                    <span>Informasi Program Studi</span> 
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="jurusan_id">
                        <i class="fas fa-microchip ue-label-icon"></i>
                        Jurusan
                        <span class="ue-required">*</span>
                    </label>
                    <select id="jurusan_id" name="jurusan_id"
                        class="ue-input @error('jurusan_id') ue-input-error @enderror"
                        required>
                        <option value="">— Pilih Jurusan —</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $prodi->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}{{ $jurusan->kode_jurusan ? ' ('.$jurusan->kode_jurusan.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('jurusan_id')
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="kode_prodi">
                        <i class="fas fa-barcode ue-label-icon"></i>
                        Kode Prodi
                    </label>
                    <input
                        type="text" id="kode_prodi" name="kode_prodi"
                        class="ue-input @error('kode_prodi') ue-input-error @enderror"
                        value="{{ old('kode_prodi', $prodi->kode_prodi) }}"
                        placeholder="Contoh: TI01"
                        maxlength="20"
                    >
                    @error('kode_prodi')
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="nama_prodi">
                        <i class="fas fa-graduation-cap ue-label-icon"></i>
                        Nama Program Studi
                        <span class="ue-required">*</span>
                    </label>
                    <input
                        type="text" id="nama_prodi" name="nama_prodi"
                        class="ue-input @error('nama_prodi') ue-input-error @enderror"    
                        value="{{ old('nama_prodi', $prodi->nama_prodi) }}"
                        placeholder="Ubah nama program studi"
                        required
                        maxlength="150"
                    >
                    @error('nama_prodi')   
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="jenjang">
                        <i class="fas fa-layer-group ue-label-icon"></i>
                        Jenjang
                        <span class="ue-required">*</span>
                    </label>
                    <select id="jenjang" name="jenjang"
                        class="ue-input @error('jenjang') ue-input-error @enderror"
                        required>
                        @foreach(['D3', 'D4', 'S1', 'S2'] as $j)
                            <option value="{{ $j }}" {{ old('jenjang', $prodi->jenjang) == $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenjang')
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer ue-footer">
                <a href="{{ route('prodi.index') }}" class="ue-btn-cancel">
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

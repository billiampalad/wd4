@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-users"></i>
            <span class="sep">/</span>
            <a href="{{ route('roles.index') }}" style="color: inherit; text-decoration: none;">User Management</a>
            <span class="sep">/</span>
            <span class="current">Tambah Role</span>
        </div>
        <h2 id="pageTitle">Tambah Role Baru</h2>
        <p id="pageDesc">Isi formulir untuk menambahkan role baru ke sistem.</p>
    </div>

    <div class="card uc-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header uc-form-header">
            <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Role Baru</div>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body uc-body">
                <div class="uc-section-label">
                    <span class="uc-section-num">01</span>
                    <span>Informasi Role</span>
                </div>
                
                <div class="uc-form-group">
                    <label class="uc-label" for="role_name">
                        <i class="fas fa-shield-alt uc-label-icon"></i>
                        Nama Role
                        <span class="uc-required">*</span>
                    </label>
                    <input 
                        type="text" id="role_name" name="role_name" 
                        class="uc-input @error('role_name') uc-input-error @enderror"
                        placeholder="Contoh: Admin, Pimpinan, dsb"
                        value="{{ old('role_name') }}"
                        required
                    >
                    @error('role_name')
                        <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer uc-footer">
                <a href="{{ route('roles.index') }}" class="uc-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="uc-btn-submit">
                    <i class="fas fa-floppy-disk"></i> Simpan Role
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

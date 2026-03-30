@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-users"></i>
            <span class="sep">/</span>
            <a href="{{ route('roles.index') }}" style="color: inherit; text-decoration: none;">User Management</a>
            <span class="sep">/</span>
            <span class="current">Edit Role</span>
        </div>
        <h2 id="pageTitle">Edit Role</h2>
        <p id="pageDesc">Ubah data role yang sudah ada.</p>
    </div>

    <div class="card ue-form-card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header ue-form-header">
            <div class="card-title"><i class="fas fa-edit"></i> Formulir Edit Role</div>
            <span class="ue-edit-badge">
                <i class="fas fa-circle" style="font-size:7px;"></i> Mode Edit
            </span>
        </div>
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body ue-body">
                <div class="ue-section-label">
                    <span class="ue-section-num">01</span>
                    <span>Informasi Role</span>
                </div>

                <div class="ue-form-group">
                    <label class="ue-label" for="role_name">
                        <i class="fas fa-shield-alt ue-label-icon"></i>
                        Nama Role
                        <span class="ue-required">*</span>
                    </label>
                    <input 
                        type="text" id="role_name" name="role_name" 
                        class="ue-input @error('role_name') ue-input-error @enderror"
                        value="{{ old('role_name', $role->role_name) }}"
                        placeholder="Ubah nama role"
                        required
                    >
                    @error('role_name')
                        <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer ue-footer">
                <a href="{{ route('roles.index') }}" class="ue-btn-cancel">
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

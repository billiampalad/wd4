@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-microchip"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">Master Data</span>
            <span class="sep">/</span>
            <a href="{{ route('klasifikasi.index') }}" style="color: inherit; text-decoration: none;">Klasifikasi Mitra</a>
            <span class="sep">/</span>
            <span class="current">Edit</span>
        </div>
        <h2 id="pageTitle">Edit Klasifikasi Mitra</h2>
    </div>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="card-title"><i class="fas fa-edit"></i> Form Edit Klasifikasi</div>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <form action="{{ route('klasifikasi.update', $klasifikasi->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="nama" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nama Klasifikasi</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama', $klasifikasi->nama) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;" required>
                    @error('nama')
                        <div style="color: var(--danger-color); margin-top: 0.5rem; font-size: 0.875rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions" style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="{{ route('klasifikasi.index') }}" class="btn-cancel" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; cursor: pointer; text-decoration: none; color: inherit;">Batal</a>
                    <button type="submit" class="btn-save" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: none; background: var(--primary-color); color: white; cursor: pointer;">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

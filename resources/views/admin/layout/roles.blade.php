@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-users"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">User Management</span>
            <span class="sep">/</span>
            <span class="current">Roles</span>
        </div>
        <h2 id="pageTitle">Role Management</h2>
        <p id="pageDesc">Tambah, edit, dan hapus data role pengguna.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="card-title"><i class="fas fa-shield-alt"></i> Daftar Role</div>
            <a href="{{ route('roles.create') }}" class="um-btn-add">
                <i class="fas fa-plus"></i> Tambah Role
            </a>
        </div>
        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Role</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $i => $role)
                    <tr class="um-row">
                        <td class="um-td um-td-num">
                            <span class="um-num">{{ $i + 1 }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-name">{{ $role->role_name ?? '-' }}</span>
                        </td>
                        <td class="um-td um-td-aksi">
                            <div class="actions um-actions">
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn-action edit um-btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete um-btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <p class="um-empty-title">Belum ada data role</p>
                                <p class="um-empty-sub">Klik tombol <strong>Tambah Role</strong> untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection

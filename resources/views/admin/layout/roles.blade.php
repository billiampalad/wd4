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
                    <span>Roles</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-shield-halved"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Role Management</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Tambah, edit, dan hapus data role pengguna.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="card um-card">
            <div class="card-header um-header">
                <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                    <div class="card-title"><i class="fas fa-shield-alt"></i> Daftar Role</div>
                    <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
                </div>
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
                            <th class="um-th">Deskripsi</th>
                            <th class="um-th">Dibuat</th>
                            <th class="um-th">Diperbarui</th>
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
                                <td class="um-td">
                                    <span class="um-meta">{{ $role->description ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-plus um-date-icon"></i>
                                        {{ $role->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-check um-date-icon"></i>
                                        {{ $role->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="actions um-actions">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn-action edit um-btn-edit"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form id="form-delete-role-{{ $role->id }}"
                                            action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                                title: 'Hapus Role',
                                                message: 'Menghapus role pengguna <span class=\'custom-alert-highlight\'>{{ addslashes($role->role_name) }}</span> tidak dapat dikembalikan.',
                                                type: 'danger',
                                                confirmText: 'Hapus',
                                                confirmColor: 'danger',
                                                onConfirm: () => document.getElementById('form-delete-role-{{ $role->id }}').submit()
                                            })">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="um-empty">
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
            <x-paginav id="roleTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
                :showPerPage="false" />
        </div>
    </main>
@endsection
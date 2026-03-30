@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-users"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">User Management</span>
            <span class="sep">/</span>
            <span class="current">Users</span>
        </div>
        <h2 id="pageTitle">User Management</h2>
        <p id="pageDesc">Tambah, edit, dan hapus data pengguna sistem.</p>
    </div>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="card-title"><i class="fas fa-users"></i> Daftar Pengguna</div>
            <a href="{{ route('users.create') }}" class="um-btn-add">
                <i class="fas fa-plus"></i> Tambah Pengguna
            </a>
        </div>
        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">NIK</th>
                        <th class="um-th">Nama</th>
                        <th class="um-th">Password</th>
                        <th class="um-th">Role</th>
                        <th class="um-th">Jabatan</th>
                        <th class="um-th">Jurusan</th>
                        <th class="um-th">Unit</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th">Diperbarui</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr class="um-row">
                        <td class="um-td um-td-num">
                            <span class="um-num">{{ $i + 1 }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-nik">{{ $user->nik ?? '-' }}</span>
                        </td>
                        <td class="um-td">
                            <div class="user-cell um-user-cell">
                                <div class="avatar um-avatar um-avatar-color-{{ ($i % 6) }}">{{ strtoupper(substr($user->name ?? '??', 0, 2)) }}</div>
                                <span class="um-name">{{ $user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="um-td">
                            <div class="um-pass-wrap">
                                <span class="um-pass-dots">••••••••</span>
                                <span class="um-pass-real" style="display:none;" title="{{ $user->password ?? '-' }}">{{ Str::limit($user->password ?? '-', 10) }}</span>
                                <button class="um-pass-toggle" onclick="togglePass(this)" title="Lihat password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                        <td class="um-td">
                            <span class="tag tag-{{ strtolower($user->role?->role_name ?? 'default') }} um-role-tag">
                                {{ $user->role?->role_name ?? '-' }}
                            </span>
                        </td>
                        <td class="um-td"><span class="um-meta">{{ $user->profile?->jabatan ?? '-' }}</span></td>
                        <td class="um-td"><span class="um-meta">{{ $user->profile?->jurusan?->nama_jurusan ?? '-' }}</span></td>
                        <td class="um-td"><span class="um-meta">{{ $user->profile?->unitKerja?->nama_unit_pelaksana ?? '-' }}</span></td>
                        <td class="um-td">
                            <div class="um-date">
                                <i class="fas fa-calendar-plus um-date-icon"></i>
                                {{ $user->created_at?->format('d-m-Y H:i') ?? '-' }}
                            </div>
                        </td>
                        <td class="um-td">
                            <div class="um-date">
                                <i class="fas fa-calendar-check um-date-icon"></i>
                                {{ $user->updated_at?->format('d-m-Y H:i') ?? '-' }}
                            </div>
                        </td>
                        <td class="um-td um-td-aksi">
                            <div class="actions um-actions">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-action edit um-btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
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
                        <td colspan="11" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <p class="um-empty-title">Belum ada data pengguna</p>
                                <p class="um-empty-sub">Klik tombol <strong>Tambah Pengguna</strong> untuk memulai.</p>
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
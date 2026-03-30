@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-users"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">User Management</span>
            <span class="sep">/</span>
            <span class="current">Profiles</span>
        </div>
        <h2 id="pageTitle">User Profiles</h2>
        <p id="pageDesc">Kelola data profil pengguna.</p>
    </div>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="card-title"><i class="fas fa-id-card"></i> Daftar Profil Pengguna</div>
        </div>
        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Pengguna</th>
                        <th class="um-th">Jabatan</th>
                        <th class="um-th">Jurusan</th>
                        <th class="um-th">Unit Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $i => $profile)
                    <tr class="um-row">
                        <td class="um-td um-td-num">
                            <span class="um-num">{{ $i + 1 }}</span>
                        </td>
                        <td class="um-td">
                            <div class="user-cell um-user-cell">
                                <div class="avatar um-avatar um-avatar-color-{{ ($i % 6) }}">{{ strtoupper(substr($profile->user?->name ?? '??', 0, 2)) }}</div>
                                <span class="um-name">{{ $profile->user?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="um-td">
                            <span class="um-meta">{{ $profile->jabatan ?? '-' }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-meta">{{ $profile->jurusan?->nama_jurusan ?? '-' }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-meta">{{ $profile->unitKerja?->nama_unit_pelaksana ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon">
                                    <i class="fas fa-id-card-clip"></i>
                                </div>
                                <p class="um-empty-title">Belum ada data profil</p>
                                <p class="um-empty-sub">Profil pengguna akan muncul secara otomatis ketika data pengguna ditambahkan.</p>
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

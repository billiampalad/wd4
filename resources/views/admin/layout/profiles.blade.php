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
                <span>Profiles</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-id-card"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">User Profiles</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Kelola data profil pengguna.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-id-card"></i> Daftar Profil Pengguna</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="profileSearchInput" placeholder="Cari data profil..." target=".um-table tbody tr.um-row"
                    emptyTarget="#profileSearchEmptyRow" querySpan="#profileSearchQueryText" />
            </div>
        </div>
        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Pengguna</th>
                        <th class="um-th">Jabatan</th>
                        <th class="um-th">Jurusan</th>
                        <th class="um-th">Unit</th>
                        <th class="um-th">UPA</th>
                        <th class="um-th">Pusat</th>
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
                        <td class="um-td">
                            <span class="um-meta">{{ $profile->upa?->nama_upa ?? '-' }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-meta">{{ $profile->pusat?->nama_pusat ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="um-empty">
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
                    <tr id="profileSearchEmptyRow" style="display: none;">
                        <td colspan="7" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada profil yang cocok dengan kata kunci "<span
                                        id="profileSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="profileTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>
</main>
@endsection

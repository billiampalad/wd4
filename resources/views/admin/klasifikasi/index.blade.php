@extends('admin.dashboard')

@section('styles')
    <style>
        .um-table-wrap {
            overflow-x: visible;
        }
        .um-table {
            min-width: unset;
            width: 100%;
        }
    </style>
@endsection

@section('content')
<main class="main-content admin-dashboard">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                <span>/</span>
                <span>Klasifikasi Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-layer-group"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Klasifikasi Mitra</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Kelola data klasifikasi mitra.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-microchip"></i> Daftar Klasifikasi</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="klasifikasiSearchInput" placeholder="Cari data klasifikasi..." target=".um-table tbody tr.um-row"
                    emptyTarget="#klasifikasiSearchEmptyRow" querySpan="#klasifikasiSearchQueryText" />
                <a href="{{ route('klasifikasi.create') }}" class="um-btn-add">
                    <i class="fas fa-plus"></i> Tambah Klasifikasi
                </a>
            </div>
        </div>
        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">No</th>
                        <th class="um-th">Nama Klasifikasi</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($klasifikasi as $i => $item)
                    <tr class="um-row">
                        <td class="um-td um-td-num">
                            <span class="um-num">{{ $i + 1 }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-name" style="font-weight: 600;">{{ $item->nama }}</span>
                        </td>
                        <td class="um-td um-td-aksi">
                            <div class="actions um-actions">
                                <a href="{{ route('klasifikasi.edit', $item->id) }}" class="btn-action edit um-btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('klasifikasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus klasifikasi ini?')">
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
                        <td colspan="3" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <p class="um-empty-title">Belum ada data klasifikasi</p>
                                <p class="um-empty-sub">Klik tombol <strong>Tambah Klasifikasi</strong> untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    <tr id="klasifikasiSearchEmptyRow" style="display: none;">
                        <td colspan="3" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada klasifikasi yang cocok dengan kata kunci "<span
                                        id="klasifikasiSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="klasifikasiTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>
</main>
@endsection
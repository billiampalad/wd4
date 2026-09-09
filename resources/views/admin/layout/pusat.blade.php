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
                <span>Pusat</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-landmark"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Pusat</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data Pusat.
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
                <div class="card-title"><i class="fas fa-landmark"></i> Daftar Pusat</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="pusatSearchInput" placeholder="Cari data pusat..." target=".um-table tbody tr.um-row"
                    emptyTarget="#pusatSearchEmptyRow" querySpan="#pusatSearchQueryText" />
                <a href="{{ route('pusat.create') }}" class="um-btn-add">
                    <i class="fas fa-plus"></i> Tambah Pusat
                </a>
            </div>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Pusat</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th">Diperbarui</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pusats as $i => $pusat)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $pusat->nama_pusat ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $pusat->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-check um-date-icon"></i>
                                    {{ $pusat->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <a href="{{ route('pusat.edit', $pusat->id) }}" class="btn-action edit um-btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pusat.destroy', $pusat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus Pusat ini?')">
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
                            <td colspan="5" class="um-empty">
                                <div class="um-empty-state">
                                    <div class="um-empty-icon">
                                        <i class="fas fa-landmark"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data Pusat</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah Pusat</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="pusatSearchEmptyRow" style="display: none;">
                        <td colspan="5" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada pusat yang cocok dengan kata kunci "<span
                                        id="pusatSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="pusatTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>
</main>
@endsection

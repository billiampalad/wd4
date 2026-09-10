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
                <span>Program Studi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-graduation-cap"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Program Studi</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data Program Studi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-graduation-cap"></i> Daftar Program Studi</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="prodiSearchInput" placeholder="Cari program studi..." target=".um-table tbody tr.um-row"
                    emptyTarget="#prodiSearchEmptyRow" querySpan="#prodiSearchQueryText" />
                <a href="{{ route('prodi.create') }}" class="um-btn-add">
                    <i class="fas fa-plus"></i> Tambah Prodi
                </a>
            </div>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Kode</th>
                        <th class="um-th">Nama Prodi</th>
                        <th class="um-th">Jurusan</th>
                        <th class="um-th">Jenjang</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $i => $prodi)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-meta" style="font-family: monospace;">{{ $prodi->kode_prodi ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $prodi->nama_prodi }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-meta">{{ $prodi->jurusan->nama_jurusan ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <span class="tag tag-blue" style="font-size: 11px;">{{ $prodi->jenjang }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $prodi->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <a href="{{ route('prodi.edit', $prodi->id) }}" class="btn-action edit um-btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="form-delete-prodi-{{ $prodi->id }}" action="{{ route('prodi.destroy', $prodi->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                            title: 'Hapus Program Studi',
                                            message: 'Menghapus program studi <span class=\'custom-alert-highlight\'>{{ addslashes($prodi->nama_prodi) }}</span> tidak dapat dikembalikan.',
                                            type: 'danger',
                                            confirmText: 'Hapus',
                                            confirmColor: 'danger',
                                            onConfirm: () => document.getElementById('form-delete-prodi-{{ $prodi->id }}').submit()
                                        })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="um-empty">
                                <div class="um-empty-state">
                                    <div class="um-empty-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data Program Studi</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah Prodi</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="prodiSearchEmptyRow" style="display: none;">
                        <td colspan="7" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada program studi yang cocok dengan kata kunci "<span
                                        id="prodiSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="prodiTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>
</main>
@endsection

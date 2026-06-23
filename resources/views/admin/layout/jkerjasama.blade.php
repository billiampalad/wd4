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
                <span>Jenis Kerjasama</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-tags"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Jenis Kerjasama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data jenis kerjasama.
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
            <div class="card-title"><i class="fas fa-tags"></i> Daftar Jenis Kerjasama</div>
            <a href="{{ route('jkerjasama.create') }}" class="um-btn-add">
                <i class="fas fa-plus"></i> Tambah Jenis
            </a>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Jenis</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th">Diperbarui</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisKerjasamas as $i => $jkerjasama)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $jkerjasama->nama_kerjasama ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $jkerjasama->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-check um-date-icon"></i>
                                    {{ $jkerjasama->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <a href="{{ route('jkerjasama.edit', $jkerjasama->id) }}" class="btn-action edit um-btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('jkerjasama.destroy', $jkerjasama->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jenis kerjasama ini?')">
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
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data jenis kerjasama</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah Jenis</strong> untuk memulai.</p>
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


@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-handshake"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">Master Data</span>
            <span class="sep">/</span>
            <span class="current">Mitra</span>
        </div>
        <h2 id="pageTitle">Mitra Kerjasama</h2>
        <p id="pageDesc">Kelola data mitra kerjasama nasional dan internasional.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="card-title"><i class="fas fa-handshake"></i> Daftar Mitra</div>
            <a href="{{ route('mitra.create') }}" class="um-btn-add">
                <i class="fas fa-plus"></i> Tambah Mitra
            </a>
        </div>
        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Mitra</th>
                        <th class="um-th">Negara</th>
                        <th class="um-th">Kategori</th>
                        <th class="um-th">Total Kegiatan</th>
                        <th class="um-th">Status Kegiatan</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mitras as $i => $mitra)
                    <tr class="um-row">
                        <td class="um-td um-td-num">
                            <span class="um-num">{{ $i + 1 }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-name" style="font-weight: 600;">{{ $mitra->nama_mitra }}</span>
                        </td>
                        <td class="um-td">
                            <span class="um-meta"><i class="fas fa-globe-asia" style="margin-right: 5px; color: var(--text-sub);"></i>{{ $mitra->negara ?? '-' }}</span>
                        </td>
                        <td class="um-td">
                            <span class="tag tag-{{ $mitra->kategori == 'nasional' ? 'blue' : 'purple' }} um-role-tag">
                                {{ ucfirst($mitra->kategori) }}
                            </span>
                        </td>
                        <td class="um-td">
                            <span class="tag tag-green" style="font-family: 'DM Mono', monospace;">
                                {{ $mitra->kegiatanKerjasamas->count() }} Kegiatan
                            </span>
                        </td>
                        <td class="um-td">
                            @php
                                $kegiatanAktif = $mitra->kegiatanKerjasamas->filter(fn($k) => $k->isAktif())->count();
                            @endphp
                            @if($mitra->kegiatanKerjasamas->count() > 0)
                                @if($kegiatanAktif > 0)
                                    <span class="tag tag-green"><i class="fas fa-check-circle" style="margin-right: 4px;"></i> {{ $kegiatanAktif }} Aktif</span>
                                @else
                                    <span class="tag tag-red"><i class="fas fa-clock" style="margin-right: 4px;"></i> Selesai/Expired</span>
                                @endif
                            @else
                                <span class="um-meta">-</span>
                            @endif
                        </td>
                        <td class="um-td um-td-aksi">
                            <div class="actions um-actions">
                                <a href="{{ route('mitra.edit', $mitra->id) }}" class="btn-action edit um-btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('mitra.destroy', $mitra->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus mitra ini?')">
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
                        <td colspan="7" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon">
                                    <i class="fas fa-handshake-slash"></i>
                                </div>
                                <p class="um-empty-title">Belum ada data mitra</p>
                                <p class="um-empty-sub">Klik tombol <strong>Tambah Mitra</strong> untuk memulai.</p>
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

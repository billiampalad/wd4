<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current" id="breadcrumbCurrent">Data Kerjasama</span>
        </div>
        <h2 id="pageTitle">Data Kerjasama Jurusan</h2>
        <p id="pageDesc">Kelola dan lihat data kerjasama khusus untuk <strong>{{ auth()->user()->profile?->jurusan?->nama_jurusan ?? 'Jurusan' }}</strong>.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(5,150,105,.08)); border: 1px solid rgba(16,185,129,.3); color: #065f46; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle" style="font-size: 16px; color: #10b981;"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error" style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-exclamation-circle" style="font-size: 16px; color: #ef4444;"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="card um-card">
        <div class="card-header um-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
            <div class="um-title" style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-folder-open" style="color: var(--accent);"></i>
                <span>Daftar Kerjasama</span>
            </div>
            <a href="{{ route('jurusan.kerjasama.create') }}" class="btn-add" style="background: linear-gradient(135deg, var(--accent), var(--accent2)); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(79,70,229,.3); transition: all 0.3s;">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Nama Kegiatan</th>
                            <th class="um-th">Jenis Kerjasama</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th">Periode</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kerjasamaJurusan as $index => $kegiatan)
                            <tr class="um-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-name">{{ $kegiatan->nama_kegiatan ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="tag tag-purple" style="font-size: 11px;">
                                        <i class="fas fa-handshake" style="font-size:9px; margin-right:4px;"></i>
                                        {{ $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}
                                    </span>
                                </td>
                                <td class="um-td">
                                    @if($kegiatan->mitras->count() > 0)
                                        <span class="um-meta" title="{{ $kegiatan->mitras->pluck('nama_mitra')->join(', ') }}">
                                            {{ $kegiatan->mitras->first()->nama_mitra }}
                                            @if($kegiatan->mitras->count() > 1)
                                                +{{ $kegiatan->mitras->count() - 1 }} mitra lainnya
                                            @endif
                                        </span>
                                    @else
                                        <span class="um-meta">-</span>
                                    @endif
                                </td>
                                <td class="um-td">
                                    @php
                                        $mulai = $kegiatan->periode_mulai?->format('d M Y');
                                        $selesai = $kegiatan->periode_selesai?->format('d M Y');
                                    @endphp
                                    <span class="um-meta">{{ $mulai ? $mulai : '-' }} s/d {{ $selesai ? $selesai : '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="tag {{ $kegiatan->status_class }}">
                                        <i class="fas fa-circle" style="font-size:6px;"></i> {{ $kegiatan->status_label ?? '-' }}
                                    </span>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="um-actions">
                                        <a href="{{ route('jurusan.kerjasama.show', $kegiatan->id) }}" class="um-btn-edit" title="Detail" style="background: rgba(79,70,229,.12); color: #4f46e5;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('jurusan.kerjasama.edit', $kegiatan->id) }}" class="um-btn-edit" title="Edit" style="background: rgba(245, 158, 11, .12); color: #f59e0b;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('jurusan.kerjasama.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data kerjasama ini? Semua data terkait (tujuan, pelaksanaan, hasil, dokumentasi) akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-btn-delete" title="Hapus">
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
                                            <i class="fas fa-folder-open" style="font-size: 28px; opacity: 0.3; color: var(--text-sub);"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada data kerjasama</p>
                                        <p class="um-empty-sub">Klik tombol <strong>Tambah Data</strong> untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

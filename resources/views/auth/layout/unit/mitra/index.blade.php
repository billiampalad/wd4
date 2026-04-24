<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">Kerjasama</span>
            <span class="sep">/</span>
            <span class="current">Mitra</span>
        </div>
        <h2 id="pageTitle">Daftar Mitra Unit</h2>
        <p id="pageDesc">Daftar instansi/mitra yang pernah melakukan kerjasama dengan 
            <strong>{{ auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja' }}</strong>.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"
            style="background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(5,150,105,.08)); border: 1px solid rgba(16,185,129,.3); color: #065f46; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle" style="font-size: 16px; color: #10b981;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error"
            style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="font-size: 16px; color: #ef4444;"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="card um-card">
        <div class="card-header um-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
            <div class="um-title" style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-handshake" style="color: var(--accent);"></i>
                <span>Mitra Kerjasama</span>
            </div>
            <a href="{{ route('unit.mitra.create') }}" class="btn-add"
                style="background: linear-gradient(135deg, var(--accent), var(--accent2)); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(79,70,229,.3); transition: all 0.3s;">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">No</th>
                            <th class="um-th">Nama Mitra</th>
                            <th class="um-th">Klasifikasi</th>
                            <th class="um-th">Negara</th>
                            <th class="um-th um-th-num">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($mitras ?? collect()) as $index => $mitra)
                            <tr class="um-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-name">{{ $mitra->nama_mitra }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">
                                        {{ $mitra->klasifikasi->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">
                                        <i class="fas fa-globe-asia" style="margin-right: 5px; color: var(--text-sub);"></i>
                                        {{ $mitra->negara ?? 'Indonesia' }}
                                    </span>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="um-actions">
                                        <a href="{{ route('unit.mitra.show', $mitra->id) }}" class="um-btn-view" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('unit.mitra.edit', $mitra->id) }}" class="um-btn-warn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('unit.mitra.destroy', $mitra->id) }}" method="POST" style="display: inline-flex;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')">
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
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state">
                                        <div class="um-empty-icon"><i class="fas fa-handshake-slash"></i></div>
                                        <p class="um-empty-text">Belum ada mitra yang terdaftar untuk unit ini.</p>
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
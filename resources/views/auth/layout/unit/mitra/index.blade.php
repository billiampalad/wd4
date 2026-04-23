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
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #059669; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; font-size: 13px;">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #dc2626; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; font-size: 13px;">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card um-card">
        <div class="card-header um-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
            <div class="um-title" style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-handshake" style="color: var(--accent);"></i>
                <span>Mitra Kerjasama</span>
            </div>
            <a href="{{ route('unit.mitra.create') }}" class="rfc-btn rfc-btn-primary" style="text-decoration: none;">
                <i class="fas fa-plus-circle"></i> Tambah Mitra
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Nama Mitra</th>
                            <th class="um-th">Kategori</th>
                            <th class="um-th">Negara</th>
                            <th class="um-th">Total Kegiatan</th>
                            <th class="um-th">Aksi</th>
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
                                    <span class="tag tag-{{ $mitra->kategori == 'nasional' ? 'blue' : 'purple' }}" style="font-size: 11px;">
                                        {{ ucfirst($mitra->kategori) }}
                                    </span>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">
                                        <i class="fas fa-globe-asia" style="margin-right: 5px; color: var(--text-sub);"></i>
                                        {{ $mitra->negara ?? 'Indonesia' }}
                                    </span>
                                </td>
                                <td class="um-td">
                                    <span class="tag tag-green" style="font-family: 'DM Mono', monospace; font-size: 11px;">
                                        {{ $mitra->kegiatan_kerjasamas_count ?? 0 }} Kegiatan
                                    </span>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="um-actions">
                                        <a href="{{ route('unit.mitra.show', $mitra->id) }}" class="btn-action view" title="Detail Mitra">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('unit.mitra.edit', $mitra->id) }}" class="btn-action edit" title="Edit Mitra">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('unit.mitra.destroy', $mitra->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action delete" title="Hapus Mitra">
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
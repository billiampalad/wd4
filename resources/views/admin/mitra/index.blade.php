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
                <span>Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Mitra Kerjasama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Kelola data mitra kerjasama nasional dan internasional.
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
                        <th class="um-th">Status Akun</th>
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
                                {{ $mitra->cooperations->count() }} Kegiatan
                            </span>
                        </td>
                        <td class="um-td">
                            @php
                                $kegiatanAktif = $mitra->cooperations
                                    ->filter(fn($cooperation) => !$cooperation->end_date || now()->isBefore($cooperation->end_date))
                                    ->count();
                            @endphp
                            @if($mitra->cooperations->count() > 0)
                                @if($kegiatanAktif > 0)
                                    <span class="tag tag-green"><i class="fas fa-check-circle" style="margin-right: 4px;"></i> {{ $kegiatanAktif }} Aktif</span>
                                @else
                                    <span class="tag tag-red"><i class="fas fa-clock" style="margin-right: 4px;"></i> Selesai/Expired</span>
                                @endif
                            @else
                                <span class="um-meta">-</span>
                            @endif
                        </td>
                        <td class="um-td">
                            @if($mitra->users->count() > 0)
                                <span class="tag tag-blue"><i class="fas fa-check-circle" style="margin-right: 4px;"></i> Terdaftar</span>
                            @else
                                <span class="tag tag-yellow" style="background-color: rgba(245, 158, 11, 0.1); color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; border: 1px solid rgba(245, 158, 11, 0.2);"><i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> Belum Punya Akun</span>
                            @endif
                        </td>
                        <td class="um-td um-td-aksi">
                            <div class="actions um-actions">
                                @if($mitra->users->count() == 0)
                                    <button type="button" class="btn-action send um-btn-send" title="Kirim Akses Login" onclick="openAccessModal({{ $mitra->id }}, '{{ addslashes($mitra->nama_mitra) }}')" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9px; border: none; cursor: pointer; background: rgba(37, 99, 235, 0.15); color: #2563eb;">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-action send um-btn-send" title="Kirim Ulang Akses Login" onclick="openAccessModal({{ $mitra->id }}, '{{ addslashes($mitra->nama_mitra) }}')" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9px; border: none; cursor: pointer; background: rgba(37, 99, 235, 0.15); color: #2563eb;">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                @endif
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
                        <td colspan="8" class="um-empty">
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

    <!-- Premium Modal Kirim Akses Login -->


    <div id="accessModal" class="premium-modal-overlay">
        <div class="premium-modal-card">
            <button type="button" class="premium-modal-close" onclick="closeAccessModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            <form id="accessForm" method="POST" action="">
                @csrf
                <div class="premium-modal-body">
                    <div class="premium-modal-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <h3 class="premium-modal-title">Kirim Akses Login</h3>
                    <p class="premium-modal-desc">
                        Tentukan alamat email untuk mengirimkan kredensial login kepada:
                        <strong id="modalMitraName"></strong>
                    </p>
                    
                    <div class="premium-input-group">
                        <label for="email" class="premium-label">Alamat Email Mitra</label>
                        <div class="premium-input-wrapper">
                            <input type="email" name="email" id="email" class="premium-input" required placeholder="contoh@mitra.com">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
                <div class="premium-modal-footer">
                    <button type="button" class="premium-btn premium-btn-cancel" onclick="closeAccessModal()">Batal</button>
                    <button type="submit" class="premium-btn premium-btn-submit">
                        <i class="fas fa-paper-plane"></i> Kirim Akses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAccessModal(id, name) {
            document.getElementById('modalMitraName').textContent = name;
            document.getElementById('accessForm').action = '/admin/mitra/' + id + '/send-access';
            
            var modal = document.getElementById('accessModal');
            modal.classList.add('active');
            
            // Set focus ke input email
            setTimeout(() => {
                document.getElementById('email').focus();
            }, 300); // Tunggu animasi selesai
        }

        function closeAccessModal() {
            var modal = document.getElementById('accessModal');
            modal.classList.remove('active');
            
            // Bersihkan form setelah animasi selesai
            setTimeout(() => {
                document.getElementById('email').value = '';
            }, 300);
        }
        
        // Tutup modal jika klik area luar modal
        document.getElementById('accessModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAccessModal();
            }
        });
        
        // Tutup dengan tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('accessModal').classList.contains('active')) {
                closeAccessModal();
            }
        });
    </script>
</main>
@endsection

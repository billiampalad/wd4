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

    <!-- Modal Kirim Akses Login -->
    <div id="accessModal" class="um-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card um-card" style="width: 450px; max-width: 90%; background: var(--bg-card); border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: 1px solid var(--border-color);">
            <div class="card-header um-header" style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
                <div class="card-title" style="margin: 0; font-size: 1.1rem;"><i class="fas fa-key" style="color: var(--blue-500);"></i> Kirim Akses Login</div>
                <button type="button" onclick="closeAccessModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-sub); transition: color 0.2s;">&times;</button>
            </div>
            <form id="accessForm" method="POST" action="">
                @csrf
                <div style="padding: 20px;">
                    <p style="margin-bottom: 20px; font-size: 0.95rem; color: var(--text-sub); line-height: 1.5;">
                        Kirimkan kredensial login ke mitra: <br>
                        <strong id="modalMitraName" style="color: var(--text-main); font-size: 1.05rem;"></strong>
                    </p>
                    <div style="margin-bottom: 15px;">
                        <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.9rem; color: var(--text-main);">Alamat Email</label>
                        <input type="email" name="email" id="email" required placeholder="Masukkan email aktif mitra..." style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-body); color: var(--text-main); font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
                    </div>
                </div>
                <div style="padding: 15px 20px; border-top: 1px solid var(--border-color); text-align: right; background: var(--bg-body);">
                    <button type="button" onclick="closeAccessModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; cursor: pointer; margin-right: 10px; color: var(--text-sub); font-weight: 500;">Batal</button>
                    <button type="submit" class="um-btn-add" style="padding: 10px 18px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-paper-plane"></i> Kirim Akses</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAccessModal(id, name) {
            document.getElementById('modalMitraName').textContent = name;
            // Kita bisa menggunakan route manual atau menyesuaikan action URL
            document.getElementById('accessForm').action = '/admin/mitra/' + id + '/send-access';
            
            var modal = document.getElementById('accessModal');
            modal.style.display = 'flex';
            
            // Set focus ke input email
            setTimeout(() => {
                document.getElementById('email').focus();
            }, 100);
        }

        function closeAccessModal() {
            document.getElementById('accessModal').style.display = 'none';
            document.getElementById('email').value = '';
        }
        
        // Tutup modal jika klik area luar modal
        document.getElementById('accessModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAccessModal();
            }
        });
    </script>
</main>
@endsection

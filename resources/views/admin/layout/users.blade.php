@extends('admin.dashboard')

@section('content')
    @php
        $roleLabels = [
            'pimpinan' => 'Pimpinan',
            'jurusan' => 'Jurusan',
            'prodi' => 'Prodi',
            'mitra' => 'Mitra',
            'unit_kerja' => 'Humas',
            'upa' => 'Upa',
            'pusat' => 'Pusat',
            'admin' => 'Admin',
        ];
    @endphp
    <main class="main-content admin-dashboard">
        <section class="ud-topbar">
            <div class="ud-hero-copy">
                <div class="ud-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>/</span>
                    <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                    <span>/</span>
                    <span>Pengguna</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-users"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Master Data</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Tambah, edit, lihat detail dan hapus data pengguna sistem.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="card um-card">
            <div class="card-header um-header">
                <div class="card-title"><i class="fas fa-users"></i> Daftar Pengguna</div>
                <a href="{{ route('users.create') }}" class="um-btn-add">
                    <i class="fas fa-plus"></i> Tambah Pengguna
                </a>
            </div>
            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">NIK</th>
                            <th class="um-th">Nama</th>
                            <th class="um-th">Email</th>
                            <th class="um-th">Password</th>
                            <th class="um-th">Role</th>
                            <th class="um-th">Status</th>
                            <th class="um-th">Jabatan</th>
                            <th class="um-th">Jurusan</th>
                            <th class="um-th">Unit</th>
                            <th class="um-th">UPA</th>
                            <th class="um-th">Pusat</th>
                            <th class="um-th">Dibuat</th>
                            <th class="um-th">Diperbarui</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                            <tr class="um-row {{ !$user->isActive() ? 'um-row-inactive' : '' }}">
                                <td class="um-td um-td-num">
                                    <span class="um-num">{{ $i + 1 }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-nik">{{ $user->nik ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <div class="user-cell um-user-cell">
                                        <div class="avatar um-avatar um-avatar-color-{{ ($i % 6) }}">
                                            {{ strtoupper(substr($user->name ?? '??', 0, 2)) }}
                                        </div>
                                        <span class="um-name">{{ $user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">{{ $user->email ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <div class="um-pass-wrap">
                                        <span class="um-pass-dots">••••••••</span>
                                        <span class="um-pass-real" style="display:none;"
                                            title="{{ $user->password ?? '-' }}">{{ Str::limit($user->password ?? '-', 10) }}</span>
                                        <button class="um-pass-toggle" onclick="togglePass(this)" title="Lihat password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="um-td">
                                    <span class="tag tag-{{ strtolower($user->role?->role_name ?? 'default') }} um-role-tag">
                                        {{ $roleLabels[$user->role?->role_name] ?? $user->role?->role_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="um-td">
                                    @if($user->isActive())
                                        <span class="badge-status badge-status-active" title="Akun aktif dan dapat login">
                                            <i class="fas fa-circle-check"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge-status badge-status-danger" title="Akun dinonaktifkan sementara">
                                            <i class="fas fa-circle-xmark"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="um-td"><span class="um-meta">{{ $user->profile?->jabatan ?? '-' }}</span></td>
                                <td class="um-td">
                                    <span class="um-meta">{{ $user->profile?->jurusan?->nama_jurusan ?? '-' }}</span>
                                    @if($user->profile?->prodi)
                                        <div style="font-size: 11px; color: var(--text-muted, #64748b); margin-top: 2px;">
                                            <i class="fas fa-book-open" style="font-size: 10px; margin-right: 3px;"></i>{{ $user->profile->prodi->jenjang ? '[' . $user->profile->prodi->jenjang . '] ' : '' }}{{ $user->profile->prodi->nama_prodi }}
                                        </div>
                                    @endif
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">{{ $user->profile?->unitKerja?->nama_unit_pelaksana ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">{{ $user->profile?->upa?->nama_upa ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">{{ $user->profile?->pusat?->nama_pusat ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-plus um-date-icon"></i>
                                        {{ $user->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-check um-date-icon"></i>
                                        {{ $user->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="actions um-actions">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn-action detail um-btn-detail"
                                            title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn-action edit um-btn-edit"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.toggle-status', $user->id) }}" method="POST"
                                                onsubmit="return confirm('{{ $user->isActive() ? 'Apakah Anda yakin ingin menonaktifkan sementara akun ' . addslashes($user->name) . '? Pengguna tidak akan dapat login ke portal.' : 'Apakah Anda yakin ingin mengaktifkan kembali akun ' . addslashes($user->name) . '?' }}')">
                                                @csrf
                                                @method('PATCH')
                                                @if($user->isActive())
                                                    <button type="submit" class="btn-action toggle-deactivate um-btn-deactivate" title="Nonaktifkan Akun">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn-action toggle-activate um-btn-activate" title="Aktifkan Akun">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?')">
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
                                <td colspan="15" class="um-empty">
                                    <div class="um-empty-state">
                                        <div class="um-empty-icon">
                                            <i class="fas fa-users-slash"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada data pengguna</p>
                                        <p class="um-empty-sub">Klik tombol <strong>Tambah Pengguna</strong> untuk memulai.</p>
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
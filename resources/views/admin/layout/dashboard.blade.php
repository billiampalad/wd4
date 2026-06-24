@extends('admin.dashboard')

@section('content')
<main class="admin-dashboard">
    <!-- Page Header -->
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <span id="breadcrumbCurrent">Dashboard</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-user-shield"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Dashboard Admin</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Sistem Informasi Manajemen Kerjasama Polimdo &amp; DUDIKA
                    </p>
                </div>
            </div>
        </div>
        <a href="{{ route('users.index') }}" class="ud-create-menu">
            <span class="ud-create-icon"><i class="fas fa-users-gear"></i></span>
            <span class="ud-create-copy">
                <strong>Kelola Pengguna</strong>
                <small>Tambah, ubah, atau hapus akun pengguna</small>
            </span>
            <span class="ud-create-arrow"><i class="fas fa-arrow-right"></i></span>
        </a>
    </section>

    <!-- Profil Admin yang Sedang Login -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-user-shield"></i> Informasi Admin</div>
        </div>
        <div class="card-body" style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            <div class="avatar" style="background:#4f46e5; width:52px; height:52px; font-size:18px; flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 2)) }}
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px,1fr)); gap:8px 28px; flex:1;">
                <div>
                    <div style="font-size:11px; color:var(--text-sub); margin-bottom:2px;">Nama</div>
                    <div style="font-weight:600; font-size:13px;">{{ auth()->user()?->name ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--text-sub); margin-bottom:2px;">NIK</div>
                    <div style="font-weight:600; font-size:13px; font-family:'DM Mono',monospace;">{{ auth()->user()?->nik ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--text-sub); margin-bottom:2px;">Role</div>
                    <div>
                        <span class="tag tag-green">
                            <i class="fas fa-circle" style="font-size:6px;"></i>
                            {{ auth()->user()?->role?->role_name ?? '-' }}
                        </span>
                    </div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--text-sub); margin-bottom:2px;">Jabatan / Unit Kerja</div>
                    <div style="font-weight:600; font-size:13px;">{{ auth()->user()?->profile?->jabatan ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(79,70,229,.12); color:var(--accent);">
                    <i class="fas fa-users"></i>
                </div>
                <span class="stat-badge badge-up"><i class="fas fa-database" style="font-size:8px;"></i> {{ $totalUsersPct }}%</span>
            </div>
            <div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value">{{ number_format($totalUsers) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(14,165,233,.12); color:#0ea5e9;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="stat-badge badge-up"><i class="fas fa-database" style="font-size:8px;"></i> {{ $pimpinanPct }}%</span>
            </div>
            <div>
                <div class="stat-label">Total Pimpinan</div>
                <div class="stat-value">{{ number_format($totalPimpinan) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(16,185,129,.12); color:#10b981;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="stat-badge badge-up"><i class="fas fa-database" style="font-size:8px;"></i> {{ $unitKerjaGabunganPct }}%</span>
            </div>
            <div>
                <div class="stat-label">Total Unit Kerja</div>
                <div class="stat-value">{{ number_format($totalUnitKerjaGabungan) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:rgba(245,158,11,.12); color:#f59e0b;">
                    <i class="fas fa-briefcase"></i>
                </div>
                <span class="stat-badge badge-up"><i class="fas fa-database" style="font-size:8px;"></i> {{ $unitPct }}%</span>
            </div>
            <div>
                <div class="stat-label">Total Humas</div>
                <div class="stat-value">{{ number_format($totalUnitKerja) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:#EED9B9; color:#D53E0F;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <span class="stat-badge badge-up"><i class="fas fa-database" style="font-size:8px;"></i> {{ $adminPct }}%</span>
            </div>
            <div>
                <div class="stat-label">Total Admin</div>
                <div class="stat-value">{{ number_format($totalAdmin) }}</div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="content-row">

        <!-- Tabel User Terbaru -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-list-alt"></i>User Terbaru</div>
                <a href="{{ route('users.index') }}" class="card-action">Lihat Semua</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userTerbaru as $i => $user)
                        <tr>
                            <td><span style="font-family:'DM Mono',monospace; font-size:11px; color:var(--text-sub);">{{ str_pad($i+1, 3, '0', STR_PAD_LEFT) }}</span></td>
                            <td><span style="font-family:'DM Mono',monospace; font-size:12px;">{{ $user->nik }}</span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div class="avatar" style="width:28px; height:28px; font-size:10px; background:#4f46e5; flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <span style="font-weight:600; font-size:13px;">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleName = strtolower($user->role?->role_name ?? '');
                                    $roleLabels = [
                                        'pusat' => 'Pusat',
                                        'upa' => 'UPA',
                                        'jurusan' => 'Jurusan',
                                        'unit_kerja' => 'Humas',
                                        'pimpinan' => 'Pimpinan',
                                    ];
                                    $roleTagClasses = [
                                        'pimpinan' => 'tag-blue',
                                        'pusat' => 'tag-orange',
                                        'upa' => 'tag-orange',
                                        'jurusan' => 'tag-orange',
                                        'unit_kerja' => 'tag-green',
                                    ];
                                    $roleLabel = $roleLabels[$roleName] ?? \Illuminate\Support\Str::headline($roleName ?: 'unit_kerja');
                                    $roleTagClass = $roleTagClasses[$roleName] ?? 'tag-orange';
                                @endphp
                                <span class="tag {{ $roleTagClass }}">
                                    <i class="fas fa-circle" style="font-size:6px;"></i> {{ $roleLabel }}
                                </span>
                            </td>
                            <td><span style="font-size:12px; color:var(--text-sub);">{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:28px 16px; color:var(--text-sub);">
                                <i class="fas fa-users-slash" style="display:block; margin-bottom:8px; font-size:20px;"></i>
                                Belum ada data user pada database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right column -->
        <div style="display:flex; flex-direction:column; gap:20px;">

            <!-- Role Sistem -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-shield-alt"></i> Role dalam Sistem</div>
                </div>
                <div class="card-body">
                    <div class="progress-row">
                        <div class="progress-label">
                            <span><i class="fas fa-user-tie" style="margin-right:6px; color:#4f46e5;"></i>Pimpinan</span>
                            <span>{{ number_format($totalPimpinan) }} user</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" @style(['width'=> $pimpinanPct . '%'])></div>
                        </div>
                    </div>
                    <div class="progress-row">
                        <div class="progress-label">
                            <span><i class="fas fa-graduation-cap" style="margin-right:6px; color:#0ea5e9;"></i>Unit Kerja</span>
                            <span>{{ number_format($totalUnitKerjaGabungan) }} user</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill sky" @style(['width'=> $unitKerjaGabunganPct . '%'])></div>
                        </div>
                    </div>
                    <div class="progress-row">
                        <div class="progress-label">
                            <span><i class="fas fa-briefcase" style="margin-right:6px; color:#f59e0b;"></i>Humas</span>
                            <span>{{ number_format($totalUnitKerja) }} user</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill amber" @style(['width'=> $unitPct . '%'])></div>
                        </div>
                    </div>
                    <div class="progress-row">
                        <div class="progress-label">
                            <span><i class="fas fa-user-shield" style="margin-right:6px; color:#D53E0F;"></i>Admin</span>
                            <span>{{ number_format($totalAdmin) }} user</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill amber" @style(['width'=> $adminPct . '%'])></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Menu -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-th"></i> Menu Cepat</div>
                </div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                    <a href="{{ route('users.index') }}" style="text-decoration:none;">
                        <div class="activity-item" style="cursor:pointer; border-radius:10px; padding:10px 12px; transition:background .15s;" onmouseover="this.style.background='rgba(79,70,229,.06)'" onmouseout="this.style.background='transparent'">
                            <div class="activity-dot" style="background:rgba(79,70,229,.12); color:var(--accent);">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title" style="font-weight:600;">Kelola Users</div>
                                <div class="activity-meta">Tambah, edit, hapus pengguna sistem</div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-sub); font-size:11px; margin-left:auto;"></i>
                        </div>
                    </a>
                    <a href="{{ route('roles.index') }}" style="text-decoration:none;">
                        <div class="activity-item" style="cursor:pointer; border-radius:10px; padding:10px 12px; transition:background .15s;" onmouseover="this.style.background='rgba(14,165,233,.06)'" onmouseout="this.style.background='transparent'">
                            <div class="activity-dot" style="background:rgba(14,165,233,.12); color:#0ea5e9;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title" style="font-weight:600;">Kelola Roles</div>
                                <div class="activity-meta">Atur hak akses dan peran pengguna</div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-sub); font-size:11px; margin-left:auto;"></i>
                        </div>
                    </a>
                    <a href="{{ route('admin.profiles') }}" style="text-decoration:none;">
                        <div class="activity-item" style="cursor:pointer; border-radius:10px; padding:10px 12px; transition:background .15s;" onmouseover="this.style.background='rgba(16,185,129,.06)'" onmouseout="this.style.background='transparent'">
                            <div class="activity-dot" style="background:rgba(16,185,129,.12); color:#10b981;">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title" style="font-weight:600;">Kelola Profiles</div>
                                <div class="activity-meta">Kelola data profil &amp; jabatan pengguna</div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-sub); font-size:11px; margin-left:auto;"></i>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

</main>
@endsection
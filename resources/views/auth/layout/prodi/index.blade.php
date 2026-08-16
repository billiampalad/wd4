@php
$user = auth()->user();
$prodi = $user->profile?->prodi;
$prodiName = $prodi->nama_prodi ?? 'Program Studi';

$penempatanList = $penempatans ?? collect();
if ($penempatanList->isEmpty() && $user->profile?->prodi_id) {
    $penempatanList = \App\Models\KegiatanMahasiswa::with(['mahasiswa', 'kegiatan', 'mitra', 'pembimbings'])
        ->whereHas('mahasiswa', function($q) use ($user) {
            $q->where('prodi_id', $user->profile->prodi_id);
        })
        ->orderBy('created_at', 'desc')
        ->get();
}

$totalMhsAktif = $totalMahasiswaAktif ?? $penempatanList->where('status', 'Aktif')->count();
$totalSemuaPenempatan = $penempatanList->count();
$totalAlumniCount = $totalAlumni ?? \App\Models\Alumni::where('prodi_id', $user->profile?->prodi_id)->count();
$alumniBekerjaCount = $alumniBekerja ?? \App\Models\AlumniMitra::where('status', 'Aktif')
    ->whereHas('alumni', function($q) use ($user) {
        $q->where('prodi_id', $user->profile?->prodi_id);
    })->count();
$persenIKU = $persentasePenyerapan ?? ($totalAlumniCount > 0 ? round(($alumniBekerjaCount / $totalAlumniCount) * 100, 1) : 0);
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<!-- Main Content -->
<main id="mainContent" class="dk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Dashboard Program Studi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-graduation-cap"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Portal Kegiatan Mahasiswa & Alumni</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Pantau penempatan magang, kegiatan kerja sama mahasiswa, dan tracking lulusan untuk
                        <strong>{{ $prodiName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Kartu Statistik KPI Prodi -->
    <section class="dk-stats-grid" aria-label="Ringkasan data program studi">
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div>
                <span class="dk-stat-label">Mahasiswa Magang Aktif</span>
                <strong>{{ number_format($totalMhsAktif) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="dk-stat-label">Total Penempatan</span>
                <strong>{{ number_format($totalSemuaPenempatan) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-users"></i></div>
            <div>
                <span class="dk-stat-label">Total Alumni Terdata</span>
                <strong>{{ number_format($totalAlumniCount) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #10b981;">
            <div class="dk-stat-icon" style="color: #10b981; background: rgba(16,185,129,0.1);"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="dk-stat-label">Penyerapan di Mitra (% IKU)</span>
                <strong>{{ $persenIKU }}%</strong>
            </div>
        </div>
    </section>

    <!-- Filter Data -->
    <div class="report-filter-container" x-data="{ showFilters: false }">
        <div class="rfc-header"
            style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;"
            @click="showFilters = !showFilters">
            <div class="rfc-title-area">
                <div class="rfc-icon"><i class="fas fa-sliders-h"></i></div>
                <div class="rfc-text">
                    <h3>Filter Data Kegiatan Mahasiswa</h3>
                    <p>Saring data penempatan berdasarkan status, kegiatan, atau mitra industri</p>
                </div>
            </div>
            <div style="color: var(--text-sub); font-size: 16px; transition: transform 0.3s;"
                :style="showFilters ? 'transform: rotate(180deg)' : 'transform: rotate(0)'">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <div class="rfc-body" x-show="showFilters" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-4">
            <form id="filterForm" class="rfc-form" method="GET" action="{{ route('prodi.penempatan.index') }}">
                <div class="rfc-grid">
                    <div class="rfc-group" x-data="{
                        open: false,
                        selected: 'all',
                        items: [{ id: 'all', label: 'Semua Status' }, { id: 'Aktif', label: 'Aktif' }, { id: 'Selesai', label: 'Selesai' }, { id: 'Dibatalkan', label: 'Dibatalkan' }],
                        get selectedLabel() { return this.items.find(i => i.id === this.selected)?.label || 'Semua Status'; }
                    }">
                        <label>Status Penempatan</label>
                        <input type="hidden" name="status" :value="selected">
                        <div class="alpine-dropdown" @click.outside="open = false">
                            <div class="ad-trigger" :class="{ 'active': open }" @click="open = !open">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-info-circle" style="color: #9ca3af; font-size: 13px;"></i>
                                    <span x-text="selectedLabel"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="open" x-transition>
                                <template x-for="item in items" :key="item.id">
                                    <div class="ad-item" :class="{ 'selected': selected == item.id }"
                                        @click="selected = item.id; open = false"
                                        x-text="item.label"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rfc-footer">
                    <button type="submit" id="btnTampilkan" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-user-graduate"></i></span>
                <span>
                    <strong>Daftar Penempatan Mahasiswa</strong>
                    <small id="penempatanCount">{{ $penempatanList->count() }} mahasiswa terdaftar</small>
                </span>
            </div>

            <div class="dk-card-tools" x-data="{ showModal: false }">
                <button @click="showModal = true" class="dk-primary-btn">
                    <i class="fas fa-plus"></i>
                    <span>Tindakan Baru</span>
                </button>

                {{-- MODAL PILIH TINDAKAN --}}
                <div x-show="showModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="modal-overlay"
                    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
                    @click.self="showModal = false"
                    x-cloak>

                    <div class="modal-card"
                        x-show="showModal"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        style="background: var(--surface); border-radius: 24px; width: 100%; max-width: 550px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--border);">

                        {{-- Modal Header --}}
                        <div style="padding: 24px 32px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, var(--surface), var(--surface2));">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">Pilih Tindakan Penginputan</h3>
                                </div>
                                <button @click="showModal = false" style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 14px; transition: 0.2s;" onmouseover="this.style.color='#ef4444'">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Modal Body --}}
                        <div style="padding: 32px;">
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                {{-- Opsi 1: Penempatan Mahasiswa --}}
                                <a href="{{ route('prodi.penempatan.create') }}"
                                    class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    onmouseover="this.style.borderColor='#4f46e5'; this.style.background='rgba(79,70,229,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Tambah Penempatan Mahasiswa</span>
                                        <p style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">Daftarkan mahasiswa ke mitra industri untuk kegiatan magang, penelitian, atau pelatihan.</p>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: #9ca3af; font-size: 14px;"></i>
                                </a>

                                {{-- Opsi 2: Data Alumni Bekerja --}}
                                <a href="{{ route('prodi.alumni.create') }}"
                                    class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    onmouseover="this.style.borderColor='#10b981'; this.style.background='rgba(16,185,129,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Tambah Data Alumni Bekerja</span>
                                        <p style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">Catat lulusan/alumni prodi yang telah terserap dan bekerja di perusahaan mitra kerja sama.</p>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: #9ca3af; font-size: 14px;"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div style="padding: 20px 32px; background: var(--surface2); border-top: 1px solid var(--border); text-align: center;">
                            <span style="font-size: 11px; color: var(--text-sub); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pilih tindakan untuk melanjutkan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th dk-th-expand">
                                <span class="dk-expand-head-icon" title="Expand Rincian">
                                    <i class="fas fa-sort-amount-down"></i>
                                </span>
                            </th>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th" style="min-width: 200px;">Mahasiswa</th>
                            <th class="um-th" style="min-width: 220px;">Mitra & Kegiatan</th>
                            <th class="um-th">Periode</th>
                            <th class="um-th" style="text-align: center;">Nilai Mitra</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penempatanList as $item)
                        @php
                            $status = strtolower($item->status ?? 'aktif');
                            $statusClass = match (true) {
                                $status === 'aktif' => 'dk-status-active',
                                $status === 'selesai' => 'dk-status-info',
                                $status === 'dibatalkan' => 'dk-status-danger',
                                default => 'dk-status-neutral',
                            };
                            $statusIcon = match (true) {
                                $status === 'aktif' => 'fa-circle-check',
                                $status === 'selesai' => 'fa-flag-checkered',
                                $status === 'dibatalkan' => 'fa-circle-xmark',
                                default => 'fa-circle-info',
                            };

                            $mhsName = $item->mahasiswa?->nama ?? '-';
                            $mhsNim = $item->mahasiswa?->nim ?? '-';
                            $mitraName = $item->mitra?->nama_mitra ?? '-';
                            $kegiatanName = $item->kegiatan?->nama_kegiatan ?? 'Kegiatan Magang/Kerjasama';

                            $mulai = $item->periode_mulai ? \Carbon\Carbon::parse($item->periode_mulai)->format('d M Y') : '-';
                            $selesai = $item->periode_selesai ? \Carbon\Carbon::parse($item->periode_selesai)->format('d M Y') : '-';

                            $pembimbingInternal = $item->pembimbings?->where('tipe', 'Internal')->first();
                            $pembimbingEksternal = $item->pembimbings?->where('tipe', 'Eksternal')->first();
                        @endphp
                        <tr class="um-row dk-row" data-row-id="{{ $item->id }}">
                            <td class="um-td dk-td-expand" style="vertical-align: top; padding-top: 12px;">
                                <button type="button" class="dk-expand-toggle" aria-expanded="false" aria-controls="dk-detail-{{ $item->id }}" title="Lihat rincian pembimbing & nilai">
                                    <i class="fas fa-angles-right"></i>
                                </button>
                            </td>
                            <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-doc-cell">
                                    <span class="dk-doc-number">NIM: {{ $mhsNim }}</span>
                                    <span class="dk-doc-title" style="font-weight: 700;">{{ $mhsName }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-entity" style="align-items: flex-start;">
                                    <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span class="dk-entity-text" style="padding-top: 2px;">
                                        <strong style="display:block; color:var(--text);">{{ $mitraName }}</strong>
                                        <small style="color:var(--text-sub);">{{ $kegiatanName }}</small>
                                    </span>
                                </div>
                            </td>
                            <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                <div class="dk-date-range-compact">
                                    <span class="date-val">{{ $mulai }}</span>
                                    <span class="date-sep">s/d</span>
                                    <span class="date-val">{{ $selesai }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="text-align: center; vertical-align: top; padding-top: 15px;">
                                @if($item->nilai_mitra)
                                    <span class="dk-status dk-status-active" style="display: inline-flex; font-weight: 800; font-size: 13px;">
                                        {{ $item->nilai_mitra }}
                                    </span>
                                @else
                                    <span class="dk-status dk-status-neutral" style="display: inline-flex; font-size: 11px;">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <span class="dk-status {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }}"></i>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                <div class="um-actions dk-actions-compact">
                                    <a href="{{ route('prodi.penempatan.show', $item->id) }}" class="dk-action-btn view" title="Detail Penempatan">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('prodi.penempatan.edit', $item->id) }}" class="dk-action-btn edit" title="Edit Penempatan">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('prodi.penempatan.destroy', $item->id) }}" method="POST"
                                        class="dk-delete-form" style="display: inline;"
                                        onsubmit="return confirm('Yakin ingin menghapus data penempatan mahasiswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dk-action-btn delete" title="Hapus">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="dk-row-detail" id="dk-detail-{{ $item->id }}" aria-hidden="true">
                            <td colspan="8" class="dk-detail-cell">
                                <div class="dk-detail-content">
                                    <div class="dk-audit-grid">
                                        <section class="dk-audit-card">
                                            <div class="dk-audit-card-head">
                                                <span class="dk-audit-icon dk-audit-created"><i class="fas fa-chalkboard-user"></i></span>
                                                <strong>Pembimbing Internal (Dosen Polimdo)</strong>
                                            </div>
                                            <div class="dk-audit-person">{{ $pembimbingInternal?->nama_pembimbing ?? '-' }}</div>
                                            <div class="dk-audit-meta">
                                                <span>Kontak: {{ $pembimbingInternal?->kontak ?? '-' }}</span>
                                            </div>
                                        </section>
                                        <section class="dk-audit-card">
                                            <div class="dk-audit-card-head">
                                                <span class="dk-audit-icon dk-audit-updated" style="color: #10b981; background: rgba(16,185,129,0.1);"><i class="fas fa-user-tie"></i></span>
                                                <strong>Pembimbing Eksternal (Mitra Industri)</strong>
                                            </div>
                                            <div class="dk-audit-person">{{ $pembimbingEksternal?->nama_pembimbing ?? '-' }}</div>
                                            <div class="dk-audit-meta">
                                                <span>Kontak: {{ $pembimbingEksternal?->kontak ?? '-' }}</span>
                                                @if($item->catatan_mitra)
                                                    <span>Catatan: "{{ $item->catatan_mitra }}"</span>
                                                @endif
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr data-empty>
                            <td colspan="8" class="um-empty">
                                <div class="um-empty-state dk-empty-state">
                                    <div class="um-empty-icon dk-empty-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada penempatan mahasiswa</p>
                                    <p class="um-empty-sub">Klik tombol "Tindakan Baru" untuk mendaftarkan mahasiswa magang atau kegiatan di mitra.</p>
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

<script>
    document.addEventListener('turbo:load', function() {
        const toggleButtons = document.querySelectorAll('.dk-expand-toggle');
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const tr = this.closest('.dk-row');
                const detailRowId = this.getAttribute('aria-controls');
                const detailRow = document.getElementById(detailRowId);
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                if (isExpanded) {
                    this.setAttribute('aria-expanded', 'false');
                    detailRow.classList.remove('open');
                    setTimeout(() => {
                        detailRow.style.display = 'none';
                    }, 300);
                } else {
                    document.querySelectorAll('.dk-row-detail.open').forEach(row => {
                        row.classList.remove('open');
                        row.previousElementSibling.querySelector('.dk-expand-toggle').setAttribute('aria-expanded', 'false');
                        setTimeout(() => row.style.display = 'none', 300);
                    });

                    this.setAttribute('aria-expanded', 'true');
                    detailRow.style.display = 'table-row';
                    setTimeout(() => detailRow.classList.add('open'), 10);
                }
            });
        });
    });
</script>

@php
$user = auth()->user();
$mitra = $user->mitra;
$mitraName = $mitra->nama_mitra ?? 'Mitra Eksternal';

// Fetch kerjasama if not passed from controller
$kerjasamaList = $kerjasamaList ?? collect();
if ($kerjasamaList->isEmpty() && $mitra) {
    $kerjasamaList = \App\Models\Cooperation::with(['jurusans', 'upas', 'pusats', 'createdBy', 'updatedBy'])
                        ->where('mitra_id', $mitra->id)
                        ->get();
}

$totalKerjasama = $kerjasamaList->count();
$aktifCount = $kerjasamaList->filter(fn ($item) => strtolower($item->status ?? '') === 'aktif')->count();
$perpanjanganCount = $kerjasamaList->filter(fn ($item) => str_contains(strtolower($item->status ?? ''), 'perpanjangan'))->count();
$expiredCount = $kerjasamaList->filter(function ($item) {
    $status = strtolower($item->status ?? '');
    return in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
})->count();

// Use stats passed from controller if available, else default
$totalMhsMagang = $totalMahasiswaAktif ?? 0;
$alumniCount = $alumniTerserap ?? 0;

$auditUserLabel = function ($user = null) {
    $roleName = $user?->role?->role_name;
    $roleLabel = match (strtolower($roleName ?? '')) {
        'unit_kerja' => 'Humas',
        'jurusan' => $user?->profile?->jurusan?->nama_jurusan
            ? 'Jurusan - ' . $user->profile->jurusan->nama_jurusan
            : 'Jurusan',
        'pusat' => $user?->profile?->pusat?->nama_pusat
            ? 'Pusat - ' . $user->profile->pusat->nama_pusat
            : 'Pusat',
        'upa' => $user?->profile?->upa?->nama_upa
            ? 'UPA - ' . $user->profile->upa->nama_upa
            : 'UPA',
        default => $roleName ? ucfirst($roleName) : '-',
    };

    return [
        'name' => $user?->name ?: '-',
        'jabatan' => $user?->profile?->jabatan ?: '-',
        'role' => $roleLabel,
    ];
};
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<!-- Main Content -->
<style>
    /* Premium Tabs */
    .dk-tabs-container {
        margin-bottom: 24px;
        background: var(--bg-card, #ffffff);
        border-radius: 16px;
        padding: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    [data-theme="dark"] .dk-tabs-container {
        border-color: rgba(255, 255, 255, 0.05);
    }
    .dk-tabs-wrapper {
        display: flex;
        gap: 8px;
        overflow-x: auto;
    }
    .dk-tab-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        background: transparent;
        border: none;
        border-radius: 12px;
        font-family: inherit;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-sub, #64748b);
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    .dk-tab-btn:hover {
        background: rgba(241, 245, 249, 0.5);
        color: var(--text-main, #334155);
    }
    [data-theme="dark"] .dk-tab-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #e2e8f0;
    }
    .dk-tab-btn.active {
        background: var(--bg-body, #f8fafc);
        color: #4f46e5;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    [data-theme="dark"] .dk-tab-btn.active {
        background: rgba(79, 70, 229, 0.15);
        color: #818cf8;
    }
    .dk-tab-badge {
        background: #ef4444;
        color: white;
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 99px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }

    /* Premium Review Modal (UC13) */
    .review-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .review-modal-card {
        background: var(--bg-card, #ffffff);
        width: 100%;
        max-width: 1100px;
        height: 90vh;
        border-radius: 24px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
    }
    .review-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 32px;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        background: linear-gradient(to right, var(--bg-card), var(--bg-body));
    }
    .review-modal-title {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .review-modal-title .icon-box {
        width: 48px;
        height: 48px;
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    [data-theme="dark"] .review-modal-title .icon-box {
        color: #818cf8;
    }
    .review-modal-title h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
    }
    .review-modal-title p {
        margin: 4px 0 0 0;
        font-size: 0.875rem;
        color: var(--text-sub);
    }
    .review-modal-close {
        background: transparent;
        border: none;
        color: var(--text-sub);
        font-size: 1.25rem;
        cursor: pointer;
        transition: color 0.2s;
        padding: 8px;
    }
    .review-modal-close:hover {
        color: #ef4444;
    }
    .review-modal-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }
    .review-pdf-panel {
        flex: 1.2;
        background: #1e293b;
        border-right: 1px solid var(--border-color, #e2e8f0);
        position: relative;
    }
    .review-form-panel {
        flex: 0.8;
        padding: 32px;
        overflow-y: auto;
        background: var(--bg-card);
    }
    .review-check-list {
        background: var(--bg-body, #f8fafc);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid var(--border-color, #e2e8f0);
    }
    .review-check-list h4 {
        margin: 0 0 16px 0;
        font-size: 0.95rem;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .review-check-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 0.875rem;
        color: var(--text-sub);
    }
    .review-check-item i {
        color: #10b981;
        margin-top: 3px;
    }
    .review-textarea {
        width: 100%;
        min-height: 150px;
        padding: 16px;
        border-radius: 16px;
        border: 2px solid var(--border-color, #e2e8f0);
        background: var(--bg-body);
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.95rem;
        resize: vertical;
        transition: all 0.3s ease;
    }
    .review-textarea:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        background: var(--bg-card);
    }
    .review-action-btns {
        display: flex;
        gap: 16px;
        margin-top: 24px;
    }
    .btn-review-submit {
        flex: 1;
        padding: 14px;
        border-radius: 14px;
        border: none;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-review-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 14px rgba(217, 119, 6, 0.3);
    }
    .btn-review-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
    }
    .btn-review-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    }
    .btn-review-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
</style>
<main id="mainContent" class="dk-page" x-data="mitraDashboard()" x-cloak>
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Dashboard Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake-angle"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Portal Kerja Sama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Selamat datang, pantau dokumen kerja sama, mahasiswa magang, dan alumni untuk
                        <strong>{{ $mitraName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Kartu Statistik -->
    <section class="dk-stats-grid" aria-label="Ringkasan data kerjasama">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-folder-open"></i></div>
            <div>
                <span class="dk-stat-label">Total Dokumen KS</span>
                <strong>{{ number_format($totalKerjasama) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Dokumen Aktif</span>
                <strong>{{ number_format($aktifCount) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div>
                <span class="dk-stat-label">Mhs Magang Aktif</span>
                <strong>{{ number_format($totalMhsMagang) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger" style="border-left-color: #8b5cf6;">
            <div class="dk-stat-icon" style="color: #8b5cf6; background: rgba(139,92,246,0.1);"><i class="fas fa-briefcase"></i></div>
            <div>
                <span class="dk-stat-label">Alumni Terserap</span>
                <strong>{{ number_format($alumniCount) }}</strong>
            </div>
        </div>
    </section>

    <div class="dk-tabs-container">
        <div class="dk-tabs-wrapper">
            <button @click="activeTab = 'all'" :class="{'active': activeTab === 'all'}" class="dk-tab-btn">
                <i class="fas fa-layer-group"></i> Semua Dokumen
            </button>
            <button @click="activeTab = 'draft'" :class="{'active': activeTab === 'draft'}" class="dk-tab-btn">
                <i class="fas fa-file-signature"></i> Menunggu Review 
                @php
                    $draftCount = $kerjasamaList->filter(function($i) {
                        $s = strtolower($i->status ?? '');
                        return in_array($s, ['draft', 'menunggu evaluasi', 'menunggu review']);
                    })->count();
                @endphp
                @if($draftCount > 0)
                <span class="dk-tab-badge">{{ $draftCount }}</span>
                @endif
            </button>
            <button @click="activeTab = 'aktif'" :class="{'active': activeTab === 'aktif'}" class="dk-tab-btn">
                <i class="fas fa-circle-check"></i> Dokumen Aktif
            </button>
            <button @click="activeTab = 'perpanjangan'" :class="{'active': activeTab === 'perpanjangan'}" class="dk-tab-btn">
                <i class="fas fa-clock-rotate-left"></i> Masa Tenggang
            </button>
        </div>
    </div>

    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                <span>
                    <strong>Daftar Dokumen Kerjasama Saya</strong>
                    <small id="dkerjasamaCount">{{ $kerjasamaList->count() }} data ditemukan</small>
                </span>
            </div>

            <div style="display: flex; gap: 10px; margin-left: auto; margin-right: 15px;">
                <select x-model="jenisFilter" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                    <option value="all">Semua Jenis</option>
                    <option value="MoU">MoU</option>
                    <option value="MoA">MoA</option>
                    <option value="IA">IA</option>
                </select>
                <select x-model="periodeFilter" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text); font-size: 13px;">
                    <option value="all">Semua Periode</option>
                    @php
                        $years = $kerjasamaList->map(function($k) { return $k->start_date ? $k->start_date->format('Y') : null; })->filter()->unique()->sortDesc();
                    @endphp
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
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
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: var(--text); letter-spacing: -0.01em;">Pilih Tindakan</h3>
                                </div>
                                <button @click="showModal = false" style="background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 8px; font-size: 14px; transition: 0.2s;" onmouseover="this.style.color='#ef4444'">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Modal Body --}}
                        <div style="padding: 32px;">
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                {{-- Opsi 1: Pengajuan Baru --}}
                                <a href="{{ route('mitra.pengajuan.create') ?? '#' }}"
                                    class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); group;"
                                    onmouseover="this.style.borderColor='#4f46e5'; this.style.background='rgba(79,70,229,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-file-signature"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Ajukan Kerja Sama Baru</span>
                                        <p style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">Gunakan ini untuk mengajukan dokumen kerja sama baru (MoU / MoA / IA) dengan Polimdo.</p>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: #9ca3af; font-size: 14px; opacity: 0; transition: 0.3s; transform: translateX(-10px);"></i>
                                </a>

                                {{-- Opsi 2: Perpanjangan --}}
                                <a href="#"
                                    class="modal-option-card"
                                    style="display: flex; align-items: center; gap: 20px; padding: 24px; border-radius: 20px; border: 2px solid var(--border); background: var(--surface); text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    onmouseover="this.style.borderColor='#d97706'; this.style.background='rgba(217,119,6,0.03)'; this.style.transform='translateY(-4px)';"
                                    onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface)'; this.style.transform='none';">
                                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(217,119,6,0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; transition: 0.3s;">
                                        <i class="fas fa-clock-rotate-left"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px;">Ajukan Perpanjangan</span>
                                        <p style="margin: 0; font-size: 12px; color: var(--text-sub); line-height: 1.5;">Ajukan perpanjangan untuk dokumen kerja sama yang masa berlakunya hampir habis.</p>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: #9ca3af; font-size: 14px; opacity: 0; transition: 0.3s; transform: translateX(-10px);"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div style="padding: 20px 32px; background: var(--surface2); border-top: 1px solid var(--border); text-align: center;">
                            <span style="font-size: 11px; color: var(--text-sub); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pilih tindakan yang ingin Anda lakukan</span>
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
                                <span class="dk-expand-head-icon" title="Expand">
                                    <i class="fas fa-sort-amount-down"></i>
                                </span>
                            </th>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 450px; min-width: 300px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana Kampus</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berlaku</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kerjasamaList as $kegiatan)
                        @php
                            $status = strtolower($kegiatan->status ?? '');
                            $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
                            $isExtended = str_contains($status, 'perpanjangan');

                            $tabCategory = match (true) {
                                $status === 'aktif' => 'aktif',
                                $status === 'draft' || str_contains($status, 'menunggu') => 'draft',
                                $isExtended || $isExpired => 'perpanjangan',
                                default => 'other',
                            };

                            $statusClass = match (true) {
                                $status === 'aktif' => 'dk-status-active',
                                $status === 'proses' || str_contains($status, 'menunggu') => 'dk-status-info',
                                $isExtended => 'dk-status-warning',
                                $isExpired => 'dk-status-danger',
                                $status === 'tidak aktif' => 'dk-status-muted',
                                default => 'dk-status-neutral',
                            };
                            $statusIcon = match (true) {
                                $status === 'aktif' => 'fa-circle-check',
                                $status === 'proses' || str_contains($status, 'menunggu') => 'fa-spinner fa-spin',
                                $isExtended => 'fa-clock',
                                $isExpired => 'fa-circle-xmark',
                                $status === 'tidak aktif' => 'fa-circle-minus',
                                default => 'fa-circle-info',
                            };
                            $statusLabel = match (true) {
                                $status === 'aktif' => 'Aktif',
                                $isExtended => 'Perpanjangan',
                                $isExpired => 'Kadaluarsa',
                                $status === 'tidak aktif' => 'Tidak Aktif',
                                $status !== '' => ucwords(str_replace('_', ' ', $status)),
                                default => 'Belum Diatur',
                            };

                            $pelaksanaGroups = collect();
                            if ($kegiatan->jurusans && $kegiatan->jurusans->isNotEmpty()) {
                                $pelaksanaGroups->push(['type' => 'Jurusan', 'icon' => 'fa-building', 'class' => 'dk-entity-indigo', 'names' => $kegiatan->jurusans->pluck('nama_jurusan')->toArray()]);
                            }
                            if ($kegiatan->upas && $kegiatan->upas->isNotEmpty()) {
                                $pelaksanaGroups->push(['type' => 'UPA', 'icon' => 'fa-building', 'class' => 'dk-entity-emerald', 'names' => $kegiatan->upas->pluck('nama_upa')->toArray()]);
                            }
                            if ($kegiatan->pusats && $kegiatan->pusats->isNotEmpty()) {
                                $pelaksanaGroups->push(['type' => 'Pusat', 'icon' => 'fa-building', 'class' => 'dk-entity-amber', 'names' => $kegiatan->pusats->pluck('nama_pusat')->toArray()]);
                            }
                            if ($pelaksanaGroups->isEmpty()) {
                                $pelaksanaGroups->push(['type' => 'Polimdo', 'icon' => 'fa-university', 'class' => 'dk-entity-indigo', 'names' => ['Tingkat Institusi']]);
                            }

                            $mulai = $kegiatan->start_date?->format('d M Y') ?? '-';
                            $selesai = $kegiatan->end_date?->format('d M Y') ?? '-';
                            $docNumber = $kegiatan->doc_number ?? '-';
                            $title = $kegiatan->judul ?? '-';
                            $docJenis = $kegiatan->jenis ?? '';
                            $docTahun = $kegiatan->start_date ? $kegiatan->start_date->format('Y') : '';
                        @endphp
                        <tr class="um-row dk-row" data-row-id="{{ $kegiatan->id }}" x-show="(activeTab === 'all' || activeTab === '{{ $tabCategory }}') && (jenisFilter === 'all' || jenisFilter === '{{ $docJenis }}') && (periodeFilter === 'all' || periodeFilter === '{{ $docTahun }}')">
                            <td class="um-td dk-td-expand" style="vertical-align: top; padding-top: 12px;">
                                <button type="button" class="dk-expand-toggle" aria-expanded="false" aria-controls="dk-detail-{{ $kegiatan->id }}" title="Lihat metadata">
                                    <i class="fas fa-angles-right"></i>
                                </button>
                            </td>
                            <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td dk-title-cell" style="vertical-align: top; padding-top: 15px;">
                                <div class="dk-doc-cell">
                                    <span class="dk-doc-number">#{{ $docNumber }}</span>
                                    <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5;">{{ $title }}</span>
                                    <span class="dk-doc-kind">{{ $kegiatan->jenis ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <div style="display: grid; gap: 8px;">
                                    @foreach ($pelaksanaGroups as $group)
                                        <div class="dk-entity" style="align-items: flex-start;">
                                            <span class="dk-entity-icon {{ $group['class'] }}" style="flex-shrink: 0;">
                                                <i class="fas {{ $group['icon'] }}"></i>
                                            </span>
                                            <span class="dk-entity-text" style="padding-top: 2px;">
                                                <small style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--text-sub); margin-bottom:2px;">{{ $group['type'] }}</small>
                                                {{ implode(', ', $group['names']) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                <div class="dk-date-range-compact">
                                    <span class="date-val">{{ $mulai }}</span>
                                    <span class="date-sep">s/d</span>
                                    <span class="date-val">{{ $selesai }}</span>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                <span class="dk-status {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }}"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                <div class="um-actions dk-actions-compact">
                                    @if($tabCategory === 'draft')
                                    <button @click="openReview({{ $kegiatan->id }}, '{{ $docNumber }}', '{{ addslashes($title) }}', '{{ route('mitra.dokumen.show', $kegiatan->id) }}')" class="dk-action-btn edit" title="Review Draf Online" style="color: #4f46e5; background: rgba(79,70,229,0.1); border:none; cursor:pointer;">
                                        <i class="fas fa-file-signature"></i>
                                    </button>
                                    @else
                                    <a href="{{ route('mitra.dokumen.show', $kegiatan->id) ?? '#' }}" class="dk-action-btn view" title="Lihat Dokumen">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr class="dk-row-detail" id="dk-detail-{{ $kegiatan->id }}" aria-hidden="true">
                            <td colspan="7" class="dk-detail-cell">
                                <div class="dk-detail-content">
                                    <div class="dk-audit-grid">
                                        <section class="dk-audit-card">
                                            <div class="dk-audit-card-head">
                                                <span class="dk-audit-icon dk-audit-created"><i class="fas fa-user-plus"></i></span>
                                                <strong>Diusulkan oleh Kampus</strong>
                                            </div>
                                            <div class="dk-audit-person">{{ $kegiatan->createdBy?->name ?? 'Sistem' }}</div>
                                            <div class="dk-audit-meta">
                                                <span>Dibuat: {{ $kegiatan->created_at?->format('d M Y') ?? '-' }}</span>
                                            </div>
                                        </section>
                                        <section class="dk-audit-card">
                                            <div class="dk-audit-card-head">
                                                <span class="dk-audit-icon dk-audit-updated" style="color: #4f46e5; background: rgba(79,70,229,0.1);"><i class="fas fa-list-check"></i></span>
                                                <strong>Status Draf Mitra</strong>
                                            </div>
                                            <div class="dk-audit-person">Menunggu Review</div>
                                            <div class="dk-audit-meta">
                                                <span>Silakan cek dokumen di tombol Review Draf</span>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr data-empty>
                            <td colspan="7" class="um-empty">
                                <div class="um-empty-state dk-empty-state">
                                    <div class="um-empty-icon dk-empty-icon">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada dokumen kerjasama</p>
                                    <p class="um-empty-sub">Anda belum memiliki dokumen kerja sama aktif dengan Politeknik Negeri Manado.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Review Modal UC13 -->
    <template x-if="showReviewModal">
        <div class="review-modal-overlay" @click.self="showReviewModal = false" x-cloak>
            <div class="review-modal-card"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                 
                <!-- Modal Header -->
                <div class="review-modal-header">
                    <div class="review-modal-title">
                        <div class="icon-box">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <h3>Review Draf Online</h3>
                            <p>Dokumen <strong x-text="reviewDocNumber"></strong> - <span x-text="reviewDocTitle"></span></p>
                        </div>
                    </div>
                    <button class="review-modal-close" @click="showReviewModal = false" title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Modal Body (Split View) -->
                <div class="review-modal-body">
                    <!-- Kiri: PDF Preview -->
                    <div class="review-pdf-panel">
                        <object :data="reviewPdfUrl" type="application/pdf" width="100%" height="100%">
                            <div style="padding: 20px; color: white; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <i class="fas fa-file-pdf" style="font-size: 48px; margin-bottom: 16px; color: #94a3b8;"></i>
                                <p>Browser Anda tidak mendukung preview PDF langsung.</p>
                                <a :href="reviewPdfUrl" target="_blank" style="color: #60a5fa; text-decoration: underline; margin-top: 8px;">Unduh PDF</a>
                            </div>
                        </object>
                    </div>
                    
                    <!-- Kanan: Form Catatan -->
                    <div class="review-form-panel">
                        <form method="POST" :action="`/mitra/dokumen/${reviewDocId}/review`">
                            @csrf
                            <div class="review-check-list">
                                <h4><i class="fas fa-list-check"></i> Poin Pemeriksaan</h4>
                                <div class="review-check-item">
                                    <i class="fas fa-check-circle"></i> <span>Kesesuaian identitas pihak yang bertanda tangan.</span>
                                </div>
                                <div class="review-check-item">
                                    <i class="fas fa-check-circle"></i> <span>Ketentuan hak dan kewajiban masing-masing pihak.</span>
                                </div>
                                <div class="review-check-item">
                                    <i class="fas fa-check-circle"></i> <span>Periode masa berlaku dan penyelesaian masalah.</span>
                                </div>
                            </div>
                            
                            <label style="display: block; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Catatan Review Klausul</label>
                            <textarea name="catatan_review" class="review-textarea" placeholder="Tuliskan catatan Anda di sini jika ada pasal/klausul yang perlu diperbaiki... Jika draf sudah sesuai, Anda bisa mengosongkannya."></textarea>
                            
                            <div class="review-action-btns">
                                <button type="submit" name="action" value="revisi" class="btn-review-submit btn-review-warning">
                                    <i class="fas fa-pen-to-square"></i> Kirim Revisi
                                </button>
                                <button type="submit" name="action" value="setuju" class="btn-review-submit btn-review-success">
                                    <i class="fas fa-check-double"></i> Setujui Draf
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</main>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mitraDashboard', () => ({
            activeTab: 'all',
            jenisFilter: 'all',
            periodeFilter: 'all',
            showReviewModal: false,
            reviewDocId: null,
            reviewDocNumber: '',
            reviewDocTitle: '',
            reviewPdfUrl: '',
            
            openReview(id, docNumber, title, pdfUrl) {
                this.reviewDocId = id;
                this.reviewDocNumber = docNumber;
                this.reviewDocTitle = title;
                this.reviewPdfUrl = pdfUrl;
                this.showReviewModal = true;
            }
        }));
    });

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

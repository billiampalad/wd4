@php
    $bentukList = $bentukKegiatans ?? collect();
    $totalBentuk = $bentukList->count();
    $totalCooperationsCount = $bentukList->sum('total_count');

    // Find the most active/frequent activity type
    $mostActive = $bentukList->sortByDesc('total_count')->first();
    $mostActiveName = $mostActive && $mostActive->total_count > 0 ? $mostActive->nama_kerjasama : '-';
    $mostActiveCount = $mostActive ? $mostActive->total_count : 0;
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('pusat.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Bentuk Kegiatan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-university"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Bentuk Kegiatan</h2>
                    <p class="ud-subtitle">
                        Daftar bentuk kegiatan yang terlibat dalam pengelolaan kerjasama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Bentuk Kegiatan</span>
                <div class="dk-stat-value">{{ $totalBentuk }} <span>Jenis</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-primary">
            <div class="dk-stat-icon"><i class="fas fa-file-contract"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Penggunaan</span>
                <div class="dk-stat-value">{{ $totalCooperationsCount }} <span>Kerjasama</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-success">
            <div class="dk-stat-icon"><i class="fas fa-star"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Terbanyak Digunakan</span>
                <div class="dk-stat-value"
                    style="font-size: 15px; font-weight: 700; line-height: 1.2; margin-top: 4px;">
                    {{ Str::limit($mostActiveName, 30) }}
                    <span style="font-size: 12px; font-weight: 500; color: var(--ud-text-muted); display: block;">
                        ({{ $mostActiveCount }} Kerjasama)
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-list-ul"></i></span>
                <span>
                    <strong>Daftar Bentuk Kegiatan</strong>
                    <small>Referensi bentuk kegiatan kerjasama</small>
                </span>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num" style="width: 80px;">#</th>
                            <th class="um-th dk-col-name">Nama Bentuk Kegiatan</th>
                            <th class="um-th" style="text-align: center; width: 200px;">Jumlah Kerjasama</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bentukList as $bentuk)
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-col-name" style="vertical-align: middle;">
                                    <div class="dk-entity" style="gap: 14px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span class="dk-entity-text"
                                                style="font-weight: 700; font-size: 14px; color: var(--ud-text);">{{ $bentuk->nama_kerjasama }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-active"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $bentuk->total_count }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="3" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon"><i class="fas fa-book-open"></i></div>
                                        <p class="um-empty-title">Belum ada data bentuk kegiatan</p>
                                        <p class="um-empty-sub">Data bentuk kegiatan kerjasama akan tampil di sini.</p>
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

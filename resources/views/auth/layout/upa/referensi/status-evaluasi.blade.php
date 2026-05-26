@php
    $statusList = $referensiStatusEvaluasi ?? collect();
    $totalStatus = $statusList->count();
    $totalCooperationsCount = $statusList->sum('total');

    // Find the status with the most cooperations
    $mostFrequent = $statusList->sortByDesc('total')->first();
    $mostFrequentName = $mostFrequent && $mostFrequent['total'] > 0 ? $mostFrequent['name'] : '-';
    $mostFrequentCount = $mostFrequent ? $mostFrequent['total'] : 0;
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('upa.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Status Evaluasi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-file-signature"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Status Evaluasi</h2>
                    <p class="ud-subtitle">
                        Referensi status tahapan evaluasi dokumen kerjasama oleh Pimpinan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-clipboard-list"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Tipe Status</span>
                <div class="dk-stat-value">{{ $totalStatus }} <span>Kategori</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-primary">
            <div class="dk-stat-icon"><i class="fas fa-file-contract"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Dokumen Terkait</span>
                <div class="dk-stat-value">{{ $totalCooperationsCount }} <span>Kerjasama</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-success">
            <div class="dk-stat-icon"><i class="fas fa-star"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Status Terbanyak</span>
                <div class="dk-stat-value"
                    style="font-size: 15px; font-weight: 700; line-height: 1.2; margin-top: 4px;">
                    {{ $mostFrequentName }}
                    <span style="font-size: 12px; font-weight: 500; color: var(--ud-text-muted); display: block;">
                        ({{ $mostFrequentCount }} Kerjasama)
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
                    <strong>Daftar Status Evaluasi</strong>
                    <small>Referensi status evaluasi dan persetujuan dokumen</small>
                </span>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num" style="width: 80px;">#</th>
                            <th class="um-th" style="width: 220px;">Kategori Status</th>
                            <th class="um-th dk-col-name">Keterangan / Deskripsi</th>
                            <th class="um-th" style="text-align: center; width: 200px;">Jumlah Kerjasama</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($statusList as $status)
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <span class="dk-status {{ $status['badge'] }}"
                                        style="font-weight: 700; font-size: 13px; text-transform: capitalize; padding: 4px 12px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ $status['name'] }}
                                    </span>
                                </td>
                                <td class="um-td dk-col-name" style="vertical-align: middle;">
                                    <div class="dk-entity" style="gap: 14px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span class="dk-entity-text"
                                                style="font-size: 13.5px; color: var(--ud-text-muted); font-weight: 500; line-height: 1.4;">
                                                {{ $status['description'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-active"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $status['total'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="4" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon"><i class="fas fa-check-circle"></i></div>
                                        <p class="um-empty-title">Belum ada data status</p>
                                        <p class="um-empty-sub">Referensi status evaluasi kerjasama akan tampil di sini.</p>
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

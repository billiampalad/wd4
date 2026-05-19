@php
    $jurusanList = $jurusans ?? collect();
    $upaList = $upas ?? collect();
    $pusatList = $pusats ?? collect();
    $totalInstitusi = $jurusanList->count() + $upaList->count() + $pusatList->count();
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Institusi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-chart-line"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Institusi Pelaksana</h2>
                    <p class="ud-subtitle">
                        Daftar jurusan, UPA, dan pusat yang terlibat dalam pengelolaan kerjasama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stat-grid">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah Institusi</span>
                <div class="dk-stat-value">{{ $totalInstitusi }} <span>Data</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-primary">
            <div class="dk-stat-icon"><i class="fas fa-microchip"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah Jurusan</span>
                <div class="dk-stat-value">{{ $jurusanList->count() }} <span>Data</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-building-columns"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah UPA</span>
                <div class="dk-stat-value">{{ $upaList->count() }} <span>Data</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-success">
            <div class="dk-stat-icon"><i class="fas fa-landmark"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah Pusat</span>
                <div class="dk-stat-value">{{ $pusatList->count() }} <span>Data</span></div>
            </div>
        </div>
    </section>

    <section class="dk-jenis-grid">
        <div class="dk-jenis-card dk-jenis-mou">
            <div class="dk-jenis-icon"><i class="fas fa-file-signature"></i></div>
            <div class="dk-jenis-content">
                <span class="dk-jenis-label">Memorandum of Understanding</span>
                <div class="dk-jenis-value">{{ $mouCount ?? 0 }} <span>Dokumen</span></div>
                <span class="dk-jenis-abbr">MoU</span>
            </div>
        </div>
        <div class="dk-jenis-card dk-jenis-moa">
            <div class="dk-jenis-icon"><i class="fas fa-file-contract"></i></div>
            <div class="dk-jenis-content">
                <span class="dk-jenis-label">Memorandum of Agreement</span>
                <div class="dk-jenis-value">{{ $moaCount ?? 0 }} <span>Dokumen</span></div>
                <span class="dk-jenis-abbr">MoA</span>
            </div>
        </div>
        <div class="dk-jenis-card dk-jenis-ia">
            <div class="dk-jenis-icon"><i class="fas fa-file-circle-check"></i></div>
            <div class="dk-jenis-content">
                <span class="dk-jenis-label">Implementation Agreement</span>
                <div class="dk-jenis-value">{{ $iaCount ?? 0 }} <span>Dokumen</span></div>
                <span class="dk-jenis-abbr">IA</span>
            </div>
        </div>
    </section>

    <div class="card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-list-ul"></i></span>
                <span>
                    <strong>Daftar Institusi</strong>
                    <small>Referensi unit pelaksana kerjasama</small>
                </span>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num" style="width: 72px;">#</th>
                            <th class="um-th">Nama Institusi</th>
                            <th class="um-th" style="width: 180px;">Jenis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jurusanList as $jurusan)
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <div class="dk-entity">
                                        <span class="dk-entity-icon dk-entity-indigo" style="flex-shrink: 0;">
                                            <i class="fas fa-microchip"></i>
                                        </span>
                                        <span class="dk-entity-text">{{ $jurusan->nama_jurusan }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <span class="dk-status dk-status-info">
                                        <i class="fas fa-microchip"></i>
                                        Jurusan
                                    </span>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        @foreach ($upaList as $upa)
                            @php $index = $jurusanList->count() + $loop->iteration; @endphp
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <div class="dk-entity">
                                        <span class="dk-entity-icon dk-entity-cyan" style="flex-shrink: 0;">
                                            <i class="fas fa-building-columns"></i>
                                        </span>
                                        <span class="dk-entity-text">{{ $upa->nama_upa }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <span class="dk-status dk-status-warning">
                                        <i class="fas fa-building-columns"></i>
                                        UPA
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($pusatList as $pusat)
                            @php $index = $jurusanList->count() + $upaList->count() + $loop->iteration; @endphp
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <div class="dk-entity">
                                        <span class="dk-entity-icon dk-entity-violet" style="flex-shrink: 0;">
                                            <i class="fas fa-landmark"></i>
                                        </span>
                                        <span class="dk-entity-text">{{ $pusat->nama_pusat }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <span class="dk-status dk-status-active">
                                        <i class="fas fa-landmark"></i>
                                        Pusat
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                        @if ($totalInstitusi === 0)
                            <tr data-empty>
                                <td colspan="3" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon"><i class="fas fa-university"></i></div>
                                        <p class="um-empty-title">Belum ada data institusi</p>
                                        <p class="um-empty-sub">Data jurusan, UPA, dan pusat akan tampil di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="{{ asset('js/auth/user.js') }}" data-turbo-track="reload"></script>
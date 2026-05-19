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

    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-list-ul"></i></span>
                <span>
                    <strong>Daftar Institusi</strong>
                    <small>Referensi unit pelaksana kerjasama</small>
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="um-table dk-table">
                <thead>
                    <tr>
                        <th class="um-th" style="width: 72px;">No</th>
                        <th class="um-th">Nama Institusi</th>
                        <th class="um-th" style="width: 180px;">Jenis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jurusanList as $jurusan)
                        <tr>
                            <td class="um-td">{{ $loop->iteration }}</td>
                            <td class="um-td">{{ $jurusan->nama_jurusan }}</td>
                            <td class="um-td"><span class="dk-badge dk-badge-info">Jurusan</span></td>
                        </tr>
                    @empty
                    @endforelse

                    @foreach ($upaList as $upa)
                        <tr>
                            <td class="um-td">{{ $jurusanList->count() + $loop->iteration }}</td>
                            <td class="um-td">{{ $upa->nama_upa }}</td>
                            <td class="um-td"><span class="dk-badge dk-badge-warning">UPA</span></td>
                        </tr>
                    @endforeach

                    @foreach ($pusatList as $pusat)
                        <tr>
                            <td class="um-td">{{ $jurusanList->count() + $upaList->count() + $loop->iteration }}</td>
                            <td class="um-td">{{ $pusat->nama_pusat }}</td>
                            <td class="um-td"><span class="dk-badge dk-badge-success">Pusat</span></td>
                        </tr>
                    @endforeach

                    @if ($totalInstitusi === 0)
                        <tr>
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
</main>
@php
    $jurusanList = $jurusans ?? collect();
    $upaList = $upas ?? collect();
    $pusatList = $pusats ?? collect();
    $totalInstitusi = $jurusanList->count() + $upaList->count() + $pusatList->count();
@endphp

<main id="mainContent" class="dk-page">
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('unit.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <span class="current">Institusi</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <span class="dk-eyebrow">Referensi Unit</span>
                    <h2 id="pageTitle">Institusi Pelaksana</h2>
                    <p id="pageDesc">Daftar jurusan, UPA, dan pusat yang terlibat dalam pengelolaan kerjasama.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stats-grid">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="dk-stat-label">Total Institusi</span>
                <div>{{ $totalInstitusi }} Data</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-microchip"></i></div>
            <div>
                <span class="dk-stat-label">Jurusan</span>
                <div>{{ $jurusanList->count() }} Data</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-building-columns"></i></div>
            <div>
                <span class="dk-stat-label">UPA</span>
                <div>{{ $upaList->count() }} Data</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger">
            <div class="dk-stat-icon"><i class="fas fa-landmark"></i></div>
            <div>
                <span class="dk-stat-label">Pusat</span>
                <div>{{ $pusatList->count() }} Data</div>
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

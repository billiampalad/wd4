@php
    $jurusanList = $jurusans ?? collect();
    $upaList = $upas ?? collect();
    $pusatList = $pusats ?? collect();
    $instansiData = $instansi ?? (object) [
        'nama_instansi' => 'Politeknik Negeri Manado',
        'mou_count' => 0,
        'moa_count' => 0,
        'ia_count' => 0,
        'total_count' => 0,
    ];
    $totalInstitusi = 1 + $jurusanList->count() + $upaList->count() + $pusatList->count();
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
                <span class="ud-title-icon"><i class="fas fa-university"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Institusi Pelaksana</h2>
                    <p class="ud-subtitle">
                        Daftar Instansi, jurusan, UPA, dan pusat yang terlibat dalam pengelolaan kerjasama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stat-grid">
        <div class="dk-stat-card dk-stat-total" data-filter="all">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah Institusi</span>
                <div class="dk-stat-value">{{ $totalInstitusi }} <span>Data</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-primary" data-filter="type-jurusan">
            <div class="dk-stat-icon"><i class="fas fa-microchip"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah Jurusan</span>
                <div class="dk-stat-value">{{ $jurusanList->count() }} <span>Data</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning" data-filter="type-upa">
            <div class="dk-stat-icon"><i class="fas fa-building-columns"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah UPA</span>
                <div class="dk-stat-value">{{ $upaList->count() }} <span>Data</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-success" data-filter="type-pusat">
            <div class="dk-stat-icon"><i class="fas fa-landmark"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Jumlah Pusat</span>
                <div class="dk-stat-value">{{ $pusatList->count() }} <span>Data</span></div>
            </div>
        </div>
    </section>

    <section class="dk-jenis-grid">
        <div class="dk-jenis-card dk-jenis-mou" data-filter="doc-mou">
            <div class="dk-jenis-icon"><i class="fas fa-file-signature"></i></div>
            <div class="dk-jenis-content">
                <span class="dk-jenis-label">Memorandum of Understanding</span>
                <div class="dk-jenis-value">{{ $mouCount ?? 0 }} <span>Dokumen</span></div>
                <span class="dk-jenis-abbr">MoU</span>
            </div>
        </div>
        <div class="dk-jenis-card dk-jenis-moa" data-filter="doc-moa">
            <div class="dk-jenis-icon"><i class="fas fa-file-contract"></i></div>
            <div class="dk-jenis-content">
                <span class="dk-jenis-label">Memorandum of Agreement</span>
                <div class="dk-jenis-value">{{ $moaCount ?? 0 }} <span>Dokumen</span></div>
                <span class="dk-jenis-abbr">MoA</span>
            </div>
        </div>
        <div class="dk-jenis-card dk-jenis-ia" data-filter="doc-ia">
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
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-col-name">Nama Institusi</th>
                            <th class="um-th" style="text-align: center;">MoU</th>
                            <th class="um-th" style="text-align: center;">MoA</th>
                            <th class="um-th" style="text-align: center;">IA</th>
                            <th class="um-th" style="text-align: center;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="um-row dk-row" data-type="instansi" data-mou="{{ $instansiData->mou_count }}"
                            data-moa="{{ $instansiData->moa_count }}" data-ia="{{ $instansiData->ia_count }}">
                            <td class="um-td um-td-num" style="vertical-align: middle;">
                                <span class="um-num dk-num">01</span>
                            </td>
                            <td class="um-td dk-col-name" style="vertical-align: middle;">
                                <div class="dk-entity" style="gap: 14px;">
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span class="dk-entity-text"
                                            style="font-weight: 700; font-size: 14px; color: var(--ud-text);">{{ $instansiData->nama_instansi }}</span>
                                        <span class="dk-status dk-status-active"
                                            style="width: fit-content; font-size: 10.5px; padding: 1px 8px; height: auto;">
                                            <i class="fas fa-university" style="font-size: 9px; margin-right: 4px;"></i>
                                            Instansi
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="um-td" style="vertical-align: middle; text-align: center;">
                                <span class="dk-status dk-status-info"
                                    style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $instansiData->mou_count }}</span>
                            </td>
                            <td class="um-td" style="vertical-align: middle; text-align: center;">
                                <span class="dk-status dk-status-warning"
                                    style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $instansiData->moa_count }}</span>
                            </td>
                            <td class="um-td" style="vertical-align: middle; text-align: center;">
                                <span class="dk-status dk-status-danger"
                                    style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $instansiData->ia_count }}</span>
                            </td>
                            <td class="um-td" style="vertical-align: middle; text-align: center;">
                                <span class="dk-status dk-status-active"
                                    style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $instansiData->total_count }}</span>
                            </td>
                        </tr>

                        @forelse ($jurusanList as $jurusan)
                            <tr class="um-row dk-row" data-type="jurusan" data-mou="{{ $jurusan->mou_count }}"
                                data-moa="{{ $jurusan->moa_count }}" data-ia="{{ $jurusan->ia_count }}">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-col-name" style="vertical-align: middle;">
                                    <div class="dk-entity" style="gap: 14px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span class="dk-entity-text"
                                                style="font-weight: 700; font-size: 14px; color: var(--ud-text);">{{ $jurusan->nama_jurusan }}</span>
                                            <span class="dk-status dk-status-info"
                                                style="width: fit-content; font-size: 10.5px; padding: 1px 8px; height: auto;">
                                                <i class="fas fa-microchip" style="font-size: 9px; margin-right: 4px;"></i>
                                                Jurusan
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-info"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $jurusan->mou_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-warning"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $jurusan->moa_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-danger"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $jurusan->ia_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-active"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $jurusan->total_count }}</span>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        @foreach ($upaList as $upa)
                            @php $index = 1 + $jurusanList->count() + $loop->iteration; @endphp
                            <tr class="um-row dk-row" data-type="upa" data-mou="{{ $upa->mou_count }}"
                                data-moa="{{ $upa->moa_count }}" data-ia="{{ $upa->ia_count }}">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-col-name" style="vertical-align: middle;">
                                    <div class="dk-entity" style="gap: 14px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span class="dk-entity-text"
                                                style="font-weight: 700; font-size: 14px; color: var(--ud-text);">{{ $upa->nama_upa }}</span>
                                            <span class="dk-status dk-status-warning"
                                                style="width: fit-content; font-size: 10.5px; padding: 1px 8px; height: auto;">
                                                <i class="fas fa-building-columns"
                                                    style="font-size: 9px; margin-right: 4px;"></i>
                                                UPA
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-info"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $upa->mou_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-warning"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $upa->moa_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-danger"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $upa->ia_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-active"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $upa->total_count }}</span>
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($pusatList as $pusat)
                            @php $index = 1 + $jurusanList->count() + $upaList->count() + $loop->iteration; @endphp
                            <tr class="um-row dk-row" data-type="pusat" data-mou="{{ $pusat->mou_count }}"
                                data-moa="{{ $pusat->moa_count }}" data-ia="{{ $pusat->ia_count }}">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-col-name" style="vertical-align: middle;">
                                    <div class="dk-entity" style="gap: 14px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span class="dk-entity-text"
                                                style="font-weight: 700; font-size: 14px; color: var(--ud-text);">{{ $pusat->nama_pusat }}</span>
                                            <span class="dk-status dk-status-active"
                                                style="width: fit-content; font-size: 10.5px; padding: 1px 8px; height: auto;">
                                                <i class="fas fa-landmark" style="font-size: 9px; margin-right: 4px;"></i>
                                                Pusat
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-info"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $pusat->mou_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-warning"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $pusat->moa_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-danger"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $pusat->ia_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-active"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $pusat->total_count }}</span>
                                </td>
                            </tr>
                        @endforeach

                        @if ($totalInstitusi === 0)
                            <tr data-empty>
                                <td colspan="6" class="um-empty">
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

<script src="{{ asset('js/auth/unit/institusi.js') }}" data-turbo-track="reload"></script>

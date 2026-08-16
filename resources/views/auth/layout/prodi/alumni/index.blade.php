@extends('auth.prodi')

@section('content')
<main id="mainContent" class="dk-page">
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Tracking Lulusan (Alumni)</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-briefcase"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Data Alumni & Penyerapan Kerja Sama</h2>
                    <p class="ud-subtitle">
                        Tracking lulusan yang telah bekerja dan terserap pada instansi/perusahaan mitra Politeknik Negeri Manado.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Table Card -->
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-users"></i></span>
                <span>
                    <strong>Daftar Alumni Bekerja</strong>
                    <small id="alumniCount">{{ $alumnis->count() }} alumni terdaftar</small>
                </span>
            </div>

            <div>
                <a href="{{ route('prodi.alumni.create') }}" class="dk-primary-btn" style="text-decoration: none;">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Data Alumni</span>
                </a>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th" style="min-width: 180px;">NIM & Nama Alumni</th>
                            <th class="um-th">Tahun Lulus</th>
                            <th class="um-th" style="min-width: 220px;">Mitra Tempat Bekerja</th>
                            <th class="um-th">Posisi / Jabatan</th>
                            <th class="um-th">Tahun Mulai</th>
                            <th class="um-th">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alumnis as $alumni)
                            @php
                                $mitraRelation = $alumni->alumniMitras->first();
                            @endphp
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <div class="dk-doc-cell">
                                        <span class="dk-doc-number">NIM: {{ $alumni->nim }}</span>
                                        <span class="dk-doc-title" style="font-weight: 700;">{{ $alumni->nama }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <strong>{{ $alumni->tahun_lulus }}</strong>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    @if($mitraRelation && $mitraRelation->mitra)
                                        <div class="dk-entity" style="align-items: center;">
                                            <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <span class="dk-entity-text">
                                                <strong>{{ $mitraRelation->mitra->nama_mitra }}</strong>
                                            </span>
                                        </div>
                                    @else
                                        <span style="color: var(--text-sub);">-</span>
                                    @endif
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <span style="font-weight: 600; color: var(--text);">{{ $mitraRelation->posisi ?? '-' }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    {{ $mitraRelation->tahun_mulai ?? '-' }}
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    @if($mitraRelation && $mitraRelation->status === 'Aktif')
                                        <span class="dk-status dk-status-active">
                                            <i class="fas fa-circle-check"></i> Aktif
                                        </span>
                                    @else
                                        <span class="dk-status dk-status-neutral">
                                            {{ $mitraRelation->status ?? 'Terdaftar' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="7" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada data alumni terserap</p>
                                        <p class="um-empty-sub">Klik tombol "Tambah Data Alumni" untuk mencatat alumni yang bekerja di mitra.</p>
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
@endsection

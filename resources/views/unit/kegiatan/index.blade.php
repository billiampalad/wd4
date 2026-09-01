@extends('auth.unit')

@section('content')
<main id="mainContent" class="main-content">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h1 class="page-title">Daftar Kegiatan Kerja Sama</h1>
                <p class="page-subtitle">Pelaksanaan program kerja sama berbasis dokumen perikatan IA / SPK yang telah disahkan.</p>
            </div>
            <a href="{{ route('unit.kegiatan.create') }}" class="dk-primary-btn" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-plus"></i> Tambah Kegiatan Kerja Sama
            </a>
        </div>
    </div>

    <div class="dk-card" style="background: var(--surface); border-radius: 16px; padding: 24px; margin-top: 20px;">
        @if ($kegiatans->isEmpty())
            <div style="text-align: center; padding: 40px;">
                <div style="font-size: 3rem; color: var(--text-sub); margin-bottom: 12px;">
                    <i class="fas fa-calendar-xmark"></i>
                </div>
                <h4 style="font-weight: 700; color: var(--text);">Belum Ada Kegiatan Kerja Sama</h4>
                <p style="color: var(--text-sub); margin-bottom: 20px;">Silakan klik tombol di bawah untuk mencatat kegiatan pelaksanaan pertama.</p>
                <a href="{{ route('unit.kegiatan.create') }}" class="dk-primary-btn" style="text-decoration:none;">
                    <i class="fas fa-plus"></i> Tambah Kegiatan Baru
                </a>
            </div>
        @else
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="um-table dk-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                            <th style="padding: 12px 14px;">#</th>
                            <th style="padding: 12px 14px;">Nama Kegiatan</th>
                            <th style="padding: 12px 14px;">Dokumen Perikatan & Mitra</th>
                            <th style="padding: 12px 14px;">Bentuk Kerjasama</th>
                            <th style="padding: 12px 14px;">Periode Pelaksanaan</th>
                            <th style="padding: 12px 14px;">Volume Luaran</th>
                            <th style="padding: 12px 14px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kegiatans as $kegiatan)
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 12px 14px;">{{ $loop->iteration }}</td>
                                <td style="padding: 12px 14px;">
                                    <strong>{{ $kegiatan->nama_kegiatan }}</strong>
                                </td>
                                <td style="padding: 12px 14px;">
                                    <div>[{{ $kegiatan->cooperation?->jenis ?? 'IA' }}] {{ $kegiatan->cooperation?->judul ?? '-' }}</div>
                                    <small style="color: var(--text-sub);"><i class="fas fa-building"></i> {{ $kegiatan->cooperation?->mitra?->nama_mitra ?? '-' }}</small>
                                </td>
                                <td style="padding: 12px 14px;">
                                    {{ $kegiatan->detailKegiatan?->jenisKerjasama?->nama_kerjasama ?? $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->implode(', ') ?: '-' }}
                                </td>
                                <td style="padding: 12px 14px;">
                                    <small>
                                        {{ $kegiatan->periode_mulai?->format('d M Y') }} s/d {{ $kegiatan->periode_selesai?->format('d M Y') ?? 'Selesai' }}
                                    </small>
                                </td>
                                <td style="padding: 12px 14px;">
                                    {{ $kegiatan->detailKegiatan?->volume_luaran ?: '-' }}
                                </td>
                                <td style="padding: 12px 14px; text-align: center;">
                                    <a href="{{ route('unit.kegiatan.show', $kegiatan->id) }}" class="btn-detail" title="Lihat Detail" style="padding: 6px 12px; border-radius: 8px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; text-decoration: none;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</main>
@endsection

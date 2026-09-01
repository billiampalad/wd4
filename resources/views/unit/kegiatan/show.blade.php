@extends('auth.unit')

@section('content')
<main id="mainContent" class="main-content">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h1 class="page-title">Detail Kegiatan Kerja Sama</h1>
                <p class="page-subtitle">Informasi rincian program pelaksanaan kerja sama.</p>
            </div>
            <a href="{{ route('unit.kegiatan.index') }}" class="dk-primary-btn" style="text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="dk-card" style="background: var(--surface); border-radius: 16px; padding: 24px; margin-top: 20px;">
        <h3 style="font-weight: 700; color: var(--text); margin-bottom: 16px;">{{ $kegiatan->nama_kegiatan }}</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="padding: 16px; border-radius: 12px; background: rgba(79, 70, 229, 0.04); border: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--text-sub); display: block;">Dokumen Perikatan IA / SPK</span>
                <strong style="font-size: 14px; color: var(--text);">[{{ $kegiatan->cooperation?->jenis }}] {{ $kegiatan->cooperation?->judul }}</strong>
            </div>

            <div style="padding: 16px; border-radius: 12px; background: rgba(79, 70, 229, 0.04); border: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--text-sub); display: block;">Mitra Pelaksana</span>
                <strong style="font-size: 14px; color: var(--text);">{{ $kegiatan->cooperation?->mitra?->nama_mitra ?? '-' }}</strong>
            </div>

            <div style="padding: 16px; border-radius: 12px; background: rgba(79, 70, 229, 0.04); border: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--text-sub); display: block;">Bentuk Kerja Sama</span>
                <strong style="font-size: 14px; color: var(--text);">{{ $detail?->jenisKerjasama?->nama_kerjasama ?? '-' }}</strong>
            </div>

            <div style="padding: 16px; border-radius: 12px; background: rgba(79, 70, 229, 0.04); border: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--text-sub); display: block;">Volume Luaran Target</span>
                <strong style="font-size: 14px; color: var(--text);">{{ $detail?->volume_luaran ?: '-' }}</strong>
            </div>

            <div style="padding: 16px; border-radius: 12px; background: rgba(79, 70, 229, 0.04); border: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--text-sub); display: block;">Periode Pelaksanaan</span>
                <strong style="font-size: 14px; color: var(--text);">
                    {{ $kegiatan->periode_mulai?->format('d M Y') }} s/d {{ $kegiatan->periode_selesai?->format('d M Y') ?? 'Selesai' }}
                </strong>
            </div>

            <div style="padding: 16px; border-radius: 12px; background: rgba(79, 70, 229, 0.04); border: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--text-sub); display: block;">Sasaran & Indikator IKU</span>
                <strong style="font-size: 14px; color: var(--text);">
                    {{ $detail?->sasaran?->nama ?? '-' }} &middot; {{ $detail?->indikator?->nama ?? '-' }}
                </strong>
            </div>
        </div>

        @if ($detail?->output)
            <div style="margin-top: 20px; padding: 16px; border-radius: 12px; background: var(--surface2, #f8fafc); border: 1px solid var(--border);">
                <span style="font-size: 12px; font-weight: 700; color: var(--text); display: block; margin-bottom: 4px;">Output Luaran:</span>
                <p style="margin: 0; font-size: 13px; color: var(--text-sub);">{{ $detail->output }}</p>
            </div>
        @endif

        @if ($detail?->outcome)
            <div style="margin-top: 12px; padding: 16px; border-radius: 12px; background: var(--surface2, #f8fafc); border: 1px solid var(--border);">
                <span style="font-size: 12px; font-weight: 700; color: var(--text); display: block; margin-bottom: 4px;">Outcome / Dampak Manfaat:</span>
                <p style="margin: 0; font-size: 13px; color: var(--text-sub);">{{ $detail->outcome }}</p>
            </div>
        @endif
    </div>
</main>
@endsection

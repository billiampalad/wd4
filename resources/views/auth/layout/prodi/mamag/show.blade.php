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
                <a href="{{ route('prodi.penempatan.index') }}">Penempatan Mahasiswa</a>
                <span>/</span>
                <span>Detail Penempatan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-id-card-clip"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Rincian Penempatan Mahasiswa</h2>
                    <p class="ud-subtitle">
                        Informasi lengkap penempatan mahasiswa, pembimbing, dan evaluasi industri.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @php
        $pembimbingInternal = $penempatan->pembimbings->where('tipe', 'Internal')->first();
        $pembimbingEksternal = $penempatan->pembimbings->where('tipe', 'Eksternal')->first();
    @endphp

    <div class="card um-card dk-card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-user-graduate"></i></span>
                <span><strong>{{ $penempatan->mahasiswa?->nama }} (NIM: {{ $penempatan->mahasiswa?->nim }})</strong></span>
            </div>
            <div>
                <a href="{{ route('prodi.penempatan.edit', $penempatan->id) }}" class="dk-primary-btn" style="text-decoration: none;">
                    <i class="fas fa-pen-to-square"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body" style="padding: 24px 32px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div style="background: var(--surface2); padding: 20px; border-radius: 16px; border: 1px solid var(--border);">
                    <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 800; color: var(--text);">
                        <i class="fas fa-building" style="color: #10b981; margin-right: 8px;"></i>
                        Mitra & Kegiatan
                    </h4>
                    <p style="margin: 0 0 6px 0; font-size: 13px;"><strong>Mitra:</strong> {{ $penempatan->mitra?->nama_mitra ?? '-' }}</p>
                    <p style="margin: 0 0 6px 0; font-size: 13px;"><strong>Kegiatan:</strong> {{ $penempatan->kegiatan?->nama_kegiatan ?? '-' }}</p>
                    <p style="margin: 0; font-size: 13px;"><strong>Status:</strong> <span class="dk-status dk-status-active">{{ $penempatan->status }}</span></p>
                </div>

                <div style="background: var(--surface2); padding: 20px; border-radius: 16px; border: 1px solid var(--border);">
                    <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 800; color: var(--text);">
                        <i class="fas fa-calendar-check" style="color: #4f46e5; margin-right: 8px;"></i>
                        Periode & Evaluasi
                    </h4>
                    <p style="margin: 0 0 6px 0; font-size: 13px;"><strong>Mulai:</strong> {{ $penempatan->periode_mulai ? \Carbon\Carbon::parse($penempatan->periode_mulai)->format('d M Y') : '-' }}</p>
                    <p style="margin: 0 0 6px 0; font-size: 13px;"><strong>Selesai:</strong> {{ $penempatan->periode_selesai ? \Carbon\Carbon::parse($penempatan->periode_selesai)->format('d M Y') : '-' }}</p>
                    <p style="margin: 0; font-size: 13px;"><strong>Nilai Mitra:</strong> <strong>{{ $penempatan->nilai_mitra ?? 'Belum Dinilai' }}</strong></p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div style="border: 1px solid var(--border); padding: 20px; border-radius: 16px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: var(--text);">
                        <i class="fas fa-chalkboard-user" style="color: #4f46e5; margin-right: 8px;"></i>
                        Pembimbing Internal (Dosen)
                    </h4>
                    <p style="margin: 0 0 4px 0; font-size: 13px;"><strong>Nama:</strong> {{ $pembimbingInternal->nama_pembimbing ?? '-' }}</p>
                    <p style="margin: 0; font-size: 13px;"><strong>Kontak:</strong> {{ $pembimbingInternal->kontak ?? '-' }}</p>
                </div>

                <div style="border: 1px solid var(--border); padding: 20px; border-radius: 16px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: var(--text);">
                        <i class="fas fa-user-tie" style="color: #10b981; margin-right: 8px;"></i>
                        Pembimbing Eksternal (Mitra)
                    </h4>
                    <p style="margin: 0 0 4px 0; font-size: 13px;"><strong>Nama:</strong> {{ $pembimbingEksternal->nama_pembimbing ?? '-' }}</p>
                    <p style="margin: 0; font-size: 13px;"><strong>Kontak:</strong> {{ $pembimbingEksternal->kontak ?? '-' }}</p>
                </div>
            </div>

            @if($penempatan->catatan_mitra)
            <div style="margin-top: 24px; padding: 16px; background: rgba(79,70,229,0.05); border: 1px solid rgba(79,70,229,0.2); border-radius: 12px;">
                <strong style="color: #4f46e5; font-size: 13px; display: block; margin-bottom: 4px;">Catatan Penilaian dari Mitra:</strong>
                <p style="margin: 0; font-size: 13px; color: var(--text); font-style: italic;">"{{ $penempatan->catatan_mitra }}"</p>
            </div>
            @endif

            <div style="margin-top: 24px; text-align: right;">
                <a href="{{ route('prodi.penempatan.index') }}" class="rfc-btn" style="background: var(--surface2); color: var(--text); border: 1px solid var(--border); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</main>
@endsection

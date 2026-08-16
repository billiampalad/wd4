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
                <span>Edit Penempatan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-pen-to-square"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Edit Penempatan Mahasiswa</h2>
                    <p class="ud-subtitle">
                        Perbarui informasi status kegiatan, periode, dan pembimbing mahasiswa.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card dk-card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-file-pen"></i></span>
                <span><strong>Edit Formulir Penempatan</strong></span>
            </div>
        </div>
        <div class="card-body" style="padding: 24px 32px;">
            <form action="{{ route('prodi.penempatan.update', $penempatan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Mahasiswa <span style="color: red;">*</span></label>
                        <select name="mahasiswa_id" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                            @foreach($mahasiswas as $mhs)
                                <option value="{{ $mhs->id }}" {{ $penempatan->mahasiswa_id == $mhs->id ? 'selected' : '' }}>
                                    {{ $mhs->nim }} - {{ $mhs->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Kegiatan Kerja Sama <span style="color: red;">*</span></label>
                        <select name="kegiatan_id" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                            @foreach($kegiatans as $keg)
                                <option value="{{ $keg->id }}" {{ $penempatan->kegiatan_id == $keg->id ? 'selected' : '' }}>
                                    {{ $keg->nama_kegiatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rfc-group" style="grid-column: span 2;">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Mitra Penempatan (DUDIKA) <span style="color: red;">*</span></label>
                        <select name="mitra_id" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                            @foreach($mitras as $mitra)
                                <option value="{{ $mitra->id }}" {{ $penempatan->mitra_id == $mitra->id ? 'selected' : '' }}>
                                    {{ $mitra->nama_mitra }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Periode Mulai <span style="color: red;">*</span></label>
                        <input type="date" name="periode_mulai" value="{{ $penempatan->periode_mulai ? \Carbon\Carbon::parse($penempatan->periode_mulai)->format('Y-m-d') : '' }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>

                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Periode Selesai</label>
                        <input type="date" name="periode_selesai" value="{{ $penempatan->periode_selesai ? \Carbon\Carbon::parse($penempatan->periode_selesai)->format('Y-m-d') : '' }}" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>

                    <div class="rfc-group" style="grid-column: span 2;">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Status Penempatan <span style="color: red;">*</span></label>
                        <select name="status" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                            <option value="Aktif" {{ $penempatan->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ $penempatan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ $penempatan->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>

                @php
                    $pembimbingInternal = $penempatan->pembimbings->where('tipe', 'Internal')->first();
                    $pembimbingEksternal = $penempatan->pembimbings->where('tipe', 'Eksternal')->first();
                @endphp
                <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 800; color: var(--text);">
                        <i class="fas fa-users-rectangle" style="color: #4f46e5; margin-right: 8px;"></i>
                        Penetapan Dosen & Pembimbing Industri
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; font-weight: 700; font-size: 12px; margin-bottom: 6px; color: var(--text);">Nama Pembimbing Internal (Dosen) <span style="color: red;">*</span></label>
                            <input type="text" name="nama_pembimbing_internal" value="{{ $pembimbingInternal->nama_pembimbing ?? '' }}" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; margin-bottom: 10px;">

                            <label style="display: block; font-weight: 700; font-size: 12px; margin-bottom: 6px; color: var(--text);">Kontak Dosen</label>
                            <input type="text" name="kontak_pembimbing_internal" value="{{ $pembimbingInternal->kontak ?? '' }}" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; font-size: 12px; margin-bottom: 6px; color: var(--text);">Nama Pembimbing Eksternal (Mitra) <span style="color: red;">*</span></label>
                            <input type="text" name="nama_pembimbing_eksternal" value="{{ $pembimbingEksternal->nama_pembimbing ?? '' }}" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; margin-bottom: 10px;">

                            <label style="display: block; font-weight: 700; font-size: 12px; margin-bottom: 6px; color: var(--text);">Kontak Pembimbing Mitra</label>
                            <input type="text" name="kontak_pembimbing_eksternal" value="{{ $pembimbingEksternal->kontak ?? '' }}" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('prodi.penempatan.index') }}" class="rfc-btn" style="background: var(--surface2); color: var(--text); border: 1px solid var(--border);">
                        Batal
                    </a>
                    <button type="submit" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-save"></i> Perbarui Penempatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

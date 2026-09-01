@extends('auth.unit')

@section('content')
<main id="mainContent" class="main-content">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Input Kegiatan Kerja Sama (IA / SPK)</h1>
                <p class="page-subtitle">Pencatatan kegiatan pelaksanaan kerja sama berbasis Implementation Agreement yang telah disahkan.</p>
            </div>
            <a href="{{ route('unit.kegiatan.index') }}" class="dk-primary-btn" style="text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kegiatan
            </a>
        </div>
    </div>

    @if ($iaDocuments->isEmpty())
        <div class="dk-card" style="padding: 40px; text-align: center; background: var(--surface); border-radius: 16px; margin-top: 20px;">
            <div style="font-size: 3rem; color: #f59e0b; margin-bottom: 16px;">
                <i class="fas fa-file-circle-exclamation"></i>
            </div>
            <h3 style="font-weight: 700; color: var(--text); margin-bottom: 8px;">Belum Ada Dokumen IA yang Disahkan</h3>
            <p style="color: var(--text-sub); max-width: 500px; margin: 0 auto 20px;">
                Kegiatan kerja sama hanya dapat diinput berdasarkan dokumen Implementation Agreement (IA) atau Surat Perjanjian Kerja Sama (SPK) yang status dokumennya telah <strong>Disahkan</strong>.
            </p>
            <a href="{{ route('unit.kerjasama.create') }}" class="dk-primary-btn" style="text-decoration:none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Buat Dokumen IA Baru
            </a>
        </div>
    @else
        <div class="dk-card" style="background: var(--surface); border-radius: 16px; padding: 24px; margin-top: 20px;">
            <form action="{{ route('unit.kegiatan.store') }}" method="POST" id="formKegiatan">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    {{-- 1. Pilih Dokumen IA --}}
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="cooperation_id" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Pilih Dokumen IA / SPK Terkait <span style="color:red;">*</span>
                        </label>
                        <select name="cooperation_id" id="cooperation_id" class="form-control" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                            <option value="">-- Pilih Dokumen IA / SPK yang Disahkan --</option>
                            @foreach ($iaDocuments as $doc)
                                <option value="{{ $doc->id }}" {{ old('cooperation_id') == $doc->id ? 'selected' : '' }}>
                                    [{{ $doc->jenis }}] {{ $doc->judul }} — Mitra: {{ $doc->mitra?->nama_mitra ?? 'Mitra Terdaftar' }} (No: {{ $doc->doc_number ?: 'Tanpa Nomor' }})
                                </option>
                            @endforeach
                        </select>
                        @error('cooperation_id')
                            <small style="color: red;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- 2. Nama Kegiatan --}}
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="nama_kegiatan" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Nama / Judul Kegiatan Kerja Sama <span style="color:red;">*</span>
                        </label>
                        <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Magang Industri Bersertifikat Batch 1" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                        @error('nama_kegiatan')
                            <small style="color: red;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- 3. Jenis Kerjasama --}}
                    <div class="form-group">
                        <label for="jenis_kerjasama_id" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Jenis / Bentuk Kerja Sama
                        </label>
                        <select name="jenis_kerjasama_id" id="jenis_kerjasama_id" class="form-control" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                            <option value="">-- Pilih Jenis Kerjasama --</option>
                            @foreach ($jenisKerjasamas as $jk)
                                <option value="{{ $jk->id }}" {{ old('jenis_kerjasama_id') == $jk->id ? 'selected' : '' }}>
                                    {{ $jk->nama_kerjasama }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_kerjasama_id')
                            <small style="color: red;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- 4. Target Volume Luaran --}}
                    <div class="form-group">
                        <label for="volume_luaran" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Target Volume Luaran (Jumlah Peserta / Hasil)
                        </label>
                        <input type="text" name="volume_luaran" id="volume_luaran" class="form-control" value="{{ old('volume_luaran') }}" placeholder="Contoh: 25 Mahasiswa / 2 Modul" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                    </div>

                    {{-- 5. Periode Mulai --}}
                    <div class="form-group">
                        <label for="periode_mulai" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Periode Mulai Pelaksanaan <span style="color:red;">*</span>
                        </label>
                        <input type="date" name="periode_mulai" id="periode_mulai" class="form-control" value="{{ old('periode_mulai') }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                        @error('periode_mulai')
                            <small style="color: red;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- 6. Periode Selesai --}}
                    <div class="form-group">
                        <label for="periode_selesai" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Periode Selesai Pelaksanaan
                        </label>
                        <input type="date" name="periode_selesai" id="periode_selesai" class="form-control" value="{{ old('periode_selesai') }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                        @error('periode_selesai')
                            <small style="color: red;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- 7. Sasaran IKU --}}
                    <div class="form-group">
                        <label for="sasaran_id" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Sasaran Kegiatan (IKU)
                        </label>
                        <select name="sasaran_id" id="sasaran_id" class="form-control" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                            <option value="">-- Pilih Sasaran --</option>
                            @foreach ($sasarans as $s)
                                <option value="{{ $s->id }}" {{ old('sasaran_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama ?? $s->nama_sasaran ?? 'Sasaran ' . $s->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 8. Indikator IKU --}}
                    <div class="form-group">
                        <label for="indikator_id" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Indikator Kinerja (IKU)
                        </label>
                        <select name="indikator_id" id="indikator_id" class="form-control" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
                            <option value="">-- Pilih Indikator --</option>
                            @foreach ($indikators as $ind)
                                <option value="{{ $ind->id }}" {{ old('indikator_id') == $ind->id ? 'selected' : '' }}>
                                    {{ $ind->nama ?? $ind->nama_indikator ?? 'Indikator ' . $ind->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 9. Output --}}
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="output" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Output Luaran Kegiatan
                        </label>
                        <textarea name="output" id="output" rows="2" class="form-control" placeholder="Rincian output luaran yang dicapai..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">{{ old('output') }}</textarea>
                    </div>

                    {{-- 10. Outcome --}}
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="outcome" style="font-weight: 600; display: block; margin-bottom: 6px;">
                            Outcome / Dampak Manfaat
                        </label>
                        <textarea name="outcome" id="outcome" rows="2" class="form-control" placeholder="Dampak manfaat bagi institusi dan mahasiswa..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">{{ old('outcome') }}</textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('unit.kegiatan.index') }}" class="btn-cancel" style="padding: 10px 20px; border-radius: 10px; text-decoration: none; border: 1px solid var(--border); color: var(--text);">
                        Batal
                    </a>
                    <button type="submit" class="dk-primary-btn" style="padding: 10px 24px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer;">
                        <i class="fas fa-save"></i> Simpan Data Kegiatan
                    </button>
                </div>
            </form>
        </div>
    @endif
</main>
@endsection

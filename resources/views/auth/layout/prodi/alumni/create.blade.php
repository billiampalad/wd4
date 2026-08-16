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
                <a href="{{ route('prodi.alumni.index') }}">Tracking Lulusan</a>
                <span>/</span>
                <span>Tambah Data Alumni</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-user-plus"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Tambah Data Alumni Bekerja di Mitra</h2>
                    <p class="ud-subtitle">
                        Catat data alumni program studi yang bekerja atau terserap di perusahaan/instansi mitra.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card dk-card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-id-card"></i></span>
                <span><strong>Formulir Data Alumni & Penyerapan</strong></span>
            </div>
        </div>
        <div class="card-body" style="padding: 24px 32px;">
            <form action="{{ route('prodi.alumni.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">NIM Alumni <span style="color: red;">*</span></label>
                        <input type="text" name="nim" required placeholder="Contoh: 21021001" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>

                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Nama Lengkap Alumni <span style="color: red;">*</span></label>
                        <input type="text" name="nama" required placeholder="Nama Lengkap Alumni" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>

                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Tahun Lulus <span style="color: red;">*</span></label>
                        <input type="number" name="tahun_lulus" value="{{ date('Y') }}" required min="2000" max="2099" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>

                    <div class="rfc-group">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="telepon" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>

                    <div class="rfc-group" style="grid-column: span 2;">
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Email Alumni</label>
                        <input type="email" name="email" placeholder="alumni@email.com" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                    </div>
                </div>

                <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 800; color: var(--text);">
                        <i class="fas fa-building" style="color: #10b981; margin-right: 8px;"></i>
                        Informasi Penyerapan di Instansi Mitra
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="rfc-group" style="grid-column: span 2;">
                            <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text);">Perusahaan / Instansi Mitra <span style="color: red;">*</span></label>
                            <select name="mitra_id" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: 13px;">
                                <option value="">-- Pilih Mitra Tempat Bekerja --</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}">{{ $mitra->nama_mitra }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-weight: 700; font-size: 12px; margin-bottom: 6px; color: var(--text);">Posisi / Jabatan Pekerjaan <span style="color: red;">*</span></label>
                            <input type="text" name="posisi" required placeholder="Contoh: Software Engineer, Staff IT" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px;">
                        </div>

                        <div>
                            <label style="display: block; font-weight: 700; font-size: 12px; margin-bottom: 6px; color: var(--text);">Tahun Mulai Bekerja <span style="color: red;">*</span></label>
                            <input type="number" name="tahun_mulai" value="{{ date('Y') }}" required min="2000" max="2099" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('prodi.alumni.index') }}" class="rfc-btn" style="background: var(--surface2); color: var(--text); border: 1px solid var(--border); text-decoration: none;">
                        Batal
                    </a>
                    <button type="submit" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-save"></i> Simpan Data Alumni
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

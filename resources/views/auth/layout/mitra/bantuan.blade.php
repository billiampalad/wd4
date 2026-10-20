{{-- ══════════════════════════════════════════════════════════════════════════════
     PORTAL MITRA - HUBUNGI ADMINISTRATOR (UC35)
     File: resources/views/auth/layout/mitra/bantuan.blade.php
     Ref: UC35 | Flowchart Section 10.2 | DFD Process 6.8 | ERD Section 4.8
     ══════════════════════════════════════════════════════════════════════════════ --}}

@php
    $user = auth()->user();
    $mitra = $user->mitra;
    $mitraName = $mitra->nama_mitra ?? ($user->name ?? 'Mitra Eksternal');
    $sentMessages = $sentMessages ?? collect();
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<style>
    .bantuan-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        margin-top: 24px;
    }
    @media (max-width: 992px) {
        .bantuan-grid {
            grid-template-columns: 1fr;
        }
    }
    .bantuan-card {
        background: var(--surface, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .form-group-bantuan {
        margin-bottom: 20px;
    }
    .form-label-bantuan {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-main, #1e293b);
        margin-bottom: 8px;
    }
    .form-label-bantuan span.req {
        color: #ef4444;
    }
    .form-control-bantuan {
        width: 100%;
        padding: 11px 14px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid var(--border-color, #cbd5e1);
        border-radius: 10px;
        background: var(--bg-card, #ffffff);
        color: var(--text-main, #0f172a);
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .form-control-bantuan:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }
    .info-kategori-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        border-radius: 10px;
        background: rgba(241, 245, 249, 0.6);
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
    }
    .info-kategori-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
    }
    .badge-kat {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-kendala { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .badge-pertanyaan { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .badge-bantuan { background: rgba(16, 185, 129, 0.12); color: #059669; }
</style>

<main id="mainContent" class="dk-page">

    {{-- ═══ TOPBAR / HERO SECTION ═══ --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('mitra.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Hubungi Administrator</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                    <i class="fas fa-headset"></i>
                </span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Hubungi Administrator (Helpdesk Sistem)</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Sampaikan kendala teknis, pertanyaan operasional kerjasama, atau permintaan bantuan langsung kepada tim Administrator Politeknik Negeri Manado.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ 3 KARTU KATEGORI INFO ═══ --}}
    <section class="dk-stats-grid" style="margin-top: 20px;">
        <div class="dk-stat-card" style="border-left: 4px solid #ef4444;">
            <div class="dk-stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="dk-stat-label">Kategori 1</span>
                <strong style="font-size: 16px;">Kendala Teknis</strong>
                <p style="font-size: 12px; color: var(--text-sub, #64748b); margin-top: 4px;">Akses akun, autentikasi, dokumen error, atau bug aplikasi</p>
            </div>
        </div>

        <div class="dk-stat-card" style="border-left: 4px solid #2563eb;">
            <div class="dk-stat-icon" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                <i class="fas fa-circle-question"></i>
            </div>
            <div>
                <span class="dk-stat-label">Kategori 2</span>
                <strong style="font-size: 16px;">Pertanyaan Umum</strong>
                <p style="font-size: 12px; color: var(--text-sub, #64748b); margin-top: 4px;">Regulasi MoU/MoA/IA, alur pengesahan, dan dokumen kerjasama</p>
            </div>
        </div>

        <div class="dk-stat-card" style="border-left: 4px solid #10b981;">
            <div class="dk-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div>
                <span class="dk-stat-label">Kategori 3</span>
                <strong style="font-size: 16px;">Permintaan Bantuan</strong>
                <p style="font-size: 12px; color: var(--text-sub, #64748b); margin-top: 4px;">Pembaruan data profil instansi, user PIC baru, atau asistensi khusus</p>
            </div>
        </div>
    </section>

    {{-- ═══ MAIN CONTENT GRID: FORMULIR KONTAK & PANDUAN ═══ --}}
    <div class="bantuan-grid">
        {{-- KOLOM KIRI: FORMULIR KIRIM PESAN KE ADMIN --}}
        <div class="bantuan-card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-color, #e2e8f0);">
                <i class="fas fa-paper-plane" style="font-size: 18px; color: #2563eb;"></i>
                <h3 style="font-size: 17px; font-weight: 700; color: var(--text-main, #1e293b); margin: 0;">Formulir Kontak Administrator</h3>
            </div>

            <form action="{{ route('mitra.bantuan.store') }}" method="POST" enctype="multipart/form-data" id="formHubungiAdmin">
                @csrf

                {{-- Field Subjek Pesan --}}
                <div class="form-group-bantuan">
                    <label for="subjek" class="form-label-bantuan">
                        Subjek Pesan <span class="req">*</span>
                    </label>
                    <input type="text" name="subjek" id="subjek" class="form-control-bantuan"
                        placeholder="Contoh: Kendala akses login akun PIC Mitra atau Pertanyaan MoU"
                        value="{{ old('subjek') }}" required>
                </div>

                {{-- Field Kategori --}}
                <div class="form-group-bantuan">
                    <label for="kategori" class="form-label-bantuan">
                        Kategori Pesan <span class="req">*</span>
                    </label>
                    <select name="kategori" id="kategori" class="form-control-bantuan" required>
                        <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>-- Pilih Kategori Kendala / Pertanyaan --</option>
                        <option value="Kendala Teknis" {{ old('kategori') === 'Kendala Teknis' ? 'selected' : '' }}>Kendala Teknis (Error Aplikasi / Akun)</option>
                        <option value="Pertanyaan Umum" {{ old('kategori') === 'Pertanyaan Umum' ? 'selected' : '' }}>Pertanyaan Umum (Dokumen / Prosedur)</option>
                        <option value="Permintaan Bantuan" {{ old('kategori') === 'Permintaan Bantuan' ? 'selected' : '' }}>Permintaan Bantuan (Data Instansi / Layanan)</option>
                    </select>
                </div>

                {{-- Field Isi Pesan --}}
                <div class="form-group-bantuan">
                    <label for="pesan" class="form-label-bantuan">
                        Isi Pesan / Detail Kendala <span class="req">*</span>
                    </label>
                    <textarea name="pesan" id="pesan" rows="5" class="form-control-bantuan"
                        placeholder="Tuliskan detail permasalahan, nomor dokumen kerjasama terkait (jika ada), serta informasi lengkap agar administrator dapat menindaklanjuti secara cepat..."
                        required>{{ old('pesan') }}</textarea>
                </div>

                {{-- Field Lampiran (Opsional) --}}
                <div class="form-group-bantuan">
                    <label for="lampiran" class="form-label-bantuan">
                        Lampiran Berkas Pendukung <span style="font-weight: 400; color: #64748b;">(Opsional, Maks. 5MB: PDF, JPG, PNG, DOCX)</span>
                    </label>
                    <input type="file" name="lampiran" id="lampiran" class="form-control-bantuan"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>

                {{-- Tombol Kirim --}}
                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="reset" class="dk-secondary-btn" style="padding: 10px 20px; border-radius: 10px;">
                        <i class="fas fa-rotate-left"></i>
                        <span>Reset Form</span>
                    </button>
                    <button type="submit" id="btnSubmitBantuan" class="dk-primary-btn" style="padding: 10px 24px; border-radius: 10px;">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Pesan ke Admin</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- KOLOM KANAN: PANDUAN & INFORMASI KONTAK RESMI --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="bantuan-card">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-main, #1e293b); margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-circle-info" style="color: #2563eb;"></i>
                    <span>Informasi Pelayanan</span>
                </h4>
                <p style="font-size: 13px; color: var(--text-sub, #64748b); line-height: 1.6; margin-bottom: 16px;">
                    Setiap pesan yang Anda kirimkan akan diteruskan otomatis ke notifikasi Administrator POLIMDO dan tercatat dalam sistem monitoring bantuan.
                </p>

                <div class="info-kategori-card">
                    <div class="info-kategori-icon" style="background: rgba(37,99,235,0.1); color: #2563eb;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <strong style="font-size: 12px; display: block; color: var(--text-main);">Email Resmi</strong>
                        <span style="font-size: 12px; color: var(--text-sub);">humas@polimdo.ac.id</span>
                    </div>
                </div>

                <div class="info-kategori-card">
                    <div class="info-kategori-icon" style="background: rgba(16,185,129,0.1); color: #059669;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <strong style="font-size: 12px; display: block; color: var(--text-main);">Jam Operasional</strong>
                        <span style="font-size: 12px; color: var(--text-sub);">Senin - Jumat: 08.00 - 16.00 WITA</span>
                    </div>
                </div>

                <div class="info-kategori-card">
                    <div class="info-kategori-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <strong style="font-size: 12px; display: block; color: var(--text-main);">Keamanan Data</strong>
                        <span style="font-size: 12px; color: var(--text-sub);">Pesan terlindungi secara terenkripsi internal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ RIWAYAT PESAN TERKIRIM KE ADMIN ═══ --}}
    <section class="bantuan-card" style="margin-top: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color, #e2e8f0);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-clock-rotate-left" style="font-size: 17px; color: #64748b;"></i>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main, #1e293b); margin: 0;">Riwayat Pesan Bantuan Terkirim</h3>
            </div>
            <span style="font-size: 12px; color: var(--text-sub, #64748b);">Total: {{ $sentMessages->count() }} Pesan</span>
        </div>

        @if($sentMessages->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color, #e2e8f0); text-align: left; color: var(--text-sub, #64748b); font-size: 12px; text-transform: uppercase;">
                            <th style="padding: 10px 14px;">No</th>
                            <th style="padding: 10px 14px;">Kategori &amp; Subjek</th>
                            <th style="padding: 10px 14px;">Pesan</th>
                            <th style="padding: 10px 14px;">Waktu Kirim</th>
                            <th style="padding: 10px 14px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sentMessages as $index => $msg)
                            @php
                                $badgeClass = 'badge-pertanyaan';
                                if (str_contains($msg->title, 'Kendala Teknis')) {
                                    $badgeClass = 'badge-kendala';
                                } elseif (str_contains($msg->title, 'Permintaan Bantuan')) {
                                    $badgeClass = 'badge-bantuan';
                                }
                            @endphp
                            <tr style="border-bottom: 1px solid var(--border-color, #f1f5f9);">
                                <td style="padding: 12px 14px; font-weight: 600;">{{ $index + 1 }}</td>
                                <td style="padding: 12px 14px;">
                                    <span class="badge-kat {{ $badgeClass }}" style="margin-bottom: 4px;">{{ $msg->title }}</span>
                                </td>
                                <td style="padding: 12px 14px; max-width: 400px; color: var(--text-main, #334155); line-height: 1.4;">
                                    {{ Str::limit($msg->message, 120) }}
                                </td>
                                <td style="padding: 12px 14px; white-space: nowrap; color: var(--text-sub, #64748b);">
                                    {{ $msg->created_at?->format('d M Y, H:i') }}
                                </td>
                                <td style="padding: 12px 14px; white-space: nowrap;">
                                    <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 12px; color: #059669; font-weight: 600;">
                                        <i class="fas fa-check-double"></i> Terkirim ke Admin
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 40px 20px; text-align: center; color: var(--text-sub, #64748b);">
                <i class="fas fa-inbox" style="font-size: 38px; color: #cbd5e1; margin-bottom: 12px;"></i>
                <p style="font-size: 14px; margin: 0;">Belum ada pesan bantuan yang dikirimkan ke administrator.</p>
            </div>
        @endif
    </section>

</main>

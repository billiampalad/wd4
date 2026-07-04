<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengajuan Kerja Sama</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(15,23,42,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="padding:0;">
                            @if($isApproved)
                                <div style="background:linear-gradient(135deg,#10b981,#047857); padding:32px 28px; text-align:center;">
                                    <div style="width:56px; height:56px; margin:0 auto 14px; border-radius:50%; background:rgba(255,255,255,0.22); display:flex; align-items:center; justify-content:center;">
                                        <span style="font-size:28px; line-height:1;">✅</span>
                                    </div>
                                    <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:800; line-height:1.3;">Pengajuan Kerja Sama Disetujui</h1>
                                    <p style="margin:8px 0 0; color:rgba(255,255,255,0.85); font-size:14px; line-height:1.5;">Selamat! Pengajuan Anda telah berhasil disetujui.</p>
                                </div>
                            @else
                                <div style="background:linear-gradient(135deg,#ef4444,#b91c1c); padding:32px 28px; text-align:center;">
                                    <div style="width:56px; height:56px; margin:0 auto 14px; border-radius:50%; background:rgba(255,255,255,0.22); display:flex; align-items:center; justify-content:center;">
                                        <span style="font-size:28px; line-height:1;">❌</span>
                                    </div>
                                    <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:800; line-height:1.3;">Pengajuan Kerja Sama Ditolak</h1>
                                    <p style="margin:8px 0 0; color:rgba(255,255,255,0.85); font-size:14px; line-height:1.5;">Mohon maaf, pengajuan Anda belum dapat disetujui saat ini.</p>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:28px;">
                            {{-- Detail Pengajuan --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 16px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                        <strong style="font-size:13px; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">Detail Pengajuan</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0; color:#64748b; font-size:13px; width:140px; vertical-align:top;">Kode Pengajuan</td>
                                                <td style="padding:6px 0; color:#0f172a; font-size:14px; font-weight:700;">{{ $submission->kode_pengajuan }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; color:#64748b; font-size:13px; vertical-align:top;">Judul</td>
                                                <td style="padding:6px 0; color:#0f172a; font-size:14px; font-weight:600;">{{ $submission->judul_pengajuan }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; color:#64748b; font-size:13px; vertical-align:top;">Nama Mitra</td>
                                                <td style="padding:6px 0; color:#0f172a; font-size:14px;">{{ $submission->nama_mitra }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; color:#64748b; font-size:13px; vertical-align:top;">Kategori</td>
                                                <td style="padding:6px 0; color:#0f172a; font-size:14px;">{{ ucfirst($submission->kategori) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; color:#64748b; font-size:13px; vertical-align:top;">Status</td>
                                                <td style="padding:6px 0;">
                                                    @if($isApproved)
                                                        <span style="display:inline-block; padding:4px 12px; border-radius:999px; background:rgba(16,185,129,0.12); color:#047857; font-size:12px; font-weight:700;">Disetujui</span>
                                                    @else
                                                        <span style="display:inline-block; padding:4px 12px; border-radius:999px; background:rgba(239,68,68,0.12); color:#b91c1c; font-size:12px; font-weight:700;">Ditolak</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Pesan --}}
                            <div style="padding:18px 20px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0; margin-bottom:24px;">
                                <strong style="display:block; margin-bottom:8px; font-size:13px; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">Pesan</strong>
                                <p style="margin:0; color:#334155; font-size:14px; line-height:1.7; white-space:pre-line;">{{ $customMessage }}</p>
                            </div>

                            @if($submission->catatan_pimpinan)
                                <div style="padding:18px 20px; border-radius:12px; border-left:4px solid {{ $isApproved ? '#10b981' : '#ef4444' }}; background:{{ $isApproved ? 'rgba(16,185,129,0.06)' : 'rgba(239,68,68,0.06)' }}; margin-bottom:24px;">
                                    <strong style="display:block; margin-bottom:8px; font-size:13px; color:#475569;">Catatan Pimpinan</strong>
                                    <p style="margin:0; color:#334155; font-size:14px; line-height:1.7;">{{ $submission->catatan_pimpinan }}</p>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 28px; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
                            <p style="margin:0; color:#94a3b8; font-size:12px; line-height:1.6;">
                                Email ini dikirim secara otomatis oleh sistem {{ config('app.name', 'Institusi Kami') }}.<br>
                                Mohon jangan membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

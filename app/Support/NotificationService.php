<?php

namespace App\Support;

use App\Mail\MitraStatusNotificationMail;
use App\Models\PengajuanKerjasamaMitra;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Kirim notifikasi email ke mitra.
     */
    public static function sendEmail(PengajuanKerjasamaMitra $submission, string $customMessage): void
    {
        $email = $submission->email;

        if (! $email) {
            Log::warning("NotificationService: Email mitra kosong untuk pengajuan #{$submission->id}");
            return;
        }

        try {
            Mail::to($email)->send(new MitraStatusNotificationMail($submission, $customMessage));
            Log::info("NotificationService: Email berhasil dikirim ke {$email} untuk pengajuan #{$submission->id}");
        } catch (\Exception $e) {
            Log::error("NotificationService: Gagal mengirim email ke {$email} — {$e->getMessage()}");
        }
    }

    /**
     * Kirim notifikasi WhatsApp ke mitra via API Gateway (Fonnte).
     *
     * Pastikan env FONNTE_TOKEN sudah diisi.
     * Jika menggunakan gateway lain, sesuaikan endpoint dan payload.
     */
    public static function sendWhatsApp(PengajuanKerjasamaMitra $submission, string $customMessage): void
    {
        $phone = $submission->telepon;
        $token = config('services.fonnte.token');

        if (! $phone) {
            Log::warning("NotificationService: Nomor telepon mitra kosong untuk pengajuan #{$submission->id}");
            return;
        }

        if (! $token) {
            Log::warning('NotificationService: FONNTE_TOKEN belum dikonfigurasi di .env — WhatsApp tidak dikirim.');
            return;
        }

        // Normalisasi nomor (hapus spasi/dash, pastikan format internasional)
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = Http::withToken($token)
                ->post('https://api.fonnte.com/send', [
                    'target'  => $phone,
                    'message' => $customMessage,
                ]);

            if ($response->successful()) {
                Log::info("NotificationService: WhatsApp berhasil dikirim ke {$phone} untuk pengajuan #{$submission->id}");
            } else {
                Log::error("NotificationService: WhatsApp gagal — Response: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("NotificationService: Gagal mengirim WhatsApp ke {$phone} — {$e->getMessage()}");
        }
    }

    /**
     * Generate default template pesan berdasarkan keputusan.
     */
    public static function generateDefaultMessage(PengajuanKerjasamaMitra $submission, string $channel = 'email'): string
    {
        $isApproved = $submission->status === PengajuanKerjasamaMitra::STATUS_DISETUJUI;
        $namaInstitusi = config('app.name', 'Institusi Kami');
        $catatan = $submission->catatan_pimpinan;

        if ($channel === 'whatsapp') {
            return $isApproved
                ? "Halo *{$submission->nama_mitra}*,\n\n"
                  . "Kami dari *{$namaInstitusi}* ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode *{$submission->kode_pengajuan}* — _{$submission->judul_pengajuan}_ telah *DISETUJUI*. ✅\n\n"
                  . ($catatan ? "Catatan: _{$catatan}_\n\n" : '')
                  . "Terima kasih atas minat dan kepercayaan Anda. Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\n"
                  . "Salam hangat,\n{$namaInstitusi}"

                : "Halo *{$submission->nama_mitra}*,\n\n"
                  . "Kami dari *{$namaInstitusi}* ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode *{$submission->kode_pengajuan}* — _{$submission->judul_pengajuan}_ saat ini *belum dapat kami setujui*. ❌\n\n"
                  . ($catatan ? "Catatan dari pimpinan: _{$catatan}_\n\n" : '')
                  . "Kami tetap menghargai minat Anda. Jangan ragu untuk mengajukan kembali di kemudian hari.\n\n"
                  . "Salam hangat,\n{$namaInstitusi}";
        }

        // Email: plain-text version (HTML dihandle oleh Mailable template)
        return $isApproved
            ? "Yth. {$submission->nama_mitra},\n\n"
              . "Dengan hormat, kami dari {$namaInstitusi} ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode {$submission->kode_pengajuan} — \"{$submission->judul_pengajuan}\" telah DISETUJUI.\n\n"
              . ($catatan ? "Catatan: {$catatan}\n\n" : '')
              . "Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\n"
              . "Hormat kami,\n{$namaInstitusi}"

            : "Yth. {$submission->nama_mitra},\n\n"
              . "Dengan hormat, kami dari {$namaInstitusi} ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode {$submission->kode_pengajuan} — \"{$submission->judul_pengajuan}\" saat ini belum dapat kami setujui.\n\n"
              . ($catatan ? "Catatan dari pimpinan: {$catatan}\n\n" : '')
              . "Kami tetap menghargai minat Anda dan berharap dapat bekerja sama di kesempatan mendatang.\n\n"
              . "Hormat kami,\n{$namaInstitusi}";
    }
}

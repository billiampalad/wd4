<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\DetailKegiatan;
use App\Models\JenisKerjasama;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\Pejabat;
use App\Models\PengajuanKerjasamaMitra;
use App\Models\User;
use App\Support\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PengajuanKerjasamaMitraController extends Controller
{
    public function index()
    {
        $pendingSubmissions = PengajuanKerjasamaMitra::with(['klasifikasi'])
            ->where('status', PengajuanKerjasamaMitra::STATUS_DIAJUKAN)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        $reviewedSubmissions = PengajuanKerjasamaMitra::with(['klasifikasi', 'reviewer', 'mitra'])
            ->whereIn('status', [
                PengajuanKerjasamaMitra::STATUS_DISETUJUI,
                PengajuanKerjasamaMitra::STATUS_DITOLAK,
            ])
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $submissionStats = [
            'total' => PengajuanKerjasamaMitra::count(),
            'pending' => PengajuanKerjasamaMitra::where('status', PengajuanKerjasamaMitra::STATUS_DIAJUKAN)->count(),
            'approved' => PengajuanKerjasamaMitra::where('status', PengajuanKerjasamaMitra::STATUS_DISETUJUI)->count(),
            'rejected' => PengajuanKerjasamaMitra::where('status', PengajuanKerjasamaMitra::STATUS_DITOLAK)->count(),
        ];

        return view('auth.pimpinan', [
            'view' => 'pengajuan_mitra',
            'pendingSubmissions' => $pendingSubmissions,
            'reviewedSubmissions' => $reviewedSubmissions,
            'submissionStats' => $submissionStats,
        ]);
    }

    public function review(Request $request, $id)
    {
        $submission = PengajuanKerjasamaMitra::findOrFail($id);

        if ($submission->status !== PengajuanKerjasamaMitra::STATUS_DIAJUKAN) {
            $sendEmail = $request->boolean('send_email');
            $sendWhatsApp = $request->boolean('send_whatsapp');

            if ($sendEmail || $sendWhatsApp) {
                $validated = $request->validate([
                    'send_email' => ['nullable'],
                    'send_whatsapp' => ['nullable'],
                    'custom_message_email' => ['nullable', 'string', 'max:5000'],
                    'custom_message_whatsapp' => ['nullable', 'string', 'max:5000'],
                ]);

                $notifInfo = [];
                if ($sendEmail) {
                    $emailMessage = !empty($validated['custom_message_email'])
                        ? $validated['custom_message_email']
                        : NotificationService::generateDefaultMessage($submission, 'email');
                    NotificationService::sendEmail($submission, $emailMessage);
                    $notifInfo[] = 'Email';
                }

                if ($sendWhatsApp) {
                    $waMessage = !empty($validated['custom_message_whatsapp'])
                        ? $validated['custom_message_whatsapp']
                        : NotificationService::generateDefaultMessage($submission, 'whatsapp');
                    NotificationService::sendWhatsApp($submission, $waMessage);
                    $notifInfo[] = 'WhatsApp';
                }

                $resendMsg = 'Notifikasi berhasil dikirim ulang ke mitra via ' . implode(' & ', $notifInfo) . '.';
                return redirect()->route('pimpinan.pengajuan_mitra')->with('success', $resendMsg);
            }

            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'keputusan' => ['required', Rule::in([
                PengajuanKerjasamaMitra::STATUS_DISETUJUI,
                PengajuanKerjasamaMitra::STATUS_DITOLAK,
            ])],
            'catatan_pimpinan' => [
                Rule::requiredIf(fn () => $request->input('keputusan') === PengajuanKerjasamaMitra::STATUS_DITOLAK),
                'nullable',
                'string',
                'max:2000',
            ],
            'send_email' => ['nullable'],
            'send_whatsapp' => ['nullable'],
            'custom_message_email' => ['nullable', 'string', 'max:5000'],
            'custom_message_whatsapp' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::beginTransaction();

        try {
            $mitraId = $submission->mitra_id;

            if ($validated['keputusan'] === PengajuanKerjasamaMitra::STATUS_DISETUJUI) {
                // 1. Simpan / update data Mitra
                $mitra = Mitra::whereRaw('LOWER(nama_mitra) = ?', [strtolower($submission->nama_mitra)])
                    ->first();

                if (! $mitra) {
                    $mitra = Mitra::create([
                        'nama_mitra' => $submission->nama_mitra,
                        'id_klasifikasi' => $submission->id_klasifikasi,
                        'alamat' => $submission->alamat,
                        'kategori' => $submission->kategori,
                        'negara' => $submission->negara,
                        'telp' => $submission->telp,
                        'website' => $submission->website,
                    ]);
                } else {
                    $mitra->fill([
                        'id_klasifikasi' => $mitra->id_klasifikasi ?: $submission->id_klasifikasi,
                        'alamat' => $mitra->alamat ?: $submission->alamat,
                        'negara' => $mitra->negara ?: $submission->negara,
                        'telp' => $mitra->telp ?: $submission->telp,
                        'website' => $mitra->website ?: $submission->website,
                    ])->save();
                }

                $mitraId = $mitra->id;

                // 2. Buat record Pejabat penandatangan & penanggung jawab mitra
                $penandatanganMitra = Pejabat::create([
                    'nama' => $submission->nama_penandatangan,
                    'jabatan' => $submission->jabatan_penandatangan,
                ]);

                $pjMitra = null;
                if ($submission->nama_penanggung_jawab) {
                    $pjMitra = Pejabat::create([
                        'nama' => $submission->nama_penanggung_jawab,
                        'jabatan' => $submission->jabatan_penanggung_jawab,
                    ]);
                }

                // 3. Buat record Cooperation (Status: proses, Status Dokumen: Draft)
                $cooperation = Cooperation::create([
                    'jenis' => $submission->jenis ?? 'MoU (Memorandum of Understanding)',
                    'doc_number' => $submission->doc_number,
                    'title' => $submission->judul_pengajuan,
                    'description' => $submission->tujuan_pengajuan,
                    'start_date' => $submission->start_date,
                    'end_date' => $submission->end_date,
                    'status' => 'proses',
                    'status_dokumen' => 'Draft',
                    'mitra_id' => $mitra->id,
                    'penandatangan_mitra_id' => $penandatanganMitra->id,
                    'pj_mitra_id' => $pjMitra?->id,
                    'pengajuan_kerjasama_mitra_id' => $submission->id,
                    'created_by' => Auth::id(),
                ]);

                // 4. Cari JenisKerjasama berdasarkan ruang_lingkup pengajuan
                $jenisKerjasamaId = 1;
                if ($submission->ruang_lingkup) {
                    $jenisKerjasamaMatch = JenisKerjasama::whereRaw('LOWER(nama_kerjasama) = ?', [strtolower(trim($submission->ruang_lingkup))])->first();
                    if ($jenisKerjasamaMatch) {
                        $jenisKerjasamaId = $jenisKerjasamaMatch->id;
                    } else {
                        $jenisKerjasamaId = JenisKerjasama::first()?->id ?? 1;
                    }
                }

                // 5. Simpan detail_kegiatans (keterangan diset null karena ruang_lingkup sudah terwakili oleh jenis_kerjasamas)
                DetailKegiatan::create([
                    'cooperation_id' => $cooperation->id,
                    'jenis_kerjasama_id' => $jenisKerjasamaId,
                    'tujuan' => $submission->tujuan_pengajuan,
                    'keterangan' => null,
                    'nilai_kontrak' => 0,
                ]);

                // 6. Kirim notifikasi ke Humas / Unit Kerja
                $senderId = Auth::id() ?: 1;
                $linkRepositori = route('unit.dkerjasama');
                $judulNotif = 'Pengajuan Kerja Sama Baru Disahkan';
                $pesanNotif = "Pimpinan menyetujui pengajuan mitra '{$submission->nama_mitra}' ({$submission->judul_pengajuan}). Silakan lengkapi data & dokumen kerjasama.";

                User::whereHas('role', fn ($query) => $query->whereIn(DB::raw('LOWER(TRIM(role_name))'), ['unit_kerja', 'unit', 'humas']))
                    ->get()
                    ->each(function (User $unitUser) use ($senderId, $cooperation, $judulNotif, $pesanNotif, $linkRepositori) {
                        Notifikasi::send(
                            $unitUser->id,
                            $senderId,
                            $cooperation->id,
                            'data_baru',
                            $judulNotif,
                            $pesanNotif,
                            $linkRepositori
                        );
                    });
            }

            $submission->update([
                'status' => $validated['keputusan'],
                'catatan_pimpinan' => $validated['catatan_pimpinan'] ?? null,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'mitra_id' => $mitraId,
            ]);

            Notifikasi::where('source_type', 'pengajuan_mitra')
                ->where('source_id', $submission->id)
                ->update(['is_read' => 1]);

            DB::commit();

            // --- Kirim notifikasi ke mitra setelah DB commit berhasil ---
            $sendEmail = $request->boolean('send_email');
            $sendWhatsApp = $request->boolean('send_whatsapp');

            if ($sendEmail) {
                $emailMessage = !empty($validated['custom_message_email'])
                    ? $validated['custom_message_email']
                    : NotificationService::generateDefaultMessage($submission, 'email');
                NotificationService::sendEmail($submission, $emailMessage);
            }

            if ($sendWhatsApp) {
                $waMessage = !empty($validated['custom_message_whatsapp'])
                    ? $validated['custom_message_whatsapp']
                    : NotificationService::generateDefaultMessage($submission, 'whatsapp');
                NotificationService::sendWhatsApp($submission, $waMessage);
            }

            $message = $validated['keputusan'] === PengajuanKerjasamaMitra::STATUS_DISETUJUI
                ? 'Pengajuan mitra berhasil disetujui dan dicatat ke master mitra.'
                : 'Pengajuan mitra berhasil ditolak.';

            // Tambahkan info notifikasi ke flash message
            $notifInfo = [];
            if ($sendEmail) $notifInfo[] = 'Email';
            if ($sendWhatsApp) $notifInfo[] = 'WhatsApp';

            if (count($notifInfo) > 0) {
                $message .= ' Notifikasi dikirim via ' . implode(' & ', $notifInfo) . '.';
            }

            return redirect()->route('pimpinan.pengajuan_mitra')->with('success', $message);
        } catch (\Exception $exception) {
            DB::rollBack();

            Log::error('PengajuanMitra review error: ' . $exception->getMessage(), [
                'submission_id' => $id,
                'trace' => $exception->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal memproses pengajuan mitra: ' . $exception->getMessage());
        }
    }
}


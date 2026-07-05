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
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

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
            $isPerpanjangan = !empty($submission->mitra_id) || !empty($submission->doc_number);

            if ($validated['keputusan'] === PengajuanKerjasamaMitra::STATUS_DISETUJUI) {

                if ($isPerpanjangan) {
                    // === PERPANJANGAN ===
                    // Tidak membuat record Cooperation/Pejabat/DetailKegiatan.
                    // Data hanya masuk ke menu "Pengajuan Perpanjangan" Humas/Unit Kerja.
                    // Record Cooperation baru akan dibuat oleh Humas di halaman proses perpanjangan.

                    $senderId = Auth::id() ?: 1;
                    $linkPerpanjangan = route('unit.pengajuan_perpanjangan');

                    User::whereHas('role', fn ($query) => $query->whereIn(DB::raw('LOWER(TRIM(role_name))'), ['unit_kerja', 'unit', 'humas']))
                        ->get()
                        ->each(function (User $unitUser) use ($senderId, $submission, $linkPerpanjangan) {
                            Notifikasi::send(
                                $unitUser->id,
                                $senderId,
                                $submission->id,
                                'pengajuan_perpanjangan',
                                'Pengajuan Perpanjangan Disetujui Pimpinan',
                                "Pimpinan menyetujui pengajuan perpanjangan mitra '{$submission->nama_mitra}' ({$submission->judul_pengajuan}). Silakan lengkapi berkas perpanjangan.",
                                $linkPerpanjangan,
                                'pengajuan_mitra'
                            );
                        });

                } else {
                    // === KERJA SAMA BARU ===
                    // 1. Simpan / update data Mitra
                    $mitra = null;
                    if ($submission->nama_mitra) {
                        $mitra = Mitra::whereRaw('LOWER(nama_mitra) = ?', [strtolower($submission->nama_mitra)])->first();
                    }

                    if (! $mitra && $submission->nama_mitra) {
                        $mitra = Mitra::create([
                            'nama_mitra' => $submission->nama_mitra,
                            'id_klasifikasi' => $submission->id_klasifikasi,
                            'alamat' => $submission->alamat ?: '-',
                            'kategori' => $submission->kategori ?: 'nasional',
                            'negara' => $submission->negara,
                            'telp' => $submission->telp,
                            'website' => $submission->website,
                        ]);
                    } elseif ($mitra) {
                        $mitra->fill([
                            'id_klasifikasi' => $mitra->id_klasifikasi ?: $submission->id_klasifikasi,
                            'alamat' => $mitra->alamat ?: $submission->alamat,
                            'negara' => $mitra->negara ?: $submission->negara,
                            'telp' => $mitra->telp ?: $submission->telp,
                            'website' => $mitra->website ?: $submission->website,
                        ])->save();
                    }

                    $mitraId = $mitra?->id ?: $submission->mitra_id;

                    // 2. Buat / Ambil Pejabat penandatangan & penanggung jawab mitra
                    $penandatanganMitra = Pejabat::create([
                        'nama' => $submission->nama_penandatangan ?: 'Mitra',
                        'jabatan' => $submission->jabatan_penandatangan ?: '-',
                    ]);

                    $pjMitra = null;
                    if ($submission->nama_penanggung_jawab) {
                        $pjMitra = Pejabat::create([
                            'nama' => $submission->nama_penanggung_jawab,
                            'jabatan' => $submission->jabatan_penanggung_jawab ?: '-',
                        ]);
                    }

                    // 3. Buat / Update record Cooperation (Status: proses, Status Dokumen: Draft)
                    $cooperation = Cooperation::where('pengajuan_kerjasama_mitra_id', $submission->id)->first();
                    if (! $cooperation) {
                        $cooperation = Cooperation::create([
                            'jenis' => $submission->jenis ?? 'MoU (Memorandum of Understanding)',
                            'doc_number' => $submission->doc_number,
                            'title' => $submission->judul_pengajuan,
                            'description' => $submission->tujuan_pengajuan,
                            'start_date' => $submission->start_date,
                            'end_date' => $submission->end_date,
                            'status' => 'proses',
                            'status_dokumen' => 'Draft',
                            'mitra_id' => $mitraId,
                            'penandatangan_mitra_id' => $penandatanganMitra->id,
                            'pj_mitra_id' => $pjMitra?->id,
                            'pengajuan_kerjasama_mitra_id' => $submission->id,
                            'created_by' => Auth::id(),
                        ]);
                    } else {
                        $cooperation->update([
                            'status' => 'proses',
                            'status_dokumen' => 'Draft',
                            'mitra_id' => $mitraId,
                        ]);
                    }

                    // 4. Cari / assign JenisKerjasama
                    $jenisKerjasamaId = 1;
                    if ($submission->ruang_lingkup) {
                        $jenisKerjasamaMatch = JenisKerjasama::whereRaw('LOWER(nama_kerjasama) = ?', [strtolower(trim($submission->ruang_lingkup))])->first();
                        if ($jenisKerjasamaMatch) {
                            $jenisKerjasamaId = $jenisKerjasamaMatch->id;
                        } else {
                            $jenisKerjasamaId = JenisKerjasama::first()?->id ?? 1;
                        }
                    }

                    DetailKegiatan::firstOrCreate(
                        ['cooperation_id' => $cooperation->id],
                        [
                            'jenis_kerjasama_id' => $jenisKerjasamaId,
                            'tujuan' => $submission->tujuan_pengajuan,
                            'keterangan' => null,
                            'nilai_kontrak' => 0,
                        ]
                    );

                    // 5. Kirim notifikasi ke Humas / Unit Kerja
                    $senderId = Auth::id() ?: 1;
                    $linkRepositori = route('unit.dkerjasama');

                    User::whereHas('role', fn ($query) => $query->whereIn(DB::raw('LOWER(TRIM(role_name))'), ['unit_kerja', 'unit', 'humas']))
                        ->get()
                        ->each(function (User $unitUser) use ($senderId, $cooperation, $linkRepositori, $submission) {
                            Notifikasi::send(
                                $unitUser->id,
                                $senderId,
                                $cooperation->id,
                                'data_baru',
                                'Pengajuan Kerja Sama Baru Disahkan',
                                "Pimpinan menyetujui pengajuan mitra '{$submission->nama_mitra}' ({$submission->judul_pengajuan}). Silakan lengkapi data & dokumen kerjasama.",
                                $linkRepositori
                            );
                        });
                }
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
                ? ($isPerpanjangan ? 'Pengajuan perpanjangan berhasil disetujui dan notifikasi dikirim ke Humas/Unit Kerja.' : 'Pengajuan mitra berhasil disetujui dan dicatat ke master mitra.')
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


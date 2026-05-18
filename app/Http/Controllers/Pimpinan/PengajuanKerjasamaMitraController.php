<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\PengajuanKerjasamaMitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        ]);

        DB::beginTransaction();

        try {
            $mitraId = $submission->mitra_id;

            if ($validated['keputusan'] === PengajuanKerjasamaMitra::STATUS_DISETUJUI) {
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

            $message = $validated['keputusan'] === PengajuanKerjasamaMitra::STATUS_DISETUJUI
                ? 'Pengajuan mitra berhasil disetujui dan dicatat ke master mitra.'
                : 'Pengajuan mitra berhasil ditolak.';

            return redirect()->route('pimpinan.pengajuan_mitra')->with('success', $message);
        } catch (\Exception $exception) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses pengajuan mitra: ' . $exception->getMessage());
        }
    }
}

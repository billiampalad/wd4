<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Kesimpulan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluasiPimpinanController extends Controller
{
    public function show($id)
    {
        $kegiatan = Cooperation::with([
            'jurusans',
            'upas',
            'pusats',
            'mitra',
            'details.jenisKerjasama',
            'details.sasaran',
            'evaluasis.penilai',
            'laporanFiles',
            'pjInternal',
        ])->findOrFail($id);

        return view('auth.pimpinan', [
            'view' => 'detail_evaluasi',
            'kegiatan' => $kegiatan
        ]);
    }

    /**
     * Pimpinan memberikan penilaian dan mengubah status menjadi selesai.
     */
    public function evaluate(Request $request, $id)
    {
        $kegiatan = Cooperation::findOrFail($id);
        
        // Validasi Umum
        $request->validate([
            'ringkasan'        => 'nullable|string',
            'saran'            => 'nullable|string',
            'status_validasi'  => 'required|in:layak,tidak_layak',
            'tindak_lanjut'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan/Update Evaluasi (jika dari Jurusan, Pimpinan isi skor)
            $isJurusan = $kegiatan->tipe_pelaksana === 'jurusan';

            if ($kegiatan->status_dokumen === 'Menunggu Evaluasi' && $isJurusan) {
                $request->validate([
                    'sesuai_rencana' => 'required|integer|min:1|max:5',
                    'kualitas'       => 'required|integer|min:1|max:5',
                    'keterlibatan'   => 'required|integer|min:1|max:5',
                    'efisiensi'      => 'required|integer|min:1|max:5',
                    'kepuasan'       => 'required|integer|min:1|max:5',
                    'catatan'        => 'nullable|string',
                ]);

                Evaluasi::updateOrCreate(
                    ['id_kegiatan' => $kegiatan->id],
                    [
                        'dinilai_oleh'   => Auth::id(),
                        'sesuai_rencana' => $request->sesuai_rencana,
                        'kualitas'       => $request->kualitas,
                        'keterlibatan'   => $request->keterlibatan,
                        'efisiensi'      => $request->efisiensi,
                        'kepuasan'       => $request->kepuasan,
                        'catatan'        => $request->catatan,
                    ]
                );
            }

            // 2. Simpan/Update Kesimpulan
            Kesimpulan::updateOrCreate(
                ['id_kegiatan' => $kegiatan->id],
                [
                    'ringkasan'     => $request->ringkasan,
                    'saran'         => $request->saran,
                    'tindak_lanjut' => $request->tindak_lanjut ?? null,
                ]
            );

            // 3. Update Status Dokumen
            $statusDokumen = $request->status_validasi === 'layak' ? 'Disahkan' : 'Revisi';
            $kegiatan->update(['status_dokumen' => $statusDokumen]);

            // 4. KIRIM NOTIFIKASI KE PENGUSUL
            $sender = Auth::user();
            $pesan = $statusDokumen === 'Disahkan' 
                ? "Pimpinan telah menyetujui dokumen kerjasama: '{$kegiatan->title}'."
                : "Pimpinan meminta revisi untuk dokumen kerjasama: '{$kegiatan->title}'.";
            
            // Logika notifikasi bisa diperluas di sini untuk mengirim ke pemilik dokumen

            DB::commit();
            return redirect()->route('pimpinan.evaluasi')->with('success', 'Berhasil memproses evaluasi dokumen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses evaluasi: ' . $e->getMessage());
        }
    }
}

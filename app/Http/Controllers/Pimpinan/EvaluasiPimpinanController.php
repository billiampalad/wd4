<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\KegiatanKerjasama;
use App\Models\Evaluasi;
use App\Models\Kesimpulan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluasiPimpinanController extends Controller
{
    /**
     * Pimpinan memberikan penilaian dan mengubah status menjadi selesai.
     */
    public function evaluate(Request $request, $id)
    {
        $kegiatan = KegiatanKerjasama::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // 1. Simpan/Update Evaluasi (jika dari Jurusan, Pimpinan isi skor)
            if ($kegiatan->status === 'menunggu_evaluasi') {
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
            $request->validate([
                'ringkasan'     => 'required|string',
                'saran'         => 'required|string',
                'tindak_lanjut' => 'nullable|string',
            ]);

            Kesimpulan::updateOrCreate(
                ['id_kegiatan' => $kegiatan->id],
                [
                    'ringkasan'     => $request->ringkasan,
                    'saran'         => $request->saran,
                    'tindak_lanjut' => $request->tindak_lanjut,
                ]
            );

            // 3. Update Status
            $kegiatan->update(['status' => 'selesai']);

            // 4. KIRIM NOTIFIKASI KE PENGUSUL (JURUSAN/UNIT KERJA)
            $recipientId = $kegiatan->created_by;
            
            Notifikasi::send(
                $recipientId,
                Auth::id(),
                $kegiatan->id,
                'selesai',
                'Dokumen Telah Disetujui/Layak',
                "Pimpinan telah mengevaluasi dan menyetujui laporan kegiatan $kegiatan->nama_kegiatan.",
                // Link detail disesuaikan dengan role pengirim
                $kegiatan->jurusans()->exists() 
                    ? route('jurusan.kerjasama.show', $kegiatan->id) 
                    : route('unit.kerjasama.show', $kegiatan->id)
            );

            DB::commit();
            return back()->with('success', 'Evaluasi berhasil disimpan dan dokumen dinyatakan selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan evaluasi: ' . $e->getMessage());
        }
    }
}

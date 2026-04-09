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
    public function show($id)
    {
        $kegiatan = KegiatanKerjasama::with([
            'jurusans',
            'unitKerjas',
            'mitras',
            'tujuans',
            'pelaksanaans',
            'hasils',
            'dokumentasis',
            'evaluasis.penilai',
            'kesimpulans',
            'permasalahanSolusis',
            'jenisKerjasama'
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
        $kegiatan = KegiatanKerjasama::findOrFail($id);
        
        // Validasi Umum (Dibuat opsional sesuai permintaan)
        $request->validate([
            'ringkasan'        => 'nullable|string',
            'saran'            => 'nullable|string',
            'status_validasi'  => 'required|in:layak,tidak_layak',
            'tindak_lanjut'    => 'nullable|string',
        ]);

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
            Kesimpulan::updateOrCreate(
                ['id_kegiatan' => $kegiatan->id],
                [
                    'ringkasan'     => $request->ringkasan,
                    'saran'         => $request->saran,
                    'tindak_lanjut' => $request->tindak_lanjut ?? null,
                ]
            );

            // 3. Update Status
            $statusValidasi = $request->status_validasi === 'layak' ? 'selesai' : 'revisi';
            $kegiatan->update(['status' => $statusValidasi]);

            // 4. KIRIM NOTIFIKASI KE PENGUSUL (JURUSAN/UNIT KERJA)
            $recipientId = $kegiatan->created_by;
            if ($recipientId) {
                $judulNotif = $statusValidasi === 'selesai' ? 'Dokumen Telah Disetujui/Layak' : 'Dokumen Perlu Revisi';
                $pesanNotif = $statusValidasi === 'selesai' 
                    ? "Pimpinan telah mengevaluasi dan menyetujui laporan kegiatan $kegiatan->nama_kegiatan."
                    : "Pimpinan telah mengevaluasi laporan kegiatan $kegiatan->nama_kegiatan. Silakan cek catatan revisi.";
                
                Notifikasi::send(
                    $recipientId,
                    Auth::id(),
                    $kegiatan->id,
                    $statusValidasi,
                    $judulNotif,
                    $pesanNotif,
                    $kegiatan->jurusans()->exists() 
                        ? route('jurusan.kerjasama.show', $kegiatan->id) 
                        : route('unit.kerjasama.show', $kegiatan->id)
                );
            }

            DB::commit();
            
            // Berikan response flash data
            return redirect()->route('pimpinan.evaluasi')->with('success', 'Evaluasi berhasil disimpan dan dokumen dinyatakan ' . ($statusValidasi === 'selesai' ? 'selesai.' : 'perlu revisi.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan evaluasi: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;
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
        $kegiatan = Cooperation::with(['upas', 'pusats'])->findOrFail($id);

        // Validasi Umum
        $request->validate([
            'ringkasan' => 'nullable|string',
            'saran' => 'nullable|string',
            'status_validasi' => 'required|in:layak,tidak_layak,revisi',
            'tindak_lanjut' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan/Update Evaluasi pada tabel evaluasis.
            $isJurusan = $kegiatan->tipe_pelaksana === 'jurusan';
            $evaluasiData = [
                'dinilai_oleh' => Auth::id(),
                'ringkasan' => $request->ringkasan,
                'saran' => $request->saran,
                'tindak_lanjut' => $request->tindak_lanjut,
                'status_validasi' => $request->status_validasi,
            ];

            if ($kegiatan->status_dokumen === 'Menunggu Evaluasi' && $isJurusan && $request->status_validasi === 'layak') {
                $request->validate([
                    'sesuai_rencana' => 'required|integer|min:1|max:5',
                    'kualitas' => 'required|integer|min:1|max:5',
                    'keterlibatan' => 'required|integer|min:1|max:5',
                    'efisiensi' => 'required|integer|min:1|max:5',
                    'kepuasan' => 'required|integer|min:1|max:5',
                    'catatan' => 'nullable|string',
                ]);

                $evaluasiData = array_merge($evaluasiData, [
                    'sesuai_rencana' => $request->sesuai_rencana,
                    'kualitas' => $request->kualitas,
                    'keterlibatan' => $request->keterlibatan,
                    'efisiensi' => $request->efisiensi,
                    'kepuasan' => $request->kepuasan,
                    'catatan' => $request->catatan,
                ]);
            }

            Evaluasi::updateOrCreate(
                ['cooperation_id' => $kegiatan->id],
                $evaluasiData
            );

            // 3. Update Status Dokumen
            $statusDokumen = $request->status_validasi === 'layak' ? 'Disahkan' : 'Revisi';
            $updateKegiatan = ['status_dokumen' => $statusDokumen];

            if ($statusDokumen === 'Disahkan') {
                $updateKegiatan['status'] = 'aktif';
            }

            $kegiatan->update($updateKegiatan);

            // 4. KIRIM NOTIFIKASI KE PENGUSUL
            $pesan = $statusDokumen === 'Disahkan'
                ? "Pimpinan telah menyetujui dokumen kerjasama: '{$kegiatan->title}'."
                : "Pimpinan meminta revisi untuk dokumen kerjasama: '{$kegiatan->title}'.";

            foreach ($this->unitRecipients($kegiatan) as $unitUser) {
                Notifikasi::send(
                    $unitUser->id,
                    Auth::id(),
                    $kegiatan->id,
                    $statusDokumen === 'Disahkan' ? 'disahkan' : 'revisi',
                    $statusDokumen === 'Disahkan' ? 'Dokumen Disahkan' : 'Dokumen Perlu Revisi',
                    $pesan,
                    route('unit.kerjasama.show', $kegiatan->id)
                );
            }

            DB::commit();
            return redirect()->route('pimpinan.evaluasi')->with('success', 'Berhasil memproses evaluasi dokumen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses evaluasi: ' . $e->getMessage());
        }
    }

    private function isBerlakuAktifHariIni(Cooperation $kegiatan): bool
    {
        if (!$kegiatan->start_date || !$kegiatan->end_date) {
            return false;
        }

        $today = Carbon::today();

        return $today->betweenIncluded(
            Carbon::parse($kegiatan->start_date)->startOfDay(),
            Carbon::parse($kegiatan->end_date)->endOfDay()
        );
    }

    private function unitRecipients(Cooperation $kegiatan)
    {
        $unitNames = $kegiatan->upas->pluck('nama_upa')
            ->merge($kegiatan->pusats->pluck('nama_pusat'))
            ->filter()
            ->values();

        $query = User::whereHas('role', function ($q) {
            $q->where('role_name', 'unit_kerja');
        });

        if ($unitNames->isNotEmpty()) {
            $targeted = (clone $query)
                ->whereHas('profile.unitKerja', function ($q) use ($unitNames) {
                    $q->whereIn('nama_unit_pelaksana', $unitNames);
                })
                ->get();

            if ($targeted->isNotEmpty()) {
                return $targeted;
            }
        }

        return $query->get();
    }
}

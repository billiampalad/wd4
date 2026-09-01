<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\KegiatanMahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiProdiController extends Controller
{
    /**
     * Display the Evaluation and Reporting view for Program Studi.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $prodiId = $user->profile?->prodi_id ?? null;
        $jurusanId = $user->profile?->jurusan_id ?? null;

        if (!$jurusanId && $prodiId) {
            $prodiModel = Prodi::find($prodiId);
            $jurusanId = $prodiModel?->jurusan_id;
        }

        $currentProdi = $prodiId ? Prodi::with('jurusan')->find($prodiId) : null;
        $prodiName = $currentProdi?->nama_prodi ?? 'Program Studi';

        // 1. Ambil Dokumen Kerja Sama Terkait Prodi
        $cooperations = Cooperation::with([
            'mitra',
            'evaluasis',
            'laporanFiles',
            'prodis',
            'jurusans'
        ])
        ->when($prodiId || $jurusanId, function ($query) use ($prodiId, $jurusanId) {
            $query->where(function ($sub) use ($prodiId, $jurusanId) {
                $sub->where('tingkat', 'Institusi');

                if ($prodiId) {
                    $sub->orWhereHas('prodis', function ($pQuery) use ($prodiId) {
                        $pQuery->where('prodis.id', $prodiId);
                    });
                }

                if ($jurusanId) {
                    $sub->orWhere('jurusan_id', $jurusanId)
                        ->orWhereHas('jurusans', function ($jQuery) use ($jurusanId) {
                            $jQuery->where('jurusans.id', $jurusanId);
                        });
                }
            });
        })
        ->orderBy('created_at', 'desc')
        ->get();

        // 2. Ambil Penempatan Mahasiswa Prodi
        $penempatans = KegiatanMahasiswa::with(['mahasiswa', 'kegiatan', 'mitra', 'pembimbings'])
            ->when($prodiId, function ($query) use ($prodiId) {
                $query->whereHas('mahasiswa', function ($mQuery) use ($prodiId) {
                    $mQuery->where('prodi_id', $prodiId);
                });
            })
            ->get();

        // 3. Perhitungan KPI Metrics
        $totalKerjasama = $cooperations->count();
        $coopWithEvaluasi = $cooperations->filter(fn($c) => $c->evaluasis->isNotEmpty())->count();

        $penempatanDinilai = $penempatans->whereNotNull('nilai_mitra');
        $totalDinilaiCount = $penempatanDinilai->count();
        $avgScore = $totalDinilaiCount > 0 ? round($penempatanDinilai->avg('nilai_mitra'), 1) : 0;

        $satisfactionCount = $penempatanDinilai->filter(fn($p) => (float)$p->nilai_mitra >= 80)->count();
        $kepuasanPersen = $totalDinilaiCount > 0 ? round(($satisfactionCount / $totalDinilaiCount) * 100, 1) : 100;

        $uniqueMitras = $cooperations->map(fn($c) => $c->mitra?->nama_mitra)->filter()->unique()->values();

        return view('auth.layout.prodi.evaluasi', compact(
            'cooperations',
            'penempatans',
            'totalKerjasama',
            'coopWithEvaluasi',
            'totalDinilaiCount',
            'avgScore',
            'kepuasanPersen',
            'uniqueMitras',
            'prodiName'
        ));
    }
}

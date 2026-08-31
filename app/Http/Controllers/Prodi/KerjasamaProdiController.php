<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KerjasamaProdiController extends Controller
{
    /**
     * Display a listing of cooperations relevant to the logged-in Prodi.
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

        // Query cooperations connected to this prodi, its parent jurusan, or institutional level (Institusi)
        $baseQuery = Cooperation::with([
            'mitra.klasifikasi',
            'pjInternal',
            'pjMitra',
            'penandatanganInternal',
            'penandatanganMitra',
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
        }, function ($query) {
            // If user has no specific prodi assigned, show all cooperations
            $query->whereNotNull('id');
        })
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc');

        $allKerjasama = $baseQuery->get();

        // ─── Metric Calculations ─────────────────────────────────
        $totalKerjasama = $allKerjasama->count();
        $aktifCount = $allKerjasama->filter(function ($item) {
            $st = strtolower($item->status_berlaku ?? $item->status_dokumen ?? '');
            return $st === 'aktif' || $st === 'disahkan';
        })->count();

        $perpanjanganCount = $allKerjasama->filter(function ($item) {
            $st = strtolower($item->status_berlaku ?? '');
            return str_contains($st, 'perpanjangan') || $st === 'akan berakhir';
        })->count();

        $expiredCount = $allKerjasama->filter(function ($item) {
            $st = strtolower($item->status_berlaku ?? '');
            return in_array($st, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa', 'tidak aktif'], true);
        })->count();

        return view('auth.prodi', [
            'kerjasamaList'     => $allKerjasama,
            'currentProdi'      => $currentProdi,
            'prodiName'         => $prodiName,
            'totalKerjasama'    => $totalKerjasama,
            'aktifCount'        => $aktifCount,
            'perpanjanganCount' => $perpanjanganCount,
            'expiredCount'      => $expiredCount,
        ]);
    }
}

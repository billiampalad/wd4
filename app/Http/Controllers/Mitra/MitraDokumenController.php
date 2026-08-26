<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraDokumenController extends Controller
{
    /**
     * Menampilkan daftar dokumen kerja sama yang terikat dengan Mitra (UC14).
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->mitra_id) {
            return redirect()->route('mitra.dashboard')->with('error', 'Akun Anda belum terhubung dengan instansi Mitra.');
        }

        $cooperations = Cooperation::with(['jurusans', 'upas', 'pusats', 'laporanFiles', 'pksNumbers'])
            ->where('mitra_id', $user->mitra_id)
            ->latest()
            ->paginate(10);

        return view('auth.mitra', [
            'view' => 'dokumen_list',
            'cooperations' => $cooperations,
        ]);
    }

    /**
     * Menampilkan detail & berkas scan PDF dokumen kerja sama milik Mitra (UC14).
     */
    public function show($id)
    {
        $user = Auth::user();
        $cooperation = Cooperation::with([
            'jurusans',
            'prodis',
            'upas',
            'pusats',
            'penandatanganInternal',
            'pjInternal',
            'penandatanganMitra',
            'pjMitra',
            'details.jenisKerjasama',
            'details.sasaran',
            'details.indikator',
            'laporanFiles',
            'pksNumbers',
        ])->where('mitra_id', $user->mitra_id)->findOrFail($id);

        return view('auth.mitra', [
            'view' => 'dokumen_detail',
            'cooperation' => $cooperation,
        ]);
    }

    /**
     * Mengirimkan catatan review draf dokumen online oleh Mitra (UC13).
     */
    public function storeReview(Request $request, $id)
    {
        $user = Auth::user();
        $cooperation = Cooperation::where('mitra_id', $user->mitra_id)->findOrFail($id);

        $request->validate([
            'catatan_review' => 'required|string|max:2000',
        ]);

        // Simpan catatan review dan kirim notifikasi ke pembuat/unit pengusul
        if ($cooperation->created_by) {
            $creator = User::with('role')->find($cooperation->created_by);
            $roleName = $creator && $creator->role ? $creator->role->name : 'unit_kerja';
            
            $routePrefix = match($roleName) {
                'jurusan' => 'jurusan',
                'upa' => 'upa',
                'pusat' => 'pusat',
                default => 'unit',
            };

            Notifikasi::send(
                $cooperation->created_by,
                $user->id,
                $cooperation->id,
                'review_draf',
                'Catatan Review Draf dari Mitra',
                "Mitra '{$user->name}' mengirimkan catatan review draf untuk dokumen: {$cooperation->judul}",
                route("{$routePrefix}.kerjasama.show", $cooperation->id)
            );
        }

        return back()->with('success', 'Catatan review draf dokumen berhasil dikirim ke unit pengusul.');
    }
}

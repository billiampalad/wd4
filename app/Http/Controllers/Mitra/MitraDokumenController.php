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
        $mitra = $user->mitra ?: ($user->mitra_id ? \App\Models\Mitra::find($user->mitra_id) : \App\Models\Mitra::first());
        $mitraId = $mitra?->id;
        $mitraName = $mitra ? $mitra->nama_mitra : ($user->name ?? 'Mitra Kerjasama');

        $query = Cooperation::with([
            'jurusans', 'upas', 'pusats', 'jurusan', 'upa', 'pusat',
            'laporanFiles', 'pksNumbers', 'createdBy', 'updatedBy'
        ]);

        if ($mitraId) {
            $query->where('mitra_id', $mitraId);
        }

        $kerjasamaList = $query->orderBy('created_at', 'desc')->get();

        // Metrik Statistik
        $totalKerjasama = $kerjasamaList->count();
        $aktifCount = $kerjasamaList->filter(function ($item) {
            $statusBerlaku = strtolower($item->status_berlaku ?? '');
            $statusDokumen = strtolower($item->status_dokumen ?? '');
            return $statusBerlaku === 'aktif' || $statusDokumen === 'disahkan';
        })->count();

        // 1. Mahasiswa Magang Aktif
        $totalMhsMagang = \App\Models\KegiatanMahasiswa::when($mitraId, function ($q) use ($mitraId) {
            return $q->where('mitra_id', $mitraId);
        })->where('status', 'Aktif')->count();

        // 2. Tracking Alumni Terserap
        $alumniCount = \App\Models\AlumniMitra::when($mitraId, function ($q) use ($mitraId) {
            return $q->where('mitra_id', $mitraId);
        })->where('status', 'Aktif')->count();

        return view('auth.mitra', compact(
            'user',
            'mitra',
            'mitraName',
            'kerjasamaList',
            'totalKerjasama',
            'aktifCount',
            'totalMhsMagang',
            'alumniCount'
        ));
    }

    /**
     * Menampilkan detail & berkas scan PDF dokumen kerja sama milik Mitra (UC14).
     */
    public function show($id)
    {
        $user = Auth::user();
        $cooperation = Cooperation::with([
            'mitra',
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
            'evaluasis.penilai',
        ])->where('mitra_id', $user->mitra_id)->findOrFail($id);

        return view('auth.mitra', [
            'view' => 'dokumen_detail',
            'cooperation' => $cooperation,
            'kegiatan' => $cooperation,
        ]);
    }

    /**
     * Mengirimkan catatan review draf dokumen online oleh Mitra (UC13).
     */
    public function storeReview(Request $request, $id)
    {
        $user = Auth::user();
        $cooperation = Cooperation::where('mitra_id', $user->mitra_id)->findOrFail($id);

        $action = $request->input('action', 'revisi');

        $request->validate([
            'catatan_review' => $action === 'setuju' ? 'nullable|string|max:2000' : 'required|string|max:2000',
        ]);

        $isSetuju = $action === 'setuju';
        $catatan = $request->input('catatan_review');

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

            $notifTitle = $isSetuju ? 'Draf Telah Disetujui oleh Mitra' : 'Catatan Review Draf dari Mitra';
            $notifType = $isSetuju ? 'review_draf_setuju' : 'review_draf';
            $notifMsg = $isSetuju 
                ? "Mitra '{$user->name}' telah menyetujui draf dokumen: {$cooperation->judul}" . ($catatan ? " (Catatan: {$catatan})" : "")
                : "Mitra '{$user->name}' mengirimkan catatan review draf untuk dokumen: {$cooperation->judul}" . ($catatan ? " (Catatan: {$catatan})" : "");

            Notifikasi::send(
                $cooperation->created_by,
                $user->id,
                $cooperation->id,
                $notifType,
                $notifTitle,
                $notifMsg,
                route("{$routePrefix}.kerjasama.show", $cooperation->id)
            );
        }

        $successMsg = $isSetuju
            ? 'Draf dokumen berhasil disetujui. Pemberitahuan telah dikirim ke unit pengusul.'
            : 'Catatan review draf dokumen berhasil dikirim ke unit pengusul.';

        return back()->with('success', $successMsg);
    }
}

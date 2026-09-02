<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\KegiatanMahasiswa;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianMahasiswaController extends Controller
{
    /**
     * Menampilkan daftar mahasiswa magang dan monitoring penempatan untuk Mitra (UC21 & UC22).
     */
    public function index()
    {
        $user = Auth::user();
        $mitra = $user->mitra ?: ($user->mitra_id ? Mitra::find($user->mitra_id) : Mitra::first());
        $mitraId = $mitra?->id;
        $mitraName = $mitra ? $mitra->nama_mitra : ($user->name ?? 'Mitra Kerjasama');

        // Mengambil seluruh penempatan mahasiswa di instansi mitra ini
        $query = KegiatanMahasiswa::with([
            'mahasiswa.prodi.jurusan',
            'kegiatan.cooperation',
            'pembimbings',
            'mitra'
        ]);

        if ($mitraId) {
            $query->where('mitra_id', $mitraId);
        }

        $penempatans = $query->orderBy('created_at', 'desc')->get();

        // ─── Metrik KPI Ringkasan (UC22) ───
        $totalMahasiswa = $penempatans->count();

        $aktifCount = $penempatans->filter(function ($item) {
            $status = strtolower($item->status ?? '');
            return $status === 'aktif' || $status === 'berjalan';
        })->count();

        $belumDinilaiCount = $penempatans->filter(function ($item) {
            return is_null($item->nilai_mitra);
        })->count();

        $sudahDinilaiCount = $penempatans->filter(function ($item) {
            return !is_null($item->nilai_mitra);
        })->count();

        $avgNilai = $sudahDinilaiCount > 0 
            ? round($penempatans->whereNotNull('nilai_mitra')->avg('nilai_mitra'), 1) 
            : 0;

        // Distinct filter options
        $availableProdis = $penempatans->map(function ($item) {
            return $item->mahasiswa?->prodi?->nama_prodi;
        })->filter()->unique()->values();

        $availableYears = $penempatans->map(function ($item) {
            if ($item->periode_mulai) {
                return $item->periode_mulai instanceof \Carbon\Carbon 
                    ? $item->periode_mulai->format('Y') 
                    : substr((string)$item->periode_mulai, 0, 4);
            }
            return null;
        })->filter()->unique()->sortDesc()->values();

        return view('auth.mitra', [
            'view' => 'penilaian',
            'user' => $user,
            'mitra' => $mitra,
            'mitraName' => $mitraName,
            'penempatans' => $penempatans,
            'totalMahasiswa' => $totalMahasiswa,
            'aktifCount' => $aktifCount,
            'belumDinilaiCount' => $belumDinilaiCount,
            'sudahDinilaiCount' => $sudahDinilaiCount,
            'avgNilai' => $avgNilai,
            'availableProdis' => $availableProdis,
            'availableYears' => $availableYears,
        ]);
    }

    /**
     * Menampilkan detail mahasiswa dan penempatannya (AJAX / Show).
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $penempatan = KegiatanMahasiswa::with([
            'mahasiswa.prodi.jurusan',
            'kegiatan.cooperation',
            'pembimbings',
            'mitra'
        ])
        ->when($mitraId, fn($q) => $q->where('mitra_id', $mitraId))
        ->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $penempatan
            ]);
        }

        return redirect()->route('mitra.penilaian.index');
    }

    /**
     * Show the form for editing (opsional fallback).
     */
    public function edit(string $id)
    {
        return $this->show($id);
    }

    /**
     * Menyimpan atau memperbarui nilai evaluasi mahasiswa magang (UC21).
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nilai_mitra' => 'required|numeric|min:0|max:100',
            'catatan_mitra' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $penempatan = KegiatanMahasiswa::with(['mahasiswa', 'kegiatan'])
            ->when($mitraId, fn($q) => $q->where('mitra_id', $mitraId))
            ->findOrFail($id);

        $nilai = (float) $request->nilai_mitra;
        $catatan = $request->catatan_mitra;

        $penempatan->update([
            'nilai_mitra' => $nilai,
            'catatan_mitra' => $catatan,
            'status' => 'Selesai', // update status jika sudah dinilai
        ]);

        // Kirim notifikasi sistem ke pengusul kegiatan, prodi, dan jurusan (UC21)
        $namaMhs = $penempatan->mahasiswa ? $penempatan->mahasiswa->nama : 'Mahasiswa Magang';
        $namaMitra = $user->mitra ? $user->mitra->nama_mitra : ($user->name ?? 'Mitra Kerjasama');

        $recipients = collect();
        if ($penempatan->kegiatan && $penempatan->kegiatan->created_by) {
            $recipients->push($penempatan->kegiatan->created_by);
        }

        // Notify prodi users
        $prodiUsers = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['prodi', 'jurusan']);
        })->pluck('id');

        $recipients = $recipients->merge($prodiUsers)->unique();

        foreach ($recipients as $recipientId) {
            Notifikasi::send(
                $recipientId,
                $user->id,
                $penempatan->id,
                'penilaian_magang',
                'Penilaian Magang Mahasiswa Baru',
                "Mitra '{$namaMitra}' telah menginput nilai ({$nilai}) untuk mahasiswa: {$namaMhs}.",
                route('prodi.penempatan.index'),
                'kegiatan_mahasiswa'
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Nilai mahasiswa {$penempatan->mahasiswa?->nama} berhasil disimpan!",
                'data' => $penempatan
            ]);
        }

        return redirect()->route('mitra.penilaian.index')->with('success', "Penilaian untuk mahasiswa {$penempatan->mahasiswa?->nama} berhasil disimpan.");
    }
}

<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KegiatanMahasiswa;
use Illuminate\Support\Facades\Auth;

class PenilaianMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $mitraId = $user->mitra_id;

        // Fetch all student placements for this mitra
        $penempatans = KegiatanMahasiswa::with(['mahasiswa', 'kegiatan'])
            ->where('mitra_id', $mitraId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.penilaian.index', compact('penempatans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $penempatan = KegiatanMahasiswa::with(['mahasiswa', 'kegiatan'])
            ->where('mitra_id', $mitraId)
            ->findOrFail($id);

        return view('mitra.penilaian.edit', compact('penempatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nilai_mitra' => 'required|numeric|min:0|max:100',
            'catatan_mitra' => 'nullable|string',
        ]);

        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $penempatan = KegiatanMahasiswa::where('mitra_id', $mitraId)->findOrFail($id);
        
        $penempatan->update([
            'nilai_mitra' => $request->nilai_mitra,
            'catatan_mitra' => $request->catatan_mitra,
        ]);

        return redirect()->route('mitra.penilaian.index')->with('success', 'Penilaian Mahasiswa berhasil disimpan.');
    }
}

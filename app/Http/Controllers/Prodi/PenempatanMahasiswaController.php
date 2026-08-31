<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KegiatanMahasiswa;
use App\Models\KegiatanKerjasama;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\Pembimbing;
use Illuminate\Support\Facades\Auth;

class PenempatanMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Assuming Prodi only sees their students' placements or all for their prodi
        // but for now, we just list all or filter if needed.
        $penempatans = KegiatanMahasiswa::with(['mahasiswa', 'kegiatan', 'mitra', 'pembimbings'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.layout.prodi.mamag.index', compact('penempatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kegiatans = KegiatanKerjasama::where('status', '!=', 'draft')->get();
        // Since Prodi should filter its own students, ideally we filter by prodi_id, but here just all
        $mahasiswas = Mahasiswa::all();
        $mitras = Mitra::all();

        return view('auth.layout.prodi.mamag.create', compact('kegiatans', 'mahasiswas', 'mitras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatan_kerjasamas,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'mitra_id' => 'required|exists:mitras,id',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            
            'nama_pembimbing_internal' => 'required|string|max:255',
            'kontak_pembimbing_internal' => 'nullable|string|max:255',
            
            'nama_pembimbing_eksternal' => 'required|string|max:255',
            'kontak_pembimbing_eksternal' => 'nullable|string|max:255',
        ]);

        $penempatan = KegiatanMahasiswa::create([
            'kegiatan_id' => $request->kegiatan_id,
            'mahasiswa_id' => $request->mahasiswa_id,
            'mitra_id' => $request->mitra_id,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
            'status' => 'Aktif', // default status
        ]);

        // Pembimbing Internal
        Pembimbing::create([
            'kegiatan_mahasiswa_id' => $penempatan->id,
            'nama_pembimbing' => $request->nama_pembimbing_internal,
            'tipe' => 'Internal',
            'kontak' => $request->kontak_pembimbing_internal,
        ]);

        // Pembimbing Eksternal (Mitra)
        Pembimbing::create([
            'kegiatan_mahasiswa_id' => $penempatan->id,
            'nama_pembimbing' => $request->nama_pembimbing_eksternal,
            'tipe' => 'Eksternal',
            'kontak' => $request->kontak_pembimbing_eksternal,
        ]);

        return redirect()->route('prodi.penempatan.index')->with('success', 'Penempatan Mahasiswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penempatan = KegiatanMahasiswa::with(['mahasiswa', 'kegiatan', 'mitra', 'pembimbings'])->findOrFail($id);

        return view('auth.layout.prodi.mamag.show', compact('penempatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penempatan = KegiatanMahasiswa::with(['pembimbings'])->findOrFail($id);
        $kegiatans = KegiatanKerjasama::where('status', '!=', 'draft')->get();
        $mahasiswas = Mahasiswa::all();
        $mitras = Mitra::all();

        return view('auth.layout.prodi.mamag.edit', compact('penempatan', 'kegiatans', 'mahasiswas', 'mitras'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatan_kerjasamas,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'mitra_id' => 'required|exists:mitras,id',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'status' => 'required|string',
            
            'nama_pembimbing_internal' => 'required|string|max:255',
            'kontak_pembimbing_internal' => 'nullable|string|max:255',
            
            'nama_pembimbing_eksternal' => 'required|string|max:255',
            'kontak_pembimbing_eksternal' => 'nullable|string|max:255',
        ]);

        $penempatan = KegiatanMahasiswa::findOrFail($id);
        $penempatan->update([
            'kegiatan_id' => $request->kegiatan_id,
            'mahasiswa_id' => $request->mahasiswa_id,
            'mitra_id' => $request->mitra_id,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
            'status' => $request->status,
        ]);

        // Update Pembimbing Internal
        $pembimbingInternal = Pembimbing::where('kegiatan_mahasiswa_id', $penempatan->id)
                                        ->where('tipe', 'Internal')->first();
        if ($pembimbingInternal) {
            $pembimbingInternal->update([
                'nama_pembimbing' => $request->nama_pembimbing_internal,
                'kontak' => $request->kontak_pembimbing_internal,
            ]);
        } else {
            Pembimbing::create([
                'kegiatan_mahasiswa_id' => $penempatan->id,
                'nama_pembimbing' => $request->nama_pembimbing_internal,
                'tipe' => 'Internal',
                'kontak' => $request->kontak_pembimbing_internal,
            ]);
        }

        // Update Pembimbing Eksternal
        $pembimbingEksternal = Pembimbing::where('kegiatan_mahasiswa_id', $penempatan->id)
                                         ->where('tipe', 'Eksternal')->first();
        if ($pembimbingEksternal) {
            $pembimbingEksternal->update([
                'nama_pembimbing' => $request->nama_pembimbing_eksternal,
                'kontak' => $request->kontak_pembimbing_eksternal,
            ]);
        } else {
            Pembimbing::create([
                'kegiatan_mahasiswa_id' => $penempatan->id,
                'nama_pembimbing' => $request->nama_pembimbing_eksternal,
                'tipe' => 'Eksternal',
                'kontak' => $request->kontak_pembimbing_eksternal,
            ]);
        }

        return redirect()->route('prodi.penempatan.index')->with('success', 'Penempatan Mahasiswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penempatan = KegiatanMahasiswa::findOrFail($id);
        // Delete pembimbings first
        Pembimbing::where('kegiatan_mahasiswa_id', $penempatan->id)->delete();
        $penempatan->delete();

        return redirect()->route('prodi.penempatan.index')->with('success', 'Penempatan Mahasiswa berhasil dihapus.');
    }
}

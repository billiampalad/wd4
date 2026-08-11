<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\AlumniMitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniMitraController extends Controller
{
    /**
     * Display a listing of alumni working for the Mitra.
     */
    public function index()
    {
        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $alumniMitras = AlumniMitra::with(['alumni.prodi'])
            ->where('mitra_id', $mitraId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.alumni.index', compact('alumniMitras'));
    }

    /**
     * Update the status of an alumni working for the Mitra.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Resign,Pensiun,Tidak Diketahui',
        ]);

        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $alumniMitra = AlumniMitra::where('mitra_id', $mitraId)->findOrFail($id);

        $alumniMitra->update([
            'status' => $request->status,
        ]);

        return redirect()->route('mitra.alumni.index')->with('success', 'Status kerja alumni berhasil diperbarui.');
    }
}

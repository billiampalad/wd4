<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\AlumniMitra;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniMitraController extends Controller
{
    /**
     * Display a listing of the alumni and their placements for the Prodi.
     */
    public function index()
    {
        $user = Auth::user();
        $prodiId = $user->profile->prodi_id ?? null;

        $alumnis = Alumni::with(['alumniMitras.mitra', 'prodi'])
            ->where('prodi_id', $prodiId)
            ->orderBy('tahun_lulus', 'desc')
            ->get();

        $mitras = Mitra::orderBy('nama_mitra')->get();

        return view('auth.layout.prodi.alumni.index', compact('alumnis', 'mitras'));
    }

    /**
     * Show the form for creating a new alumni record and their placement.
     */
    public function create()
    {
        $mitras = Mitra::orderBy('nama_mitra')->get();
        return view('auth.layout.prodi.alumni.create', compact('mitras'));
    }

    /**
     * Store a newly created alumni and placement in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'tahun_lulus' => 'required|integer',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'mitra_id' => 'required|exists:mitras,id',
            'posisi' => 'required|string|max:255',
            'tahun_mulai' => 'required|integer',
        ]);

        $user = Auth::user();
        $prodiId = $user->profile->prodi_id;

        // Cari atau buat data alumni baru berdasarkan NIM
        $alumni = Alumni::firstOrCreate(
            ['nim' => $request->nim],
            [
                'nama' => $request->nama,
                'prodi_id' => $prodiId,
                'tahun_lulus' => $request->tahun_lulus,
                'email' => $request->email,
                'telepon' => $request->telepon,
            ]
        );

        // Buat relasi penempatan alumni di mitra (AlumniMitra)
        AlumniMitra::updateOrCreate(
            [
                'alumni_id' => $alumni->id,
                'mitra_id' => $request->mitra_id,
            ],
            [
                'posisi' => $request->posisi,
                'tahun_mulai' => $request->tahun_mulai,
                'status' => 'Aktif',
                'sumber_data' => 'Prodi',
            ]
        );

        return redirect()->route('prodi.alumni.index')->with('success', 'Data alumni dan penempatan berhasil ditambahkan.');
    }
}

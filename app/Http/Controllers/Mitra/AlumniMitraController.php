<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\AlumniMitra;
use App\Models\Mitra;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniMitraController extends Controller
{
    /**
     * Menampilkan repositori tracking lulusan / alumni yang bekerja di Mitra (UC32 & UC33).
     */
    public function index()
    {
        $user = Auth::user();
        $mitra = $user->mitra ?: ($user->mitra_id ? Mitra::find($user->mitra_id) : Mitra::first());
        $mitraId = $mitra?->id;
        $mitraName = $mitra ? $mitra->nama_mitra : ($user->name ?? 'Mitra Kerjasama');

        $query = AlumniMitra::with(['alumni.prodi.jurusan', 'mitra']);

        if ($mitraId) {
            $query->where('mitra_id', $mitraId);
        }

        $alumniMitras = $query->orderBy('created_at', 'desc')->get();

        // ─── Metrik KPI Ringkasan IKU 1 (UC33) ───
        $totalAlumni = $alumniMitras->count();

        $aktifCount = $alumniMitras->filter(function ($item) {
            return strtolower($item->status ?? '') === 'aktif';
        })->count();

        $nonAktifCount = $alumniMitras->filter(function ($item) {
            $st = strtolower($item->status ?? '');
            return in_array($st, ['resign', 'kontrak selesai', 'pensiun', 'tidak aktif'], true);
        })->count();

        $prodisCovered = $alumniMitras->map(function ($item) {
            return $item->alumni?->prodi?->nama_prodi;
        })->filter()->unique()->values();

        $totalProdiCount = $prodisCovered->count();

        // Distinct filter options
        $availableProdis = Prodi::orderBy('nama_prodi')->get();

        $availableYears = $alumniMitras->map(function ($item) {
            return $item->alumni?->tahun_lulus;
        })->filter()->unique()->sortDesc()->values();

        // Daftar master alumni untuk opsi autocomplete / quick select
        $masterAlumnis = Alumni::with('prodi')->orderBy('nama')->get();

        return view('auth.mitra', compact(
            'alumniMitras',
            'totalAlumni',
            'aktifCount',
            'nonAktifCount',
            'totalProdiCount',
            'availableProdis',
            'availableYears',
            'masterAlumnis',
            'mitraName',
            'mitra'
        ))->with('view', 'tracking');
    }

    /**
     * Menyimpan pencatatan data alumni yang bekerja di Mitra (UC32).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $mitra = $user->mitra ?: ($user->mitra_id ? Mitra::find($user->mitra_id) : Mitra::first());
        $mitraId = $mitra?->id;

        if (!$mitraId) {
            return redirect()->back()->with('error', 'Instansi Mitra Anda tidak ditemukan di sistem.');
        }

        // Mode 1: Pilih Alumni yang sudah terdaftar
        if ($request->filled('alumni_id')) {
            $request->validate([
                'alumni_id'   => 'required|exists:alumnis,id',
                'posisi'      => 'required|string|max:150',
                'tahun_mulai' => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
                'status'      => 'required|string|max:50',
            ]);

            // Cek duplikasi di mitra ini
            $exists = AlumniMitra::where('mitra_id', $mitraId)
                ->where('alumni_id', $request->alumni_id)
                ->first();

            if ($exists) {
                return redirect()->back()->with('error', 'Alumni tersebut sudah tercatat bekerja di instansi Anda.');
            }

            AlumniMitra::create([
                'alumni_id'   => $request->alumni_id,
                'mitra_id'    => $mitraId,
                'posisi'      => $request->posisi,
                'tahun_mulai' => $request->tahun_mulai,
                'status'      => $request->status ?? 'Aktif',
                'sumber_data' => 'Mitra',
            ]);

            return redirect()->route('mitra.alumni.index')->with('success', 'Data alumni berhasil ditambahkan ke instansi Anda.');
        }

        // Mode 2: Input Manual Alumni Baru
        $request->validate([
            'nim'         => 'required|string|max:30|unique:alumnis,nim',
            'nama'        => 'required|string|max:255',
            'prodi_id'    => 'required|exists:prodis,id',
            'tahun_lulus' => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'email'       => 'nullable|email|max:255',
            'telepon'     => 'nullable|string|max:30',
            'posisi'      => 'required|string|max:150',
            'tahun_mulai' => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'status'      => 'required|string|max:50',
        ]);

        $alumni = Alumni::create([
            'nim'         => $request->nim,
            'nama'        => $request->nama,
            'prodi_id'    => $request->prodi_id,
            'tahun_lulus' => $request->tahun_lulus,
            'email'       => $request->email,
            'telepon'     => $request->telepon,
        ]);

        AlumniMitra::create([
            'alumni_id'   => $alumni->id,
            'mitra_id'    => $mitraId,
            'posisi'      => $request->posisi,
            'tahun_mulai' => $request->tahun_mulai,
            'status'      => $request->status ?? 'Aktif',
            'sumber_data' => 'Mitra',
        ]);

        return redirect()->route('mitra.alumni.index')->with('success', 'Data alumni baru berhasil dicatat dan dihubungkan ke instansi Anda.');
    }

    /**
     * Memperbarui status kerja & posisi alumni yang bekerja di Mitra (UC32).
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'posisi'      => 'required|string|max:150',
            'tahun_mulai' => 'required|digits:4|integer',
            'status'      => 'required|string|max:50',
        ]);

        $user = Auth::user();
        $mitra = $user->mitra ?: ($user->mitra_id ? Mitra::find($user->mitra_id) : Mitra::first());
        $mitraId = $mitra?->id;

        $alumniMitra = AlumniMitra::when($mitraId, function ($q) use ($mitraId) {
            return $q->where('mitra_id', $mitraId);
        })->findOrFail($id);

        $alumniMitra->update([
            'posisi'      => $request->posisi,
            'tahun_mulai' => $request->tahun_mulai,
            'status'      => $request->status,
        ]);

        return redirect()->route('mitra.alumni.index')->with('success', 'Informasi karir alumni berhasil diperbarui.');
    }

    /**
     * Menghapus relasi alumni dari instansi Mitra.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $mitra = $user->mitra ?: ($user->mitra_id ? Mitra::find($user->mitra_id) : Mitra::first());
        $mitraId = $mitra?->id;

        $alumniMitra = AlumniMitra::when($mitraId, function ($q) use ($mitraId) {
            return $q->where('mitra_id', $mitraId);
        })->findOrFail($id);

        $alumniMitra->delete();

        return redirect()->route('mitra.alumni.index')->with('success', 'Data alumni berhasil dihapus dari instansi Anda.');
    }
}


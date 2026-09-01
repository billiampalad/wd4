<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KegiatanKerjasama;
use App\Models\DetailKegiatan;
use App\Models\Cooperation;
use App\Models\Sasaran;
use App\Models\Indikator;
use App\Models\JenisKerjasama;
use Illuminate\Support\Facades\Auth;

class KegiatanKerjasamaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $kegiatans = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.sasaran', 'detailKegiatan.indikator', 'detailKegiatan.jenisKerjasama'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('unit.kegiatan.index', compact('kegiatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get IA and SPK cooperations that are Disahkan (as per UC19)
        $iaDocuments = Cooperation::where(function ($q) {
                $q->where('jenis', 'IA')
                  ->orWhere('jenis', 'SPK');
            })
            ->where('status_dokumen', 'Disahkan')
            ->with(['mitra', 'penandatanganMitra', 'pjMitra'])
            ->get();
        
        $sasarans = Sasaran::all();
        $indikators = Indikator::all();
        $jenisKerjasamas = JenisKerjasama::all();

        return view('unit.kegiatan.create', compact('iaDocuments', 'sasarans', 'indikators', 'jenisKerjasamas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'cooperation_id' => 'required|exists:cooperations,id',
            'jenis_kerjasama_id' => 'nullable',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'sasaran_id' => 'nullable|exists:sasarans,id',
            'indikator_id' => 'nullable|exists:indikators,id',
            'volume_luaran' => 'nullable|string|max:255',
            'output' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $cooperation = Cooperation::findOrFail($request->cooperation_id);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $cooperation->id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
            'status' => 'Perencanaan',
        ]);

        $jenisId = is_array($request->jenis_kerjasama_id) ? ($request->jenis_kerjasama_id[0] ?? null) : $request->jenis_kerjasama_id;

        DetailKegiatan::create([
            'kegiatan_kerjasama_id' => $kegiatan->id,
            'cooperation_id' => $cooperation->id,
            'jenis_kerjasama_id' => $jenisId,
            'sasaran_id' => $request->sasaran_id,
            'indikator_id' => $request->indikator_id,
            'volume_luaran' => $request->volume_luaran ?: $request->target_volume,
            'output' => $request->output,
            'outcome' => $request->outcome,
        ]);

        return redirect()->route('unit.kegiatan.index')->with('success', 'Kegiatan Kerja Sama berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kegiatan = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.sasaran', 'detailKegiatan.indikator', 'detailKegiatan.jenisKerjasama'])->findOrFail($id);
        $detail = DetailKegiatan::where('kegiatan_kerjasama_id', $kegiatan->id)->with(['sasaran', 'indikator', 'jenisKerjasama'])->first();

        return view('unit.kegiatan.show', compact('kegiatan', 'detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kegiatan = KegiatanKerjasama::with('detailKegiatan')->findOrFail($id);
        $detail = DetailKegiatan::where('kegiatan_kerjasama_id', $kegiatan->id)->first();
        
        $iaDocuments = Cooperation::where(function ($q) {
                $q->where('jenis', 'IA')
                  ->orWhere('jenis', 'SPK');
            })
            ->where('status_dokumen', 'Disahkan')
            ->get();

        $sasarans = Sasaran::all();
        $indikators = Indikator::all();
        $jenisKerjasamas = JenisKerjasama::all();

        return view('unit.kegiatan.edit', compact('kegiatan', 'detail', 'iaDocuments', 'sasarans', 'indikators', 'jenisKerjasamas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'sasaran_id' => 'nullable|exists:sasarans,id',
            'indikator_id' => 'nullable|exists:indikators,id',
            'volume_luaran' => 'nullable|string|max:255',
            'output' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $kegiatan = KegiatanKerjasama::findOrFail($id);
        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
        ]);

        $detail = DetailKegiatan::where('kegiatan_kerjasama_id', $kegiatan->id)->first();
        $jenisId = is_array($request->jenis_kerjasama_id) ? ($request->jenis_kerjasama_id[0] ?? null) : $request->jenis_kerjasama_id;

        if ($detail) {
            $detail->update([
                'jenis_kerjasama_id' => $jenisId ?: $detail->jenis_kerjasama_id,
                'sasaran_id' => $request->sasaran_id,
                'indikator_id' => $request->indikator_id,
                'volume_luaran' => $request->volume_luaran ?: $request->target_volume,
                'output' => $request->output,
                'outcome' => $request->outcome,
            ]);
        } else {
            DetailKegiatan::create([
                'kegiatan_kerjasama_id' => $kegiatan->id,
                'cooperation_id' => $kegiatan->cooperation_id,
                'jenis_kerjasama_id' => $jenisId,
                'sasaran_id' => $request->sasaran_id,
                'indikator_id' => $request->indikator_id,
                'volume_luaran' => $request->volume_luaran ?: $request->target_volume,
                'output' => $request->output,
                'outcome' => $request->outcome,
            ]);
        }

        return redirect()->route('unit.kegiatan.index')->with('success', 'Kegiatan Kerja Sama berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kegiatan = KegiatanKerjasama::findOrFail($id);
        DetailKegiatan::where('kegiatan_kerjasama_id', $kegiatan->id)->delete();
        $kegiatan->delete();

        return redirect()->route('unit.kegiatan.index')->with('success', 'Kegiatan Kerja Sama berhasil dihapus.');
    }
}

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
        $kegiatans = KegiatanKerjasama::where('created_by', $user->id)
            ->with(['jenisKerjasama', 'mitras'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('unit.kegiatan.index', compact('kegiatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get IA cooperations that are Disahkan
        $iaDocuments = Cooperation::where('jenis_kerjasama', 'IA')
            ->where('status', 'Disahkan')
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
            'jenis_kerjasama_id' => 'required|array',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'sasaran_id' => 'required|exists:sasarans,id',
            'indikator_id' => 'required|exists:indikators,id',
            'target_volume' => 'required|integer',
            'output' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $cooperation = Cooperation::find($request->cooperation_id);

        $kegiatan = KegiatanKerjasama::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'jenis_dokumen' => 'IA',
            'nomor_mou' => $cooperation->no_dokumen,
            'tanggal_mou' => $cooperation->tanggal_mulai,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
            'created_by' => Auth::id(),
            'status' => 'draft',
        ]);

        $kegiatan->jenisKerjasama()->attach($request->jenis_kerjasama_id);

        DetailKegiatan::create([
            'kegiatan_id' => $kegiatan->id,
            'sasaran_id' => $request->sasaran_id,
            'indikator_id' => $request->indikator_id,
            'target_volume' => $request->target_volume,
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
        $kegiatan = KegiatanKerjasama::with(['jenisKerjasama', 'mitras', 'tujuans', 'pelaksanaans', 'hasils'])->findOrFail($id);
        $detail = DetailKegiatan::where('kegiatan_id', $kegiatan->id)->with(['sasaran', 'indikator'])->first();

        return view('unit.kegiatan.show', compact('kegiatan', 'detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kegiatan = KegiatanKerjasama::findOrFail($id);
        $detail = DetailKegiatan::where('kegiatan_id', $kegiatan->id)->first();
        
        $iaDocuments = Cooperation::where('jenis_kerjasama', 'IA')
            ->where('status', 'Disahkan')
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
            'jenis_kerjasama_id' => 'required|array',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'sasaran_id' => 'required|exists:sasarans,id',
            'indikator_id' => 'required|exists:indikators,id',
            'target_volume' => 'required|integer',
            'output' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $kegiatan = KegiatanKerjasama::findOrFail($id);
        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
        ]);

        $kegiatan->jenisKerjasama()->sync($request->jenis_kerjasama_id);

        $detail = DetailKegiatan::where('kegiatan_id', $kegiatan->id)->first();
        if ($detail) {
            $detail->update([
                'sasaran_id' => $request->sasaran_id,
                'indikator_id' => $request->indikator_id,
                'target_volume' => $request->target_volume,
                'output' => $request->output,
                'outcome' => $request->outcome,
            ]);
        } else {
            DetailKegiatan::create([
                'kegiatan_id' => $kegiatan->id,
                'sasaran_id' => $request->sasaran_id,
                'indikator_id' => $request->indikator_id,
                'target_volume' => $request->target_volume,
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
        DetailKegiatan::where('kegiatan_id', $kegiatan->id)->delete();
        $kegiatan->jenisKerjasama()->detach();
        $kegiatan->delete();

        return redirect()->route('unit.kegiatan.index')->with('success', 'Kegiatan Kerja Sama berhasil dihapus.');
    }
}

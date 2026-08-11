<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Auth;

class UmpanBalikController extends Controller
{
    /**
     * Display a listing of cooperations for feedback.
     */
    public function index()
    {
        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $cooperations = Cooperation::with(['evaluasis'])
            ->where('mitra_id', $mitraId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.umpan_balik.index', compact('cooperations'));
    }

    /**
     * Show the form for creating/editing feedback (UC26).
     */
    public function edit(string $cooperationId)
    {
        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $cooperation = Cooperation::where('mitra_id', $mitraId)->findOrFail($cooperationId);
        $evaluasi = Evaluasi::where('cooperation_id', $cooperationId)->first();

        return view('mitra.umpan_balik.form', compact('cooperation', 'evaluasi'));
    }

    /**
     * Store or update feedback from Mitra (UC26).
     */
    public function update(Request $request, string $cooperationId)
    {
        $request->validate([
            'skor_kepuasan' => 'required|integer|min:1|max:5',
            'catatan_mitra' => 'nullable|string',
            'saran_perbaikan' => 'nullable|string',
            'kesediaan_perpanjang' => 'required|in:ya,tidak,ragu',
        ]);

        $user = Auth::user();
        $mitraId = $user->mitra_id;

        $cooperation = Cooperation::where('mitra_id', $mitraId)->findOrFail($cooperationId);

        Evaluasi::updateOrCreate(
            ['cooperation_id' => $cooperation->id],
            [
                'mitra_id' => $mitraId,
                'kepuasan' => $request->skor_kepuasan,
                'catatan' => $request->catatan_mitra,
                'saran' => $request->saran_perbaikan,
                'tindak_lanjut' => 'Kesediaan Perpanjang: ' . ucfirst($request->kesediaan_perpanjang),
            ]
        );

        return redirect()->route('mitra.umpan_balik.index')->with('success', 'Umpan balik kerja sama berhasil dikirimkan. Terima kasih atas masukan Anda!');
    }
}

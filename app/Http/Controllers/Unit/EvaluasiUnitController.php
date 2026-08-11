<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EvaluasiUnitController extends Controller
{
    /**
     * Display a listing of evaluasis.
     */
    public function index()
    {
        $cooperations = Cooperation::with(['mitra', 'evaluasis'])
            ->whereIn('status', ['aktif', 'selesai'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('unit.evaluasi.index', compact('cooperations'));
    }

    /**
     * Show the form for creating/editing evaluasi (UC23).
     */
    public function edit(string $cooperationId)
    {
        $cooperation = Cooperation::with(['mitra', 'evaluasis'])->findOrFail($cooperationId);
        $evaluasi = Evaluasi::where('cooperation_id', $cooperationId)->first();

        return view('unit.evaluasi.form', compact('cooperation', 'evaluasi'));
    }

    /**
     * Store or update evaluasi draft (UC23).
     */
    public function update(Request $request, string $cooperationId)
    {
        $request->validate([
            'sesuai_rencana' => 'required|integer|min:1|max:5',
            'kualitas' => 'required|integer|min:1|max:5',
            'keterlibatan' => 'required|integer|min:1|max:5',
            'efisiensi' => 'required|integer|min:1|max:5',
            'kepuasan' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string',
            'ringkasan' => 'nullable|string',
            'saran' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
        ]);

        $cooperation = Cooperation::findOrFail($cooperationId);

        $evaluasi = Evaluasi::updateOrCreate(
            ['cooperation_id' => $cooperation->id],
            [
                'dinilai_oleh' => Auth::id(),
                'sesuai_rencana' => $request->sesuai_rencana,
                'kualitas' => $request->kualitas,
                'keterlibatan' => $request->keterlibatan,
                'efisiensi' => $request->efisiensi,
                'kepuasan' => $request->kepuasan,
                'catatan' => $request->catatan,
                'ringkasan' => $request->ringkasan,
                'saran' => $request->saran,
                'tindak_lanjut' => $request->tindak_lanjut,
                'status_validasi' => 'draft',
            ]
        );

        return redirect()->route('unit.evaluasi.index')->with('success', 'Form Evaluasi berhasil disimpan sebagai Draf.');
    }

    /**
     * Submit evaluasi to Pimpinan (UC24).
     */
    public function submit(string $cooperationId)
    {
        $cooperation = Cooperation::findOrFail($cooperationId);
        $evaluasi = Evaluasi::where('cooperation_id', $cooperationId)->firstOrFail();

        $evaluasi->update([
            'status_validasi' => 'menunggu_validasi',
        ]);

        $cooperation->update([
            'status_dokumen' => 'Menunggu Evaluasi',
        ]);

        // Send Notification to Pimpinan
        $pimpinanUsers = User::whereHas('role', function ($q) {
            $q->where('role_name', 'pimpinan');
        })->get();

        foreach ($pimpinanUsers as $pimpinan) {
            Notifikasi::send(
                $pimpinan->id,
                Auth::id(),
                $cooperation->id,
                'evaluasi',
                'Pengajuan Evaluasi Dokumen Kerja Sama',
                "Unit " . Auth::user()->name . " telah mensubmit form evaluasi untuk kerjasama: '{$cooperation->title}'.",
                route('pimpinan.evaluasi.show', $cooperation->id)
            );
        }

        return redirect()->route('unit.evaluasi.index')->with('success', 'Evaluasi berhasil disubmit ke Pimpinan.');
    }
}

<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UmpanBalikController extends Controller
{
    /**
     * Menampilkan daftar dokumen kerjasama mitra dan evaluasi kepuasan (UC26 / DFD 6.6 / Flowchart 7.4).
     */
    public function index()
    {
        $user = Auth::user();
        $mitra = $user->mitra ?: ($user->mitra_id ? Mitra::find($user->mitra_id) : Mitra::first());
        $mitraId = $mitra?->id;
        $mitraName = $mitra ? $mitra->nama_mitra : ($user->name ?? 'Mitra Eksternal');

        $query = Cooperation::with(['evaluasis.penilai', 'mitra', 'kegiatanKerjasamas', 'prodis', 'jurusans']);

        if ($mitraId) {
            $query->where('mitra_id', $mitraId);
        }

        $cooperations = $query->orderBy('created_at', 'desc')->get();

        // ─── Metrik KPI Ringkasan Umpan Balik (CSAT) ───
        $totalCooperations = $cooperations->count();

        $filledCooperations = $cooperations->filter(function ($c) {
            return $c->evaluasis->where('tipe_evaluasi', 'Umpan_Balik_Mitra')->count() > 0;
        })->count();

        $pendingCooperations = max(0, $totalCooperations - $filledCooperations);

        // Menghitung rata-rata skor kepuasan CSAT (Skala 1 - 5)
        $mitraEvaluations = $cooperations->flatMap(function ($c) {
            return $c->evaluasis->where('tipe_evaluasi', 'Umpan_Balik_Mitra');
        });

        $allScores = $mitraEvaluations->pluck('score')->filter();
        $avgScore = $allScores->count() > 0 ? round($allScores->avg(), 1) : 0;
        $csatPercent = $avgScore > 0 ? round(($avgScore / 5) * 100) : 0;

        // Opsi filter
        $availableJenis = $cooperations->pluck('jenis')->filter()->unique()->values();
        $availableYears = $cooperations->map(function ($c) {
            if ($c->start_date) {
                return $c->start_date->format('Y');
            }
            return $c->created_at ? $c->created_at->format('Y') : null;
        })->filter()->unique()->sortDesc()->values();

        return view('auth.mitra', compact(
            'cooperations',
            'totalCooperations',
            'filledCooperations',
            'pendingCooperations',
            'avgScore',
            'csatPercent',
            'availableJenis',
            'availableYears',
            'mitraName',
            'mitra'
        ))->with('view', 'umpan_balik');
    }

    /**
     * Menyimpan atau memperbarui umpan balik / kuesioner kepuasan mitra (UC26).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cooperation_id' => 'required|exists:cooperations,id',
            'kepuasan' => 'required|numeric|min:1|max:5',
            'sesuai_rencana' => 'nullable|numeric|min:1|max:5',
            'kualitas' => 'nullable|numeric|min:1|max:5',
            'keterlibatan' => 'nullable|numeric|min:1|max:5',
            'efisiensi' => 'nullable|numeric|min:1|max:5',
            'ringkasan' => 'nullable|string|max:2000',
            'kendala' => 'nullable|string|max:2000',
            'rekomendasi' => 'nullable|string|max:2000',
            'kesimpulan' => 'nullable|in:Sangat Baik,Baik,Cukup,Perlu Perbaikan',
            'tindak_lanjut' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Hitung skor rata-rata komposit dari 4 aspek + overall kepuasan
        $aspects = array_filter([
            $validated['sesuai_rencana'] ?? null,
            $validated['kualitas'] ?? null,
            $validated['keterlibatan'] ?? null,
            $validated['efisiensi'] ?? null,
            $validated['kepuasan'] ?? null,
        ]);

        $finalScore = count($aspects) > 0 ? round(array_sum($aspects) / count($aspects), 2) : $validated['kepuasan'];

        // Tentukan kesimpulan otomatis jika tidak dipilih
        $kesimpulan = $validated['kesimpulan'] ?? match (true) {
            $finalScore >= 4.5 => 'Sangat Baik',
            $finalScore >= 3.5 => 'Baik',
            $finalScore >= 2.5 => 'Cukup',
            default => 'Perlu Perbaikan',
        };

        Evaluasi::updateOrCreate(
            [
                'cooperation_id' => $validated['cooperation_id'],
                'tipe_evaluasi' => 'Umpan_Balik_Mitra',
            ],
            [
                'evaluator_id' => $user->id,
                'score' => $finalScore,
                'sesuai_rencana' => $validated['sesuai_rencana'] ?? $validated['kepuasan'],
                'kualitas' => $validated['kualitas'] ?? $validated['kepuasan'],
                'keterlibatan' => $validated['keterlibatan'] ?? $validated['kepuasan'],
                'efisiensi' => $validated['efisiensi'] ?? $validated['kepuasan'],
                'kepuasan' => $validated['kepuasan'],
                'ringkasan' => $validated['ringkasan'] ?? null,
                'kendala' => $validated['kendala'] ?? null,
                'rekomendasi' => $validated['rekomendasi'] ?? null,
                'kesimpulan' => $kesimpulan,
                'tindak_lanjut' => $validated['tindak_lanjut'] ?? 'Bersedia Melanjutkan Kerjasama',
                'status_validasi' => 'Divalidasi',
            ]
        );

        return redirect()->route('mitra.umpan_balik.index')->with('success', 'Umpan balik kemitraan dan survei kepuasan berhasil disimpan. Terima kasih atas kontribusi Anda!');
    }

    /**
     * Memperbarui data umpan balik berdasarkan ID Evaluasi (UC26).
     */
    public function update(Request $request, $id)
    {
        $evaluasi = Evaluasi::findOrFail($id);

        $validated = $request->validate([
            'kepuasan' => 'required|numeric|min:1|max:5',
            'sesuai_rencana' => 'nullable|numeric|min:1|max:5',
            'kualitas' => 'nullable|numeric|min:1|max:5',
            'keterlibatan' => 'nullable|numeric|min:1|max:5',
            'efisiensi' => 'nullable|numeric|min:1|max:5',
            'ringkasan' => 'nullable|string|max:2000',
            'kendala' => 'nullable|string|max:2000',
            'rekomendasi' => 'nullable|string|max:2000',
            'kesimpulan' => 'nullable|in:Sangat Baik,Baik,Cukup,Perlu Perbaikan',
            'tindak_lanjut' => 'nullable|string|max:255',
        ]);

        $aspects = array_filter([
            $validated['sesuai_rencana'] ?? null,
            $validated['kualitas'] ?? null,
            $validated['keterlibatan'] ?? null,
            $validated['efisiensi'] ?? null,
            $validated['kepuasan'] ?? null,
        ]);

        $finalScore = count($aspects) > 0 ? round(array_sum($aspects) / count($aspects), 2) : $validated['kepuasan'];

        $kesimpulan = $validated['kesimpulan'] ?? match (true) {
            $finalScore >= 4.5 => 'Sangat Baik',
            $finalScore >= 3.5 => 'Baik',
            $finalScore >= 2.5 => 'Cukup',
            default => 'Perlu Perbaikan',
        };

        $evaluasi->update([
            'score' => $finalScore,
            'sesuai_rencana' => $validated['sesuai_rencana'] ?? $validated['kepuasan'],
            'kualitas' => $validated['kualitas'] ?? $validated['kepuasan'],
            'keterlibatan' => $validated['keterlibatan'] ?? $validated['kepuasan'],
            'efisiensi' => $validated['efisiensi'] ?? $validated['kepuasan'],
            'kepuasan' => $validated['kepuasan'],
            'ringkasan' => $validated['ringkasan'] ?? null,
            'kendala' => $validated['kendala'] ?? null,
            'rekomendasi' => $validated['rekomendasi'] ?? null,
            'kesimpulan' => $kesimpulan,
            'tindak_lanjut' => $validated['tindak_lanjut'] ?? 'Bersedia Melanjutkan Kerjasama',
        ]);

        return redirect()->route('mitra.umpan_balik.index')->with('success', 'Umpan balik kemitraan berhasil diperbarui.');
    }
}

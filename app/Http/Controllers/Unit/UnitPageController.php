<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\JenisKerjasama;
use App\Models\Klasifikasi;
use Illuminate\Http\Request;

class UnitPageController extends Controller
{
    /**
     * Resolve the unit_kerja_id for the currently logged-in user.
     */
    private function resolveUnitId()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile || !$profile->unit_kerja_id) {
            abort(403, 'Profil unit kerja tidak ditemukan.');
        }

        return $profile->unit_kerja_id;
    }

    /**
     * Helper: scope query to kegiatan belonging to this unit.
     */
    private function scopeUnit($query, $unitId)
    {
        // Temporarily disabled unit scoping as cooperations table lacks unit relation
        return $query;
    }

    // ─── Data Kerjasama ──────────────────────────────────────────
    public function dkerjasama()
    {
        $unitId = $this->resolveUnitId();

        $kerjasamaUnit = Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.unit', compact('kerjasamaUnit'));
    }

    // ─── Mitra Unit ──────────────────────────────────────────────
    public function mitra()
    {
        $unitId = $this->resolveUnitId();

        // Ambil semua mitra dengan hitungan kerjasama
        $mitras = \App\Models\Mitra::with('klasifikasi')
            ->withCount('cooperations')
            ->orderBy('nama_mitra', 'asc')->get();

        return view('auth.unit', compact('mitras'));
    }

    public function mitraCreate()
    {
        $klasifikasi = Klasifikasi::orderBy('nama', 'asc')->get();
        return view('auth.unit', compact('klasifikasi'));
    }

    public function mitraStore(Request $request)
    {
        $request->validate([
            'nama_mitra'   => 'required|string|max:255',
            'id_klasifikasi' => 'nullable|exists:klasifikasi,id',
            'alamat'       => 'nullable|string|max:255',
            'kategori'     => 'required|string|in:nasional,internasional',
            'negara'       => 'nullable|string|max:255',
            'telp'         => 'nullable|string|max:20',
            'website'      => 'nullable|string|max:255',
        ]);

        \App\Models\Mitra::create([
            'nama_mitra'   => $request->nama_mitra,
            'id_klasifikasi' => $request->id_klasifikasi,
            'alamat'       => $request->alamat,
            'kategori'     => $request->kategori,
            'negara'       => $request->negara ?? 'Indonesia',
            'telp'         => $request->telp,
            'website'      => $request->website,
        ]);

        return redirect()->route('unit.mitra')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function mitraShow($id)
    {
        $mitra = \App\Models\Mitra::with(['klasifikasi', 'cooperations'])->findOrFail($id);

        return view('auth.unit', compact('mitra'));
    }

    public function mitraEdit($id)
    {
        $mitra = \App\Models\Mitra::with('klasifikasi')->findOrFail($id);
        $klasifikasi = Klasifikasi::orderBy('nama', 'asc')->get();
        return view('auth.unit', compact('mitra', 'klasifikasi'));
    }

    public function mitraUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_mitra'   => 'required|string|max:255',
            'id_klasifikasi' => 'nullable|exists:klasifikasi,id',
            'alamat'       => 'nullable|string|max:255',
            'kategori'     => 'required|string|in:nasional,internasional',
            'negara'       => 'nullable|string|max:255',
            'telp'         => 'nullable|string|max:20',
            'website'      => 'nullable|string|max:255',
        ]);

        $mitra = \App\Models\Mitra::findOrFail($id);
        $mitra->update([
            'nama_mitra'   => $request->nama_mitra,
            'id_klasifikasi' => $request->id_klasifikasi,
            'alamat'       => $request->alamat,
            'kategori'     => $request->kategori,
            'negara'       => $request->negara ?? 'Indonesia',
            'telp'         => $request->telp,
            'website'      => $request->website,
        ]);

        return redirect()->route('unit.mitra')->with('success', 'Data mitra berhasil diperbarui.');
    }

    public function mitraDestroy($id)
    {
        $mitra = \App\Models\Mitra::findOrFail($id);
        
        // Cek apakah mitra memiliki riwayat kerjasama
        if ($mitra->cooperations()->exists()) {
            return back()->with('error', 'Mitra tidak bisa dihapus karena masih memiliki riwayat kerjasama.');
        }

        $mitra->delete();
        return redirect()->route('unit.mitra')->with('success', 'Mitra berhasil dihapus.');
    }

    // ─── Evaluasi Kinerja ────────────────────────────────────────
    public function evaluasi()
    {
        $unitId = $this->resolveUnitId();

        // Stubbing as evaluasi depends on kegiatan_kerjasamas
        $evaluasiList = collect();
        $belumEvaluasi = collect();

        return view('auth.unit', compact('evaluasiList', 'belumEvaluasi'));
    }

    // ─── Form Evaluasi (GET) ────────────────────────────────────
    public function formEvaluasi($id)
    {
        $unitId = $this->resolveUnitId();

        $kegiatan = Cooperation::findOrFail($id);

        $existingEval = null; // Stubbing evaluation as relation is broken

        return view('auth.unit', compact('kegiatan', 'existingEval'));
    }

    // ─── Store Evaluasi (POST) ──────────────────────────────────
    public function storeEvaluasi(Request $request, $id)
    {
        $request->validate([
            'sesuai_rencana' => 'required|integer|min:1|max:5',
            'kualitas'       => 'required|integer|min:1|max:5',
            'keterlibatan'   => 'required|integer|min:1|max:5',
            'efisiensi'      => 'required|integer|min:1|max:5',
            'kepuasan'       => 'required|integer|min:1|max:5',
            'catatan'        => 'nullable|string|max:2000',
        ]);

        $unitId = $this->resolveUnitId();
        $kegiatan = Cooperation::findOrFail($id);

        Evaluasi::create([
            'id_kegiatan'   => $kegiatan->id,
            'dinilai_oleh'  => Auth::id(),
            'sesuai_rencana' => $request->sesuai_rencana,
            'kualitas'       => $request->kualitas,
            'keterlibatan'   => $request->keterlibatan,
            'efisiensi'      => $request->efisiensi,
            'kepuasan'       => $request->kepuasan,
            'catatan'        => $request->catatan,
        ]);

        // Update status kegiatan menjadi menunggu validasi pimpinan
        $kegiatan->update(['status' => 'menunggu_validasi']);

        // ─── KIRIM NOTIFIKASI KE PIMPINAN ───────────────────────
        $pimpinans = \App\Models\User::whereHas('role', function($q) {
            $q->where('role_name', 'pimpinan');
        })->get();

        $namaUnit = Auth::user()->profile->unitKerja->nama_unit_pelaksana;
        
        foreach ($pimpinans as $pimpinan) {
            \App\Models\Notifikasi::send(
                $pimpinan->id,
                Auth::id(),
                $kegiatan->id,
                'validasi',
                'Dokumen Menunggu Validasi',
                "$namaUnit mengirimkan evaluasi $kegiatan->title untuk divalidasi.",
                route('pimpinan.evaluasi')
            );
        }

        return redirect()->route('unit.evaluasi')->with('success', 'Evaluasi berhasil dikirim ke Pimpinan untuk divalidasi.');
    }

    // ─── Update Evaluasi (PUT) ──────────────────────────────────
    public function updateEvaluasi(Request $request, $id)
    {
        $request->validate([
            'sesuai_rencana' => 'required|integer|min:1|max:5',
            'kualitas'       => 'required|integer|min:1|max:5',
            'keterlibatan'   => 'required|integer|min:1|max:5',
            'efisiensi'      => 'required|integer|min:1|max:5',
            'kepuasan'       => 'required|integer|min:1|max:5',
            'catatan'        => 'nullable|string|max:2000',
        ]);

        $unitId = $this->resolveUnitId();
        $kegiatan = Cooperation::findOrFail($id);

        $eval = Evaluasi::where('id_kegiatan', $kegiatan->id)
            ->where('dinilai_oleh', Auth::id())
            ->firstOrFail();

        $eval->update([
            'sesuai_rencana' => $request->sesuai_rencana,
            'kualitas'       => $request->kualitas,
            'keterlibatan'   => $request->keterlibatan,
            'efisiensi'      => $request->efisiensi,
            'kepuasan'       => $request->kepuasan,
            'catatan'        => $request->catatan,
        ]);

        // Update status kegiatan menjadi menunggu validasi pimpinan
        $kegiatan->update(['status' => 'menunggu_validasi']);

        // ─── KIRIM NOTIFIKASI KE PIMPINAN ───────────────────────
        $pimpinans = \App\Models\User::whereHas('role', function($q) {
            $q->where('role_name', 'pimpinan');
        })->get();

        $namaUnit = Auth::user()->profile->unitKerja->nama_unit_pelaksana;
        
        foreach ($pimpinans as $pimpinan) {
            \App\Models\Notifikasi::send(
                $pimpinan->id,
                Auth::id(),
                $kegiatan->id,
                'validasi',
                'Dokumen Menunggu Validasi',
                "$namaUnit mengirimkan evaluasi $kegiatan->title untuk divalidasi.",
                route('pimpinan.dashboard')
            );
        }

        return redirect()->route('unit.evaluasi')->with('success', 'Evaluasi berhasil diperbarui dan dikirim ke Pimpinan.');
    }

    // ─── Submit Evaluasi to Pimpinan (POST) ─────────────────────
    public function submitEvaluasiToPimpinan($id)
    {
        $unitId = $this->resolveUnitId();
        $kegiatan = Cooperation::findOrFail($id);

        // Pastikan sudah ada evaluasi
        $hasEval = Evaluasi::where('id_kegiatan', $kegiatan->id)
            ->where('dinilai_oleh', Auth::id())
            ->exists();

        if (!$hasEval) {
            return back()->with('error', 'Tidak bisa mengirim ke Pimpinan. Silakan isi evaluasi terlebih dahulu.');
        }

        $kegiatan->update(['status' => 'menunggu_validasi']);

        // ─── KIRIM NOTIFIKASI KE PIMPINAN ───────────────────────
        $pimpinans = \App\Models\User::whereHas('role', function($q) {
            $q->where('role_name', 'pimpinan');
        })->get();

        $namaUnit = Auth::user()->profile->unitKerja->nama_unit_pelaksana;
        
        foreach ($pimpinans as $pimpinan) {
            \App\Models\Notifikasi::send(
                $pimpinan->id,
                Auth::id(),
                $kegiatan->id,
                'validasi',
                'Dokumen Menunggu Validasi',
                "$namaUnit mengirimkan evaluasi $kegiatan->title untuk divalidasi.",
                route('pimpinan.dashboard')
            );
        }

        return redirect()->route('unit.evaluasi')->with('success', 'Evaluasi berhasil dikirim ke Pimpinan untuk divalidasi.');
    }

    // ─── Laporan Data ────────────────────────────────────────────
    public function laporan()
    {
        $unitId = $this->resolveUnitId();

        $jenisKerjasama = JenisKerjasama::orderBy('nama_kerjasama')->get();

        $kerjasamaUnit = Cooperation::orderBy('created_at', 'desc')
            ->get();

        return view('auth.unit', compact('jenisKerjasama', 'kerjasamaUnit'));
    }

    public function laporanPreview(Request $request)
    {
        $data = $this->buildLaporanQuery($request)
            ->get();

        return response()->json($data);
    }

    public function laporanPdf(Request $request)
    {
        $data = $this->buildLaporanQuery($request)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('auth.layout.unit.laporan_pdf', compact('data'));
        return $pdf->download('laporan_kerjasama_unit.pdf');
    }

    public function laporanExcel(Request $request)
    {
        $data = $this->buildLaporanQuery($request)
            ->get();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LaporanKerjasamaExport($data, 'auth.layout.unit.laporan_excel'), 'laporan_kerjasama_unit.xlsx');
    }

    private function buildLaporanQuery(Request $request)
    {
        $unitId = $this->resolveUnitId();
        $query = Cooperation::query();

        if ($request->filled('tanggal_awal')) {
            $query->where('periode_mulai', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('periode_mulai', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('id_jenis') && $request->id_jenis != 'all') {
            $query->where('jenis', 'like', '%' . $request->id_jenis . '%');
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        return $query;
    }


    // ─── Statistik Data ──────────────────────────────────────────
    public function statistik()
    {
        $unitId = $this->resolveUnitId();

        $totalKerjasama = Cooperation::count();

        // Status breakdown
        $statusBreakdown = Cooperation::select('jenis', DB::raw('count(*) as total'))
            ->groupBy('jenis')
            ->pluck('total', 'jenis');

        // Tren per tahun
        $trenPerTahun = Cooperation::select(DB::raw('YEAR(created_at) as tahun'), DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // Sebaran jenis kerjasama
        $sebaranJenis = Cooperation::select('jenis', DB::raw('count(*) as total'))
            ->groupBy('jenis')
            ->get();

        // Rata-rata evaluasi
        $avgEvaluasi = (object)[
            'avg_kualitas' => 0,
            'avg_keterlibatan' => 0,
            'avg_efisiensi' => 0,
            'avg_kepuasan' => 0
        ];


        return view('auth.unit', compact(
            'totalKerjasama',
            'statusBreakdown',
            'trenPerTahun',
            'sebaranJenis',
            'avgEvaluasi'
        ));
    }

    // ─── Form Laporan (PDF Upload) ──────────────────────────────
    public function formLaporan()
    {
        $unitId = $this->resolveUnitId();

        $laporanFiles = \App\Models\LaporanFile::where('unit_kerja_id', $unitId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.unit', compact('laporanFiles'));
    }

    public function formLaporanStore(Request $request)
    {
        $request->validate([
            'file_pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        $unitId = $this->resolveUnitId();

        $file = $request->file('file_pdf');
        $path = $file->store('laporan_unit', 'public');

        \App\Models\LaporanFile::create([
            'unit_kerja_id' => $unitId,
            'uploaded_by'   => Auth::id(),
            'judul'         => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
        ]);

        return redirect()->route('unit.form')->with('success', 'Laporan berhasil diupload.');
    }

    public function formLaporanDestroy($id)
    {
        $unitId = $this->resolveUnitId();

        $file = \App\Models\LaporanFile::where('unit_kerja_id', $unitId)->findOrFail($id);

        // Delete the physical file
        \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);

        $file->delete();

        return redirect()->route('unit.form')->with('success', 'Laporan berhasil dihapus.');
    }
}

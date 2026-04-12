<?php

namespace App\Http\Controllers\Jurusan;

use App\Http\Controllers\Controller;
use App\Models\KegiatanKerjasama;
use App\Models\JenisKerjasama;
use App\Models\Profile;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanJurusanController extends Controller
{
    private function getJurusanId()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        if (!$profile || !$profile->jurusan_id) {
            abort(403, 'Profil jurusan tidak ditemukan.');
        }
        return $profile->jurusan_id;
    }

    public function index()
    {
        $id_jurusan = $this->getJurusanId();
        $jenisKerjasama = JenisKerjasama::all();
        
        $notifikasiTerbaru = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('auth.jurusan', compact('notifikasiTerbaru', 'jenisKerjasama'));
    }

    public function preview(Request $request)
    {
        $query = $this->buildQuery($request);
        $data = $query->with(['jenisKerjasama', 'mitras', 'evaluasis'])->get();

        return response()->json($data);
    }

    private function buildQuery(Request $request)
    {
        $id_jurusan = $this->getJurusanId();
        $query = KegiatanKerjasama::whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan));

        if ($request->filled('tanggal_awal')) {
            $query->where('periode_mulai', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->where('periode_mulai', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('id_jenis') && $request->id_jenis != 'all') {
            $query->whereHas('jenisKerjasama', fn($q) => $q->where('jenis_kerjasamas.id', $request->id_jenis));
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildQuery($request);
        $data = $query->with([
            'jenisKerjasama',
            'mitras',
            'tujuans',
            'pelaksanaans',
            'hasils',
            'evaluasis'
        ])->get();

        return Excel::download(new \App\Exports\LaporanKerjasamaExport($data), 'laporan_kerjasama_jurusan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildQuery($request);
        $data = $query->with([
            'jenisKerjasama',
            'mitras',
            'tujuans',
            'pelaksanaans',
            'hasils',
            'evaluasis'
        ])->get();

        $pdf = Pdf::loadView('auth.layout.jurusan.laporan_pdf', compact('data'));
        return $pdf->download('laporan_kerjasama_jurusan.pdf');
    }
}

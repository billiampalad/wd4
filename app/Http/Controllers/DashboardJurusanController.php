<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cooperation;
use App\Models\Notifikasi;
use App\Models\Profile;
use App\Support\CooperationAccess;
use Illuminate\Support\Facades\DB;

class DashboardJurusanController extends Controller
{
    private function resolveJurusanId(): int
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile || !$profile->jurusan_id) {
            abort(403, 'Profil jurusan tidak ditemukan.');
        }

        return (int) $profile->jurusan_id;
    }

    public function index()
    {
        // 1. Dapatkan user yang sedang login beserta data profilenya (untuk tahu dia jurusan apa)
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        
        // Cek jika profile ada, jika tidak berikan nilai default atau tangani error
        if (!$profile || !$profile->jurusan_id) {
            return redirect()->route('login')->with('error', 'Profil jurusan tidak ditemukan.');
        }

        $id_jurusan = $profile->jurusan_id;

        $scopeJurusan = fn($query) => CooperationAccess::scopeForProfile($query, $profile);

        // 2. Hitung Total Kerjasama (Hanya milik jurusannya)
        $baseQuery = $scopeJurusan(Cooperation::query());
        $totalKerjasama = (clone $baseQuery)->count();

        // 3. Hitung yang Belum Dievaluasi (misal statusnya masih draft atau menunggu_evaluasi)
        $draftCount = (clone $baseQuery)
                            ->where('status_dokumen', 'Draft')
                            ->count();

        $menungguEvaluasi = (clone $baseQuery)
                            ->where('status_dokumen', 'Menunggu Evaluasi')
                            ->count();

        // Tetap sediakan agregat lama (untuk backward compatibility)
        $belumDievaluasi = $draftCount + $menungguEvaluasi;

        // 4. Hitung yang Sudah Dievaluasi (misal statusnya selesai atau revisi)
        $sudahDievaluasi = (clone $baseQuery)
                            ->whereIn('status_dokumen', ['Disahkan', 'Revisi'])
                            ->count();

        // 5. Ambil 5 Notifikasi Terbaru khusus untuk user ini
        $notifikasiTerbaru = Notifikasi::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        // 6. (Opsional) Ambil 5 data kerjasama terbaru untuk ditampilkan di tabel summary
        $kerjasamaTerbaru = (clone $baseQuery)
                            ->with(['mitra', 'pjInternal', 'pksNumbers'])
                            ->orderBy('created_at', 'asc')
                            ->get()
                            ->take(-5);

        // 7. Statistik Tambahan
        $mitraStats = DB::table('mitras')
            ->join('cooperations', 'mitras.id', '=', 'cooperations.mitra_id')
            ->leftJoin('kerjasama_jurusan', 'cooperations.id', '=', 'kerjasama_jurusan.cooperation_id')
            ->where(function ($query) use ($id_jurusan) {
                $query->where('cooperations.jurusan_id', $id_jurusan)
                    ->orWhere('kerjasama_jurusan.jurusan_id', $id_jurusan);
            })
            ->select('mitras.kategori', DB::raw('count(DISTINCT mitras.id) as total'))
            ->groupBy('mitras.kategori')
            ->pluck('total', 'kategori');

        // Tren Kerjasama per Tahun
        $trenKerjasama = (clone $baseQuery)
            ->select(DB::raw('YEAR(created_at) as tahun'), DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        $sebaranJenis = DB::table('detail_kegiatans')
            ->join('jenis_kerjasamas', 'detail_kegiatans.jenis_kerjasama_id', '=', 'jenis_kerjasamas.id')
            ->join('cooperations', 'detail_kegiatans.cooperation_id', '=', 'cooperations.id')
            ->leftJoin('kerjasama_jurusan', 'cooperations.id', '=', 'kerjasama_jurusan.cooperation_id')
            ->where(function ($query) use ($id_jurusan) {
                $query->where('cooperations.jurusan_id', $id_jurusan)
                    ->orWhere('kerjasama_jurusan.jurusan_id', $id_jurusan);
            })
            ->select('jenis_kerjasamas.nama_kerjasama', DB::raw('count(*) as total'))
            ->groupBy('jenis_kerjasamas.nama_kerjasama')
            ->get();

        $today = now()->startOfDay();
        $upcomingDeadlines = (clone $baseQuery)
            ->with(['mitra', 'pjInternal', 'pksNumbers'])
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$today, now()->addDays(30)->endOfDay()])
            ->orderBy('end_date')
            ->get();

        $kerjasamaTable = (clone $baseQuery)
            ->with(['mitra', 'pjInternal', 'pksNumbers'])
            ->latest()
            ->take(10)
            ->get();

        $jenisCounts = collect([
            'Semua' => (clone $baseQuery)->count(),
            'MoU' => (clone $baseQuery)->where('jenis', 'like', '%MoU%')->count(),
            'MoA' => (clone $baseQuery)->where('jenis', 'like', '%MoA%')->count(),
            'IA' => (clone $baseQuery)->where('jenis', 'like', '%IA%')->count(),
        ]);

        // Lempar data ke view dashboard jurusan
        return view('auth.jurusan', compact(
            'totalKerjasama',
            'draftCount',
            'menungguEvaluasi',
            'belumDievaluasi',
            'sudahDievaluasi',
            'notifikasiTerbaru',
            'kerjasamaTerbaru',
            'mitraStats',
            'trenKerjasama',
            'sebaranJenis',
            'upcomingDeadlines',
            'kerjasamaTable',
            'jenisCounts'
        ));
    }

    public function hasilEvaluasi()
    {
        $id_jurusan = $this->resolveJurusanId();

        $evaluasiList = KegiatanKerjasama::query()
            ->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan))
            ->whereHas('evaluasis')
            ->with(['evaluasis', 'mitras', 'jenisKerjasama'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('auth.jurusan', compact('evaluasiList'));
    }

    public function formEvaluasi($id)
    {
        $id_jurusan = $this->resolveJurusanId();

        $kegiatan = KegiatanKerjasama::query()
            ->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan))
            ->with([
                'mitras',
                'jenisKerjasama',
                'jurusans',
                'unitKerjas',
                'creator',
                'tujuans',
                'pelaksanaans',
                'hasils',
                'dokumentasis',
                'permasalahanSolusis',
                'evaluasis',
            ])
            ->findOrFail($id);

        $existingEval = $kegiatan->evaluasis->first();
        $readonly = true;

        return view('auth.jurusan', compact('kegiatan', 'existingEval', 'readonly'));
    }
}

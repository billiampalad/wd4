<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KegiatanKerjasama;
use App\Models\Notifikasi;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class DashboardJurusanController extends Controller
{
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

        // Helper closure for scoping to jurusan via pivot
        $scopeJurusan = fn($query) => $query->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan));

        // 2. Hitung Total Kerjasama (Hanya milik jurusannya)
        $totalKerjasama = $scopeJurusan(KegiatanKerjasama::query())->count();

        // 3. Hitung yang Belum Dievaluasi (misal statusnya masih draft atau menunggu_evaluasi)
        $draftCount = $scopeJurusan(KegiatanKerjasama::query())
                            ->where('status', 'draft')
                            ->count();

        $menungguEvaluasi = $scopeJurusan(KegiatanKerjasama::query())
                            ->where('status', 'menunggu_evaluasi')
                            ->count();

        // Tetap sediakan agregat lama (untuk backward compatibility)
        $belumDievaluasi = $draftCount + $menungguEvaluasi;

        // 4. Hitung yang Sudah Dievaluasi (misal statusnya selesai atau revisi)
        $sudahDievaluasi = $scopeJurusan(KegiatanKerjasama::query())
                            ->whereIn('status', ['selesai', 'revisi'])
                            ->count();

        // 5. Ambil 5 Notifikasi Terbaru khusus untuk user ini
        $notifikasiTerbaru = Notifikasi::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        // 6. (Opsional) Ambil 5 data kerjasama terbaru untuk ditampilkan di tabel summary
        $kerjasamaTerbaru = $scopeJurusan(KegiatanKerjasama::query())
                            ->orderBy('created_at', 'asc')
                            ->get()
                            ->take(-5);

        // 7. Statistik Tambahan
        // Mitra Stats: Nasional vs Internasional (via pivot kegiatan_jurusans)
        $mitraStats = DB::table('mitras')
            ->join('kegiatan_mitras', 'mitras.id', '=', 'kegiatan_mitras.id_mitra')
            ->join('kegiatan_jurusans', 'kegiatan_mitras.id_kegiatan', '=', 'kegiatan_jurusans.id_kegiatan')
            ->where('kegiatan_jurusans.id_jurusan', $id_jurusan)
            ->select('mitras.kategori', DB::raw('count(DISTINCT mitras.id) as total'))
            ->groupBy('mitras.kategori')
            ->pluck('total', 'kategori');

        // Tren Kerjasama per Tahun
        $trenKerjasama = $scopeJurusan(KegiatanKerjasama::query())
            ->select(DB::raw('YEAR(created_at) as tahun'), DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        // Sebaran Jenis Kerjasama (via pivot kegiatan_jenis_kerjasamas)
        $sebaranJenis = DB::table('kegiatan_jenis_kerjasamas')
            ->join('jenis_kerjasamas', 'kegiatan_jenis_kerjasamas.id_jenis', '=', 'jenis_kerjasamas.id')
            ->join('kegiatan_jurusans', 'kegiatan_jenis_kerjasamas.id_kegiatan', '=', 'kegiatan_jurusans.id_kegiatan')
            ->where('kegiatan_jurusans.id_jurusan', $id_jurusan)
            ->select('jenis_kerjasamas.nama_kerjasama', DB::raw('count(*) as total'))
            ->groupBy('jenis_kerjasamas.nama_kerjasama')
            ->get();

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
            'sebaranJenis'
        ));
    }
}

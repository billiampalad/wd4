<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Models\KegiatanKerjasama;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function pimpinan()
    {
        $tahunIni = now()->year;

        // ── 1. WIDGET KARTU STATISTIK ─────────────────────────────

        // Total Kerja Sama Tahun Ini (status layak/selesai atau semua valid)
        $totalKerjasamaTahunIni = KegiatanKerjasama::whereYear('created_at', $tahunIni)->count();

        // Menunggu Evaluasi Pimpinan (dari Jurusan)
        $menungguEvaluasi = KegiatanKerjasama::where('status', 'menunggu_evaluasi')->count();

        // Menunggu Validasi Akhir (dari Unit Kerja)
        $menungguValidasi = KegiatanKerjasama::where('status', 'menunggu_validasi')->count();

        // Kerjasama Internasional vs Nasional
        $internasional = KegiatanKerjasama::whereYear('created_at', $tahunIni)
            ->whereHas('mitras', fn($q) => $q->where('kategori', 'internasional'))
            ->count();
        $nasional = KegiatanKerjasama::whereYear('created_at', $tahunIni)
            ->whereHas('mitras', fn($q) => $q->where('kategori', 'nasional'))
            ->count();

        // ── 2. VISUALISASI GRAFIK ─────────────────────────────────

        // A. Tren Kerja Sama Per Bulan (tahun berjalan)
        $trenPerBulan = KegiatanKerjasama::whereYear('created_at', $tahunIni)
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // B. Distribusi Sebaran Jenis Kerjasama (Donut)
        $sebaranJenis = DB::table('kegiatan_jenis_kerjasamas')
            ->join('jenis_kerjasamas', 'kegiatan_jenis_kerjasamas.id_jenis', '=', 'jenis_kerjasamas.id')
            ->select('jenis_kerjasamas.nama_kerjasama', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_kerjasamas.nama_kerjasama')
            ->get();

        // C. Kinerja Jurusan (Horizontal Bar)
        $kinerjaJurusan = DB::table('kegiatan_jurusans')
            ->join('jurusans', 'kegiatan_jurusans.id_jurusan', '=', 'jurusans.id')
            ->select('jurusans.nama_jurusan', DB::raw('COUNT(*) as total'))
            ->groupBy('jurusans.nama_jurusan')
            ->orderByDesc('total')
            ->get();

        // C2. Kinerja Unit Kerja (Horizontal Bar)
        $kinerjaUnit = DB::table('kegiatan_units')
            ->join('unit_kerjas', 'kegiatan_units.id_unit', '=', 'unit_kerjas.id')
            ->select('unit_kerjas.nama_unit_pelaksana', DB::raw('COUNT(*) as total'))
            ->groupBy('unit_kerjas.nama_unit_pelaksana')
            ->orderByDesc('total')
            ->get();

        // ── 3. TABEL AKSI CEPAT ──────────────────────────────────

        $dokumenMenunggu = KegiatanKerjasama::with(['jurusans', 'unitKerjas'])
            ->whereIn('status', ['menunggu_evaluasi', 'menunggu_validasi'])
            ->latest()
            ->take(10)
            ->get();

        return view('auth.pimpinan', compact(
            'totalKerjasamaTahunIni',
            'menungguEvaluasi',
            'menungguValidasi',
            'internasional',
            'nasional',
            'trenPerBulan',
            'sebaranJenis',
            'kinerjaJurusan',
            'kinerjaUnit',
            'dokumenMenunggu'
        ));
    }

    public function pimpinanMonitoring()
    {
        $dataKerjasama = KegiatanKerjasama::with(['jurusans', 'unitKerjas', 'mitras', 'evaluasis', 'kesimpulans'])
            ->latest()
            ->get();

        return view('auth.pimpinan', [
            'view' => 'monitoring',
            'dataKerjasama' => $dataKerjasama
        ]);
    }

    public function pimpinanEvaluasi()
    {
        // 1. Antrean Laporan Jurusan (menunggu_evaluasi)
        $laporanJurusan = KegiatanKerjasama::where('status', 'menunggu_evaluasi')
            ->whereHas('jurusans')
            ->with(['jurusans', 'mitras'])
            ->latest()
            ->get();

        // 2. Antrean Laporan Unit Kerja (menunggu_validasi)
        $laporanUnit = KegiatanKerjasama::where('status', 'menunggu_validasi')
            ->whereHas('unitKerjas')
            ->with(['unitKerjas', 'mitras', 'evaluasis'])
            ->latest()
            ->get();

        return view('auth.pimpinan', [
            'view' => 'evaluasi',
            'laporanJurusan' => $laporanJurusan,
            'laporanUnit' => $laporanUnit
        ]);
    }

    public function unit()
    {
        $user    = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile || !$profile->unit_kerja_id) {
            return redirect()->route('login')->with('error', 'Profil unit kerja tidak ditemukan.');
        }

        $unitId = $profile->unit_kerja_id;

        // Helper closure for scoping to unit via pivot
        $scopeUnit = fn($query) => $query->whereHas('unitKerjas', fn($q) => $q->where('unit_kerjas.id', $unitId));

        // ── 1. Quick Stats ──────────────────────────────────────
        $totalKerjasama = $scopeUnit(KegiatanKerjasama::query())->count();

        // Kegiatan yang BELUM punya record evaluasi
        $menungguEvaluasi = $scopeUnit(KegiatanKerjasama::query())
            ->whereDoesntHave('evaluasis')
            ->count();

        // Kegiatan yang SUDAH dievaluasi tapi status belum selesai (proxy for menunggu validasi pimpinan)
        $menungguValidasi = $scopeUnit(KegiatanKerjasama::query())
            ->whereHas('evaluasis')
            ->where('status', '!=', 'selesai')
            ->count();

        // Kegiatan selesai / tervalidasi
        $selesai = $scopeUnit(KegiatanKerjasama::query())
            ->where('status', 'selesai')
            ->count();

        // ── 2. Tabel Action Required (5 terbaru belum dievaluasi) ─
        $tugasEvaluasi = $scopeUnit(KegiatanKerjasama::query())
            ->whereDoesntHave('evaluasis')
            ->with('mitras')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ── 3. Rata-rata Evaluasi (untuk Bar Chart) ─────────────
        $avgEvaluasi = Evaluasi::whereHas('kegiatanKerjasama', function ($q) use ($unitId) {
                $q->whereHas('unitKerjas', fn($qu) => $qu->where('unit_kerjas.id', $unitId));
            })
            ->select(
                DB::raw('ROUND(AVG(kualitas),1)     as avg_kualitas'),
                DB::raw('ROUND(AVG(keterlibatan),1)  as avg_keterlibatan'),
                DB::raw('ROUND(AVG(efisiensi),1)     as avg_efisiensi'),
                DB::raw('ROUND(AVG(kepuasan),1)      as avg_kepuasan')
            )
            ->first();

        // ── 4. Tren Kerjasama Per Tahun ──────────────────────────
        $trenPerTahun = $scopeUnit(KegiatanKerjasama::query())
            ->select(DB::raw('YEAR(created_at) as tahun'), DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // ── 5. Sebaran Jenis Kerjasama ───────────────────────────
        $sebaranJenis = DB::table('kegiatan_jenis_kerjasamas')
            ->join('jenis_kerjasamas', 'kegiatan_jenis_kerjasamas.id_jenis', '=', 'jenis_kerjasamas.id')
            ->join('kegiatan_units', 'kegiatan_jenis_kerjasamas.id_kegiatan', '=', 'kegiatan_units.id_kegiatan')
            ->where('kegiatan_units.id_unit', $unitId)
            ->select('jenis_kerjasamas.nama_kerjasama', DB::raw('count(*) as total'))
            ->groupBy('jenis_kerjasamas.nama_kerjasama')
            ->get();

        return view('auth.unit', compact(
            'totalKerjasama',
            'menungguEvaluasi',
            'menungguValidasi',
            'selesai',
            'tugasEvaluasi',
            'avgEvaluasi',
            'trenPerTahun',
            'sebaranJenis'
        ));
    }

    // page halaman admin
    public function admin()
    {
        return redirect()->route('dashboard');
    }

    public function users()
    {
        return view('admin.layout.users');
    }

    public function roles()
    {
        $roles = Role::all();
        return view('admin.layout.roles', compact('roles'));
    }

    public function profiles()
    {
        $profiles = Profile::with(['user', 'jurusan', 'unitKerja'])->get();
        return view('admin.layout.profiles', compact('profiles'));
    }

    // data dashboard admin
    public function index()
    {
        $totalUsers = User::count();

        // Menggunakan whereHas untuk keamanan jika ID role berubah
        $totalPimpinan  = User::whereHas('role', fn($q) => $q->where('role_name', 'Pimpinan'))->count();
        $totalJurusan   = User::whereHas('role', fn($q) => $q->where('role_name', 'Jurusan'))->count();
        $totalUnitKerja = User::whereHas('role', fn($q) => $q->where('role_name', 'unit_kerja'))->count();
        $totalAdmin     = User::whereHas('role', fn($q) => $q->where('role_name', 'Admin'))->count();

        // Persentase untuk progress bar role
        $pimpinanPct = $totalUsers > 0 ? round($totalPimpinan / $totalUsers * 100) : 0;
        $jurusanPct  = $totalUsers > 0 ? round($totalJurusan  / $totalUsers * 100) : 0;
        $unitPct     = $totalUsers > 0 ? round($totalUnitKerja / $totalUsers * 100) : 0;
        $adminPct    = $totalUsers > 0 ? round($totalAdmin / $totalUsers * 100) : 0;

        // 5 user terbaru
        $userTerbaru = User::with(['role', 'profile'])
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'nik', 'role_id', 'created_at']);

        return view('admin.layout.dashboard', compact(
            'totalUsers',
            'totalPimpinan',
            'totalJurusan',
            'totalUnitKerja',
            'totalAdmin',
            'pimpinanPct',
            'jurusanPct',
            'unitPct',
            'adminPct',
            'userTerbaru'
        ));
    }
}

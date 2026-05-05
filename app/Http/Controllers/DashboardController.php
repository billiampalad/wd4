<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function pimpinan()
    {
        $tahunIni = now()->year;

        // ── 1. WIDGET KARTU STATISTIK ─────────────────────────────

        // Total Kerja Sama Tahun Ini
        $totalKerjasamaTahunIni = Cooperation::whereYear('created_at', $tahunIni)->count();

        // Menunggu Evaluasi Pimpinan (status_dokumen = 'Menunggu Evaluasi')
        $menungguEvaluasi = Cooperation::where('status_dokumen', 'Menunggu Evaluasi')->count();

        // Status 'Menunggu Validasi' tidak ada dalam list status terbaru (Draft, Menunggu Evaluasi, Disahkan)
        $menungguValidasi = 0;

        // Kerjasama Internasional vs Nasional (via mitra belongsTo)
        $internasional = Cooperation::whereYear('created_at', $tahunIni)
            ->whereHas('mitra', fn($q) => $q->where('kategori', 'internasional'))
            ->count();
        $nasional = Cooperation::whereYear('created_at', $tahunIni)
            ->whereHas('mitra', fn($q) => $q->where('kategori', 'nasional'))
            ->count();

        // ── 2. VISUALISASI GRAFIK ─────────────────────────────────

        // A. Tren Kerja Sama Per Bulan (tahun berjalan)
        $trenPerBulan = Cooperation::whereYear('created_at', $tahunIni)
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // B. Distribusi Sebaran Jenis Kerjasama (Donut) — from detail_kegiatans pivot
        $sebaranJenis = DB::table('detail_kegiatans')
            ->join('jenis_kerjasamas', 'detail_kegiatans.jenis_kerjasama_id', '=', 'jenis_kerjasamas.id')
            ->select('jenis_kerjasamas.nama_kerjasama', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_kerjasamas.nama_kerjasama')
            ->get();

        // C. Kinerja Jurusan (Horizontal Bar) — from kerjasama_jurusan pivot
        $kinerjaJurusan = DB::table('kerjasama_jurusan')
            ->join('jurusans', 'kerjasama_jurusan.jurusan_id', '=', 'jurusans.id')
            ->select('jurusans.nama_jurusan', DB::raw('COUNT(*) as total'))
            ->groupBy('jurusans.nama_jurusan')
            ->orderByDesc('total')
            ->get();

        // C2. Kinerja Unit Pelaksana — based on tipe_pelaksana grouping
        $kinerjaUnit = Cooperation::whereNotNull('tipe_pelaksana')
            ->select('tipe_pelaksana', DB::raw('COUNT(*) as total'))
            ->groupBy('tipe_pelaksana')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                $item->nama_unit_pelaksana = match ($item->tipe_pelaksana) {
                    'jurusan' => 'Jurusan',
                    'upa' => 'UPA',
                    'pusat' => 'Pusat',
                    default => ucfirst($item->tipe_pelaksana),
                };
                return $item;
            });

        // ── 3. TABEL AKSI CEPAT ──────────────────────────────────

        $dokumenMenunggu = Cooperation::with(['mitra', 'jurusans'])
            ->where('status_dokumen', 'Menunggu Evaluasi')
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
        $dataKerjasama = Cooperation::with(['mitra', 'jurusans', 'details'])
            ->latest()
            ->get();

        return view('auth.pimpinan', [
            'view' => 'monitoring',
            'dataKerjasama' => $dataKerjasama
        ]);
    }

    public function pimpinanMonitoringDetail($id)
    {
        $kegiatan = Cooperation::with([
            'mitra',
            'jurusans',
            'prodis',
            'details.jenisKerjasama',
            'details.sasaran',
            'penandatanganInternal',
            'pjInternal',
            'penandatanganMitra',
            'pjMitra',
            'laporanFiles',
        ])->findOrFail($id);

        return view('auth.pimpinan', [
            'view' => 'detail_monitoring',
            'kegiatan' => $kegiatan
        ]);
    }

    public function pimpinanEvaluasi()
    {
        // 1. Antrean Laporan Jurusan (Menunggu Evaluasi, tipe_pelaksana = jurusan)
        $laporanJurusan = Cooperation::where('status_dokumen', 'Menunggu Evaluasi')
            ->where('tipe_pelaksana', 'jurusan')
            ->with(['jurusans', 'mitra'])
            ->latest()
            ->get();

        // 2. Antrean Laporan Unit Kerja (Menunggu Evaluasi, tipe != jurusan)
        $laporanUnit = Cooperation::where('status_dokumen', 'Menunggu Evaluasi')
            ->where(function ($q) {
                $q->whereNull('tipe_pelaksana')
                    ->orWhere('tipe_pelaksana', '!=', 'jurusan');
            })
            ->with(['mitra'])
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

        // ── 1. Quick Stats ──────────────────────────────────────
        // Temporarily using Cooperation count instead of unit-specific count
        $totalKerjasama = \App\Models\Cooperation::count();

        // These metrics depend on relationships/columns not yet in the new schema
        $menungguEvaluasi = 0;
        $menungguValidasi = 0;
        $selesai = 0;

        // ── 2. Tabel Action Required ─
        $tugasEvaluasi = collect();

        // ── 3. Rata-rata Evaluasi ─────────────
        $avgEvaluasi = (object)[
            'avg_kualitas' => 0,
            'avg_keterlibatan' => 0,
            'avg_efisiensi' => 0,
            'avg_kepuasan' => 0
        ];

        // ── 4. Tren Kerjasama Per Tahun ──────────────────────────
        $trenPerTahun = \App\Models\Cooperation::select(DB::raw('YEAR(created_at) as tahun'), DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // ── 5. Sebaran Jenis Kerjasama ───────────────────────────
        $sebaranJenis = collect();

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
        $totalPimpinan  = User::whereHas('role', fn($q) => $q->where('role_name', 'pimpinan'))->count();
        $totalJurusan   = User::whereHas('role', fn($q) => $q->where('role_name', 'jurusan'))->count();
        $totalUnitKerja = User::whereHas('role', fn($q) => $q->where('role_name', 'unit_kerja'))->count();
        $totalAdmin     = User::whereHas('role', fn($q) => $q->where('role_name', 'admin'))->count();

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

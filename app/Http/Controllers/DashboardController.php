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
        return view('auth.pimpinan');
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

        return view('auth.unit', compact(
            'totalKerjasama',
            'menungguEvaluasi',
            'menungguValidasi',
            'selesai',
            'tugasEvaluasi',
            'avgEvaluasi'
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

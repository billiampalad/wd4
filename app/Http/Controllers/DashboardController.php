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
        $user = Auth::user();

        // ── 1. WIDGET KARTU STATISTIK ─────────────────────────────
        $totalKerjasamaAktif = Cooperation::where('status', 'aktif')->count();
        $totalMitra = \App\Models\Mitra::count();
        $totalNilaiKontrak = \App\Models\DetailKegiatan::sum('nilai_kontrak');
        
        $capaianSasaran = DB::table('detail_kegiatans')
            ->join('sasarans', 'detail_kegiatans.sasaran_id', '=', 'sasarans.id')
            ->select('sasarans.deskripsi as nama_sasaran', DB::raw('COUNT(*) as total'))
            ->groupBy('sasarans.deskripsi')
            ->get();

        // ── 2. VISUALISASI GRAFIK ─────────────────────────────────
        $distribusiJenis = Cooperation::select('jenis', DB::raw('COUNT(*) as total'))
            ->whereNotNull('jenis')
            ->groupBy('jenis')
            ->get();

        $trenTahunan = Cooperation::whereNotNull('start_date')
            ->select(DB::raw('YEAR(start_date) as tahun'), DB::raw('COUNT(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $topJurusan = DB::table('kerjasama_jurusan')
            ->join('jurusans', 'kerjasama_jurusan.jurusan_id', '=', 'jurusans.id')
            ->select('jurusans.nama_jurusan', DB::raw('COUNT(*) as total'))
            ->groupBy('jurusans.nama_jurusan')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $klasifikasiMitra = DB::table('mitras')
            ->join('klasifikasi', 'mitras.id_klasifikasi', '=', 'klasifikasi.id')
            ->select('klasifikasi.nama as klasifikasi', DB::raw('COUNT(*) as total'))
            ->groupBy('klasifikasi.nama')
            ->get();

        $internasional = \App\Models\Mitra::where('kategori', 'internasional')->count();
        $nasional = \App\Models\Mitra::where('kategori', 'nasional')->count();

        // ── 3. RINGKASAN TUGAS HARI INI ───────────────────────────
        $expiringSoon = Cooperation::with('mitra')
            ->where('status', 'aktif')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(60)])
            ->get();

        $dalamPerpanjangan = Cooperation::with('mitra')
            ->where('status', 'proses') // Asumsi proses = perpanjangan
            ->get();

        $dokumenTanpaLink = Cooperation::with('mitra')
            ->where(function($q) {
                $q->whereNull('document_link')->orWhere('document_link', '');
            })
            ->get();

        $implementasiTerbaru = \App\Models\DetailKegiatan::with(['cooperation.mitra', 'jenisKerjasama'])
            ->latest()
            ->limit(5)
            ->get();

        $realisasiLuaran = DB::table('detail_kegiatans')
            ->select('satuan_luaran', DB::raw('SUM(volume_luaran) as total_volume'))
            ->whereNotNull('satuan_luaran')
            ->where('satuan_luaran', '!=', '')
            ->groupBy('satuan_luaran')
            ->get();

        $notifikasiSistem = \App\Models\Notifikasi::where('user_id', $user->id)
            ->where('is_read', 0)
            ->latest()
            ->get();

        // Old variables for backward compatibility if needed:
        $totalKerjasamaTahunIni = Cooperation::whereYear('created_at', $tahunIni)->count();
        $menungguEvaluasi = Cooperation::where('status_dokumen', 'Menunggu Evaluasi')->count();
        $menungguValidasi = 0;
        
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
            'dokumenMenunggu',
            // New Dashboard variables
            'totalKerjasamaAktif',
            'totalMitra',
            'totalNilaiKontrak',
            'capaianSasaran',
            'distribusiJenis',
            'trenTahunan',
            'topJurusan',
            'klasifikasiMitra',
            'expiringSoon',
            'dalamPerpanjangan',
            'dokumenTanpaLink',
            'implementasiTerbaru',
            'realisasiLuaran',
            'notifikasiSistem'
        ));
    }

    public function pimpinanMonitoring()
    {
        $dataKerjasama = Cooperation::with(['mitra', 'jurusans', 'upas', 'pusats', 'details', 'pjInternal'])
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
            ->with(['jurusans', 'mitra', 'evaluasis'])
            ->latest()
            ->get();

        // 2. Antrean Laporan Unit Kerja (Menunggu Evaluasi, tipe != jurusan)
        $laporanUnit = Cooperation::where('status_dokumen', 'Menunggu Evaluasi')
            ->where(function ($q) {
                $q->whereNull('tipe_pelaksana')
                    ->orWhere('tipe_pelaksana', '!=', 'jurusan');
            })
            ->with(['mitra', 'upas', 'pusats', 'evaluasis'])
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

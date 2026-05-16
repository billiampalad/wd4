<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\JenisKerjasama;
use App\Models\Jurusan;
use App\Models\Klasifikasi;
use App\Models\Pusat;
use App\Models\Upa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function statusKerjasama()
    {
        $unitId = $this->resolveUnitId();
        $baseQuery = $this->scopeUnit(Cooperation::query(), $unitId);
        $today = now()->toDateString();

        $kadaluarsa = (clone $baseQuery)
            ->where(function ($query) use ($today) {
                $query->whereIn(DB::raw("LOWER(COALESCE(status, ''))"), ['kadaluarsa', 'kadarluarsa', 'kedaluwarsa'])
                    ->orWhereDate('end_date', '<', $today);
            })
            ->whereNotIn(DB::raw("LOWER(COALESCE(status, ''))"), ['dalam perpanjangan', 'proses', 'tidak aktif', 'nonaktif', 'non aktif'])
            ->count();

        $dalamPerpanjangan = (clone $baseQuery)
            ->whereRaw("LOWER(COALESCE(status, '')) = ?", ['dalam perpanjangan'])
            ->count();

        $proses = (clone $baseQuery)
            ->whereRaw("LOWER(COALESCE(status, '')) = ?", ['proses'])
            ->count();

        $tidakAktif = (clone $baseQuery)
            ->whereIn(DB::raw("LOWER(COALESCE(status, ''))"), ['tidak aktif', 'nonaktif', 'non aktif'])
            ->count();

        $totalKerjasama = (clone $baseQuery)->count();
        $aktif = max($totalKerjasama - $kadaluarsa - $dalamPerpanjangan - $proses - $tidakAktif, 0);

        $statusKerjasamaData = [
            'labels' => ['Aktif', 'Dalam Perpanjangan', 'Kadaluarsa', 'Tidak Aktif', 'Proses'],
            'data' => [$aktif, $dalamPerpanjangan, $kadaluarsa, $tidakAktif, $proses],
            'colors' => ['#10b981', '#f59e0b', '#ef4444', '#94a3b8', '#8b5cf6'],
        ];

        $currentYear = now()->year;
        $firstYear = (int) ((clone $baseQuery)->whereNotNull('created_at')->min(DB::raw('YEAR(created_at)')) ?: $currentYear);
        $startYear = max($firstYear, $currentYear - 8);
        $years = range($startYear, $currentYear);

        $growthRows = (clone $baseQuery)
            ->selectRaw('YEAR(created_at) as year_label')
            ->selectRaw("SUM(CASE WHEN jenis LIKE '%MoU%' THEN 1 ELSE 0 END) as mou_total")
            ->selectRaw("SUM(CASE WHEN jenis LIKE '%MoA%' THEN 1 ELSE 0 END) as moa_total")
            ->selectRaw("SUM(CASE WHEN jenis LIKE '%IA%' THEN 1 ELSE 0 END) as ia_total")
            ->whereNotNull('created_at')
            ->whereYear('created_at', '>=', $startYear)
            ->groupBy('year_label')
            ->get()
            ->keyBy('year_label');

        $growthData = [
            'labels' => array_map('strval', $years),
            'mou' => [],
            'moa' => [],
            'ia' => [],
        ];

        foreach ($years as $year) {
            $row = $growthRows->get($year);
            $growthData['mou'][] = (int) ($row->mou_total ?? 0);
            $growthData['moa'][] = (int) ($row->moa_total ?? 0);
            $growthData['ia'][] = (int) ($row->ia_total ?? 0);
        }

        $yearCount = max(count($years), 1);
        $growthAverages = [
            'mou' => (int) round(array_sum($growthData['mou']) / $yearCount),
            'moa' => (int) round(array_sum($growthData['moa']) / $yearCount),
            'ia' => (int) round(array_sum($growthData['ia']) / $yearCount),
        ];

        $calendarDate = now();
        $calendarStart = $calendarDate->copy()->startOfMonth();
        $calendarEnd = $calendarDate->copy()->endOfMonth();
        $calendarEventsByDay = [];
        $calendarEventItems = [];

        $calendarCooperations = (clone $baseQuery)
            ->with('mitra')
            ->where(function ($query) use ($calendarStart, $calendarEnd) {
                $query->whereBetween('start_date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
                    ->orWhereBetween('end_date', [$calendarStart->toDateString(), $calendarEnd->toDateString()]);
            })
            ->orderByRaw('COALESCE(start_date, end_date) asc')
            ->limit(20)
            ->get();

        foreach ($calendarCooperations as $cooperation) {
            $calendarDates = [
                ['date' => $cooperation->start_date, 'label' => 'Mulai', 'tone' => 'start'],
                ['date' => $cooperation->end_date, 'label' => 'Berakhir', 'tone' => 'end'],
            ];

            foreach ($calendarDates as $calendarItem) {
                $eventDate = $calendarItem['date'];

                if (!$eventDate || $eventDate->lt($calendarStart) || $eventDate->gt($calendarEnd)) {
                    continue;
                }

                $dateKey = $eventDate->toDateString();
                $calendarEventsByDay[$dateKey][] = [
                    'title' => $cooperation->title ?: 'Kerjasama Tanpa Judul',
                    'jenis' => $cooperation->jenis ?: 'Kerjasama',
                    'mitra' => optional($cooperation->mitra)->nama_mitra ?: 'Mitra belum diisi',
                    'label' => $calendarItem['label'],
                    'tone' => $calendarItem['tone'],
                    'date_label' => $eventDate->translatedFormat('d M Y'),
                ];
                $calendarEventItems[] = [
                    'title' => $cooperation->title ?: 'Kerjasama Tanpa Judul',
                    'jenis' => $cooperation->jenis ?: 'Kerjasama',
                    'mitra' => optional($cooperation->mitra)->nama_mitra ?: 'Mitra belum diisi',
                    'label' => $calendarItem['label'],
                    'tone' => $calendarItem['tone'],
                    'date_label' => $eventDate->translatedFormat('d M Y'),
                    'date_sort' => $dateKey,
                    'status' => $cooperation->status ?: 'Status belum diisi',
                ];
            }
        }

        usort($calendarEventItems, function ($firstEvent, $secondEvent) {
            return strcmp($firstEvent['date_sort'], $secondEvent['date_sort']);
        });

        $calendarDays = [];

        for ($day = 1; $day <= $calendarDate->daysInMonth; $day++) {
            $date = $calendarDate->copy()->day($day);
            $dateKey = $date->toDateString();
            $calendarDays[] = [
                'day' => $day,
                'date' => $dateKey,
                'is_today' => $date->isToday(),
                'events' => $calendarEventsByDay[$dateKey] ?? [],
            ];
        }

        $calendarData = [
            'month_label' => $calendarDate->translatedFormat('F Y'),
            'weekdays' => ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            'start_offset' => (int) $calendarStart->dayOfWeek,
            'days' => $calendarDays,
            'events' => $calendarEventItems,
        ];

        $dueDateYear = now()->year;
        $dueDateYearStart = now()->setDate($dueDateYear, 1, 1)->startOfDay();
        $dueDateYearEnd = now()->setDate($dueDateYear, 12, 31)->startOfDay();

        $dueDateQuery = (clone $baseQuery)
            ->with('mitra')
            ->whereNotNull('end_date')
            ->whereYear('end_date', $dueDateYear)
            ->orderBy('end_date');

        $dueDateTotal = (clone $dueDateQuery)->count();
        $dueDateCooperations = $dueDateQuery->limit(5)->get();

        $dueDateHeatRows = (clone $baseQuery)
            ->whereNotNull('end_date')
            ->whereYear('end_date', $dueDateYear)
            ->get(['end_date']);

        $dueDateCountsByDate = [];

        foreach ($dueDateHeatRows as $dueDateItem) {
            $dueDateKey = $dueDateItem->end_date->toDateString();
            $dueDateCountsByDate[$dueDateKey] = ($dueDateCountsByDate[$dueDateKey] ?? 0) + 1;
        }

        $dueDateWeeks = [];
        $dueDateCurrentWeek = [];
        $dueDateMonthLabels = [];
        $dueDateWeekIndex = 0;

        for ($emptyDay = 0; $emptyDay < $dueDateYearStart->dayOfWeek; $emptyDay++) {
            $dueDateCurrentWeek[] = null;
        }

        for ($date = $dueDateYearStart->copy(); $date->lte($dueDateYearEnd); $date->addDay()) {
            if ($date->day === 1) {
                $dueDateMonthLabels[] = [
                    'label' => $date->format('M'),
                    'week' => $dueDateWeekIndex,
                ];
            }

            $dateKey = $date->toDateString();
            $count = $dueDateCountsByDate[$dateKey] ?? 0;
            $dueDateCurrentWeek[] = [
                'date' => $dateKey,
                'label' => $date->translatedFormat('d M Y'),
                'day' => $date->day,
                'count' => $count,
                'level' => min($count, 4),
                'is_today' => $date->isToday(),
                'is_month_start' => $date->day === 1,
            ];

            if (count($dueDateCurrentWeek) === 7) {
                $dueDateWeeks[] = $dueDateCurrentWeek;
                $dueDateCurrentWeek = [];
                $dueDateWeekIndex++;
            }
        }

        if ($dueDateCurrentWeek) {
            while (count($dueDateCurrentWeek) < 7) {
                $dueDateCurrentWeek[] = null;
            }

            $dueDateWeeks[] = $dueDateCurrentWeek;
        }

        $dueDateData = [
            'year' => $dueDateYear,
            'total' => $dueDateTotal,
            'showing' => $dueDateCooperations->count(),
            'weeks' => $dueDateWeeks,
            'month_labels' => $dueDateMonthLabels,
            'weekdays' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            'rows' => $dueDateCooperations->map(function ($cooperation) {
                return [
                    'id' => $cooperation->id,
                    'doc_number' => $cooperation->doc_number ?: '-',
                    'title' => $cooperation->title ?: 'Kerjasama Tanpa Judul',
                    'jenis' => $cooperation->jenis ?: 'Kerjasama',
                    'mitra' => optional($cooperation->mitra)->nama_mitra ?: 'Mitra belum diisi',
                    'due' => optional($cooperation->end_date)->format('j/n/Y'),
                ];
            }),
        ];

        return view('auth.unit', compact(
            'statusKerjasamaData',
            'growthData',
            'growthAverages',
            'calendarData',
            'dueDateData'
        ));
    }

    // ─── Data Kerjasama ──────────────────────────────────────────
    public function dkerjasama(Request $request)
    {
        $unitId = $this->resolveUnitId();

        $kerjasamaUnit = $this->buildLaporanQuery($request)->get();

        return view('auth.unit', [
            'kerjasamaUnit' => $kerjasamaUnit,
            'jurusans' => Jurusan::orderBy('nama_jurusan')->get(),
            'upas' => Upa::orderBy('nama_upa')->get(),
            'pusats' => Pusat::orderBy('nama_pusat')->get(),
        ]);
    }

    public function dkerjasamaPreview(Request $request)
    {
        return $this->laporanPreview($request);
    }

    public function dkerjasamaPdf(Request $request)
    {
        return $this->laporanPdf($request);
    }

    public function dkerjasamaExcel(Request $request)
    {
        return $this->laporanExcel($request);
    }

    // ─── Mitra Unit ──────────────────────────────────────────────
    public function mitra()
    {
        $unitId = $this->resolveUnitId();

        // Ambil semua mitra dengan hitungan kerjasama
        $mitras = \App\Models\Mitra::with('klasifikasi')
            ->withCount('cooperations')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

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
            'nama_mitra' => 'required|string|max:255',
            'id_klasifikasi' => 'nullable|exists:klasifikasi,id',
            'alamat' => 'nullable|string|max:255',
            'kategori' => 'required|string|in:nasional,internasional',
            'negara' => 'nullable|string|max:255',
            'telp' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
        ]);

        $mitra = \App\Models\Mitra::create([
            'nama_mitra' => $request->nama_mitra,
            'id_klasifikasi' => $request->id_klasifikasi,
            'alamat' => $request->alamat,
            'kategori' => $request->kategori,
            'negara' => $request->negara ?? 'Indonesia',
            'telp' => $request->telp,
            'website' => $request->website,
        ]);

        $mitra->load('klasifikasi');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil ditambahkan.',
                'data' => $this->mitraPayload($mitra),
            ]);
        }

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
            'nama_mitra' => 'required|string|max:255',
            'id_klasifikasi' => 'nullable|exists:klasifikasi,id',
            'alamat' => 'nullable|string|max:255',
            'kategori' => 'required|string|in:nasional,internasional',
            'negara' => 'nullable|string|max:255',
            'telp' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
        ]);

        $mitra = \App\Models\Mitra::findOrFail($id);
        $mitra->update([
            'nama_mitra' => $request->nama_mitra,
            'id_klasifikasi' => $request->id_klasifikasi,
            'alamat' => $request->alamat,
            'kategori' => $request->kategori,
            'negara' => $request->negara ?? 'Indonesia',
            'telp' => $request->telp,
            'website' => $request->website,
        ]);

        $mitra->load('klasifikasi');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data mitra berhasil diperbarui.',
                'data' => $this->mitraPayload($mitra),
            ]);
        }

        return redirect()->route('unit.mitra')->with('success', 'Data mitra berhasil diperbarui.');
    }

    public function mitraDestroy(Request $request, $id)
    {
        $mitra = \App\Models\Mitra::findOrFail($id);

        // Cek apakah mitra memiliki riwayat kerjasama
        if ($mitra->cooperations()->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitra tidak bisa dihapus karena masih memiliki riwayat kerjasama.',
                ], 422);
            }

            return back()->with('error', 'Mitra tidak bisa dihapus karena masih memiliki riwayat kerjasama.');
        }

        $mitra->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil dihapus.',
                'deleted_id' => $id,
            ]);
        }

        return redirect()->route('unit.mitra')->with('success', 'Mitra berhasil dihapus.');
    }

    private function mitraPayload($mitra)
    {
        return [
            'id' => $mitra->id,
            'nama' => $mitra->nama_mitra,
            'nama_mitra' => $mitra->nama_mitra,
            'id_klasifikasi' => $mitra->id_klasifikasi,
            'klasifikasi' => $mitra->klasifikasi?->nama,
            'kategori' => $mitra->kategori,
            'negara' => $mitra->negara ?? 'Indonesia',
            'alamat' => $mitra->alamat,
            'telp' => $mitra->telp,
            'website' => $mitra->website,
        ];
    }

    // ─── Evaluasi Kinerja ────────────────────────────────────────
    public function evaluasi()
    {
        $unitId = $this->resolveUnitId();

        // Query dasar kerjasama
        $baseQuery = Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat', 'pksNumbers'])
            ->orderBy('created_at', 'asc');

        // 1. List DRAFT (Status: Draft)
        $draftList = (clone $baseQuery)->where('status_dokumen', 'Draft')->get();

        // 2. List REVISI (Status: Revisi — dikembalikan oleh Pimpinan)
        $revisiList = (clone $baseQuery)->where('status_dokumen', 'Revisi')
            ->with('evaluasis.penilai')
            ->get();

        // 3. List MENUNGGU EVALUASI
        $belumEvaluasi = (clone $baseQuery)->where('status_dokumen', 'Menunggu Evaluasi')->get();

        // 4. List SUDAH DIEVALUASI / DISAHKAN
        $evaluasiList = (clone $baseQuery)->where('status_dokumen', 'Disahkan')->get();

        return view('auth.unit', [
            'view' => 'evaluasi_kinerja',
            'draftList' => $draftList,
            'revisiList' => $revisiList,
            'belumEvaluasi' => $belumEvaluasi,
            'evaluasiList' => $evaluasiList
        ]);
    }

    // ─── Form Evaluasi (GET) ────────────────────────────────────
    public function formEvaluasi($id)
    {
        $unitId = $this->resolveUnitId();

        $kegiatan = Cooperation::findOrFail($id);

        $existingEval = Evaluasi::where('cooperation_id', $kegiatan->id)
            ->where('dinilai_oleh', Auth::id())
            ->first();

        return view('auth.unit', compact('kegiatan', 'existingEval'));
    }

    // ─── Store Evaluasi (POST) ──────────────────────────────────
    public function storeEvaluasi(Request $request, $id)
    {
        $request->validate([
            'sesuai_rencana' => 'required|integer|min:1|max:5',
            'kualitas' => 'required|integer|min:1|max:5',
            'keterlibatan' => 'required|integer|min:1|max:5',
            'efisiensi' => 'required|integer|min:1|max:5',
            'kepuasan' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string|max:2000',
        ]);

        $unitId = $this->resolveUnitId();
        $kegiatan = Cooperation::findOrFail($id);

        Evaluasi::create([
            'cooperation_id' => $kegiatan->id,
            'dinilai_oleh' => Auth::id(),
            'sesuai_rencana' => $request->sesuai_rencana,
            'kualitas' => $request->kualitas,
            'keterlibatan' => $request->keterlibatan,
            'efisiensi' => $request->efisiensi,
            'kepuasan' => $request->kepuasan,
            'catatan' => $request->catatan,
        ]);

        // Update status kegiatan menjadi menunggu validasi pimpinan
        $kegiatan->update(['status_dokumen' => 'Menunggu Evaluasi']);

        // ─── KIRIM NOTIFIKASI KE PIMPINAN ───────────────────────
        $pimpinans = \App\Models\User::whereHas('role', function ($q) {
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
            'kualitas' => 'required|integer|min:1|max:5',
            'keterlibatan' => 'required|integer|min:1|max:5',
            'efisiensi' => 'required|integer|min:1|max:5',
            'kepuasan' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string|max:2000',
        ]);

        $unitId = $this->resolveUnitId();
        $kegiatan = Cooperation::findOrFail($id);

        $eval = Evaluasi::where('cooperation_id', $kegiatan->id)
            ->where('dinilai_oleh', Auth::id())
            ->firstOrFail();

        $eval->update([
            'sesuai_rencana' => $request->sesuai_rencana,
            'kualitas' => $request->kualitas,
            'keterlibatan' => $request->keterlibatan,
            'efisiensi' => $request->efisiensi,
            'kepuasan' => $request->kepuasan,
            'catatan' => $request->catatan,
        ]);

        // Update status kegiatan menjadi menunggu validasi pimpinan
        $kegiatan->update(['status_dokumen' => 'Menunggu Evaluasi']);

        // ─── KIRIM NOTIFIKASI KE PIMPINAN ───────────────────────
        $pimpinans = \App\Models\User::whereHas('role', function ($q) {
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

        return redirect()->route('unit.evaluasi')->with('success', 'Evaluasi berhasil diperbarui dan dikirim ke Pimpinan.');
    }

    // ─── Submit Evaluasi to Pimpinan (POST) ─────────────────────
    public function submitEvaluasiToPimpinan($id)
    {
        $unitId = $this->resolveUnitId();
        $kegiatan = Cooperation::findOrFail($id);

        // Pastikan sudah ada evaluasi
        $hasEval = Evaluasi::where('cooperation_id', $kegiatan->id)
            ->where('dinilai_oleh', Auth::id())
            ->exists();

        if (!$hasEval) {
            return back()->with('error', 'Tidak bisa mengirim ke Pimpinan. Silakan isi evaluasi terlebih dahulu.');
        }

        $kegiatan->update(['status_dokumen' => 'Menunggu Evaluasi']);

        // ─── KIRIM NOTIFIKASI KE PIMPINAN ───────────────────────
        $pimpinans = \App\Models\User::whereHas('role', function ($q) {
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

    // ─── Laporan Data ────────────────────────────────────────────
    public function laporan()
    {
        $unitId = $this->resolveUnitId();

        return view('auth.unit', [
            'jurusans' => Jurusan::orderBy('nama_jurusan')->get(),
            'upas' => Upa::orderBy('nama_upa')->get(),
            'pusats' => Pusat::orderBy('nama_pusat')->get(),
        ]);
    }

    public function laporanPreview(Request $request)
    {
        $rows = $this->buildLaporanQuery($request)
            ->get()
            ->filter(fn($c) => !empty($c->title))
            ->values()
            ->map(function ($c) {
                return [
                    'id'             => $c->id,
                    'title'          => $c->title,
                    'doc_number'     => $c->doc_number,
                    'jenis'          => $c->jenis,
                    'tipe_pelaksana' => $c->tipe_pelaksana,
                    'start_date'     => $c->start_date ? $c->start_date->toDateString() : null,
                    'end_date'       => $c->end_date   ? $c->end_date->toDateString()   : null,
                    // status: coba field status dulu, fallback ke status_dokumen
                    'status'         => $c->status ?? $c->status_dokumen ?? null,
                    'mitra'  => $c->mitra  ? ['nama_mitra'   => $c->mitra->nama_mitra]   : null,
                    'jurusan'=> $c->jurusan? ['nama_jurusan' => $c->jurusan->nama_jurusan]: null,
                    'upa'    => $c->upa    ? ['nama_upa'     => $c->upa->nama_upa]        : null,
                    'pusat'  => $c->pusat  ? ['nama_pusat'   => $c->pusat->nama_pusat]    : null,
                ];
            });

        return response()->json($rows);
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
        $query = Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat', 'pksNumbers'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('tanggal_awal')) {
            $query->where('start_date', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('start_date', '<=', $request->tanggal_akhir);
        }
        // Filter Unit Pelaksana berdasarkan kolom FK yang terisi
        if ($request->filled('tipe_pelaksana') && $request->tipe_pelaksana !== 'all') {
            match ($request->tipe_pelaksana) {
                'jurusan' => $query->whereNotNull('jurusan_id'),
                'upa'     => $query->whereNotNull('upa_id'),
                'pusat'   => $query->whereNotNull('pusat_id'),
                default   => null,
            };
        }
        if ($request->filled('jurusan_id') && $request->jurusan_id !== 'all') {
            $query->where('jurusan_id', $request->jurusan_id);
        }
        if ($request->filled('upa_id') && $request->upa_id !== 'all') {
            $query->where('upa_id', $request->upa_id);
        }
        if ($request->filled('pusat_id') && $request->pusat_id !== 'all') {
            $query->where('pusat_id', $request->pusat_id);
        }
        // Filter status cocok dengan nilai ENUM DB: aktif | proses | dalam perpanjangan | kadarluarsa | tidak aktif
        if ($request->filled('status') && $request->status !== 'all') {
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
        $avgEvaluasi = (object) [
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
        return view('auth.unit');
    }

    public function previewTemplate()
    {
        $path = public_path('templates/Laporan Pelaksanaan Kerjasama.docx');

        if (!file_exists($path)) {
            return back()->with('error', 'File template tidak ditemukan.');
        }

        // Menggunakan response()->file() untuk mencoba membuka secara inline di browser
        return response()->file($path, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'inline; filename="Laporan Pelaksanaan Kerjasama.docx"'
        ]);
    }

    public function formLaporanStore(Request $request)
    {
        $request->validate([
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:5120', // Menaikkan limit ke 5MB
            'cooperation_id' => 'nullable|exists:cooperations,id',
        ]);

        $unitId = $this->resolveUnitId();
        $file = $request->file('file_laporan');

        // Dapatkan nama asli dan ekstensi
        $originalName = $file->getClientOriginalName();
        $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        // Bersihkan nama file dari karakter aneh agar aman di filesystem
        $cleanName = Str::slug($nameOnly) . '.' . $extension;

        // Logika Unik: Jika file sudah ada, tambahkan angka di belakangnya
        $filename = $cleanName;
        $counter = 1;
        while (Storage::disk('public')->exists('laporan_unit/' . $filename)) {
            $filename = Str::slug($nameOnly) . '-' . $counter . '.' . $extension;
            $counter++;
        }

        $path = $file->storeAs('laporan_unit', $filename, 'public');

        \App\Models\LaporanFile::create([
            'unit_kerja_id' => $unitId,
            'cooperation_id' => $request->cooperation_id,
            'uploaded_by' => Auth::id(),
            'file_path' => $path,
            'original_name' => $originalName, // Tetap simpan nama asli untuk tampilan
            'file_size' => $file->getSize(),
        ]);

        if ($request->has('cooperation_id')) {
            return back()->with('success', 'Dokumen laporan berhasil diupload.');
        }

        return redirect()->route('unit.form')->with('success', 'Laporan berhasil diupload.');
    }

    public function formLaporanDestroy($id)
    {
        $laporan = \App\Models\LaporanFile::findOrFail($id);

        // Pastikan hanya pengunggah atau unit terkait yang bisa menghapus (Opsional, sesuaikan kebutuhan)
        // if ($laporan->uploaded_by !== Auth::id()) {
        //     abort(403);
        // }

        // Hapus file fisik dari storage
        if (Storage::disk('public')->exists($laporan->file_path)) {
            Storage::disk('public')->delete($laporan->file_path);
        }

        // Hapus data dari database
        $laporan->delete();

        return back()->with('success', 'Dokumen laporan berhasil dihapus.');
    }
}

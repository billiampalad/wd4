<?php

namespace App\Http\Controllers\Upa;

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
use App\Support\CooperationAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpaPageController extends Controller
{
    /**
     * Resolve the upa_id for the currently logged-in user.
     */
    private function resolveUnitId()
    {
        $user = Auth::user();
        $profile = CooperationAccess::profileForUser($user);

        if (!$profile->upa_id) {
            abort(403, 'Profil UPA tidak ditemukan.');
        }

        return $profile->upa_id;
    }

    /**
     * Helper: scope query to kegiatan belonging to this UPA.
     */
    private function scopeUnit($query, $unitId)
    {
        return CooperationAccess::scopeForProfile($query, CooperationAccess::profileForUser(Auth::user()));
    }

    private function findOwnedCooperation($id): Cooperation
    {
        return $this->scopeUnit(Cooperation::query(), null)->findOrFail($id);
    }

    public function statusKerjasama(Request $request)
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

        $statusDefinitions = [
            'dalam_perpanjangan' => function ($query) {
                $query->whereRaw("LOWER(COALESCE(status, '')) = ?", ['dalam perpanjangan']);
            },
            'kadaluarsa' => function ($query) use ($today) {
                $query->where(function ($statusQuery) use ($today) {
                    $statusQuery->whereIn(DB::raw("LOWER(COALESCE(status, ''))"), ['kadaluarsa', 'kadarluarsa', 'kedaluwarsa'])
                        ->orWhereDate('end_date', '<', $today);
                })
                    ->whereNotIn(DB::raw("LOWER(COALESCE(status, ''))"), ['dalam perpanjangan', 'proses', 'tidak aktif', 'nonaktif', 'non aktif']);
            },
            'tidak_aktif' => function ($query) {
                $query->whereIn(DB::raw("LOWER(COALESCE(status, ''))"), ['tidak aktif', 'nonaktif', 'non aktif']);
            },
            'proses' => function ($query) {
                $query->whereRaw("LOWER(COALESCE(status, '')) = ?", ['proses']);
            },
        ];

        $buildJenisQuery = function (string $jenisKey) use ($baseQuery) {
            $query = clone $baseQuery;

            if ($jenisKey === 'mou') {
                $query->where('jenis', 'like', '%MoU%');
            } else {
                $query->where(function ($jenisQuery) {
                    $jenisQuery->where('jenis', 'like', '%MoA%')
                        ->orWhere('jenis', 'like', '%IA%');
                });
            }

            return $query;
        };

        $countStatusByJenis = function (string $statusKey, string $jenisKey) use ($buildJenisQuery, $statusDefinitions) {
            $query = $buildJenisQuery($jenisKey);
            $statusDefinitions[$statusKey]($query);

            return $query->count();
        };

        $makeJenisStatusCounts = function (string $jenisKey) use ($buildJenisQuery, $countStatusByJenis) {
            $total = $buildJenisQuery($jenisKey)->count();
            $dalamPerpanjanganCount = $countStatusByJenis('dalam_perpanjangan', $jenisKey);
            $kadaluarsaCount = $countStatusByJenis('kadaluarsa', $jenisKey);
            $tidakAktifCount = $countStatusByJenis('tidak_aktif', $jenisKey);
            $prosesCount = $countStatusByJenis('proses', $jenisKey);
            $aktifCount = max($total - $dalamPerpanjanganCount - $kadaluarsaCount - $tidakAktifCount - $prosesCount, 0);

            return [$aktifCount, $dalamPerpanjanganCount, $kadaluarsaCount, $tidakAktifCount, $prosesCount];
        };

        $mouVsMoaIaData = [
            'labels' => $statusKerjasamaData['labels'],
            'colors' => $statusKerjasamaData['colors'],
            'mou' => $makeJenisStatusCounts('mou'),
            'moa_ia' => $makeJenisStatusCounts('moa_ia'),
        ];

        // ─── Sebaran Dokumen (per jenis individu × status) ───────
        $buildSingleJenisQuery = function (string $jenisKey) use ($baseQuery) {
            $query = clone $baseQuery;

            if ($jenisKey === 'mou') {
                $query->where('jenis', 'like', '%MoU%');
            } elseif ($jenisKey === 'moa') {
                $query->where('jenis', 'like', '%MoA%');
            } else {
                $query->where('jenis', 'like', '%IA%')
                      ->where('jenis', 'not like', '%MoA%');
            }

            return $query;
        };

        $countSebaranStatus = function (string $statusKey, string $jenisKey) use ($buildSingleJenisQuery, $statusDefinitions) {
            $query = $buildSingleJenisQuery($jenisKey);
            $statusDefinitions[$statusKey]($query);

            return $query->count();
        };

        $makeSebaranCounts = function (string $jenisKey) use ($buildSingleJenisQuery, $countSebaranStatus) {
            $total = $buildSingleJenisQuery($jenisKey)->count();
            $dalamPerpanjangan = $countSebaranStatus('dalam_perpanjangan', $jenisKey);
            $kadaluarsa = $countSebaranStatus('kadaluarsa', $jenisKey);
            $tidakAktif = $countSebaranStatus('tidak_aktif', $jenisKey);
            $proses = $countSebaranStatus('proses', $jenisKey);
            $aktif = max($total - $dalamPerpanjangan - $kadaluarsa - $tidakAktif - $proses, 0);

            return ['aktif' => $aktif, 'dalam_perpanjangan' => $dalamPerpanjangan, 'kadaluarsa' => $kadaluarsa];
        };

        $sebaranMou = $makeSebaranCounts('mou');
        $sebaranMoa = $makeSebaranCounts('moa');
        $sebaranIa  = $makeSebaranCounts('ia');

        $sebaranDokumenData = [
            'labels' => ['MoU', 'MoA', 'IA'],
            'aktif' => [$sebaranMou['aktif'], $sebaranMoa['aktif'], $sebaranIa['aktif']],
            'dalam_perpanjangan' => [$sebaranMou['dalam_perpanjangan'], $sebaranMoa['dalam_perpanjangan'], $sebaranIa['dalam_perpanjangan']],
            'kadaluarsa' => [$sebaranMou['kadaluarsa'], $sebaranMoa['kadaluarsa'], $sebaranIa['kadaluarsa']],
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

        $dueDateYears = (clone $baseQuery)
            ->whereNotNull('created_at')
            ->selectRaw('YEAR(created_at) as due_year')
            ->distinct()
            ->orderByDesc('due_year')
            ->pluck('due_year')
            ->map(fn ($year) => (int) $year)
            ->filter()
            ->values()
            ->all();

        if (empty($dueDateYears)) {
            $dueDateYears = [now()->year];
        }

        $nextDueDateYear = max($dueDateYears) + 1;
        $dueDateYears[] = $nextDueDateYear;
        $dueDateYears = collect($dueDateYears)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        $requestedDueYear = (int) $request->query('due_year', now()->year);
        $dueDateYear = in_array($requestedDueYear, $dueDateYears, true) ? $requestedDueYear : $dueDateYears[0];
        $dueDateYearStart = now()->setDate($dueDateYear, 1, 1)->startOfDay();
        $dueDateYearEnd = now()->setDate($dueDateYear, 12, 31)->startOfDay();

        $dueDateQuery = (clone $baseQuery)
            ->with('mitra')
            ->whereNotNull('created_at')
            ->whereYear('created_at', $dueDateYear)
            ->orderBy('created_at');

        $dueDateTotal = (clone $dueDateQuery)->count();
        $dueDateCooperations = $dueDateQuery->get();

        $dueDateHeatRows = (clone $baseQuery)
            ->whereNotNull('created_at')
            ->whereYear('created_at', $dueDateYear)
            ->get(['created_at']);

        $dueDateCountsByDate = [];

        foreach ($dueDateHeatRows as $dueDateItem) {
            $dueDateKey = $dueDateItem->created_at->toDateString();
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
                'level' => min($count, 5),
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
            'years' => $dueDateYears,
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
                    'detail_url' => route('upa.kerjasama.show', $cooperation->id),
                    'created_at_label' => $cooperation->created_at ? $cooperation->created_at->translatedFormat('d M Y') : '-',
                ];
            }),
        ];

        if ($request->query('partial') === 'due_date') {
            return response()->json([
                'dueDateData' => $dueDateData,
            ]);
        }

        return view('auth.upa', compact(
            'statusKerjasamaData',
            'growthData',
            'growthAverages',
            'calendarData',
            'dueDateData',
            'mouVsMoaIaData',
            'sebaranDokumenData'
        ));
    }

    // ─── Data Kerjasama ──────────────────────────────────────────
    public function institusi()
    {
        $this->resolveUnitId();

        // Count cooperations by jenis (global/top bar stats)
        $mouCount = Cooperation::where('jenis', 'like', '%MoU%')->count();
        $moaCount = Cooperation::where('jenis', 'like', '%MoA%')->count();
        $iaCount  = Cooperation::where('jenis', 'like', '%IA%')
                        ->where('jenis', 'not like', '%MoA%')
                        ->count();

        $instansi = (object) [
            'nama_instansi' => Cooperation::DEFAULT_MOU_PELAKSANA,
            'mou_count' => Cooperation::where('jenis', 'like', '%MoU%')
                ->where(function ($query) {
                    $query->whereNull('tipe_pelaksana')
                        ->orWhere('tipe_pelaksana', '');
                })
                ->whereNull('jurusan_id')
                ->whereNull('upa_id')
                ->whereNull('pusat_id')
                ->count(),
            'moa_count' => 0,
            'ia_count' => 0,
        ];
        $instansi->total_count = $instansi->mou_count + $instansi->moa_count + $instansi->ia_count;

        // Single aggregate query to get counts grouped by institution and jenis
        $coopCounts = Cooperation::select('jurusan_id', 'upa_id', 'pusat_id', 'jenis', DB::raw('count(*) as count'))
            ->where(function($q) {
                $q->whereNotNull('jurusan_id')
                  ->orWhereNotNull('upa_id')
                  ->orWhereNotNull('pusat_id');
            })
            ->groupBy('jurusan_id', 'upa_id', 'pusat_id', 'jenis')
            ->get();

        $getCounts = function($type, $id) use ($coopCounts) {
            $mou = 0;
            $moa = 0;
            $ia = 0;

            foreach ($coopCounts as $row) {
                if ($row->{$type . '_id'} == $id) {
                    $jenis = strtolower($row->jenis);
                    if (str_contains($jenis, 'mou')) {
                        $mou += $row->count;
                    } elseif (str_contains($jenis, 'moa')) {
                        $moa += $row->count;
                    } elseif (str_contains($jenis, 'ia')) {
                        $ia += $row->count;
                    }
                }
            }

            return [
                'mou_count' => $mou,
                'moa_count' => $moa,
                'ia_count' => $ia,
                'total_count' => $mou + $moa + $ia
            ];
        };

        $jurusans = Jurusan::orderBy('nama_jurusan')->get()->map(function ($jurusan) use ($getCounts) {
            $counts = $getCounts('jurusan', $jurusan->id);
            $jurusan->mou_count = $counts['mou_count'];
            $jurusan->moa_count = $counts['moa_count'];
            $jurusan->ia_count = $counts['ia_count'];
            $jurusan->total_count = $counts['total_count'];
            return $jurusan;
        });

        $upas = Upa::orderBy('nama_upa')->get()->map(function ($upa) use ($getCounts) {
            $counts = $getCounts('upa', $upa->id);
            $upa->mou_count = $counts['mou_count'];
            $upa->moa_count = $counts['moa_count'];
            $upa->ia_count = $counts['ia_count'];
            $upa->total_count = $counts['total_count'];
            return $upa;
        });

        $pusats = Pusat::orderBy('nama_pusat')->get()->map(function ($pusat) use ($getCounts) {
            $counts = $getCounts('pusat', $pusat->id);
            $pusat->mou_count = $counts['mou_count'];
            $pusat->moa_count = $counts['moa_count'];
            $pusat->ia_count = $counts['ia_count'];
            $pusat->total_count = $counts['total_count'];
            return $pusat;
        });

        return view('auth.upa', compact('instansi', 'jurusans', 'upas', 'pusats', 'mouCount', 'moaCount', 'iaCount'));
    }

    public function bentukKegiatan()
    {
        $this->resolveUnitId();

        $bentukKegiatans = \App\Models\JenisKerjasama::withCount(['details as total_count'])
            ->orderBy('nama_kerjasama', 'asc')
            ->get();

        return view('auth.upa', compact('bentukKegiatans'));
    }

    public function statusKerjasamaReferensi()
    {
        $this->resolveUnitId();

        $counts = $this->scopeUnit(Cooperation::query(), $this->resolveUnitId())
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $statusList = collect([
            [
                'key' => 'aktif',
                'name' => 'Aktif',
                'badge' => 'dk-status-active',
                'description' => 'Kerjasama yang sedang berjalan dan masa berlakunya masih aktif.',
                'total' => $counts['aktif'] ?? 0,
            ],
            [
                'key' => 'proses',
                'name' => 'Proses',
                'badge' => 'dk-status-info',
                'description' => 'Kerjasama dalam tahap pengajuan, pembahasan draft, atau penandatanganan.',
                'total' => $counts['proses'] ?? 0,
            ],
            [
                'key' => 'dalam perpanjangan',
                'name' => 'Dalam Perpanjangan',
                'badge' => 'dk-status-warning',
                'description' => 'Masa berlaku kerjasama telah habis namun sedang dalam proses perpanjangan masa aktif.',
                'total' => $counts['dalam perpanjangan'] ?? 0,
            ],
            [
                'key' => 'kadarluarsa',
                'name' => 'Kadaluarsa',
                'badge' => 'dk-status-danger',
                'description' => 'Masa berlaku kerjasama telah berakhir dan tidak diperpanjang.',
                'total' => ($counts['kadarluarsa'] ?? 0) + ($counts['kadaluarsa'] ?? 0) + ($counts['kedaluwarsa'] ?? 0),
            ],
            [
                'key' => 'tidak aktif',
                'name' => 'Tidak Aktif',
                'badge' => 'dk-status-muted',
                'description' => 'Kerjasama dibatalkan atau dinonaktifkan secara resmi.',
                'total' => ($counts['tidak aktif'] ?? 0) + ($counts['nonaktif'] ?? 0) + ($counts['non aktif'] ?? 0),
            ],
        ]);

        return view('auth.upa', ['referensiStatus' => $statusList]);
    }

    public function statusEvaluasiReferensi()
    {
        $this->resolveUnitId();

        $counts = $this->scopeUnit(Cooperation::query(), $this->resolveUnitId())
            ->select('status_dokumen', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status_dokumen')
            ->pluck('total', 'status_dokumen')
            ->all();

        $statusList = collect([
            [
                'key' => 'Draft',
                'name' => 'Draft',
                'badge' => 'dk-status-warning',
                'description' => 'Dokumen kerjasama baru dibuat dan belum dikirim untuk evaluasi.',
                'total' => $counts['Draft'] ?? 0,
            ],
            [
                'key' => 'Menunggu Evaluasi',
                'name' => 'Menunggu Evaluasi',
                'badge' => 'dk-status-info',
                'description' => 'Evaluasi telah diisi oleh unit terkait dan sedang menunggu persetujuan/validasi dari Pimpinan.',
                'total' => $counts['Menunggu Evaluasi'] ?? 0,
            ],
            [
                'key' => 'Disahkan',
                'name' => 'Disahkan',
                'badge' => 'dk-status-active',
                'description' => 'Evaluasi kerjasama telah disetujui, divalidasi, dan disahkan oleh Pimpinan.',
                'total' => $counts['Disahkan'] ?? 0,
            ],
            [
                'key' => 'Revisi',
                'name' => 'Revisi',
                'badge' => 'dk-status-danger',
                'description' => 'Dokumen evaluasi ditolak atau dikembalikan oleh Pimpinan untuk diperbaiki.',
                'total' => $counts['Revisi'] ?? 0,
            ],
        ]);

        return view('auth.upa', ['referensiStatusEvaluasi' => $statusList]);
    }

    public function kriteriaMitraReferensi()
    {
        $this->resolveUnitId();

        $kriterias = \App\Models\Klasifikasi::withCount(['mitras as total_count'])
            ->orderBy('nama', 'asc')
            ->get();

        return view('auth.upa', compact('kriterias'));
    }

    public function klasifikasiMitra()
    {
        $unitId = $this->resolveUnitId();

        $mitraIds = Cooperation::where('upa_id', $unitId)
            ->whereNotNull('mitra_id')
            ->distinct()
            ->pluck('mitra_id');

        $totalMitras = $mitraIds->count();

        $classifications = \App\Models\Klasifikasi::withCount([
                'mitras as mitras_count' => fn ($query) => $query->whereIn('id', $mitraIds),
            ])
            ->orderBy('mitras_count', 'desc')
            ->get();

        $chartLabels = $classifications->pluck('nama')->all();
        $chartData = $classifications->pluck('mitras_count')->all();

        $colors = [
            '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b',
            '#ef4444', '#14b8a6', '#6366f1', '#a855f7', '#f43f5e',
            '#eab308', '#06b6d4', '#f97316', '#84cc16', '#64748b'
        ];

        while (count($colors) < count($chartLabels)) {
            $colors[] = '#' . substr(md5(rand()), 0, 6);
        }
        $chartColors = array_slice($colors, 0, count($chartLabels));

        $chartDataPayload = [
            'labels' => $chartLabels,
            'data' => $chartData,
            'colors' => $chartColors,
        ];

        $mostFrequent = $classifications->first();
        $mostFrequentName = $mostFrequent && $mostFrequent->mitras_count > 0 ? $mostFrequent->nama : '-';
        $mostFrequentCount = $mostFrequent ? $mostFrequent->mitras_count : 0;

        $topMitras = \App\Models\Mitra::whereIn('id', $mitraIds)
            ->withCount([
                'cooperations as cooperations_count' => fn ($query) => $query->where('upa_id', $unitId),
            ])
            ->orderBy('cooperations_count', 'desc')
            ->limit(5)
            ->get();

        return view('auth.upa', compact(
            'totalMitras',
            'classifications',
            'chartDataPayload',
            'mostFrequentName',
            'mostFrequentCount',
            'topMitras'
        ));
    }

    public function geoMitra()
    {
        $unitId = $this->resolveUnitId();

        $mitraIds = Cooperation::where('upa_id', $unitId)
            ->whereNotNull('mitra_id')
            ->distinct()
            ->pluck('mitra_id');

        $nasionalCount = \App\Models\Mitra::whereIn('id', $mitraIds)->where('kategori', 'nasional')->count();
        $internasionalCount = \App\Models\Mitra::whereIn('id', $mitraIds)->where('kategori', 'internasional')->count();
        $totalMitras = $nasionalCount + $internasionalCount;

        $totalCountries = \App\Models\Mitra::whereIn('id', $mitraIds)
            ->whereNotNull('negara')
            ->where('negara', '<>', '')
            ->distinct('negara')
            ->count('negara');

        if ($totalCountries === 0 && $totalMitras > 0) {
            $totalCountries = 1;
        }

        $rawCountries = \App\Models\Mitra::whereIn('id', $mitraIds)
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(negara), ''), 'Indonesia') as country_name"),
                DB::raw("COUNT(*) as mitras_count"),
                DB::raw("SUM(CASE WHEN kategori = 'nasional' THEN 1 ELSE 0 END) as nasional_count"),
                DB::raw("SUM(CASE WHEN kategori = 'internasional' THEN 1 ELSE 0 END) as internasional_count")
            )
            ->groupBy('country_name')
            ->orderBy('mitras_count', 'desc')
            ->get();

        $categoryChartData = [
            'labels' => ['Nasional', 'Internasional'],
            'data' => [$nasionalCount, $internasionalCount],
            'colors' => ['#10b981', '#3b82f6']
        ];

        $topCountries = $rawCountries->take(10);
        $countryChartData = [
            'labels' => $topCountries->pluck('country_name')->all(),
            'data' => $topCountries->pluck('mitras_count')->all(),
            'colors' => ['#6366f1', '#4f46e5', '#4338ca', '#3730a3', '#312e81', '#1e1b4b', '#4f46e5', '#6366f1', '#818cf8', '#a5b4fc']
        ];

        $latestInternational = \App\Models\Mitra::whereIn('id', $mitraIds)
            ->where('kategori', 'internasional')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('auth.upa', compact(
            'nasionalCount',
            'internasionalCount',
            'totalMitras',
            'totalCountries',
            'rawCountries',
            'categoryChartData',
            'countryChartData',
            'latestInternational'
        ));
    }

    public function dkerjasama(Request $request)
    {
        $unitId = $this->resolveUnitId();

        $kerjasamaJurusan = $this->buildLaporanQuery($request)->get();
        $kerjasamaUnit = $kerjasamaJurusan;
        $currentJurusan = Auth::user()->profile?->jurusan;

        return view('auth.upa', [
            'kerjasamaUnit' => $kerjasamaUnit,
            'kerjasamaJurusan' => $kerjasamaJurusan,
            'currentJurusan' => $currentJurusan,
            'jenisDokumentasiOptions' => $this->jenisDokumentasiOptions(),
            'jurusans' => $currentJurusan ? collect([$currentJurusan]) : Jurusan::orderBy('nama_jurusan')->get(),
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

        return view('auth.upa', compact('mitras'));
    }

    public function mitraCreate()
    {
        return redirect()->route('upa.mitra');
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

        return redirect()->route('upa.mitra')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function mitraShow($id)
    {
        $mitra = \App\Models\Mitra::with(['klasifikasi', 'cooperations'])->findOrFail($id);

        return view('auth.upa', compact('mitra'));
    }

    public function mitraEdit($id)
    {
        $mitra = \App\Models\Mitra::with('klasifikasi')->findOrFail($id);
        $klasifikasi = Klasifikasi::orderBy('nama', 'asc')->get();
        return view('auth.upa', compact('mitra', 'klasifikasi'));
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

        return redirect()->route('upa.mitra')->with('success', 'Data mitra berhasil diperbarui.');
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

        return redirect()->route('upa.mitra')->with('success', 'Mitra berhasil dihapus.');
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
        $baseQuery = $this->scopeUnit(Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat', 'pksNumbers']), $unitId)
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

        return view('auth.upa', [
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

        $kegiatan = $this->findOwnedCooperation($id);

        $existingEval = Evaluasi::where('cooperation_id', $kegiatan->id)
            ->where('dinilai_oleh', Auth::id())
            ->first();

        return view('auth.upa', compact('kegiatan', 'existingEval'));
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
        $kegiatan = $this->findOwnedCooperation($id);

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

        $namaUnit = Auth::user()->profile?->jurusan?->nama_jurusan ?? Auth::user()->name;

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

        return redirect()->route('upa.evaluasi')->with('success', 'Evaluasi berhasil dikirim ke Pimpinan untuk divalidasi.');
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
        $kegiatan = $this->findOwnedCooperation($id);

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

        $namaUnit = Auth::user()->profile?->jurusan?->nama_jurusan ?? Auth::user()->name;

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

        return redirect()->route('upa.evaluasi')->with('success', 'Evaluasi berhasil diperbarui dan dikirim ke Pimpinan.');
    }

    // ─── Submit Evaluasi to Pimpinan (POST) ─────────────────────
    public function submitEvaluasiToPimpinan($id)
    {
        $unitId = $this->resolveUnitId();
        $kegiatan = $this->findOwnedCooperation($id);

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

        $namaUnit = Auth::user()->profile?->jurusan?->nama_jurusan ?? Auth::user()->name;

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

        return redirect()->route('upa.evaluasi')->with('success', 'Evaluasi berhasil dikirim ke Pimpinan untuk divalidasi.');
    }

    // ─── Laporan Data ────────────────────────────────────────────
    public function laporan()
    {
        return view('auth.upa', [
            'jenisDokumentasiOptions' => $this->jenisDokumentasiOptions(),
            'jurusans' => Jurusan::orderBy('nama_jurusan')->get(),
            'upas' => Upa::orderBy('nama_upa')->get(),
            'pusats' => Pusat::orderBy('nama_pusat')->get(),
        ]);
    }

    public function laporanPreview(Request $request)
    {
        $rows = $this->buildLaporanQuery($request, true)
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
                    'pelaksana_name' => $c->pelaksana_name,
                    'pelaksana_icon' => $c->pelaksana_icon,
                    'pelaksana_class' => $c->pelaksana_class,
                    'pelaksana_groups' => $c->pelaksana_groups,
                    'start_date'     => $c->start_date ? $c->start_date->toDateString() : null,
                    'end_date'       => $c->end_date   ? $c->end_date->toDateString()   : null,
                    // status: coba field status dulu, fallback ke status_dokumen
                    'status'         => $c->status ?? $c->status_dokumen ?? null,
                    'mitra'  => $c->mitra  ? ['nama_mitra'   => $c->mitra->nama_mitra]   : null,
                    'jurusan'=> $c->jurusan? ['nama_jurusan' => $c->jurusan->nama_jurusan]: null,
                    'upa'    => $c->upa    ? ['nama_upa'     => $c->upa->nama_upa]        : null,
                    'pusat'  => $c->pusat  ? ['nama_pusat'   => $c->pusat->nama_pusat]    : null,
                    'audit'  => $this->auditPayload($c),
                ];
            });

        return response()->json($rows);
    }

    public function laporanPdf(Request $request)
    {
        $data = $this->buildLaporanQuery($request, true)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('auth.layout.upa.laporan_pdf', compact('data'));
        return $pdf->download('laporan_kerjasama_upa.pdf');
    }

    public function laporanExcel(Request $request)
    {
        $data = $this->buildLaporanQuery($request, true)
            ->get();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LaporanKerjasamaExport($data, 'auth.layout.upa.laporan_excel'), 'laporan_kerjasama_upa.xlsx');
    }

    private function auditPayload(Cooperation $cooperation): array
    {
        return [
            'created_at' => $cooperation->created_at?->toIso8601String(),
            'updated_at' => $cooperation->updated_at?->toIso8601String(),
            'created_by' => $this->auditUserPayload($cooperation->createdBy),
            'updated_by' => $this->auditUserPayload($cooperation->updatedBy),
        ];
    }

    private function auditUserPayload($user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'name' => $user->name,
            'jabatan' => $user->profile?->jabatan,
            'role' => $user->role?->role_name,
        ];
    }

    private function buildLaporanQuery(Request $request, bool $global = false)
    {
        $query = Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat', 'jurusans', 'upas', 'pusats', 'pksNumbers', 'createdBy.role', 'createdBy.profile', 'updatedBy.role', 'updatedBy.profile']);

        if (!$global) {
            $query = $this->scopeUnit($query, $this->resolveUnitId());
        }

        $query
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');

        if ($request->filled('tanggal_awal')) {
            $query->where('start_date', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('start_date', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('jenis_dokumentasi') && $request->jenis_dokumentasi !== 'all') {
            $jenisDokumentasi = strtolower($request->jenis_dokumentasi);

            $query->where(function ($jenisQuery) use ($jenisDokumentasi) {
                if ($jenisDokumentasi === 'mou') {
                    $jenisQuery->where('jenis', 'like', '%MoU%');
                } elseif ($jenisDokumentasi === 'moa') {
                    $jenisQuery->where('jenis', 'like', '%MoA%');
                } elseif ($jenisDokumentasi === 'ia') {
                    $jenisQuery->where('jenis', 'like', '%IA%')
                        ->where('jenis', 'not like', '%MoA%');
                }
            });
        }
        // Filter Unit Pelaksana berdasarkan kolom FK yang terisi
        if ($request->filled('tipe_pelaksana') && $request->tipe_pelaksana !== 'all') {
            if ($request->tipe_pelaksana === 'instansi') {
                $query->where('jenis', 'like', '%MoU%')
                    ->where(function ($typeQuery) {
                        $typeQuery->whereNull('tipe_pelaksana')
                            ->orWhere('tipe_pelaksana', '');
                    })
                    ->whereNull('jurusan_id')
                    ->whereNull('upa_id')
                    ->whereNull('pusat_id');
            } else {
                match ($request->tipe_pelaksana) {
                    'jurusan' => $query->whereNotNull('jurusan_id'),
                    'upa'     => $query->whereNotNull('upa_id'),
                    'pusat'   => $query->whereNotNull('pusat_id'),
                    default   => null,
                };
            }
        }
        if ($request->filled('jurusan_id') && $request->jurusan_id !== 'all') {
            $jurusanId = (int) $request->jurusan_id;

            $query->where(function ($jurusanQuery) use ($jurusanId) {
                $jurusanQuery->where('jurusan_id', $jurusanId)
                    ->orWhereHas('jurusans', fn ($relationQuery) => $relationQuery->whereKey($jurusanId));
            });
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
    private function jenisDokumentasiOptions()
    {
        return collect(['MoU', 'MoA', 'IA']);
    }

    public function statistik()
    {
        $unitId = $this->resolveUnitId();

        $baseQuery = $this->scopeUnit(Cooperation::query(), $unitId);

        $totalKerjasama = (clone $baseQuery)->count();

        // Status breakdown
        $statusBreakdown = (clone $baseQuery)->select('jenis', DB::raw('count(*) as total'))
            ->groupBy('jenis')
            ->pluck('total', 'jenis');

        // Tren per tahun
        $trenPerTahun = (clone $baseQuery)->select(DB::raw('YEAR(created_at) as tahun'), DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // Sebaran jenis kerjasama
        $sebaranJenis = (clone $baseQuery)->select('jenis', DB::raw('count(*) as total'))
            ->groupBy('jenis')
            ->get();

        // Rata-rata evaluasi
        $avgEvaluasi = (object) [
            'avg_kualitas' => 0,
            'avg_keterlibatan' => 0,
            'avg_efisiensi' => 0,
            'avg_kepuasan' => 0
        ];


        return view('auth.upa', compact(
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
        return view('auth.upa');
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
        while (Storage::disk('public')->exists('laporan_jurusan/' . $filename)) {
            $filename = Str::slug($nameOnly) . '-' . $counter . '.' . $extension;
            $counter++;
        }

        $path = $file->storeAs('laporan_jurusan', $filename, 'public');

        \App\Models\LaporanFile::create([
            'upa_id' => $unitId,
            'cooperation_id' => $request->cooperation_id,
            'uploaded_by' => Auth::id(),
            'uploader_role' => 'upa',
            'file_path' => $path,
            'original_name' => $originalName, // Tetap simpan nama asli untuk tampilan
            'file_size' => $file->getSize(),
        ]);

        if ($request->has('cooperation_id')) {
            return back()->with('success', 'Dokumen laporan berhasil diupload.');
        }

        return redirect()->route('upa.form')->with('success', 'Laporan berhasil diupload.');
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

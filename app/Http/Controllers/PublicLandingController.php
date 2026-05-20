<?php

namespace App\Http\Controllers;

use App\Models\Cooperation;
use App\Models\Mitra;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Support\GeoNormalizer;
use Illuminate\Support\Facades\Schema;

class PublicLandingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->resolveFilters($request);

        $cooperationBaseQuery = $this->buildCooperationBaseQuery($filters);
        $mitraBaseQuery = $this->buildMitraBaseQuery($filters);

        $kerjasama = null;
        $mitras = null;

        if ($filters['data_scope'] === 'mitra') {
            $mitraListingQuery = (clone $mitraBaseQuery)
                ->with('klasifikasi')
                ->withCount('cooperations');

            $this->applyMitraSort($mitraListingQuery, $filters['sort']);
            $mitras = $mitraListingQuery->paginate(9)->withQueryString();
        } else {
            $cooperationListingQuery = (clone $cooperationBaseQuery)
                ->with('mitra');

            $this->applyCooperationSort($cooperationListingQuery, $filters['sort']);
            $kerjasama = $cooperationListingQuery->paginate(9)->withQueryString();
        }

        [$analyticsCooperations, $analyticsMitras] = $this->loadAnalyticsDatasets(
            $filters,
            $cooperationBaseQuery,
            $mitraBaseQuery,
        );

        $stats = $this->buildStats();
        $heroSnapshot = $this->buildHeroSnapshot($stats);
        $landingAnalytics = $this->buildLandingAnalytics($analyticsCooperations, $analyticsMitras, $filters);
        $dataScope = $filters['data_scope'];

        return view('auth.welcome', compact(
            'kerjasama',
            'mitras',
            'stats',
            'heroSnapshot',
            'landingAnalytics',
            'dataScope',
        ));
    }

    private function resolveFilters(Request $request): array
    {
        $dataScope = (string) $request->get('data_scope', 'kerjasama');
        $dataScope = in_array($dataScope, ['kerjasama', 'mitra'], true) ? $dataScope : 'kerjasama';

        $kategori = (string) $request->get('kategori_mitra', 'all');
        $kategori = in_array($kategori, ['all', 'nasional', 'internasional'], true) ? $kategori : 'all';

        $statusScope = (string) $request->get('status_scope', 'all');
        $statusScope = in_array($statusScope, ['all', 'aktif'], true) ? $statusScope : 'all';
        $statusScope = $dataScope === 'kerjasama' ? $statusScope : 'all';

        $search = trim((string) $request->get('search', ''));
        $sort = (string) $request->get('sort', 'latest');

        $allowedSorts = $dataScope === 'mitra'
            ? ['latest', 'oldest', 'title', 'title_desc', 'most_cooperations']
            : ['latest', 'oldest', 'title', 'ending_soon'];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'latest';
        }

        $geoCountry = trim((string) $request->get('geo_country', ''));
        $geoCountry = mb_substr($geoCountry, 0, 120);

        $geoProvince = trim((string) $request->get('geo_province', ''));
        $geoProvince = mb_substr($geoProvince, 0, 120);

        $geoCountryCode = strtoupper(trim((string) $request->get('geo_country_code', '')));
        $geoCountryCode = preg_match('/^[A-Z]{2}$/', $geoCountryCode) === 1 ? $geoCountryCode : null;
        $geoCountryCode = $geoCountryCode ?? GeoNormalizer::normalizeCountryCode($geoCountry);

        $geoProvinceCode = trim((string) $request->get('geo_province_code', ''));
        $geoProvinceCode = preg_match('/^[0-9]{2}([0-9]{0,8})$/', $geoProvinceCode) === 1 ? $geoProvinceCode : null;

        if ($geoProvince !== '' && $geoCountry === '') {
            $geoCountry = 'Indonesia';
        }

        return [
            'data_scope' => $dataScope,
            'kategori_mitra' => $kategori,
            'status_scope' => $statusScope,
            'search' => $search,
            'sort' => $sort,
            'geo_country' => $geoCountry,
            'geo_province' => $geoProvince,
            'geo_country_code' => $geoCountryCode,
            'geo_province_code' => $geoProvinceCode,
        ];
    }

    private function buildCooperationBaseQuery(array $filters): Builder
    {
        $query = Cooperation::query();
        $search = $filters['search'];

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('doc_number', 'like', "%{$search}%")
                    ->orWhere('jenis', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('status_dokumen', 'like', "%{$search}%")
                    ->orWhere('internal_instansi', 'like', "%{$search}%")
                    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%")
                    ->orWhereHas('pksNumbers', function (Builder $pksQuery) use ($search) {
                        $pksQuery->where('number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('mitra', function (Builder $mitraQuery) use ($search) {
                        $mitraQuery->where('nama_mitra', 'like', "%{$search}%")
                            ->orWhere('kategori', 'like', "%{$search}%")
                            ->orWhere('negara', 'like', "%{$search}%")
                            ->orWhere('alamat', 'like', "%{$search}%")
                            ->orWhere('telp', 'like', "%{$search}%")
                            ->orWhere('website', 'like', "%{$search}%")
                            ->orWhereHas('klasifikasi', function (Builder $klasifikasiQuery) use ($search) {
                                $klasifikasiQuery->where('nama', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('details', function (Builder $detailQuery) use ($search) {
                        $detailQuery->where('tujuan', 'like', "%{$search}%")
                            ->orWhere('indikator_kinerja', 'like', "%{$search}%")
                            ->orWhere('keterangan', 'like', "%{$search}%")
                            ->orWhere('nilai_kontrak', 'like', "%{$search}%")
                            ->orWhere('income', 'like', "%{$search}%")
                            ->orWhere('volume_luaran', 'like', "%{$search}%")
                            ->orWhere('satuan_luaran', 'like', "%{$search}%")
                            ->orWhereHas('jenisKerjasama', function (Builder $jenisQuery) use ($search) {
                                $jenisQuery->where('nama_kerjasama', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('jurusans', function (Builder $jurusanQuery) use ($search) {
                        $jurusanQuery->where('nama_jurusan', 'like', "%{$search}%")
                            ->orWhere('kode_jurusan', 'like', "%{$search}%");
                    })
                    ->orWhereHas('prodis', function (Builder $prodiQuery) use ($search) {
                        $prodiQuery->where('nama_prodi', 'like', "%{$search}%")
                            ->orWhere('kode_prodi', 'like', "%{$search}%")
                            ->orWhere('jenjang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('upas', function (Builder $upaQuery) use ($search) {
                        $upaQuery->where('nama_upa', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pusats', function (Builder $pusatQuery) use ($search) {
                        $pusatQuery->where('nama_pusat', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['kategori_mitra'] !== 'all') {
            $query->whereHas('mitra', function (Builder $mitraQuery) use ($filters) {
                $mitraQuery->where('kategori', $filters['kategori_mitra']);
            });
        }

        if ($filters['geo_country'] !== '' || $filters['geo_province'] !== '') {
            $country = mb_strtolower(trim((string) $filters['geo_country']));
            $province = mb_strtolower(trim((string) $filters['geo_province']));
            $countryCode = strtoupper(trim((string) ($filters['geo_country_code'] ?? '')));
            $provinceCode = trim((string) ($filters['geo_province_code'] ?? ''));

            $hasCountryCode = Schema::hasColumn('mitras', 'country_code');
            $hasProvinceCode = Schema::hasColumn('mitras', 'province_code');
            $hasProvinsi = Schema::hasColumn('mitras', 'provinsi');

            $query->whereHas('mitra', function (Builder $mitraQuery) use ($country, $province, $countryCode, $provinceCode, $hasCountryCode, $hasProvinceCode, $hasProvinsi) {
                if ($countryCode !== '') {
                    if ($hasCountryCode) {
                        $mitraQuery->where('country_code', $countryCode);
                    } elseif ($country !== '') {
                        $mitraQuery->whereRaw('lower(negara) = ?', [$country]);
                    }
                } elseif ($country !== '') {
                    $mitraQuery->whereRaw('lower(negara) = ?', [$country]);
                }

                if ($provinceCode !== '') {
                    if ($hasProvinceCode) {
                        $mitraQuery->where('province_code', $provinceCode);
                    } elseif ($province !== '' && $hasProvinsi) {
                        $mitraQuery->whereRaw('lower(provinsi) = ?', [$province]);
                    }
                } elseif ($province !== '') {
                    if ($hasProvinsi) {
                        $mitraQuery->whereRaw('lower(provinsi) = ?', [$province]);
                    }
                }
            });
        }

        if ($filters['status_scope'] === 'aktif') {
            $query->where('status', 'aktif');
        }

        return $query;
    }

    private function buildMitraBaseQuery(array $filters): Builder
    {
        $query = Mitra::query();
        $search = $filters['search'];

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('nama_mitra', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%")
                    ->orWhere('negara', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('telp', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhereHas('klasifikasi', function (Builder $klasifikasiQuery) use ($search) {
                        $klasifikasiQuery->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['kategori_mitra'] !== 'all') {
            $query->where('kategori', $filters['kategori_mitra']);
        }

        if ($filters['geo_country'] !== '' || $filters['geo_province'] !== '') {
            $country = mb_strtolower(trim((string) $filters['geo_country']));
            $province = mb_strtolower(trim((string) $filters['geo_province']));
            $countryCode = strtoupper(trim((string) ($filters['geo_country_code'] ?? '')));
            $provinceCode = trim((string) ($filters['geo_province_code'] ?? ''));
            $hasCountryCode = Schema::hasColumn('mitras', 'country_code');
            $hasProvinceCode = Schema::hasColumn('mitras', 'province_code');
            $hasProvinsi = Schema::hasColumn('mitras', 'provinsi');

            if ($countryCode !== '') {
                if ($hasCountryCode) {
                    $query->where('country_code', $countryCode);
                } elseif ($country !== '') {
                    $query->whereRaw('lower(negara) = ?', [$country]);
                }
            } elseif ($country !== '') {
                $query->whereRaw('lower(negara) = ?', [$country]);
            }

            if ($provinceCode !== '') {
                if ($hasProvinceCode) {
                    $query->where('province_code', $provinceCode);
                } elseif ($province !== '' && $hasProvinsi) {
                    $query->whereRaw('lower(provinsi) = ?', [$province]);
                }
            } elseif ($province !== '') {
                if ($hasProvinsi) {
                    $query->whereRaw('lower(provinsi) = ?', [$province]);
                }
            }
        }

        return $query;
    }

    private function applyCooperationSort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest(),
            'title' => $query->orderBy('title'),
            'ending_soon' => $query
                ->orderByRaw('case when end_date is null then 1 else 0 end')
                ->orderBy('end_date'),
            default => $query->latest(),
        };
    }

    private function applyMitraSort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest(),
            'title' => $query->orderBy('nama_mitra'),
            'title_desc' => $query->orderByDesc('nama_mitra'),
            'most_cooperations' => $query->orderByDesc('cooperations_count')->orderBy('nama_mitra'),
            default => $query->latest(),
        };
    }

    private function loadAnalyticsDatasets(array $filters, Builder $cooperationQuery, Builder $mitraQuery): array
    {
        if ($filters['data_scope'] === 'mitra') {
            $mitras = (clone $mitraQuery)
                ->with('klasifikasi')
                ->get();

            $mitraIds = $mitras->pluck('id')->filter()->unique()->values();
            $cooperations = $mitraIds->isNotEmpty()
                ? Cooperation::query()
                    ->with(['mitra.klasifikasi', 'details.jenisKerjasama'])
                    ->whereIn('mitra_id', $mitraIds)
                    ->get()
                : collect();

            return [$cooperations, $mitras];
        }

        $cooperations = (clone $cooperationQuery)
            ->with(['mitra.klasifikasi', 'details.jenisKerjasama'])
            ->get();

        $mitraIds = $cooperations->pluck('mitra_id')->filter()->unique()->values();
        $mitras = $mitraIds->isNotEmpty()
            ? Mitra::query()
                ->with('klasifikasi')
                ->whereIn('id', $mitraIds)
                ->get()
            : collect();

        return [$cooperations, $mitras];
    }

    private function buildStats(): array
    {
        return [
            'total_kerjasama' => Cooperation::count(),
            'total_mitra' => Mitra::count(),
            'total_aktif' => Cooperation::where('status', 'aktif')->count(),
            'mitra_nasional' => Mitra::where('kategori', 'nasional')->count(),
            'mitra_internasional' => Mitra::where('kategori', 'internasional')->count(),
        ];
    }

    private function buildHeroSnapshot(array $stats): array
    {
        $latestUpdatedCooperation = Cooperation::query()
            ->latest('updated_at')
            ->first();

        if ($stats['total_kerjasama'] === 0) {
            $heroInsight = 'Belum ada kerja sama publik yang tercatat. Snapshot ini akan terisi otomatis saat data pertama dipublikasikan.';
        } else {
            $activeShare = (int) round(($stats['total_aktif'] / max($stats['total_kerjasama'], 1)) * 100);
            $heroInsight = $activeShare > 0
                ? "{$activeShare}% portofolio kerja sama saat ini masih berada dalam status aktif."
                : 'Belum ada kerja sama aktif saat ini, sehingga fase tindak lanjut bisa menjadi fokus pembaruan berikutnya.';
        }

        return [
            'updated_at_label' => $latestUpdatedCooperation?->updated_at?->format('d M Y, H:i') ?? 'Belum ada pembaruan',
            'latest_title' => $latestUpdatedCooperation?->title,
            'insight' => $heroInsight,
        ];
    }

    private function buildLandingAnalytics(Collection $cooperations, Collection $mitras, array $filters): array
    {
        $statusBreakdown = $this->buildStatusBreakdown($cooperations);
        $trendByYear = $this->buildTrendByYear($cooperations);
        $mitraComposition = $this->buildMitraComposition($mitras);
        $topClassifications = $this->buildTopClassifications($mitras);
        $topFields = $this->buildTopFields($cooperations);
        $geoMapPayload = $this->buildGeoMapPayload($cooperations);

        $chartPayload = $this->buildChartPayload(
            $statusBreakdown,
            $trendByYear,
            $mitraComposition,
            $topClassifications,
            $topFields,
        );

        $chartPayload['maps'] = $geoMapPayload;

        return [
            'context' => $filters['data_scope'] === 'mitra'
                ? 'Ringkasan di bawah merangkum portofolio kerja sama dari mitra yang sedang tampil.'
                : 'Semua visualisasi ikut menyesuaikan hasil pencarian dan filter aktif.',
            'status_breakdown' => $statusBreakdown,
            'trend_by_year' => $trendByYear,
            'mitra_composition' => $mitraComposition,
            'top_classifications' => $topClassifications,
            'top_fields' => $topFields,
            'chart_payload' => $chartPayload,
            'attention' => $this->buildAttentionPanel($cooperations),
        ];
    }

    private function buildChartPayload(
        array $statusBreakdown,
        array $trendByYear,
        array $mitraComposition,
        array $topClassifications,
        array $topFields
    ): array {
        return [
            'status' => [
                'labels' => array_column($statusBreakdown['items'] ?? [], 'label'),
                'values' => array_column($statusBreakdown['items'] ?? [], 'count'),
                'tones' => array_column($statusBreakdown['items'] ?? [], 'tone'),
                'total' => $statusBreakdown['total'] ?? 0,
            ],
            'trend' => [
                'labels' => array_column($trendByYear['points'] ?? [], 'label'),
                'values' => array_column($trendByYear['points'] ?? [], 'count'),
                'range_label' => $trendByYear['range_label'] ?? null,
            ],
            'mitra' => [
                'labels' => array_column($mitraComposition['items'] ?? [], 'label'),
                'values' => array_column($mitraComposition['items'] ?? [], 'count'),
                'tones' => array_column($mitraComposition['items'] ?? [], 'tone'),
                'total' => $mitraComposition['total'] ?? 0,
            ],
            'classifications' => [
                'labels' => array_column($topClassifications['items'] ?? [], 'label'),
                'values' => array_column($topClassifications['items'] ?? [], 'count'),
            ],
            'fields' => [
                'labels' => array_column($topFields['items'] ?? [], 'label'),
                'values' => array_column($topFields['items'] ?? [], 'count'),
            ],
        ];
    }

    private function buildGeoMapPayload(Collection $cooperations): array
    {
        $worldTotals = [];
        $worldActive = [];
        $worldExpiring = [];
        $worldMitraSets = [];
        $worldStatusBreakdowns = [];

        $indonesiaTotals = [];
        $indonesiaActive = [];
        $indonesiaExpiring = [];
        $indonesiaMitraSets = [];
        $indonesiaStatusBreakdowns = [];

        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(90);

        foreach ($cooperations as $cooperation) {
            $mitra = $cooperation->mitra;

            if (! $mitra) {
                continue;
            }

            $country = trim((string) ($mitra->negara ?? ''));
            $statusKey = $this->normalizeCooperationStatus($cooperation);
            $isActive = $statusKey === 'aktif';
            $isExpiring = $cooperation->end_date !== null && $cooperation->end_date->betweenIncluded($today, $limitDate);

            if ($country !== '') {
                $worldTotals[$country] = ($worldTotals[$country] ?? 0) + 1;
                $worldStatusBreakdowns[$country] = $worldStatusBreakdowns[$country] ?? [];
                $worldStatusBreakdowns[$country][$statusKey] = ($worldStatusBreakdowns[$country][$statusKey] ?? 0) + 1;

                if ($isActive) {
                    $worldActive[$country] = ($worldActive[$country] ?? 0) + 1;
                }

                if ($isExpiring) {
                    $worldExpiring[$country] = ($worldExpiring[$country] ?? 0) + 1;
                }

                $mitraId = $mitra->id;
                if ($mitraId !== null) {
                    $worldMitraSets[$country] = $worldMitraSets[$country] ?? [];
                    $worldMitraSets[$country][$mitraId] = true;
                }
            }

            if (! GeoNormalizer::isIndonesia($country, $mitra->country_code ?? null)) {
                continue;
            }

            $provinceNormalization = GeoNormalizer::normalizeIndonesiaProvince($mitra->provinsi ?? null, $mitra->alamat ?? null);
            $province = $provinceNormalization['name'];

            if ($province !== null && $province !== '') {
                $indonesiaTotals[$province] = ($indonesiaTotals[$province] ?? 0) + 1;
                $indonesiaStatusBreakdowns[$province] = $indonesiaStatusBreakdowns[$province] ?? [];
                $indonesiaStatusBreakdowns[$province][$statusKey] = ($indonesiaStatusBreakdowns[$province][$statusKey] ?? 0) + 1;

                if ($isActive) {
                    $indonesiaActive[$province] = ($indonesiaActive[$province] ?? 0) + 1;
                }

                if ($isExpiring) {
                    $indonesiaExpiring[$province] = ($indonesiaExpiring[$province] ?? 0) + 1;
                }

                $mitraId = $mitra->id;
                if ($mitraId !== null) {
                    $indonesiaMitraSets[$province] = $indonesiaMitraSets[$province] ?? [];
                    $indonesiaMitraSets[$province][$mitraId] = true;
                }
            }
        }

        $worldUnique = [];
        foreach ($worldMitraSets as $country => $set) {
            $worldUnique[$country] = count($set);
        }

        $indonesiaUnique = [];
        foreach ($indonesiaMitraSets as $province => $set) {
            $indonesiaUnique[$province] = count($set);
        }

        ksort($worldTotals);
        ksort($worldActive);
        ksort($worldExpiring);
        ksort($worldUnique);
        ksort($worldStatusBreakdowns);

        ksort($indonesiaTotals);
        ksort($indonesiaActive);
        ksort($indonesiaExpiring);
        ksort($indonesiaUnique);
        ksort($indonesiaStatusBreakdowns);

        $metrics = [
            'cooperations_total' => [
                'label' => 'Jumlah Kerja Sama',
                'unit' => 'Kerja Sama',
                'world' => $worldTotals,
                'indonesia' => $indonesiaTotals,
                'breakdowns' => [
                    'world_status' => $worldStatusBreakdowns,
                    'indonesia_status' => $indonesiaStatusBreakdowns,
                ],
            ],
            'cooperations_active' => [
                'label' => 'Kerja Sama Aktif',
                'unit' => 'Kerja Sama',
                'world' => $worldActive,
                'indonesia' => $indonesiaActive,
            ],
            'mitras_unique' => [
                'label' => 'Mitra Unik',
                'unit' => 'Mitra',
                'world' => $worldUnique,
                'indonesia' => $indonesiaUnique,
            ],
            'cooperations_expiring_90' => [
                'label' => 'Akan Berakhir (90 hari)',
                'unit' => 'Kerja Sama',
                'world' => $worldExpiring,
                'indonesia' => $indonesiaExpiring,
            ],
        ];

        return [
            'default_metric' => 'cooperations_total',
            'metrics' => $metrics,
        ];
    }

    private function buildStatusBreakdown(Collection $cooperations): array
    {
        $definitions = [
            'aktif' => ['label' => 'Aktif', 'tone' => 'success'],
            'proses' => ['label' => 'Proses', 'tone' => 'violet'],
            'perpanjangan' => ['label' => 'Perpanjangan', 'tone' => 'warning'],
            'kedaluwarsa' => ['label' => 'Kedaluwarsa', 'tone' => 'danger'],
            'tidak_aktif' => ['label' => 'Tidak Aktif', 'tone' => 'neutral'],
        ];

        $counts = array_fill_keys(array_keys($definitions), 0);

        foreach ($cooperations as $cooperation) {
            $normalizedStatus = $this->normalizeCooperationStatus($cooperation);
            $counts[$normalizedStatus] = ($counts[$normalizedStatus] ?? 0) + 1;
        }

        $total = array_sum($counts);
        $items = [];

        foreach ($definitions as $key => $definition) {
            $count = $counts[$key] ?? 0;
            $share = $total > 0 ? round(($count / $total) * 100, 1) : 0;

            $items[] = [
                'key' => $key,
                'label' => $definition['label'],
                'tone' => $definition['tone'],
                'count' => $count,
                'share' => $share,
            ];
        }

        $dominantStatus = $total > 0 ? collect($items)->sortByDesc('count')->first() : null;

        return [
            'has_data' => $total > 0,
            'total' => $total,
            'items' => $items,
            'dominant_label' => $dominantStatus['label'] ?? null,
            'dominant_share' => $dominantStatus['share'] ?? 0,
        ];
    }

    private function buildTrendByYear(Collection $cooperations): array
    {
        $yearCounts = [];

        foreach ($cooperations as $cooperation) {
            $year = $cooperation->start_date?->format('Y') ?? $cooperation->created_at?->format('Y');

            if (! $year) {
                continue;
            }

            $yearCounts[$year] = ($yearCounts[$year] ?? 0) + 1;
        }

        if ($yearCounts === []) {
            return [
                'has_data' => false,
                'points' => [],
                'range_label' => null,
            ];
        }

        $availableYears = array_map('intval', array_keys($yearCounts));
        $endYear = max($availableYears);
        $startYear = $endYear - 5;
        $windowCounts = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $windowCounts[$year] = $yearCounts[(string) $year] ?? $yearCounts[$year] ?? 0;
        }

        $maxCount = max($windowCounts);
        $points = [];

        foreach ($windowCounts as $year => $count) {
            $points[] = [
                'label' => (string) $year,
                'count' => $count,
                'share' => $maxCount > 0 ? round(($count / $maxCount) * 100, 1) : 0,
            ];
        }

        return [
            'has_data' => true,
            'points' => $points,
            'range_label' => "{$startYear} - {$endYear}",
        ];
    }

    private function buildMitraComposition(Collection $mitras): array
    {
        $total = $mitras->count();
        $nasional = $mitras
            ->filter(fn (Mitra $mitra) => strtolower((string) $mitra->kategori) === 'nasional')
            ->count();
        $internasional = $mitras
            ->filter(fn (Mitra $mitra) => strtolower((string) $mitra->kategori) === 'internasional')
            ->count();
        $belumDiatur = max($total - $nasional - $internasional, 0);

        $items = [
            [
                'label' => 'Nasional',
                'tone' => 'info',
                'count' => $nasional,
                'share' => $total > 0 ? round(($nasional / $total) * 100, 1) : 0,
            ],
            [
                'label' => 'Internasional',
                'tone' => 'indigo',
                'count' => $internasional,
                'share' => $total > 0 ? round(($internasional / $total) * 100, 1) : 0,
            ],
        ];

        if ($belumDiatur > 0) {
            $items[] = [
                'label' => 'Belum Ditentukan',
                'tone' => 'neutral',
                'count' => $belumDiatur,
                'share' => $total > 0 ? round(($belumDiatur / $total) * 100, 1) : 0,
            ];
        }

        return [
            'has_data' => $total > 0,
            'total' => $total,
            'items' => $items,
        ];
    }

    private function buildTopClassifications(Collection $mitras): array
    {
        $counts = $mitras
            ->groupBy(fn (Mitra $mitra) => $mitra->klasifikasi?->nama ?: 'Tanpa Klasifikasi')
            ->map(fn (Collection $group) => $group->count())
            ->sortDesc()
            ->take(5);

        if ($counts->isEmpty()) {
            return [
                'has_data' => false,
                'items' => [],
            ];
        }

        $maxCount = max($counts->all());
        $items = $counts->map(function (int $count, string $label) use ($maxCount) {
            return [
                'label' => $label,
                'count' => $count,
                'share' => $maxCount > 0 ? round(($count / $maxCount) * 100, 1) : 0,
            ];
        })->values()->all();

        return [
            'has_data' => true,
            'items' => $items,
        ];
    }

    private function buildTopFields(Collection $cooperations): array
    {
        $fieldCounts = [];

        foreach ($cooperations as $cooperation) {
            $labels = $cooperation->details
                ->map(fn ($detail) => trim((string) ($detail->jenisKerjasama?->nama_kerjasama ?? '')))
                ->filter()
                ->unique()
                ->values();

            if ($labels->isEmpty()) {
                $labels = collect(['Bidang belum ditetapkan']);
            }

            foreach ($labels as $label) {
                $fieldCounts[$label] = ($fieldCounts[$label] ?? 0) + 1;
            }
        }

        arsort($fieldCounts);
        $fieldCounts = array_slice($fieldCounts, 0, 5, true);

        if ($fieldCounts === []) {
            return [
                'has_data' => false,
                'items' => [],
            ];
        }

        $maxCount = max($fieldCounts);
        $items = [];

        foreach ($fieldCounts as $label => $count) {
            $items[] = [
                'label' => $label,
                'count' => $count,
                'share' => $maxCount > 0 ? round(($count / $maxCount) * 100, 1) : 0,
            ];
        }

        return [
            'has_data' => true,
            'items' => $items,
        ];
    }

    private function buildAttentionPanel(Collection $cooperations): array
    {
        if ($cooperations->isEmpty()) {
            return [
                'has_data' => false,
                'mode' => 'empty',
                'headline' => 'Belum ada sorotan portofolio',
                'description' => 'Panel perhatian akan muncul otomatis saat data kerja sama publik tersedia.',
                'items' => [],
            ];
        }

        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(90);

        $upcoming = $cooperations
            ->filter(fn (Cooperation $cooperation) => $cooperation->end_date !== null
                && $cooperation->end_date->betweenIncluded($today, $limitDate))
            ->sortBy(fn (Cooperation $cooperation) => $cooperation->end_date?->timestamp)
            ->take(5)
            ->values();

        if ($upcoming->isNotEmpty()) {
            return [
                'has_data' => true,
                'mode' => 'upcoming',
                'headline' => 'Perhatian 90 hari ke depan',
                'description' => 'Daftar ini menyorot kerja sama yang akan segera berakhir agar tindak lanjut bisa diprioritaskan.',
                'items' => $upcoming->map(function (Cooperation $cooperation) use ($today) {
                    $daysLeft = max($today->diffInDays($cooperation->end_date, false), 0);

                    return [
                        'title' => $cooperation->title,
                        'partner' => $cooperation->mitra?->nama_mitra ?? 'Mitra belum ditentukan',
                        'meta_label' => 'Berakhir ' . $cooperation->end_date?->format('d M Y'),
                        'supporting_label' => $daysLeft === 0 ? 'Berakhir hari ini' : "{$daysLeft} hari lagi",
                        'tone' => $daysLeft <= 30 ? 'danger' : 'warning',
                    ];
                })->all(),
            ];
        }

        $latest = $cooperations
            ->sortByDesc(fn (Cooperation $cooperation) => $cooperation->updated_at?->timestamp ?? 0)
            ->take(5)
            ->values();

        return [
            'has_data' => true,
            'mode' => 'latest',
            'headline' => 'Pembaruan terbaru portofolio',
            'description' => 'Jika belum ada item yang mendekati akhir masa berlaku, panel ini menampilkan data yang paling baru diperbarui.',
            'items' => $latest->map(function (Cooperation $cooperation) {
                return [
                    'title' => $cooperation->title,
                    'partner' => $cooperation->mitra?->nama_mitra ?? 'Mitra belum ditentukan',
                    'meta_label' => 'Diperbarui ' . ($cooperation->updated_at?->format('d M Y') ?? '-'),
                    'supporting_label' => $cooperation->status ?: 'Status belum diisi',
                    'tone' => 'info',
                ];
            })->all(),
        ];
    }

    private function normalizeCooperationStatus(Cooperation $cooperation): string
    {
        $status = strtolower(trim(str_replace(['_', '-'], ' ', (string) $cooperation->status)));
        $today = Carbon::today();

        if (in_array($status, ['tidak aktif', 'nonaktif', 'non aktif'], true)) {
            return 'tidak_aktif';
        }

        if ($status === 'proses') {
            return 'proses';
        }

        if (str_contains($status, 'perpanjangan')) {
            return 'perpanjangan';
        }

        if (
            in_array($status, ['kadaluarsa', 'kadarluarsa', 'kedaluwarsa'], true)
            || ($cooperation->end_date !== null && $cooperation->end_date->lt($today))
        ) {
            return 'kedaluwarsa';
        }

        return 'aktif';
    }
}

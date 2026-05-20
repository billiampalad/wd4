<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Mitra;
use App\Support\GeoNormalizer;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mitra:normalize-geo {--dry-run : Tampilkan perubahan tanpa menyimpan} {--chunk=500 : Ukuran batch update}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $chunkSize = (int) $this->option('chunk');
    $chunkSize = $chunkSize > 0 ? min($chunkSize, 2000) : 500;

    $this->info('Normalisasi geo Mitra (country_code / provinsi / province_code)');
    if ($dryRun) {
        $this->warn('Mode DRY-RUN: tidak ada data yang disimpan.');
    }

    $total = Mitra::query()->count();
    $updated = 0;
    $scanned = 0;

    Mitra::query()
        ->orderBy('id')
        ->chunkById($chunkSize, function ($mitras) use (&$updated, &$scanned, $total, $dryRun) {
            DB::transaction(function () use ($mitras, &$updated, &$scanned, $total, $dryRun) {
                foreach ($mitras as $mitra) {
                    $scanned += 1;

                    $changes = [];
                    $countryCode = GeoNormalizer::normalizeCountryCode($mitra->country_code ?: $mitra->negara);
                    if ($countryCode !== null && $mitra->country_code !== $countryCode) {
                        $changes['country_code'] = $countryCode;
                    }

                    if (($mitra->negara === null || trim((string) $mitra->negara) === '') && $countryCode !== null) {
                        $countryName = GeoNormalizer::countryNameFromCode($countryCode);
                        if ($countryName !== null) {
                            $changes['negara'] = $countryName;
                        }
                    }

                    if (GeoNormalizer::isIndonesia($mitra->negara, $countryCode)) {
                        $provinceNormalization = GeoNormalizer::normalizeIndonesiaProvince($mitra->provinsi, $mitra->alamat);
                        if ($provinceNormalization['name'] !== null && $provinceNormalization['name'] !== $mitra->provinsi) {
                            $changes['provinsi'] = $provinceNormalization['name'];
                        }

                        if ($provinceNormalization['code'] !== null && $provinceNormalization['code'] !== $mitra->province_code) {
                            $changes['province_code'] = $provinceNormalization['code'];
                        }
                    } else {
                        if ($mitra->province_code !== null) {
                            $changes['province_code'] = null;
                        }
                    }

                    if ($changes === []) {
                        continue;
                    }

                    $updated += 1;

                    $preview = 'Mitra #' . $mitra->id . ' (' . $mitra->nama_mitra . '): ' . json_encode($changes, JSON_UNESCAPED_UNICODE);
                    if ($updated <= 10 || $dryRun) {
                        $this->line($preview);
                    }

                    if (! $dryRun) {
                        $mitra->fill($changes);
                        $mitra->save();
                    }
                }

                if ($scanned % 500 === 0) {
                    $this->info("Progress: {$scanned}/{$total} scanned, {$updated} updated");
                }
            });
        });

    $this->info("Selesai. Total scanned: {$scanned}. Total updated: {$updated}.");
})->purpose('Normalisasi data geo pada tabel mitras.');

<?php

namespace App\Support;

class GeoNormalizer
{
    public static function normalizeText(?string $value): string
    {
        $text = trim((string) $value);
        $text = mb_strtolower($text);
        $text = str_replace(['_', '-'], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);

        return $text;
    }

    /**
     * @return string|null ISO-3166-1 alpha-2
     */
    public static function normalizeCountryCode(?string $country): ?string
    {
        $raw = trim((string) $country);

        if ($raw === '') {
            return null;
        }

        $upper = strtoupper($raw);
        if (preg_match('/^[A-Z]{2}$/', $upper) === 1) {
            return $upper;
        }

        $normalized = self::normalizeText($raw);

        $map = [
            'indonesia' => 'ID',
            'id' => 'ID',
            'idn' => 'ID',
            'republic of indonesia' => 'ID',
            'united states' => 'US',
            'united states of america' => 'US',
            'usa' => 'US',
            'uk' => 'GB',
            'united kingdom' => 'GB',
            'england' => 'GB',
            'australia' => 'AU',
            'japan' => 'JP',
            'south korea' => 'KR',
            'korea republic of' => 'KR',
            'china' => 'CN',
            'people s republic of china' => 'CN',
            'singapore' => 'SG',
            'malaysia' => 'MY',
            'thailand' => 'TH',
            'viet nam' => 'VN',
            'vietnam' => 'VN',
            'philippines' => 'PH',
            'new zealand' => 'NZ',
            'russian federation' => 'RU',
            'russia' => 'RU',
            'taiwan' => 'TW',
            'hong kong' => 'HK',
            'india' => 'IN',
            'netherlands' => 'NL',
            'germany' => 'DE',
            'france' => 'FR',
            'spain' => 'ES',
            'italy' => 'IT',
        ];

        return $map[$normalized] ?? null;
    }

    public static function countryNameFromCode(?string $countryCode): ?string
    {
        $code = strtoupper(trim((string) $countryCode));
        if ($code === '') {
            return null;
        }

        $map = [
            'ID' => 'Indonesia',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'CN' => 'China',
            'SG' => 'Singapore',
            'MY' => 'Malaysia',
            'TH' => 'Thailand',
            'VN' => 'Vietnam',
            'PH' => 'Philippines',
            'NZ' => 'New Zealand',
            'RU' => 'Russia',
            'IN' => 'India',
            'NL' => 'Netherlands',
            'DE' => 'Germany',
            'FR' => 'France',
            'ES' => 'Spain',
            'IT' => 'Italy',
        ];

        return $map[$code] ?? null;
    }

    public static function isIndonesia(?string $country, ?string $countryCode = null): bool
    {
        $code = strtoupper(trim((string) $countryCode));
        if ($code !== '') {
            return $code === 'ID';
        }

        $normalized = self::normalizeText($country);
        return in_array($normalized, ['indonesia', 'id', 'idn'], true);
    }

    /**
     * @return array{name: string|null, code: string|null}
     */
    public static function normalizeIndonesiaProvince(?string $province, ?string $address = null): array
    {
        $raw = trim((string) $province);
        if ($raw === '' && $address !== null) {
            $raw = trim((string) $address);
        }

        if ($raw === '') {
            return ['name' => null, 'code' => null];
        }

        $normalized = self::normalizeText($raw);
        $canonical = self::detectProvinceNameFromText($normalized);
        $code = $canonical ? (self::provinceCodeFromCanonicalName($canonical) ?? null) : null;

        return ['name' => $canonical, 'code' => $code];
    }

    /**
     * @return array<string>
     */
    public static function getIndonesianProvinces(): array
    {
        return [
            'Aceh', 'Bali', 'Banten', 'Bengkulu', 'DI Yogyakarta', 'DKI Jakarta',
            'Gorontalo', 'Jambi', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur',
            'Kalimantan Barat', 'Kalimantan Selatan', 'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Utara',
            'Kepulauan Bangka Belitung', 'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara',
            'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Papua', 'Papua Barat', 'Papua Barat Daya',
            'Papua Pegunungan', 'Papua Selatan', 'Papua Tengah', 'Riau', 'Sulawesi Barat',
            'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara',
            'Sumatera Barat', 'Sumatera Selatan', 'Sumatera Utara'
        ];
    }

    private static function detectProvinceNameFromText(string $normalizedText): ?string
    {
        $candidates = [
            'Sulawesi Utara' => ['sulawesi utara', 'sulut', 'manado', 'tomohon', 'bitung', 'minahasa', 'kotamobagu', 'sangihe', 'talaud', 'siau', 'bolmong'],
            'DKI Jakarta' => ['dki jakarta', 'daerah khusus ibukota', 'jakarta', 'jakarta raya', 'jaksel', 'jakpus', 'jaktim', 'jakbar', 'jakut'],
            'Jawa Barat' => ['jawa barat', 'jabar', 'bandung', 'bogor', 'depok', 'bekasi', 'cirebon', 'sukabumi', 'tasikmalaya', 'cimahi', 'garut', 'karawang'],
            'Jawa Timur' => ['jawa timur', 'jatim', 'surabaya', 'malang', 'sidoarjo', 'gresik', 'banyuwangi', 'jember', 'kediri', 'madiun', 'probolinggo', 'pasuruan', 'blitar', 'batu'],
            'Jawa Tengah' => ['jawa tengah', 'jateng', 'semarang', 'surakarta', 'solo', 'salatiga', 'magelang', 'pekalongan', 'tegal', 'purwokerto', 'banyumas', 'kudus'],
            'DI Yogyakarta' => ['di yogyakarta', 'd i yogyakarta', 'diy', 'yogyakarta', 'jogja', 'jogjakarta', 'sleman', 'bantul', 'gunungkidul', 'kulon progo'],
            'Banten' => ['banten', 'tangerang', 'serang', 'cilegon', 'tangsel', 'tangerang selatan', 'pandeglang', 'lebak'],
            'Bali' => ['bali', 'denpasar', 'badung', 'gianyar', 'tabanan', 'buleleng', 'singaraja', 'ubud', 'kuta'],
            'Sumatera Utara' => ['sumatera utara', 'sumut', 'medan', 'pematangsiantar', 'binjai', 'tebing tinggi', 'deli serdang', 'karo'],
            'Sumatera Barat' => ['sumatera barat', 'sumbar', 'padang', 'bukittinggi', 'payakumbuh', 'pariaman', 'solok'],
            'Sumatera Selatan' => ['sumatera selatan', 'sumsel', 'palembang', 'prabumulih', 'lubuklinggau', 'ogankomering'],
            'Riau' => ['riau', 'pekanbaru', 'dumai', 'kampar', 'siak', 'bengkalis', 'rokan'],
            'Kepulauan Riau' => ['kepulauan riau', 'kepri', 'batam', 'tanjungpinang', 'bintan', 'karimun', 'natuna', 'anambas'],
            'Lampung' => ['lampung', 'bandar lampung', 'metro'],
            'Jambi' => ['jambi', 'sungai penuh', 'muaro jambi'],
            'Bengkulu' => ['bengkulu', 'rejang lebong', 'curup'],
            'Kepulauan Bangka Belitung' => ['bangka belitung', 'kepulauan bangka belitung', 'babel', 'pangkalpinang', 'bangka', 'belitung', 'tanjung pandan'],
            'Aceh' => ['aceh', 'nanggroe aceh darussalam', 'nad', 'banda aceh', 'lhokseumawe', 'langsa', 'sabang', 'meulaboh'],
            'Kalimantan Timur' => ['kalimantan timur', 'kaltim', 'samarinda', 'balikpapan', 'bontang', 'kutai', 'penajam paser utara', 'nusantara', 'ikn'],
            'Kalimantan Barat' => ['kalimantan barat', 'kalbar', 'pontianak', 'singkawang', 'sambas', 'ketapang'],
            'Kalimantan Selatan' => ['kalimantan selatan', 'kalsel', 'banjarmasin', 'banjarbaru', 'martapura'],
            'Kalimantan Tengah' => ['kalimantan tengah', 'kalteng', 'palangkaraya', 'sampit', 'pangkalan bun'],
            'Kalimantan Utara' => ['kalimantan utara', 'kalut', 'tarakan', 'tanjung selor', 'nunukan', 'malinau'],
            'Sulawesi Selatan' => ['sulawesi selatan', 'sulsel', 'makassar', 'palopo', 'parepare', 'maros', 'gowa', 'bone'],
            'Sulawesi Tengah' => ['sulawesi tengah', 'sulteng', 'palu', 'poso', 'luwuk', 'donggala', 'toli-toli'],
            'Sulawesi Tenggara' => ['sulawesi tenggara', 'sultra', 'kendari', 'baubau', 'kolaka', 'wakatobi'],
            'Sulawesi Barat' => ['sulawesi barat', 'sulbar', 'mamuju', 'majene', 'polewali mandar', 'polman'],
            'Gorontalo' => ['gorontalo', 'limboto', 'boalemo', 'bone bolango'],
            'Nusa Tenggara Barat' => ['nusa tenggara barat', 'ntb', 'mataram', 'bima', 'lombok', 'sumbawa'],
            'Nusa Tenggara Timur' => ['nusa tenggara timur', 'ntt', 'kupang', 'labuan bajo', 'flores', 'ende', 'maumere', 'sumba', 'timor'],
            'Maluku' => ['maluku', 'ambon', 'tual', 'seram', 'buru'],
            'Maluku Utara' => ['maluku utara', 'ternate', 'tidore', 'sofifi', 'halmahera'],
            'Papua' => ['papua', 'jayapura', 'sentani', 'biak', 'yapen', 'keerom', 'sarmi'],
            'Papua Barat' => ['papua barat', 'irian jaya barat', 'manokwari', 'fakfak', 'kaimana', 'teluk bintuni'],
            'Papua Barat Daya' => ['papua barat daya', 'sorong', 'raja ampat', 'maybrat', 'tambrauw'],
            'Papua Selatan' => ['papua selatan', 'merauke', 'boven digoel', 'mappi', 'asmat'],
            'Papua Tengah' => ['papua tengah', 'nabire', 'mimika', 'timika', 'paniai', 'puncak jaya', 'intan jaya'],
            'Papua Pegunungan' => ['papua pegunungan', 'wamena', 'jayawijaya', 'yahukimo', 'tolikara', 'lanny jaya', 'yalimo'],
        ];

        foreach ($candidates as $province => $needles) {
            foreach ($needles as $needle) {
                if ($needle === '') {
                    continue;
                }
                $pattern = '/\b' . preg_quote($needle, '/') . '\b/u';
                if (preg_match($pattern, $normalizedText) === 1) {
                    return $province;
                }
            }
        }

        return null;
    }

    public static function provinceCodeFromCanonicalName(string $province): ?string
    {
        $map = [
            'Aceh' => '11',
            'Sumatera Utara' => '12',
            'Sumatera Barat' => '13',
            'Riau' => '14',
            'Jambi' => '15',
            'Sumatera Selatan' => '16',
            'Bengkulu' => '17',
            'Lampung' => '18',
            'Kepulauan Bangka Belitung' => '19',
            'Kepulauan Riau' => '21',
            'DKI Jakarta' => '31',
            'Jawa Barat' => '32',
            'Jawa Tengah' => '33',
            'DI Yogyakarta' => '34',
            'Jawa Timur' => '35',
            'Banten' => '36',
            'Bali' => '51',
            'Nusa Tenggara Barat' => '52',
            'Nusa Tenggara Timur' => '53',
            'Kalimantan Barat' => '61',
            'Kalimantan Tengah' => '62',
            'Kalimantan Selatan' => '63',
            'Kalimantan Timur' => '64',
            'Kalimantan Utara' => '65',
            'Sulawesi Utara' => '71',
            'Sulawesi Tengah' => '72',
            'Sulawesi Selatan' => '73',
            'Sulawesi Tenggara' => '74',
            'Gorontalo' => '75',
            'Sulawesi Barat' => '76',
            'Maluku' => '81',
            'Maluku Utara' => '82',
            'Papua Barat' => '91',
            'Papua' => '94',
            'Papua Selatan' => '93',
            'Papua Tengah' => '92',
            'Papua Pegunungan' => '95',
            'Papua Barat Daya' => '96',
        ];

        return $map[$province] ?? null;
    }
}

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

    private static function detectProvinceNameFromText(string $normalizedText): ?string
    {
        $candidates = [
            'Aceh' => ['aceh', 'nanggroe aceh darussalam', 'nad'],
            'Sumatera Utara' => ['sumatera utara', 'sumut'],
            'Sumatera Barat' => ['sumatera barat', 'sumbar'],
            'Riau' => ['riau'],
            'Kepulauan Riau' => ['kepulauan riau', 'kepri'],
            'Jambi' => ['jambi'],
            'Bengkulu' => ['bengkulu'],
            'Sumatera Selatan' => ['sumatera selatan', 'sumsel'],
            'Kepulauan Bangka Belitung' => ['bangka belitung', 'kepulauan bangka belitung', 'babel'],
            'Lampung' => ['lampung'],
            'Banten' => ['banten'],
            'DKI Jakarta' => ['dki jakarta', 'daerah khusus ibukota', 'jakarta', 'jakarta raya'],
            'Jawa Barat' => ['jawa barat', 'jabar'],
            'Jawa Tengah' => ['jawa tengah', 'jateng'],
            'DI Yogyakarta' => ['di yogyakarta', 'd i yogyakarta', 'diy', 'yogyakarta', 'jogja'],
            'Jawa Timur' => ['jawa timur', 'jatim'],
            'Bali' => ['bali'],
            'Nusa Tenggara Barat' => ['nusa tenggara barat', 'ntb'],
            'Nusa Tenggara Timur' => ['nusa tenggara timur', 'ntt'],
            'Kalimantan Barat' => ['kalimantan barat', 'kalbar'],
            'Kalimantan Tengah' => ['kalimantan tengah', 'kalteng'],
            'Kalimantan Selatan' => ['kalimantan selatan', 'kalsel'],
            'Kalimantan Timur' => ['kalimantan timur', 'kaltim'],
            'Kalimantan Utara' => ['kalimantan utara', 'kalut'],
            'Sulawesi Utara' => ['sulawesi utara', 'sulut'],
            'Gorontalo' => ['gorontalo'],
            'Sulawesi Tengah' => ['sulawesi tengah', 'sulteng'],
            'Sulawesi Barat' => ['sulawesi barat', 'sulbar'],
            'Sulawesi Selatan' => ['sulawesi selatan', 'sulsel'],
            'Sulawesi Tenggara' => ['sulawesi tenggara', 'sultra'],
            'Maluku' => ['maluku'],
            'Maluku Utara' => ['maluku utara'],
            'Papua' => ['papua'],
            'Papua Barat' => ['papua barat', 'irian jaya barat'],
            'Papua Selatan' => ['papua selatan'],
            'Papua Tengah' => ['papua tengah'],
            'Papua Pegunungan' => ['papua pegunungan'],
            'Papua Barat Daya' => ['papua barat daya'],
        ];

        foreach ($candidates as $province => $needles) {
            foreach ($needles as $needle) {
                if ($needle !== '' && str_contains($normalizedText, $needle)) {
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

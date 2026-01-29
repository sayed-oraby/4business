<?php

namespace Modules\Shipping\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LocationProvider
{
    /**
     * Example stub that can later call external datasets.
     *
     * @return array<int, array<string, mixed>>
     */
    public function countries(?string $search = null): array
    {
        $data = collect(config('shipping.countries', []));

        if ($search) {
            $data = $data->filter(function ($country) use ($search) {
                return str_contains(strtolower($country['name_en'] ?? ''), strtolower($search))
                    || str_contains(strtolower($country['name_ar'] ?? ''), strtolower($search));
            });
        }

        return $data->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function states(string $countryIso2, ?string $search = null): array
    {
        $iso2 = strtoupper($countryIso2);

        $states = collect($this->getStates($iso2));

        if ($search) {
            $states = $states->filter(function ($state) use ($search) {
                return str_contains(strtolower($state['name_en'] ?? ''), strtolower($search))
                    || str_contains(strtolower($state['name_ar'] ?? ''), strtolower($search))
                    || str_contains(strtolower($state['code'] ?? ''), strtolower($search));
            });
        }

        return $states->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function cities(string $countryIso2, ?string $stateCode = null, ?string $search = null): array
    {
        $iso2 = strtoupper($countryIso2);

        $cities = collect($this->getCities($iso2));

        if ($stateCode) {
            $cities = $cities->where('state_code', strtoupper($stateCode));
        }

        if ($search) {
            $cities = $cities->filter(function ($city) use ($search) {
                return str_contains(strtolower($city['name_en'] ?? ''), strtolower($search))
                    || str_contains(strtolower($city['name_ar'] ?? ''), strtolower($search))
                    || str_contains(strtolower($city['code'] ?? ''), strtolower($search));
            });
        }

        return $cities->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function packageCountries(?string $search = null): array
    {
        $dataset = $this->cachedPackageDataset();

        if (empty($dataset)) {
            return [];
        }

        $countries = collect($dataset)
            ->map(function ($country, $code) {
                $iso2 = strtoupper($country['iso_3166_1_alpha2'] ?? $code);

                return [
                    'iso2' => $iso2,
                    'iso3' => strtoupper($country['iso_3166_1_alpha3'] ?? ''),
                    'name_en' => $country['name'] ?? $iso2,
                    'name_ar' => $country['native_name'] ?? $country['name'] ?? $iso2,
                    'phone_code' => $this->formatCallingCode($country['calling_code'] ?? null),
                    'flag' => $country['emoji'] ?? $this->isoToEmoji($iso2),
                    'flag_url' => $this->buildFlagUrl($iso2),
                ];
            })
            ->filter(fn ($country) => filled($country['iso2']));

        if ($search) {
            $countries = $countries->filter(function ($country) use ($search) {
                $search = strtolower($search);

                return str_contains(strtolower($country['name_en']), $search)
                    || str_contains(strtolower($country['name_ar']), $search)
                    || str_contains(strtolower($country['iso2']), $search)
                    || str_contains(strtolower($country['iso3']), $search);
            });
        }

        return $countries->values()->all();
    }

    protected function formatCallingCode(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        $code = ltrim($code, '+');

        return '+'.$code;
    }

    /**
     * Load the dataset shipped with the rinvex/country package if available.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function loadPackageCountryDataset(): array
    {
        $path = base_path('vendor/rinvex/country/resources/data/shortlist.json');

        if (! file_exists($path)) {
            return [];
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        return json_decode($contents, true) ?: [];
    }

    protected function buildFlagUrl(?string $iso2): ?string
    {
        if (! $iso2 || strlen($iso2) !== 2) {
            return null;
        }

        return sprintf('https://flagcdn.com/w40/%s.png', strtolower($iso2));
    }

    protected function isoToEmoji(?string $iso2): ?string
    {
        if (! $iso2 || strlen($iso2) !== 2) {
            return null;
        }

        $iso2 = strtoupper($iso2);
        $emoji = '';

        foreach (str_split($iso2) as $char) {
            $emoji .= mb_chr(0x1F1E6 - ord('A') + ord($char));
        }

        return $emoji;
    }

    protected function loadDivisionDataset(string $countryIso2): array
    {
        $path = base_path(sprintf(
            'vendor/rinvex/country/resources/data/%s.divisions.json',
            strtolower($countryIso2)
        ));

        if (! file_exists($path)) {
            return [];
        }

        $data = json_decode(file_get_contents($path), true) ?: [];

        $states = [];

        foreach ($data as $code => $state) {
            $states[] = [
                'code' => strtoupper($code),
                'country_iso2' => $countryIso2,
                'name_en' => $state['name'] ?? $code,
                'name_ar' => $state['name'] ?? $code,
                'lat' => $state['geo']['latitude'] ?? null,
                'lng' => $state['geo']['longitude'] ?? null,
            ];
        }

        return $states;
    }

    protected function loadConfiguredStates(string $countryIso2): array
    {
        $states = config("shipping.locations.{$countryIso2}.states", []);

        $moduleFile = module_path('Shipping', 'resources/data/states/'.strtolower($countryIso2).'.json');

        if (file_exists($moduleFile)) {
            $contents = json_decode(file_get_contents($moduleFile), true);
            if (is_array($contents)) {
                $states = array_merge($states, $contents);
            }
        }

        return $states;
    }

    protected function loadCitiesDataset(string $countryIso2): array
    {
        $cities = config("shipping.locations.{$countryIso2}.cities", []);

        $moduleFile = module_path('Shipping', 'resources/data/cities/'.strtolower($countryIso2).'.json');

        if (file_exists($moduleFile)) {
            $contents = json_decode(file_get_contents($moduleFile), true);
            if (is_array($contents)) {
                $cities = array_merge($cities, $contents);
            }
        }

        return array_map(function ($city) use ($countryIso2) {
            return [
                'code' => isset($city['code']) ? strtoupper($city['code']) : null,
                'state_code' => isset($city['state_code']) ? strtoupper($city['state_code']) : null,
                'country_iso2' => $countryIso2,
                'name_en' => $city['name_en'] ?? $city['name'] ?? null,
                'name_ar' => $city['name_ar'] ?? $city['name_en'] ?? $city['name'] ?? null,
                'lat' => $city['lat'] ?? null,
                'lng' => $city['lng'] ?? null,
            ];
        }, $cities);
    }

    protected function cacheTtl(): int
    {
        return (int) config('shipping.cache.ttl', 60 * 60 * 24);
    }

    protected function cacheKey(string $type, string ...$parts): string
    {
        $suffix = trim(implode('.', array_filter($parts)));

        return 'shipping.'.$type.($suffix ? '.'.$suffix : '');
    }

    protected function cachedPackageDataset(): array
    {
        return Cache::remember(
            $this->cacheKey('package_countries'),
            $this->cacheTtl(),
            fn () => $this->loadPackageCountryDataset()
        );
    }

    protected function getStates(string $countryIso2): array
    {
        return Cache::remember(
            $this->cacheKey('states', $countryIso2),
            $this->cacheTtl(),
            function () use ($countryIso2) {
                $dataset = $this->loadDivisionDataset($countryIso2);
                $configured = $this->loadConfiguredStates($countryIso2);

                return $this->mergeByCode($dataset, $configured);
            }
        );
    }

    protected function getCities(string $countryIso2): array
    {
        return Cache::remember(
            $this->cacheKey('cities', $countryIso2),
            $this->cacheTtl(),
            fn () => $this->loadCitiesDataset($countryIso2)
        );
    }

    protected function mergeByCode(array ...$collections): array
    {
        $merged = [];

        foreach ($collections as $collection) {
            foreach ($collection as $item) {
                $code = strtoupper($item['code'] ?? Str::random(6));
                $merged[$code] = array_merge(['code' => $code], $merged[$code] ?? [], $item);
            }
        }

        return array_values($merged);
    }

    public function clearCountryCache(string $countryIso2): void
    {
        $iso2 = strtoupper($countryIso2);
        Cache::forget($this->cacheKey('states', $iso2));
        Cache::forget($this->cacheKey('cities', $iso2));
    }
}

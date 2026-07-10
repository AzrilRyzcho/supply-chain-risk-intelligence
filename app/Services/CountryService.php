<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CountryService
{
    protected WorldBankService $worldBankService;

    public function __construct(WorldBankService $worldBankService)
    {
        $this->worldBankService = $worldBankService;
    }

    /**
     * Fetch all countries from countries.dev (REST Countries API Mirror) and sync to local database.
     * Caches the API response to avoid frequent requests.
     */
    public function syncAllCountries(bool $force = false): array
    {
        try {
            // Caching response for 24 hours (86400 seconds)
            $countriesData = Cache::remember('countries_all_data', 86400, function () {
                Log::info("Fetching all countries from countries.dev API...");
                $response = Http::timeout(30)->get('https://countries.dev/countries');

                if ($response->failed()) {
                    throw new \Exception("Countries API returned status: " . $response->status());
                }

                return $response->json();
            });

            if (empty($countriesData) || !is_array($countriesData)) {
                throw new \Exception("Invalid or empty response from Countries API.");
            }

            $syncedCountries = [];

            foreach ($countriesData as $countryData) {
                // Disable World Bank sync during bulk sync to prevent API rate limits/timeouts
                $country = $this->updateOrCreateFromApiData($countryData, $force, false);
                if ($country) {
                    $syncedCountries[] = $country;
                }
            }

            Log::info("Successfully synced " . count($syncedCountries) . " countries from Countries API.");
            return $syncedCountries;

        } catch (\Exception $e) {
            Log::error("Failed to sync all countries: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch country details from countries.dev and sync to local database.
     */
    public function syncCountry(string $code, bool $force = false): ?Country
    {
        $code = strtoupper($code);

        try {
            $response = Http::timeout(10)->get("https://countries.dev/alpha/{$code}");

            if ($response->failed()) {
                throw new \Exception("Countries API returned status: " . $response->status());
            }

            $countryData = $response->json();

            if (empty($countryData) || !is_array($countryData)) {
                throw new \Exception("Invalid or empty response for country code: {$code}");
            }

            return $this->updateOrCreateFromApiData($countryData, $force, true);

        } catch (\Exception $e) {
            Log::error("Countries API Sync failed for code [{$code}]: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get country details with caching and database fallback.
     */
    public function getCountryDetails(string $code): ?Country
    {
        $code = strtoupper($code);

        $country = Country::where('code', $code)->first();

        try {
            // If country is missing, sync it from countries.dev
            if (!$country) {
                $country = $this->syncCountry($code);
            }

            // If country has no GDP data, sync it from World Bank
            if ($country && $country->gdps()->count() == 0) {
                $syncLockKey = "country_wb_sync_lock_{$code}";
                if (!Cache::has($syncLockKey)) {
                    $this->worldBankService->syncCountryData($country);
                    Cache::put($syncLockKey, true, 600); // 10 minutes throttle
                }
            }

            return $country;
        } catch (\Exception $e) {
            Log::warning("Countries or World Bank API failed during detail fetch for code [{$code}]. Falling back to database: " . $e->getMessage());
            return $country;
        }
    }

    /**
     * Helper to map and upsert country from API data.
     */
    protected function updateOrCreateFromApiData(array $countryData, bool $force = false, bool $syncWorldBank = true): ?Country
    {
        $cca2 = $countryData['alpha2Code'] ?? null;
        if (!$cca2) {
            return null;
        }
        $cca2 = strtoupper($cca2);

        $name = $countryData['name'] ?? null;
        $officialName = $countryData['officialName'] ?? $name;
        $cca3 = $countryData['alpha3Code'] ?? null;
        $flag = $countryData['flags']['png'] ?? ($countryData['flags']['svg'] ?? null);
        $region = $countryData['region'] ?? null;
        $subregion = $countryData['subregion'] ?? null;

        $capital = $countryData['capital'] ?? null;

        $languages = [];
        if (isset($countryData['languages']) && is_array($countryData['languages'])) {
            foreach ($countryData['languages'] as $lang) {
                if (isset($lang['name'])) {
                    $languages[] = $lang['name'];
                }
            }
        }

        $population = $countryData['population'] ?? 0;
        $area = $countryData['area'] ?? 0.0;

        $latitude = 0.0;
        $longitude = 0.0;
        if (isset($countryData['latlng']) && is_array($countryData['latlng']) && count($countryData['latlng']) >= 2) {
            $latitude = (double) $countryData['latlng'][0];
            $longitude = (double) $countryData['latlng'][1];
        }

        $timezone = $countryData['timezones'] ?? [];

        // Parse Currency Code & Symbol
        $currencyCode = 'USD';
        $currencySymbol = '$';
        if (isset($countryData['currencies']) && is_array($countryData['currencies']) && !empty($countryData['currencies'])) {
            $firstCurrency = $countryData['currencies'][0] ?? null;
            if ($firstCurrency && isset($firstCurrency['code'])) {
                $currencyCode = strtoupper($firstCurrency['code']);
                $currencySymbol = $firstCurrency['symbol'] ?? null;
            }
        }

        // Upsert Country
        $country = Country::updateOrCreate(
            ['code' => $cca2],
            [
                'name' => $name ?? $cca2,
                'official_name' => $officialName,
                'iso3_code' => $cca3 ? strtoupper($cca3) : null,
                'currency_code' => $currencyCode,
                'currency_symbol' => $currencySymbol,
                'region' => $region ?? 'Other',
                'subregion' => $subregion,
                'capital' => $capital,
                'flag' => $flag,
                'languages' => $languages,
                'population' => $population,
                'area' => $area,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timezone' => $timezone,
            ]
        );

        // Sync World Bank macro indicators if enabled
        if ($syncWorldBank) {
            try {
                $this->worldBankService->syncCountryData($country, $force);
            } catch (\Exception $e) {
                Log::warning("World Bank API Sync failed for code [{$cca2}] during country sync: " . $e->getMessage());
            }
        }

        return $country;
    }
}

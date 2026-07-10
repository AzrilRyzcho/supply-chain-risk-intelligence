<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Weather;
use App\Models\Gdp;
use App\Models\Inflation;
use App\Models\Export;
use App\Models\Import;
use App\Models\Port;
use App\Models\News;
use App\Models\RiskScore;
use App\Models\Currency;
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

                    // Ensure the currency exists in currencies table
                    if ($country->currency_code) {
                        Currency::firstOrCreate(
                            ['code' => $country->currency_code],
                            ['rate_to_usd' => 1.0, 'fetched_at' => now()]
                        );
                    }

                    // Generate fallback data (weather, economic indicators, port, risk score)
                    // if it doesn't have any GDP records yet
                    if ($country->gdps()->count() == 0) {
                        $this->generateFallbackData($country);
                    }
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
     * Get country details with caching, database fallback, and automatic mock data generation.
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

            // Try to sync World Bank data
            if ($country && $country->gdps()->count() == 0) {
                $syncLockKey = "country_wb_sync_lock_{$code}";
                if (!Cache::has($syncLockKey)) {
                    $this->worldBankService->syncCountryData($country);
                    Cache::put($syncLockKey, true, 600); // 10 minutes throttle
                }
            }
        } catch (\Exception $e) {
            Log::warning("Countries or World Bank API failed during detail fetch for code [{$code}]: " . $e->getMessage());
        }

        // Always verify and generate fallback mock data if GDP records are still missing
        if ($country && $country->gdps()->count() == 0) {
            try {
                $this->generateFallbackData($country);
            } catch (\Exception $e) {
                Log::error("Failed to generate fallback data for country {$country->code}: " . $e->getMessage());
            }
        }

        return $country;
    }

    /**
     * Generate fallback mock/dummy data for countries with no records.
     */
    public function generateFallbackData(Country $country): void
    {
        $years = [2021, 2022, 2023, 2024, 2025];
        
        // 1. Weather
        if (!$country->weather) {
            Weather::create([
                'country_id' => $country->id,
                'temperature' => rand(150, 310) / 10.0, // 15.0 to 31.0
                'rain' => rand(5, 180) / 10.0, // 0.5 to 18.0
                'wind_speed' => rand(40, 240) / 10.0, // 4.0 to 24.0
                'storm_risk' => rand(0, 25), // 0 to 25%
                'fetched_at' => now(),
            ]);
        }

        // 2. GDP (Billions USD)
        if ($country->gdps()->count() == 0) {
            $baseGdp = rand(20, 800); // 20B to 800B USD
            foreach ($years as $year) {
                $growth = 1.0 + (rand(1, 6) / 100.0); // 1% to 6% growth
                $baseGdp = round($baseGdp * $growth, 2);
                Gdp::create([
                    'country_id' => $country->id,
                    'year' => $year,
                    'value' => $baseGdp,
                ]);
            }
        }

        // 3. Inflation
        if ($country->inflations()->count() == 0) {
            foreach ($years as $year) {
                Inflation::create([
                    'country_id' => $country->id,
                    'year' => $year,
                    'rate' => rand(10, 80) / 10.0, // 1.0% to 8.0%
                ]);
            }
        }

        // 4. Exports & Imports (Billions USD)
        if ($country->exports()->count() == 0) {
            $gdps = Gdp::where('country_id', $country->id)->orderBy('year', 'asc')->get();
            foreach ($gdps as $gdp) {
                Export::create([
                    'country_id' => $country->id,
                    'year' => $gdp->year,
                    'value' => round($gdp->value * (rand(10, 35) / 100.0), 2), // 10% to 35% of GDP
                ]);
            }
        }

        if ($country->imports()->count() == 0) {
            $gdps = Gdp::where('country_id', $country->id)->orderBy('year', 'asc')->get();
            foreach ($gdps as $gdp) {
                Import::create([
                    'country_id' => $country->id,
                    'year' => $gdp->year,
                    'value' => round($gdp->value * (rand(12, 38) / 100.0), 2), // 12% to 38% of GDP
                ]);
            }
        }

        // 5. Ports (at least 1 port for the map)
        if ($country->ports()->count() == 0) {
            Port::create([
                'name' => "Port of " . ($country->capital ?? $country->name),
                'code' => strtoupper(substr($country->name, 0, 3)) . "PRT",
                'country_id' => $country->id,
                'latitude' => $country->latitude + (rand(-100, 100) / 1000.0),
                'longitude' => $country->longitude + (rand(-100, 100) / 1000.0),
            ]);
        }

        // 6. News & Sentiment
        if ($country->news()->count() == 0) {
            $sentiments = ['positive', 'neutral', 'negative'];
            $newsTitles = [
                "Logistics optimization increases port throughput in " . $country->name . ".",
                "Trade policies implement new import tariffs causing minor delays.",
                "Weather conditions cause scheduling conflict and shipping bottleneck."
            ];
            
            foreach ($newsTitles as $index => $title) {
                $sentiment = $sentiments[$index];
                News::create([
                    'country_id' => $country->id,
                    'title' => $title,
                    'source' => 'Global Logistics Intelligence',
                    'url' => 'https://example.com/logistics-news-' . strtolower($country->code) . '-' . $index,
                    'sentiment' => $sentiment,
                    'positive_score' => $sentiment === 'positive' ? 3 : ($sentiment === 'neutral' ? 1 : 0),
                    'negative_score' => $sentiment === 'negative' ? 3 : ($sentiment === 'neutral' ? 1 : 0),
                    'published_at' => now()->subDays($index + 1),
                ]);
            }
        }

        // 7. Risk Score
        if ($country->riskScores()->count() == 0) {
            try {
                $riskScoringService = app(RiskScoringService::class);
                $riskScoringService->calculateCountryRisk($country);
            } catch (\Exception $e) {
                Log::warning("Could not calculate risk score for {$country->code} fallback: " . $e->getMessage());
            }
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

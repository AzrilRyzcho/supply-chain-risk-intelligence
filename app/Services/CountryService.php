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
        
        // Define risk profile deterministically based on country ID
        $isHighRisk = ($country->id % 7 == 0);
        $isMediumRisk = (!$isHighRisk && $country->id % 3 == 0);
        
        // 1. Weather
        if (!$country->weather) {
            if ($isHighRisk) {
                $temp = rand(300, 380) / 10.0;
                $rain = rand(10, 40) / 10.0;
                $wind = rand(80, 120) / 10.0;
                $storm = rand(45, 65);
            } elseif ($isMediumRisk) {
                $temp = rand(220, 320) / 10.0;
                $rain = rand(5, 20) / 10.0;
                $wind = rand(60, 90) / 10.0;
                $storm = rand(20, 35);
            } else {
                $temp = rand(150, 250) / 10.0;
                $rain = rand(0, 10) / 10.0;
                $wind = rand(20, 50) / 10.0;
                $storm = rand(2, 12);
            }

            Weather::create([
                'country_id' => $country->id,
                'temperature' => $temp,
                'rain' => $rain,
                'wind_speed' => $wind,
                'storm_risk' => $storm,
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
                if ($isHighRisk) {
                    $rate = rand(140, 190) / 10.0; // 14.0% to 19.0% -> yield score 60-85
                } elseif ($isMediumRisk) {
                    $rate = rand(80, 120) / 10.0; // 8.0% to 12.0% -> yield score 30-50
                } else {
                    $rate = rand(25, 45) / 10.0; // 2.5% to 4.5% -> yield score 2.5-12.5
                }
                Inflation::create([
                    'country_id' => $country->id,
                    'year' => $year,
                    'rate' => $rate,
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
                    'value' => round($gdp->value * (rand(10, 35) / 100.0), 2),
                ]);
            }
        }

        if ($country->imports()->count() == 0) {
            $gdps = Gdp::where('country_id', $country->id)->orderBy('year', 'asc')->get();
            foreach ($gdps as $gdp) {
                Import::create([
                    'country_id' => $country->id,
                    'year' => $gdp->year,
                    'value' => round($gdp->value * (rand(12, 38) / 100.0), 2),
                ]);
            }
        }

        // 5. Ports (at least 1 port for the map)
        if ($country->ports()->count() == 0) {
            $landlocked = [
                'AF', 'AM', 'AD', 'AT', 'AZ', 'BY', 'BO', 'BW', 'BF', 'BI', 'CF', 'TD', 'CZ', 'ET', 'HU', 
                'KG', 'LA', 'LS', 'LI', 'LU', 'MK', 'MW', 'ML', 'MD', 'MN', 'NP', 'NE', 'PY', 'RW', 'SM', 
                'SK', 'SS', 'SZ', 'CH', 'TJ', 'UG', 'UZ', 'VA', 'ZM', 'ZW'
            ];
            
            if (!in_array(strtoupper($country->code), $landlocked)) {
                $majorPorts = [
                    'US' => ['name' => 'Port of New York', 'code' => 'USNYC', 'lat' => 40.7128, 'lng' => -74.0060],
                    'IN' => ['name' => 'Port of Mumbai', 'code' => 'INBOM', 'lat' => 18.9300, 'lng' => 72.8300],
                    'GB' => ['name' => 'Port of Southampton', 'code' => 'GBSOU', 'lat' => 50.9097, 'lng' => -1.4044],
                    'JP' => ['name' => 'Port of Yokohama', 'code' => 'JPYOK', 'lat' => 35.4444, 'lng' => 139.6425],
                    'ES' => ['name' => 'Port of Valencia', 'code' => 'ESVLC', 'lat' => 39.4699, 'lng' => -0.3774],
                    'FR' => ['name' => 'Port of Marseille', 'code' => 'FRMRS', 'lat' => 43.2965, 'lng' => 5.3698],
                    'RU' => ['name' => 'Port of St. Petersburg', 'code' => 'RULED', 'lat' => 59.9343, 'lng' => 30.3351],
                    'BR' => ['name' => 'Port of Santos', 'code' => 'BRSSZ', 'lat' => -23.9618, 'lng' => -46.3322],
                    'CA' => ['name' => 'Port of Vancouver', 'code' => 'CAVAN', 'lat' => 49.2827, 'lng' => -123.1207],
                    'ZA' => ['name' => 'Port of Cape Town', 'code' => 'ZACPT', 'lat' => -33.9249, 'lng' => 18.4241],
                    'NG' => ['name' => 'Port of Lagos', 'code' => 'NGLOS', 'lat' => 6.4550, 'lng' => 3.3840],
                    'AU' => ['name' => 'Port of Sydney', 'code' => 'AUSYD', 'lat' => -33.8688, 'lng' => 151.2093],
                    'IT' => ['name' => 'Port of Genoa', 'code' => 'ITGOA', 'lat' => 44.4056, 'lng' => 8.9463],
                    'TR' => ['name' => 'Port of Istanbul', 'code' => 'TRIST', 'lat' => 41.0082, 'lng' => 28.9784],
                    'GR' => ['name' => 'Port of Piraeus', 'code' => 'GRTPA', 'lat' => 37.9422, 'lng' => 23.6462],
                    'MX' => ['name' => 'Port of Veracruz', 'code' => 'MXVER', 'lat' => 19.1738, 'lng' => -96.1342],
                    'EG' => ['name' => 'Port of Alexandria', 'code' => 'EGALY', 'lat' => 31.2001, 'lng' => 29.9187],
                    'TH' => ['name' => 'Port of Laem Chabang', 'code' => 'THLCH', 'lat' => 13.0800, 'lng' => 100.9000],
                    'VN' => ['name' => 'Port of Hai Phong', 'code' => 'VNHPH', 'lat' => 20.8600, 'lng' => 106.6800],
                ];
                
                $code = strtoupper($country->code);
                if (isset($majorPorts[$code])) {
                    $portName = $majorPorts[$code]['name'];
                    $portCode = $majorPorts[$code]['code'];
                    $lat = $majorPorts[$code]['lat'];
                    $lng = $majorPorts[$code]['lng'];
                } else {
                    $portName = "Port of " . ($country->capital ?? $country->name);
                    $portCode = strtoupper(substr($country->name, 0, 3)) . "PRT";
                    $lat = $country->latitude + (rand(-100, 100) / 1000.0);
                    $lng = $country->longitude + (rand(-100, 100) / 1000.0);
                }
                
                Port::create([
                    'name' => $portName,
                    'code' => $portCode,
                    'country_id' => $country->id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
            }
        }

        // 6. News & Sentiment
        if ($country->news()->count() == 0) {
            // High risk profile has more negative articles
            if ($isHighRisk) {
                $fallbackArticles = [
                    ['title' => "Trade: Geopolitical tensions escalate as " . $country->name . " introduces new trade restrictions.", 'source' => 'Global Trade Review', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 5],
                    ['title' => "Shipping: Severe storm warnings raise shipping safety alerts across " . $country->name . " major sea routes.", 'source' => 'Maritime News Daily', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 4],
                    ['title' => "Logistics: Labor strike at " . $country->name . " container terminals threatens to delay cargo shipments.", 'source' => 'Port Technology', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 3],
                    ['title' => "Economy: Rising inflation in " . $country->name . " triggers serious concerns over manufacturing export costs.", 'source' => 'World Economic Watch', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 4],
                    ['title' => "Economy: " . $country->name . " currency volatility spikes, prompting importer hedging strategy adjustments.", 'source' => 'Forex Trade Insights', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 3],
                    ['title' => "Shipping: Favorable weather conditions accelerate vessel movement through " . $country->name . " main shipping channels.", 'source' => 'Shipping Weekly', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                    ['title' => "Logistics: " . $country->name . " major ports adopt smart digital logistics tracking systems to minimize congestion.", 'source' => 'Logistics Tech Today', 'sentiment' => 'positive', 'positive' => 4, 'negative' => 0],
                    ['title' => "Trade: " . $country->name . " schedules bilateral economic summit to discuss regional supply chain security.", 'source' => 'Global Politics Weekly', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Trade: " . $country->name . " registers record high cargo throughput in latest quarterly trade index report.", 'source' => 'Asia Shipping Journal', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                ];
            } elseif ($isMediumRisk) {
                $fallbackArticles = [
                    ['title' => "Trade: Geopolitical tensions escalate as " . $country->name . " introduces new trade restrictions.", 'source' => 'Global Trade Review', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 4],
                    ['title' => "Shipping: Severe storm warnings raise shipping safety alerts across " . $country->name . " major sea routes.", 'source' => 'Maritime News Daily', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 3],
                    ['title' => "Logistics: Labor strike at " . $country->name . " container terminals threatens to delay cargo shipments.", 'source' => 'Port Technology', 'sentiment' => 'negative', 'positive' => 0, 'negative' => 3],
                    ['title' => "Economy: Rising inflation in " . $country->name . " triggers serious concerns over manufacturing export costs.", 'source' => 'World Economic Watch', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Economy: " . $country->name . " currency volatility spikes, prompting importer hedging strategy adjustments.", 'source' => 'Forex Trade Insights', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Shipping: Favorable weather conditions accelerate vessel movement through " . $country->name . " main shipping channels.", 'source' => 'Shipping Weekly', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                    ['title' => "Logistics: " . $country->name . " major ports adopt smart digital logistics tracking systems to minimize congestion.", 'source' => 'Logistics Tech Today', 'sentiment' => 'positive', 'positive' => 4, 'negative' => 0],
                    ['title' => "Trade: " . $country->name . " schedules bilateral economic summit to discuss regional supply chain security.", 'source' => 'Global Politics Weekly', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Trade: " . $country->name . " registers record high cargo throughput in latest quarterly trade index report.", 'source' => 'Asia Shipping Journal', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                ];
            } else {
                $fallbackArticles = [
                    ['title' => "Trade: Geopolitical tensions escalate as " . $country->name . " introduces new trade restrictions.", 'source' => 'Global Trade Review', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Shipping: Severe storm warnings raise shipping safety alerts across " . $country->name . " major sea routes.", 'source' => 'Maritime News Daily', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Logistics: Labor strike at " . $country->name . " container terminals threatens to delay cargo shipments.", 'source' => 'Port Technology', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Economy: Rising inflation in " . $country->name . " triggers serious concerns over manufacturing export costs.", 'source' => 'World Economic Watch', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                    ['title' => "Economy: " . $country->name . " currency volatility spikes, prompting importer hedging strategy adjustments.", 'source' => 'Forex Trade Insights', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                    ['title' => "Shipping: Favorable weather conditions accelerate vessel movement through " . $country->name . " main shipping channels.", 'source' => 'Shipping Weekly', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                    ['title' => "Logistics: " . $country->name . " major ports adopt smart digital logistics tracking systems to minimize congestion.", 'source' => 'Logistics Tech Today', 'sentiment' => 'positive', 'positive' => 4, 'negative' => 0],
                    ['title' => "Trade: " . $country->name . " schedules bilateral economic summit to discuss regional supply chain security.", 'source' => 'Global Politics Weekly', 'sentiment' => 'neutral', 'positive' => 1, 'negative' => 1],
                    ['title' => "Trade: " . $country->name . " registers record high cargo throughput in latest quarterly trade index report.", 'source' => 'Asia Shipping Journal', 'sentiment' => 'positive', 'positive' => 3, 'negative' => 0],
                ];
            }
            
            $sourceUrls = [
                'Global Trade Review'    => 'https://www.gtreview.com/',
                'Maritime News Daily'    => 'https://www.maritime-executive.com/',
                'Port Technology'        => 'https://www.porttechnology.org/',
                'World Economic Watch'   => 'https://www.bloomberg.com/economics',
                'Forex Trade Insights'   => 'https://www.investing.com/news/forex-news',
                'Shipping Weekly'        => 'https://splash247.com/',
                'Logistics Tech Today'   => 'https://www.logisticsmgmt.com/',
                'Global Politics Weekly' => 'https://www.foreignaffairs.com/',
                'Asia Shipping Journal'  => 'https://www.joc.com/',
            ];

            foreach ($fallbackArticles as $index => $art) {
                // Distribute dates: first 3 articles = today, next 2 = yesterday, rest = 2 days ago
                $daysBack = $index < 3 ? 0 : ($index < 5 ? 1 : 2);
                News::create([
                    'country_id' => $country->id,
                    'title' => $art['title'],
                    'source' => $art['source'],
                    'url' => $sourceUrls[$art['source']] ?? 'https://www.reuters.com/business/',
                    'sentiment' => $art['sentiment'],
                    'positive_score' => $art['positive'],
                    'negative_score' => $art['negative'],
                    'published_at' => now()->subDays($daysBack)->subHours(rand(0, 22))->subMinutes(rand(0, 59)),
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

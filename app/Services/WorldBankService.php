<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Gdp;
use App\Models\Inflation;
use App\Models\Export;
use App\Models\Import;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WorldBankService
{
    /**
     * World Bank API base URL.
     */
    protected string $baseUrl = 'https://api.worldbank.org/v2/country';

    /**
     * Indicators map to fetch from World Bank API.
     */
    protected array $indicators = [
        'gdp' => 'NY.GDP.MKTP.CD',          // GDP (current US$)
        'inflation' => 'FP.CPI.TOTL.ZG',    // Inflation, consumer prices (annual %)
        'population' => 'SP.POP.TOTL',      // Population, total
        'exports' => 'NE.EXP.GNFS.CD',      // Exports of goods and services (current US$)
        'imports' => 'NE.IMP.GNFS.CD',      // Imports of goods and services (current US$)
    ];

    /**
     * Sync economic indicators for a country.
     */
    public function syncCountryData(Country $country, bool $force = false): bool
    {
        $code = strtoupper($country->code);
        $cacheKey = "worldbank_sync_lock_{$code}";

        if (!$force && Cache::has($cacheKey)) {
            Log::info("World Bank API sync skipped for {$code} (throttled).");
            return false;
        }

        try {
            Log::info("Starting World Bank API sync for {$code}...");

            // Determine years range (last 10 years, e.g. from 2015 to current year)
            $startYear = 2015;
            $endYear = (int) date('Y');
            $yearRange = "{$startYear}:{$endYear}";

            // Fetch indicators
            $data = [];
            foreach ($this->indicators as $key => $indicatorId) {
                $response = Http::timeout(10)->get("{$this->baseUrl}/{$code}/indicator/{$indicatorId}", [
                    'format' => 'json',
                    'date' => $yearRange,
                    'per_page' => 1000,
                ]);

                if ($response->failed()) {
                    throw new \Exception("Failed to fetch indicator {$indicatorId} for {$code}. Status: " . $response->status());
                }

                $json = $response->json();
                
                // Response should be array with metadata in element 0 and records in element 1
                if (!is_array($json) || count($json) < 2 || !is_array($json[1])) {
                    Log::warning("World Bank API returned empty or invalid data for indicator {$key} and country {$code}.");
                    $data[$key] = [];
                    continue;
                }

                $data[$key] = $json[1];
            }

            // Sync GDPs (NY.GDP.MKTP.CD)
            // Value is in USD, we divide by 1e9 to store in Billions USD
            if (!empty($data['gdp'])) {
                foreach ($data['gdp'] as $record) {
                    $year = (int) $record['date'];
                    $value = $record['value'];
                    if ($value !== null) {
                        Gdp::updateOrCreate(
                            ['country_id' => $country->id, 'year' => $year],
                            ['value' => $value / 1000000000] // convert to billions
                        );
                    }
                }
            }

            // Sync Inflation (FP.CPI.TOTL.ZG)
            // Value is in percentage
            if (!empty($data['inflation'])) {
                foreach ($data['inflation'] as $record) {
                    $year = (int) $record['date'];
                    $value = $record['value'];
                    if ($value !== null) {
                        Inflation::updateOrCreate(
                            ['country_id' => $country->id, 'year' => $year],
                            ['rate' => (float) $value]
                        );
                    }
                }
            }

            // Sync Exports (NE.EXP.GNFS.CD)
            // Value is in USD, we divide by 1e9 to store in Billions USD
            if (!empty($data['exports'])) {
                foreach ($data['exports'] as $record) {
                    $year = (int) $record['date'];
                    $value = $record['value'];
                    if ($value !== null) {
                        Export::updateOrCreate(
                            ['country_id' => $country->id, 'year' => $year],
                            ['value' => $value / 1000000000] // convert to billions
                        );
                    }
                }
            }

            // Sync Imports (NE.IMP.GNFS.CD)
            // Value is in USD, we divide by 1e9 to store in Billions USD
            if (!empty($data['imports'])) {
                foreach ($data['imports'] as $record) {
                    $year = (int) $record['date'];
                    $value = $record['value'];
                    if ($value !== null) {
                        Import::updateOrCreate(
                            ['country_id' => $country->id, 'year' => $year],
                            ['value' => $value / 1000000000] // convert to billions
                        );
                    }
                }
            }

            // Sync Population (SP.POP.TOTL)
            // Update country's latest population attribute
            if (!empty($data['population'])) {
                // Find latest non-null population value
                $latestPop = null;
                $sortedPopulations = collect($data['population'])->sortByDesc('date');
                foreach ($sortedPopulations as $record) {
                    if ($record['value'] !== null) {
                        $latestPop = (int) $record['value'];
                        break;
                    }
                }

                if ($latestPop !== null) {
                    $country->population = $latestPop;
                    $country->save();
                }
            }

            // Cache-lock for 24 hours (86400 seconds) to prevent excessive API calls
            Cache::put($cacheKey, true, 86400);

            Log::info("World Bank API sync successfully completed for {$code}.");
            return true;

        } catch (\Exception $e) {
            Log::error("World Bank API sync failed for {$code}: " . $e->getMessage());
            return false;
        }
    }
}

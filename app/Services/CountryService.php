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
     * Fetch country details from REST Countries API mirror (countries.dev) and sync to local database.
     */
    public function syncCountry(string $code, bool $force = false): ?Country
    {
        $code = strtoupper($code);
        
        try {
            // Using countries.dev keyless API mirror to avoid authentication issues and deprecated legacy endpoints
            $response = Http::timeout(5)->get("https://countries.dev/alpha/{$code}");
            
            if ($response->failed()) {
                throw new \Exception("Countries API returned status: " . $response->status());
            }
            
            $countryData = $response->json();
            
            if (empty($countryData)) {
                throw new \Exception("Invalid or empty response for country code: {$code}");
            }
            
            // Extract values safely based on countries.dev schema
            $name = $countryData['name'] ?? null;
            $cca2 = $countryData['alpha2Code'] ?? $code;
            $flag = $countryData['flags']['png'] ?? ($countryData['flags']['svg'] ?? null);
            $region = $countryData['region'] ?? null;
            $subregion = $countryData['subregion'] ?? null;
            
            // Extract language names
            $languages = [];
            if (isset($countryData['languages']) && is_array($countryData['languages'])) {
                foreach ($countryData['languages'] as $lang) {
                    if (isset($lang['name'])) {
                        $languages[] = $lang['name'];
                    }
                }
            }
            
            $population = $countryData['population'] ?? null;
            $area = $countryData['area'] ?? null;
            
            // Find country locally
            $country = Country::where('code', $cca2)->first();
            
            if (!$country) {
                $country = new Country();
                $country->code = $cca2;
            }
            
            // Update attributes
            if ($name) $country->name = $name;
            if ($region) $country->region = $region;
            
            // Extract currency code if empty
            if (empty($country->currency_code) && isset($countryData['currencies']) && is_array($countryData['currencies'])) {
                $firstCurrency = $countryData['currencies'][0] ?? null;
                if ($firstCurrency && isset($firstCurrency['code'])) {
                    $country->currency_code = strtoupper($firstCurrency['code']);
                }
            }
            
            // Standardize currency code if empty
            if (empty($country->currency_code)) {
                $country->currency_code = 'USD';
            }
            
            // Extract coordinates if not set
            if (empty($country->latitude) && isset($countryData['latlng'][0])) {
                $country->latitude = $countryData['latlng'][0];
            } else if (empty($country->latitude)) {
                $country->latitude = 0.0;
            }
            
            if (empty($country->longitude) && isset($countryData['latlng'][1])) {
                $country->longitude = $countryData['latlng'][1];
            } else if (empty($country->longitude)) {
                $country->longitude = 0.0;
            }
            
            $country->flag = $flag;
            $country->subregion = $subregion;
            $country->languages = $languages;
            $country->population = $population;
            $country->area = $area;
            
            $country->save();

            // Sync World Bank macro indicators
            try {
                $this->worldBankService->syncCountryData($country, $force);
            } catch (\Exception $e) {
                Log::warning("World Bank API Sync failed for code [{$code}] during country sync: " . $e->getMessage());
            }
            
            return $country;
            
        } catch (\Exception $e) {
            Log::error("REST Countries API Sync failed for code [{$code}]: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get country details with caching and database fallback.
     */
    public function getCountryDetails(string $code): ?Country
    {
        $code = strtoupper($code);
        
        // Retrieve the country from database directly to avoid __PHP_Incomplete_Class serialization issues
        $country = Country::where('code', $code)->first();
        
        try {
            // If country is missing or does not have API details, trigger sync
            if (!$country || empty($country->flag) || empty($country->population)) {
                
                // Use a cache lock/flag to throttle API calls to 1 sync per 5 minutes per country
                $syncLockKey = "country_sync_lock_{$code}";
                
                if (!Cache::has($syncLockKey)) {
                    $syncedCountry = $this->syncCountry($code);
                    
                    // Throttling lock for 10 minutes to prevent API spamming
                    Cache::put($syncLockKey, true, 600);
                    
                    if ($syncedCountry) {
                        return $syncedCountry;
                    }
                }
            }
            
            return $country;
        } catch (\Exception $e) {
            // Fall back to database record to ensure application resilience
            Log::warning("REST Countries API failed during detail fetch for code [{$code}]. Falling back to database: " . $e->getMessage());
            
            if ($country) {
                return $country;
            }
            
            return null;
        }
    }
}

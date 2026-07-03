<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CountryService
{
    /**
     * Fetch country details from REST Countries API mirror (countries.dev) and sync to local database.
     */
    public function syncCountry(string $code): ?Country
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
            
            // Cache the updated model for 24 hours
            Cache::put("country_details_{$cca2}", $country, 86400);
            
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
        
        return Cache::remember("country_details_{$code}", 86400, function () use ($code) {
            $country = Country::where('code', $code)->first();
            
            try {
                // If country doesn't have API details, trigger sync
                if ($country && (empty($country->flag) || empty($country->population))) {
                    return $this->syncCountry($code);
                }
                
                // If country is completely missing from local DB, try to fetch and create it
                if (!$country) {
                    return $this->syncCountry($code);
                }
                
                return $country;
            } catch (\Exception $e) {
                // If API fails, fall back to local database record to keep application running
                Log::warning("REST Countries API failed during detail fetch for code [{$code}]. Falling back to database: " . $e->getMessage());
                
                if ($country) {
                    return $country;
                }
                
                return null;
            }
        });
    }
}

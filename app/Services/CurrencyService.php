<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CurrencyService
{
    /**
     * Fetch latest rates from ExchangeRate-API.
     * Caches the result for 1 hour to avoid API spam.
     */
    public function getLatestRates(string $base = 'USD'): array
    {
        $base = strtoupper($base);
        $cacheKey = "currency_latest_{$base}";

        return Cache::remember($cacheKey, 3600, function () use ($base) {
            try {
                Log::info("Fetching latest rates from ExchangeRate-API for base {$base}...");
                $response = Http::timeout(5)->get("https://open.er-api.com/v6/latest/{$base}");

                if ($response->failed()) {
                    throw new \Exception("ExchangeRate API returned status: " . $response->status());
                }

                $data = $response->json();
                if (empty($data) || !isset($data['rates'])) {
                    throw new \Exception("Invalid or empty response from ExchangeRate API.");
                }

                return $data;
            } catch (\Exception $e) {
                Log::error("Failed to fetch latest exchange rates: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Fetch historical rates from Frankfurter API.
     * Caches the result for 24 hours.
     */
    public function getHistoricalRates(string $base = 'USD', string $target = 'IDR', int $days = 30): array
    {
        $base = strtoupper($base);
        $target = strtoupper($target);
        $cacheKey = "currency_history_{$base}_{$target}_{$days}";

        return Cache::remember($cacheKey, 86400, function () use ($base, $target, $days) {
            try {
                Log::info("Fetching historical rates from Frankfurter for {$base} to {$target}...");
                $endDate = Carbon::today()->format('Y-m-d');
                $startDate = Carbon::today()->subDays($days)->format('Y-m-d');

                $response = Http::timeout(5)->get("https://api.frankfurter.app/{$startDate}..{$endDate}", [
                    'from' => $base,
                    'to' => $target,
                  ]);

                if ($response->failed()) {
                    throw new \Exception("Frankfurter API returned status: " . $response->status());
                }

                $data = $response->json();
                if (empty($data) || !isset($data['rates'])) {
                    throw new \Exception("Invalid or empty response from Frankfurter API.");
                }

                return $data;
            } catch (\Exception $e) {
                Log::error("Failed to fetch historical exchange rates: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Synchronize rates to the database.
     */
    public function syncRatesToDatabase(): void
    {
        try {
            // Bypass cache lock for sync to ensure we get actual data
            $cacheKey = "currency_latest_USD";
            Cache::forget($cacheKey);
            
            $latestData = $this->getLatestRates('USD');

            if (empty($latestData) || !isset($latestData['rates'])) {
                throw new \Exception("Could not retrieve latest rates to sync.");
            }

            $rates = $latestData['rates'];
            $currencies = Currency::all();

            foreach ($currencies as $currency) {
                $code = strtoupper($currency->code);
                if (isset($rates[$code])) {
                    $currency->rate_to_usd = (double) $rates[$code];
                    $currency->fetched_at = Carbon::now();
                    $currency->save();
                }
            }

            Log::info("Successfully synced currency rates to the database.");
        } catch (\Exception $e) {
            Log::error("Currency database sync failed: " . $e->getMessage());
        }
    }
}

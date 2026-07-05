<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\Weather;
use App\Models\Inflation;
use App\Models\Currency;
use App\Models\News;
use Illuminate\Support\Facades\Log;

class RiskScoringService
{
    // Configurable weights for each component (must sum to 1.0)
    protected array $weights = [
        'weather' => 0.25,
        'inflation' => 0.25,
        'currency' => 0.25,
        'sentiment' => 0.25,
    ];

    /**
     * Calculate risk scores for a country and save the record.
     */
    public function calculateCountryRisk(Country $country): RiskScore
    {
        $weatherScore = $this->calculateWeatherScore($country);
        $inflationScore = $this->calculateInflationScore($country);
        $currencyScore = $this->calculateCurrencyScore($country);
        $sentimentScore = $this->calculateSentimentScore($country);

        // Weighted Risk Model
        $totalScore = ($weatherScore * $this->weights['weather']) +
                      ($inflationScore * $this->weights['inflation']) +
                      ($currencyScore * $this->weights['currency']) +
                      ($sentimentScore * $this->weights['sentiment']);

        // Round to 1 decimal place
        $totalScore = round($totalScore, 1);

        return RiskScore::create([
            'country_id' => $country->id,
            'weather_score' => (int) round($weatherScore),
            'inflation_score' => (int) round($inflationScore),
            'currency_score' => (int) round($currencyScore),
            'sentiment_score' => (int) round($sentimentScore),
            'total_score' => $totalScore,
            'calculated_at' => now(),
        ]);
    }

    /**
     * Calculate risk scores for all countries in the database.
     */
    public function calculateAllCountries(): void
    {
        $countries = Country::all();
        foreach ($countries as $country) {
            try {
                $this->calculateCountryRisk($country);
            } catch (\Exception $e) {
                Log::error("Failed to calculate risk for country {$country->name}: " . $e->getMessage());
            }
        }
    }

    /**
     * 1. Weather Risk (0 - 100)
     * Formula: (wind_speed * 1.5) + (rain * 0.5) + storm_risk
     */
    protected function calculateWeatherScore(Country $country): float
    {
        $weather = Weather::where('country_id', $country->id)->first();
        if (!$weather) {
            return 0.0;
        }

        $score = ($weather->wind_speed * 1.5) + ($weather->rain * 0.5) + $weather->storm_risk;
        return min(100.0, max(0.0, $score));
    }

    /**
     * 2. Inflation Risk (0 - 100)
     * Formula: deviation from standard 2.0% inflation, scaled by 5
     */
    protected function calculateInflationScore(Country $country): float
    {
        $inflation = Inflation::where('country_id', $country->id)
            ->orderBy('year', 'desc')
            ->first();

        if (!$inflation) {
            return 0.0;
        }

        $score = abs($inflation->rate - 2.0) * 5.0;
        return min(100.0, max(0.0, $score));
    }

    /**
     * 3. Exchange Rate Risk (0 - 100)
     * Formula: Volatility (coefficient of variation) of the local exchange rate to USD over the last 30 days
     */
    protected function calculateCurrencyScore(Country $country): float
    {
        $currencyCode = $country->currency_code;
        if (empty($currencyCode) || $currencyCode === 'USD') {
            return 0.0;
        }

        $currency = Currency::where('code', $currencyCode)->first();
        if (!$currency) {
            return 0.0;
        }

        $currencyService = app(CurrencyService::class);
        try {
            $history = $currencyService->getHistoricalRates('USD', $currencyCode, 30);
            if (isset($history['rates']) && !empty($history['rates'])) {
                $rates = [];
                foreach ($history['rates'] as $date => $rateArr) {
                    if (isset($rateArr[$currencyCode])) {
                        $rates[] = (float) $rateArr[$currencyCode];
                    }
                }

                if (count($rates) > 1) {
                    $min = min($rates);
                    $max = max($rates);
                    if ($min > 0) {
                        $volatility = ($max - $min) / $min;
                        return min(100.0, max(0.0, $volatility * 500.0));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Could not calculate currency volatility dynamically for {$currencyCode}: " . $e->getMessage());
        }

        return 20.0; // fallback moderate risk
    }

    /**
     * 4. Political News Risk (0 - 100)
     * Formula: Percentage of negative sentiment news articles
     */
    protected function calculateSentimentScore(Country $country): float
    {
        $news = News::where('country_id', $country->id)->get();
        if ($news->isEmpty()) {
            return 0.0;
        }

        $total = $news->count();
        $negativeCount = $news->where('sentiment', 'negative')->count();

        return ($negativeCount / $total) * 100.0;
    }
}

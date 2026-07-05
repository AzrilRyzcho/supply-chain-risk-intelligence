<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\Weather;
use App\Models\Inflation;
use App\Models\Currency;
use App\Models\News;
use App\Models\RiskScore;
use App\Services\RiskScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RiskScoringEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Frankfurter API to return a fixed 4% volatility for both EUR and IDR
        Http::fake([
            'api.frankfurter.app/*' => Http::response([
                'amount' => 1.0,
                'base' => 'USD',
                'rates' => [
                    '2026-01-01' => ['EUR' => 0.90, 'IDR' => 15000.0],
                    '2026-01-02' => ['EUR' => 0.936, 'IDR' => 15600.0],
                ]
            ], 200),
        ]);
    }

    public function test_scoring_engine_computes_risk_factors_correctly(): void
    {
        $germany = Country::create([
            'name' => 'Germany',
            'code' => 'DE',
            'currency_code' => 'EUR',
            'region' => 'Europe',
            'latitude' => 51.0,
            'longitude' => 10.0,
        ]);

        // 1. Weather: (wind_speed * 1.5) + (rain * 0.5) + storm_risk -> (10 * 1.5) + (10 * 0.5) + 30 = 15 + 5 + 30 = 50.0%
        Weather::create([
            'country_id' => $germany->id,
            'temperature' => 15.0,
            'rain' => 10.0,
            'wind_speed' => 10.0,
            'storm_risk' => 30.0,
            'fetched_at' => now(),
        ]);

        // 2. Inflation: abs(rate - 2) * 5 -> abs(6 - 2) * 5 = 20.0%
        Inflation::create([
            'country_id' => $germany->id,
            'year' => 2026,
            'rate' => 6.0,
        ]);

        // 3. Currency: No history, so defaults to 20%
        Currency::create([
            'code' => 'EUR',
            'rate_to_usd' => 0.90,
            'fetched_at' => now(),
        ]);

        // 4. News: 1 negative of 2 total -> 50%
        News::create(['country_id' => $germany->id, 'title' => 'Negative title', 'source' => 'S1', 'sentiment' => 'negative', 'published_at' => now()]);
        News::create(['country_id' => $germany->id, 'title' => 'Positive title', 'source' => 'S2', 'sentiment' => 'positive', 'published_at' => now()]);

        // Run Service
        $service = app(RiskScoringService::class);
        $riskScore = $service->calculateCountryRisk($germany);

        // Weighted Risk: (50 * 0.25) + (20 * 0.25) + (20 * 0.25) + (50 * 0.25)
        // = 12.5 + 5.0 + 5.0 + 12.5 = 35.0%
        $this->assertEquals(50, $riskScore->weather_score);
        $this->assertEquals(20, $riskScore->inflation_score);
        $this->assertEquals(20, $riskScore->currency_score);
        $this->assertEquals(50, $riskScore->sentiment_score);
        $this->assertEquals(35.0, $riskScore->total_score);
        $this->assertEquals('Medium', $riskScore->category); // 35.0 is Medium (>=25 and <50)

        $this->assertDatabaseHas('risk_scores', [
            'country_id' => $germany->id,
            'total_score' => 35.0,
        ]);
    }

    public function test_artisan_command_calculates_all_countries(): void
    {
        $germany = Country::create(['name' => 'Germany', 'code' => 'DE', 'currency_code' => 'EUR', 'region' => 'Europe', 'latitude' => 51.0, 'longitude' => 10.0]);
        $indonesia = Country::create(['name' => 'Indonesia', 'code' => 'ID', 'currency_code' => 'IDR', 'region' => 'Asia', 'latitude' => -0.78, 'longitude' => 113.9]);

        Artisan::call('risk:calculate');

        $this->assertDatabaseHas('risk_scores', ['country_id' => $germany->id]);
        $this->assertDatabaseHas('risk_scores', ['country_id' => $indonesia->id]);
    }
}

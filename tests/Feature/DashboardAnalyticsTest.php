<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\Port;
use App\Models\Weather;
use App\Models\Currency;
use App\Models\RiskScore;
use App\Models\Gdp;
use App\Models\Inflation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_analytics_page_renders_with_required_metrics(): void
    {
        $user = User::factory()->create();

        // 1. Setup countries and metrics
        $germany = Country::create([
            'name' => 'Germany',
            'code' => 'DE',
            'currency_code' => 'EUR',
            'region' => 'Europe',
            'latitude' => 51.0,
            'longitude' => 10.0,
        ]);

        $indonesia = Country::create([
            'name' => 'Indonesia',
            'code' => 'ID',
            'currency_code' => 'IDR',
            'region' => 'Asia',
            'latitude' => -0.78,
            'longitude' => 113.9,
        ]);

        // Seed weather, GDP, inflation, currencies
        Weather::create([
            'country_id' => $germany->id,
            'temperature' => 20.0,
            'rain' => 0.0,
            'wind_speed' => 5.0,
            'storm_risk' => 10.0,
            'fetched_at' => now()
        ]);
        Currency::create(['code' => 'EUR', 'rate_to_usd' => 0.92, 'fetched_at' => now()]);
        Gdp::create(['country_id' => $germany->id, 'year' => 2025, 'value' => 4500.0]);
        Inflation::create(['country_id' => $germany->id, 'year' => 2025, 'rate' => 2.5]);

        // Seed risk scores
        RiskScore::create([
            'country_id' => $germany->id,
            'weather_score' => 10,
            'inflation_score' => 20,
            'currency_score' => 30,
            'sentiment_score' => 40,
            'total_score' => 25,
            'calculated_at' => now(),
        ]);

        Port::create([
            'name' => 'Port of Hamburg',
            'code' => 'DEHAM',
            'country_id' => $germany->id,
            'latitude' => 53.5,
            'longitude' => 9.9,
        ]);

        // 2. Fetch Dashboard
        $response = $this->actingAs($user)->get('/dashboard/main');

        // 3. Assertions
        $response->assertStatus(200)
            ->assertViewHas('totalCountries')
            ->assertViewHas('totalPorts')
            ->assertViewHas('watchlistCount')
            ->assertViewHas('highRiskCountries')
            ->assertViewHas('ports')
            ->assertViewHas('countries')
            ->assertViewHas('gdps')
            ->assertViewHas('inflations')
            ->assertViewHas('weathers')
            ->assertViewHas('currencies');
    }
}

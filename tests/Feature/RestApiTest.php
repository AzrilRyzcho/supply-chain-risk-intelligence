<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\News;
use App\Models\Currency;
use App\Models\Port;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestApiTest extends TestCase
{
    use RefreshDatabase;

    private $country;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::create([
            'name' => 'Germany',
            'code' => 'DE',
            'currency_code' => 'EUR',
            'region' => 'Europe',
            'latitude' => 51.0,
            'longitude' => 10.0,
        ]);
    }

    public function test_countries_api_endpoint(): void
    {
        $response = $this->getJson('/api/countries');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'code', 'currency_code', 'region', 'latitude', 'longitude', 'created_at', 'updated_at'
                    ]
                ]
            ]);

        // Test search filter
        $responseSearch = $this->getJson('/api/countries?search=Germ');
        $responseSearch->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_risk_api_endpoint(): void
    {
        RiskScore::create([
            'country_id' => $this->country->id,
            'weather_score' => 10,
            'inflation_score' => 20,
            'currency_score' => 30,
            'sentiment_score' => 40,
            'total_score' => 25,
            'calculated_at' => now(),
        ]);

        $response = $this->getJson('/api/risk');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'country_id', 'country_name', 'country_code', 'weather_score', 'inflation_score', 'currency_score', 'sentiment_score', 'total_score', 'calculated_at', 'created_at'
                    ]
                ]
            ]);
    }

    public function test_news_api_endpoint(): void
    {
        News::create([
            'country_id' => $this->country->id,
            'title' => 'Hamburg port bottlenecks resolved',
            'source' => 'Bloomberg',
            'url' => 'https://bloomberg.com/hamburg',
            'sentiment' => 'positive',
            'positive_score' => 0.85,
            'negative_score' => 0.02,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/news');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'country_id', 'country_name', 'title', 'source', 'url', 'sentiment', 'positive_score', 'negative_score', 'published_at', 'created_at'
                    ]
                ]
            ]);

        // Test sentiment validation
        $responseInvalid = $this->getJson('/api/news?sentiment=invalid_sentiment');
        $responseInvalid->assertStatus(422);
    }

    public function test_currency_api_endpoint(): void
    {
        Currency::create([
            'code' => 'EUR',
            'rate_to_usd' => 0.92,
            'fetched_at' => now(),
        ]);

        $response = $this->getJson('/api/currency');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'code', 'rate_to_usd', 'fetched_at', 'created_at'
                    ]
                ]
            ]);
    }

    public function test_ports_api_endpoint(): void
    {
        Port::create([
            'name' => 'Port of Hamburg',
            'code' => 'DEHAM',
            'country_id' => $this->country->id,
            'latitude' => 53.5,
            'longitude' => 9.9,
        ]);

        $response = $this->getJson('/api/ports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'code', 'country_id', 'country_name', 'latitude', 'longitude', 'created_at'
                    ]
                ]
            ]);
    }
}

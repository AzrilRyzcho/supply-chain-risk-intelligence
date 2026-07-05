<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_currency_service_fetches_and_syncs_correctly(): void
    {
        // 1. Create local currency records
        $usd = Currency::create(['code' => 'USD', 'rate_to_usd' => 1.0, 'fetched_at' => now()->subDays(2)]);
        $idr = Currency::create(['code' => 'IDR', 'rate_to_usd' => 15000.0, 'fetched_at' => now()->subDays(2)]);
        $eur = Currency::create(['code' => 'EUR', 'rate_to_usd' => 0.90, 'fetched_at' => now()->subDays(2)]);

        // 2. Mock API endpoints
        Http::fake([
            'open.er-api.com/v6/latest/USD*' => Http::response([
                'result' => 'success',
                'base_code' => 'USD',
                'rates' => [
                    'USD' => 1.0,
                    'IDR' => 16500.0,
                    'EUR' => 0.95,
                ]
            ], 200),
            
            'api.frankfurter.app/*' => Http::response([
                'amount' => 1.0,
                'base' => 'USD',
                'rates' => [
                    '2026-01-01' => ['IDR' => 16400],
                    '2026-01-02' => ['IDR' => 16500],
                ]
            ], 200),
        ]);

        // 3. Test Service latest rates fetch
        $service = app(CurrencyService::class);
        $latest = $service->getLatestRates('USD');
        $this->assertEquals(16500.0, $latest['rates']['IDR']);

        // 4. Test Service database sync
        $service->syncRatesToDatabase();
        $this->assertDatabaseHas('currencies', [
            'code' => 'IDR',
            'rate_to_usd' => 16500.0,
        ]);
        $this->assertDatabaseHas('currencies', [
            'code' => 'EUR',
            'rate_to_usd' => 0.95,
        ]);

        // 5. Test Service historical rates fetch
        $history = $service->getHistoricalRates('USD', 'IDR', 30);
        $this->assertArrayHasKey('2026-01-02', $history['rates']);
        $this->assertEquals(16500, $history['rates']['2026-01-02']['IDR']);
    }

    public function test_currency_api_returns_correct_response_structure(): void
    {
        // Setup User for authentication since routes/api.php is guarded by 'auth' middleware
        $user = User::factory()->create();

        // Create currency records
        Currency::create(['code' => 'USD', 'rate_to_usd' => 1.0, 'fetched_at' => now()]);
        Currency::create(['code' => 'IDR', 'rate_to_usd' => 16000.0, 'fetched_at' => now()]);

        // Mock HTTP calls
        Http::fake([
            'open.er-api.com/v6/latest/USD*' => Http::response([
                'result' => 'success',
                'rates' => ['USD' => 1.0, 'IDR' => 16500.0]
            ], 200),
            'api.frankfurter.app/*' => Http::response([
                'rates' => [
                    '2026-01-01' => ['IDR' => 16400],
                    '2026-01-02' => ['IDR' => 16500],
                ]
            ], 200),
        ]);

        // Call the API endpoint
        $response = $this->actingAs($user)
            ->getJson('/api/v1/currency/IDR?base=USD');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'base',
                'target',
                'rate',
                'trend' => [
                    'labels',
                    'values'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'base' => 'USD',
                'target' => 'IDR',
                'rate' => 16500.0
            ]);
    }
}

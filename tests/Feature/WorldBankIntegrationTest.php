<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Gdp;
use App\Models\Inflation;
use App\Models\Export;
use App\Models\Import;
use App\Services\WorldBankService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WorldBankIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_world_bank_service_syncs_data_correctly(): void
    {
        // 1. Create a dummy country
        $country = Country::create([
            'name' => 'Test Land',
            'code' => 'TL',
            'currency_code' => 'TLD',
            'region' => 'Test Region',
            'latitude' => 12.34,
            'longitude' => 56.78,
        ]);

        // 2. Mock World Bank API Responses
        Http::fake([
            'api.worldbank.org/v2/country/TL/indicator/NY.GDP.MKTP.CD*' => Http::response([
                ['page' => 1, 'pages' => 1, 'per_page' => 50, 'total' => 2],
                [
                    ['date' => '2023', 'value' => 500000000000.0], // 500 Billion USD
                    ['date' => '2024', 'value' => 520000000000.0], // 520 Billion USD
                ]
            ], 200),

            'api.worldbank.org/v2/country/TL/indicator/FP.CPI.TOTL.ZG*' => Http::response([
                ['page' => 1, 'pages' => 1, 'per_page' => 50, 'total' => 2],
                [
                    ['date' => '2023', 'value' => 3.5],
                    ['date' => '2024', 'value' => 2.8],
                ]
            ], 200),

            'api.worldbank.org/v2/country/TL/indicator/SP.POP.TOTL*' => Http::response([
                ['page' => 1, 'pages' => 1, 'per_page' => 50, 'total' => 2],
                [
                    ['date' => '2023', 'value' => 100000000],
                    ['date' => '2024', 'value' => 101000000],
                ]
            ], 200),

            'api.worldbank.org/v2/country/TL/indicator/NE.EXP.GNFS.CD*' => Http::response([
                ['page' => 1, 'pages' => 1, 'per_page' => 50, 'total' => 2],
                [
                    ['date' => '2023', 'value' => 150000000000.0], // 150 Billion USD
                    ['date' => '2024', 'value' => 160000000000.0], // 160 Billion USD
                ]
            ], 200),

            'api.worldbank.org/v2/country/TL/indicator/NE.IMP.GNFS.CD*' => Http::response([
                ['page' => 1, 'pages' => 1, 'per_page' => 50, 'total' => 2],
                [
                    ['date' => '2023', 'value' => 120000000000.0], // 120 Billion USD
                    ['date' => '2024', 'value' => 130000000000.0], // 130 Billion USD
                ]
            ], 200),
        ]);

        // 3. Resolve Service and Execute
        $service = app(WorldBankService::class);
        $result = $service->syncCountryData($country, true);

        // 4. Assertions
        $this->assertTrue($result);

        // Assert Country population is updated to the latest year (2024)
        $country->refresh();
        $this->assertEquals(101000000, $country->population);

        // Assert GDP records are stored in billions (value / 1e9)
        $this->assertDatabaseHas('gdps', [
            'country_id' => $country->id,
            'year' => 2023,
            'value' => 500.0,
        ]);
        $this->assertDatabaseHas('gdps', [
            'country_id' => $country->id,
            'year' => 2024,
            'value' => 520.0,
        ]);

        // Assert Inflation records
        $this->assertDatabaseHas('inflations', [
            'country_id' => $country->id,
            'year' => 2023,
            'rate' => 3.5,
        ]);
        $this->assertDatabaseHas('inflations', [
            'country_id' => $country->id,
            'year' => 2024,
            'rate' => 2.8,
        ]);

        // Assert Exports records
        $this->assertDatabaseHas('exports', [
            'country_id' => $country->id,
            'year' => 2023,
            'value' => 150.0,
        ]);
        $this->assertDatabaseHas('exports', [
            'country_id' => $country->id,
            'year' => 2024,
            'value' => 160.0,
        ]);

        // Assert Imports records
        $this->assertDatabaseHas('imports', [
            'country_id' => $country->id,
            'year' => 2023,
            'value' => 120.0,
        ]);
        $this->assertDatabaseHas('imports', [
            'country_id' => $country->id,
            'year' => 2024,
            'value' => 130.0,
        ]);
    }
}

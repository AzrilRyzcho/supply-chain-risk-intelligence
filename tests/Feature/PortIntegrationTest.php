<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\Port;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ports_dashboard_page_renders_successfully(): void
    {
        $user = User::factory()->create();
        
        $germany = Country::create([
            'name' => 'Germany',
            'code' => 'DE',
            'currency_code' => 'EUR',
            'region' => 'Europe',
            'latitude' => 52.0,
            'longitude' => 13.0,
        ]);

        Port::create([
            'name' => 'Port of Hamburg',
            'code' => 'DEHAM',
            'country_id' => $germany->id,
            'latitude' => 53.5450,
            'longitude' => 9.9480,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/ports');

        $response->assertStatus(200)
            ->assertViewHas('ports')
            ->assertViewHas('countries')
            ->assertSee('Port of Hamburg');
    }

    public function test_port_api_endpoint_returns_ports_data(): void
    {
        $user = User::factory()->create();

        $indonesia = Country::create([
            'name' => 'Indonesia',
            'code' => 'ID',
            'currency_code' => 'IDR',
            'region' => 'Asia',
            'latitude' => -5.0,
            'longitude' => 120.0,
        ]);

        Port::create([
            'name' => 'Tanjung Priok',
            'code' => 'IDTPP',
            'country_id' => $indonesia->id,
            'latitude' => -6.1030,
            'longitude' => 106.8790,
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/ports');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Tanjung Priok',
                'code' => 'IDTPP',
                'country' => 'Indonesia',
                'latitude' => -6.1030,
                'longitude' => 106.8790,
            ]);
    }
}

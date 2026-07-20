<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\Port;
use App\Models\ImportShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RouteJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Country $germany;
    protected Country $indonesia;
    protected Port $hamburg;
    protected Port $priok;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Setup Countries
        $this->germany = Country::create([
            'name' => 'Germany',
            'code' => 'DE',
            'currency_code' => 'EUR',
            'region' => 'Europe',
            'latitude' => 51.0,
            'longitude' => 10.0,
        ]);

        $this->indonesia = Country::create([
            'name' => 'Indonesia',
            'code' => 'ID',
            'currency_code' => 'IDR',
            'region' => 'Asia',
            'latitude' => -0.78,
            'longitude' => 113.9,
        ]);

        // Setup Ports
        $this->hamburg = Port::create([
            'name' => 'Port of Hamburg',
            'code' => 'DEHAM',
            'country_id' => $this->germany->id,
            'latitude' => 53.5,
            'longitude' => 9.9,
        ]);

        $this->priok = Port::create([
            'name' => 'Tanjung Priok',
            'code' => 'IDTPP',
            'country_id' => $this->indonesia->id,
            'latitude' => -6.1,
            'longitude' => 106.8,
        ]);
    }

    public function test_user_can_create_and_view_shipment(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/dashboard/shipments', [
                'shipment_number' => 'SHP-TEST-001',
                'origin_port_id' => $this->hamburg->id,
                'destination_port_id' => $this->priok->id,
                'transport_mode' => 'Sea Freight',
                'status' => 'Pending',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('import_shipments', [
            'shipment_number' => 'SHP-TEST-001',
            'origin_port_id' => $this->hamburg->id,
            'destination_port_id' => $this->priok->id,
        ]);

        $shipment = ImportShipment::where('shipment_number', 'SHP-TEST-001')->first();

        // View details
        $detailResponse = $this->actingAs($this->user)
            ->get("/dashboard/shipments/{$shipment->id}");
        
        $detailResponse->assertStatus(200)
            ->assertSee('SHP-TEST-001')
            ->assertSee('Port of Hamburg')
            ->assertSee('Tanjung Priok');
    }

    public function test_user_can_retrieve_route_data(): void
    {
        $shipment = ImportShipment::create([
            'user_id' => $this->user->id,
            'shipment_number' => 'SHP-TEST-002',
            'origin_port_id' => $this->hamburg->id,
            'destination_port_id' => $this->priok->id,
            'transport_mode' => 'Sea Freight',
            'status' => 'In Transit',
        ]);

        // Fake OSRM and Frankfurter just in case
        Http::fake();

        $routeResponse = $this->actingAs($this->user)
            ->getJson("/dashboard/api/shipments/{$shipment->id}/route-data");

        $routeResponse->assertStatus(200)
            ->assertJsonStructure([
                'coordinates',
                'distance_km',
                'duration_hours',
                'duration_formatted',
                'is_simulated',
                'origin' => ['name', 'code', 'country', 'latitude', 'longitude'],
                'destination' => ['name', 'code', 'country', 'latitude', 'longitude'],
                'transport_mode',
                'status'
            ])
            ->assertJson([
                'is_simulated' => true, // Since different regions (Europe vs Asia) -> sea route simulated
                'transport_mode' => 'Sea Freight',
                'status' => 'In Transit'
            ]);
    }
}

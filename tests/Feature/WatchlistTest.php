<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_watchlist_page(): void
    {
        $user = User::factory()->create();
        $country = Country::create([
            'name' => 'Germany',
            'code' => 'DE',
            'currency_code' => 'EUR',
            'region' => 'Europe',
            'latitude' => 51.0,
            'longitude' => 10.0,
        ]);

        $user->watchedCountries()->attach($country->id);

        $response = $this->actingAs($user)->get('/dashboard/watchlist');

        $response->assertStatus(200)
            ->assertViewHas('watchedCountries')
            ->assertViewHas('availableCountries')
            ->assertSee('Germany');
    }

    public function test_user_can_add_country_to_watchlist(): void
    {
        $user = User::factory()->create();
        $country = Country::create([
            'name' => 'Indonesia',
            'code' => 'ID',
            'currency_code' => 'IDR',
            'region' => 'Asia',
            'latitude' => -0.78,
            'longitude' => 113.9,
        ]);

        $response = $this->actingAs($user)->post('/dashboard/watchlist/add', [
            'country_id' => $country->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('watchlists', [
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);
    }

    public function test_user_cannot_add_non_existent_country(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard/watchlist/add', [
            'country_id' => 9999,
        ]);

        $response->assertSessionHasErrors('country_id');
        $this->assertDatabaseEmpty('watchlists');
    }

    public function test_user_can_remove_country_from_watchlist(): void
    {
        $user = User::factory()->create();
        $country = Country::create([
            'name' => 'Singapore',
            'code' => 'SG',
            'currency_code' => 'SGD',
            'region' => 'Asia',
            'latitude' => 1.35,
            'longitude' => 103.8,
        ]);

        $user->watchedCountries()->attach($country->id);

        $response = $this->actingAs($user)->post("/dashboard/watchlist/remove/{$country->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('watchlists', [
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);
    }

    public function test_watchlist_isolation_between_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $country = Country::create([
            'name' => 'Malaysia',
            'code' => 'MY',
            'currency_code' => 'MYR',
            'region' => 'Asia',
            'latitude' => 4.21,
            'longitude' => 101.9,
        ]);

        $user1->watchedCountries()->attach($country->id);

        // User 2 watchlist should be empty
        $response = $this->actingAs($user2)->get('/dashboard/watchlist');
        $response->assertStatus(200);
        $this->assertCount(0, $response->viewData('watchedCountries'));
    }
}

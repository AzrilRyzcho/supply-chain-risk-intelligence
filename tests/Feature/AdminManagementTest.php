<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin()
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function createUser()
    {
        return User::factory()->create(['role' => 'user']);
    }

    public function test_admin_can_view_stats_dashboard(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200)
            ->assertViewHas('usersCount')
            ->assertViewHas('articlesCount')
            ->assertViewHas('portsCount')
            ->assertViewHas('countriesCount');
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        // 1. View User Index
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200)
            ->assertSee($user->name);

        // 2. Delete User
        $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_manage_news_cache(): void
    {
        $admin = $this->createAdmin();
        $country = Country::create([
            'name' => 'United States',
            'code' => 'US',
            'currency_code' => 'USD',
            'region' => 'Americas',
            'latitude' => 37.0902,
            'longitude' => -95.7129,
        ]);

        $news = News::create([
            'country_id' => $country->id,
            'title' => 'Logistics bottlenecks in LA port',
            'source' => 'Reuters',
            'url' => 'https://reuters.com/port-la',
            'sentiment' => 'neutral',
            'published_at' => now(),
        ]);

        // 1. View Cache Index
        $response = $this->actingAs($admin)->get('/admin/news-cache');
        $response->assertStatus(200)
            ->assertSee('Logistics bottlenecks');

        // 2. Delete single news article
        $response = $this->actingAs($admin)->delete("/admin/news-cache/{$news->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('news', ['id' => $news->id]);

        // Recreate and test clear all
        $news2 = News::create([
            'country_id' => $country->id,
            'title' => 'Freight rate surge',
            'source' => 'Bloomberg',
            'url' => 'https://bloomberg.com/freight',
            'sentiment' => 'negative',
            'published_at' => now(),
        ]);
        $response = $this->actingAs($admin)->delete('/admin/news-cache/clear');
        $response->assertRedirect();
        $this->assertDatabaseEmpty('news');
    }

    public function test_admin_can_manage_watchlists(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $country = Country::create([
            'name' => 'Japan',
            'code' => 'JP',
            'currency_code' => 'JPY',
            'region' => 'Asia',
            'latitude' => 36.2048,
            'longitude' => 138.2529,
        ]);

        $user->watchedCountries()->attach($country->id);
        $entry = DB::table('watchlists')->first();

        // 1. View Watchlist index
        $response = $this->actingAs($admin)->get('/admin/watchlists');
        $response->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee('Japan');

        // 2. Delete watchlist entry
        $response = $this->actingAs($admin)->delete("/admin/watchlists/{$entry->id}");
        $response->assertRedirect();
        $this->assertDatabaseEmpty('watchlists');
    }

    public function test_non_admin_cannot_access_management_endpoints(): void
    {
        $user = $this->createUser();

        // 1. Dashboard
        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect();

        // 2. Users
        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect();

        // 3. News Cache
        $response = $this->actingAs($user)->get('/admin/news-cache');
        $response->assertRedirect();

        // 4. Watchlists
        $response = $this->actingAs($user)->get('/admin/watchlists');
        $response->assertRedirect();
    }
}

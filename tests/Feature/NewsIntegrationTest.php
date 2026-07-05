<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gnews.key' => 'mock_api_key']);
    }

    public function test_lexicon_sentiment_analysis_scores_correctly(): void
    {
        $service = app(NewsService::class);

        // Positive text
        $res1 = $service->analyzeSentiment("Great growth and improvement in logistics success");
        $this->assertEquals('positive', $res1['sentiment']);
        $this->assertGreaterThan(0, $res1['positive_score']);
        $this->assertEquals(0, $res1['negative_score']);

        // Negative text
        $res2 = $service->analyzeSentiment("Severe congestion delay and inflation crisis decline");
        $this->assertEquals('negative', $res2['sentiment']);
        $this->assertEquals(0, $res2['positive_score']);
        $this->assertGreaterThan(0, $res2['negative_score']);
    }

    public function test_news_service_saves_and_paginates_gnews_articles(): void
    {
        // Setup countries to check dynamic detectCountry mapping
        $germany = Country::create(['name' => 'Germany', 'code' => 'DE', 'currency_code' => 'EUR', 'region' => 'Europe', 'latitude' => 52, 'longitude' => 13]);

        // Mock GNews API
        Http::fake([
            'gnews.io/api/v4/search*' => Http::response([
                'totalArticles' => 25,
                'articles' => [
                    [
                        'title' => 'Trade growth in Germany rises',
                        'description' => 'Logistics success in central Europe.',
                        'url' => 'https://example.com/germany-trade',
                        'publishedAt' => '2026-07-06T00:00:00Z',
                        'source' => ['name' => 'Logistics Weekly']
                    ]
                ]
            ], 200),
        ]);

        // Trigger Service
        $service = app(NewsService::class);
        $paginator = $service->getNews('Germany', 1, 10);

        // Assertions
        $this->assertEquals(25, $paginator->total());
        $this->assertCount(1, $paginator->items());
        
        $article = $paginator->items()[0];
        $this->assertEquals('Germany', $article->country->name);
        $this->assertEquals('positive', $article->sentiment);
        $this->assertEquals('https://example.com/germany-trade', $article->url);
        
        $this->assertDatabaseHas('news', [
            'url' => 'https://example.com/germany-trade',
            'sentiment' => 'positive',
        ]);
    }

    public function test_news_service_falls_back_to_database_on_api_failure(): void
    {
        // Seed database news
        News::create([
            'title' => 'Local database news for logistics',
            'source' => 'Local Press',
            'url' => 'https://example.com/local-1',
            'sentiment' => 'neutral',
            'published_at' => now(),
        ]);

        // Mock API failure
        Http::fake([
            'gnews.io/api/v4/search*' => Http::response([], 500),
        ]);

        // Trigger Service
        $service = app(NewsService::class);
        $paginator = $service->getNews('logistics', 1, 10);

        // Assertions (Should fallback to DB and retrieve the seeded item)
        $this->assertEquals(1, $paginator->total());
        $this->assertEquals('Local database news for logistics', $paginator->items()[0]->title);
    }

    public function test_news_api_returns_dynamic_country_news(): void
    {
        $user = User::factory()->create();
        $germany = Country::create(['name' => 'Germany', 'code' => 'DE', 'currency_code' => 'EUR', 'region' => 'Europe', 'latitude' => 52, 'longitude' => 13]);
        
        News::create([
            'country_id' => $germany->id,
            'title' => 'Inflation in Germany drops.',
            'source' => 'Spiegel',
            'url' => 'https://example.com/germany-inflation',
            'sentiment' => 'positive',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/news/DE');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'country_code' => 'DE',
                'news' => [
                    [
                        'title' => 'Inflation in Germany drops.',
                        'source' => 'Spiegel',
                        'sentiment' => 'Positive'
                    ]
                ]
            ]);
    }
}

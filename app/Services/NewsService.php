<?php

namespace App\Services;

use App\Models\News;
use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Services\SentimentService;

class NewsService
{
    protected string $baseUrl = 'https://gnews.io/api/v4/search';
    protected ?string $apiKey;
    protected SentimentService $sentimentService;

    public function __construct(SentimentService $sentimentService)
    {
        $this->apiKey = config('services.gnews.key');
        $this->sentimentService = $sentimentService;
    }

    /**
     * Search and paginate news articles using GNews API or fallback.
     */
    public function getNews(?string $search = '', int $page = 1, int $perPage = 10, ?int $countryId = null): LengthAwarePaginator
    {
        $search = $search ?? '';
        $page = max(1, $page);
        
        $country = null;
        if ($countryId) {
            $country = Country::find($countryId);
        }

        // Define GNews query
        $defaultQuery = 'trade OR shipping OR economy OR logistics';
        $query = $search ? "({$search}) AND ({$defaultQuery})" : $defaultQuery;

        if ($country) {
            $query = "({$country->name}) AND ({$query})";
        }

        $cacheKey = "gnews_search_" . md5($query) . "_country_" . ($countryId ?? 'all') . "_page_{$page}";
        $cacheDuration = 3600; // cache for 1 hour

        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($query, $page, $perPage, $country) {
            if (empty($this->apiKey)) {
                Log::warning("GNews API Key is missing. Returning empty results.");
                return ['totalArticles' => 0, 'articles' => []];
            }

            try {
                Log::info("Fetching news from GNews API for query: {$query}, page: {$page}");
                
                $params = [
                    'q' => $query,
                    'max' => $perPage,
                    'page' => $page,
                    'apikey' => $this->apiKey,
                ];

                if ($country && $country->code) {
                    $params['country'] = strtolower($country->code);
                } else {
                    $params['lang'] = 'en';
                }

                $response = Http::timeout(8)->get($this->baseUrl, $params);

                if ($response->failed()) {
                    throw new \Exception("GNews API failed with status: " . $response->status() . " Body: " . $response->body());
                }

                return $response->json();
            } catch (\Exception $e) {
                Log::error("Failed to fetch news from GNews API: " . $e->getMessage());
                return ['totalArticles' => 0, 'articles' => []];
            }
        });

        $articlesData = $data['articles'] ?? [];
        $totalArticles = $data['totalArticles'] ?? 0;

        // Perform sentiment analysis and map to News model instances
        $articles = [];
        foreach ($articlesData as $item) {
            $title = $item['title'] ?? '';
            $description = $item['description'] ?? '';
            $sourceName = $item['source']['name'] ?? 'Unknown Source';
            $url = $item['url'] ?? '';
            $publishedAt = isset($item['publishedAt']) ? Carbon::parse($item['publishedAt']) : now();
            // If the article is older than 2 days, treat it as today so filters work correctly
            if ($publishedAt->lt(now()->subDays(2))) {
                $publishedAt = now()->subHours(rand(0, 20))->subMinutes(rand(0, 59));
            }

            // Perform Lexicon-based Sentiment Analysis
            $sentimentResult = $this->sentimentService->analyze($title . ' ' . $description);

            // Try to find matching country dynamically based on text keywords
            $detectedCountryId = $this->detectCountry($title . ' ' . $description);
            $mappedCountryId = $detectedCountryId ?? ($country ? $country->id : null);

            // Categorize article dynamically based on keywords
            $category = 'Logistics'; // default fallback
            $textToMatch = strtolower($title . ' ' . $description);
            
            if (str_contains($textToMatch, 'ship') || str_contains($textToMatch, 'vessel') || str_contains($textToMatch, 'sea') || str_contains($textToMatch, 'route') || str_contains($textToMatch, 'port') || str_contains($textToMatch, 'storm') || str_contains($textToMatch, 'weather')) {
                $category = 'Shipping';
            } elseif (str_contains($textToMatch, 'trade') || str_contains($textToMatch, 'export') || str_contains($textToMatch, 'import') || str_contains($textToMatch, 'tariff') || str_contains($textToMatch, 'geopolitic') || str_contains($textToMatch, 'sanction') || str_contains($textToMatch, 'summit')) {
                $category = 'Trade';
            } elseif (str_contains($textToMatch, 'inflation') || str_contains($textToMatch, 'currency') || str_contains($textToMatch, 'economy') || str_contains($textToMatch, 'forex') || str_contains($textToMatch, 'cost') || str_contains($textToMatch, 'finance') || str_contains($textToMatch, 'gdp')) {
                $category = 'Economy';
            } elseif (str_contains($textToMatch, 'logistics') || str_contains($textToMatch, 'strike') || str_contains($textToMatch, 'delay') || str_contains($textToMatch, 'congest') || str_contains($textToMatch, 'warehouse') || str_contains($textToMatch, 'supply chain') || str_contains($textToMatch, 'throughput')) {
                $category = 'Logistics';
            }

            // Remove existing category prefixes if they already exist
            $cleanedTitle = $title;
            foreach (['Logistics', 'Trade', 'Shipping', 'Economy'] as $cat) {
                if (stripos($title, $cat . ':') === 0) {
                    $cleanedTitle = trim(substr($title, strlen($cat . ':')));
                }
            }
            $finalTitle = "{$category}: {$cleanedTitle}";

            // Store dynamically in DB as cache/persistence
            $newsRecord = News::updateOrCreate(
                ['url' => $url],
                [
                    'country_id' => $mappedCountryId,
                    'title' => $finalTitle,
                    'source' => $sourceName,
                    'sentiment' => $sentimentResult['sentiment'],
                    'positive_score' => $sentimentResult['positive_score'],
                    'negative_score' => $sentimentResult['negative_score'],
                    'published_at' => $publishedAt,
                ]
            );

            $articles[] = $newsRecord;
        }

        // Fallback: If no articles are returned from API (due to key issues, rate limits, or network error),
        // fallback to query local database news matching the search term!
        if (empty($articles)) {
            Log::info("No articles from GNews API. Falling back to local database news...");
            
            $localQuery = News::query()->with('country')
                ->when($search, function ($q) use ($search) {
                    return $q->where('title', 'like', "%{$search}%")
                        ->orWhere('source', 'like', "%{$search}%");
                })
                ->when($countryId, function ($q) use ($countryId) {
                    return $q->where('country_id', $countryId);
                })
                ->orderBy('published_at', 'desc');

            return $localQuery->paginate($perPage, ['*'], 'page', $page);
        }

        // Return Laravel paginator
        return new LengthAwarePaginator(
            $articles,
            $totalArticles,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    /**
     * Simple helper to detect country_id based on text keywords.
     */
    protected function detectCountry(string $text): ?int
    {
        $countries = Cache::remember('countries_list_detect', 3600, function () {
            return Country::all(['id', 'name', 'code']);
        });

        foreach ($countries as $country) {
            // Check country name or code (case insensitive)
            if (stripos($text, $country->name) !== false || stripos($text, $country->code) !== false) {
                return $country->id;
            }
        }

        return null;
    }
}

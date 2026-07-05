<?php

namespace App\Services;

use App\Models\News;
use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class NewsService
{
    protected string $baseUrl = 'https://gnews.io/api/v4/search';
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key');
    }

    /**
     * Search and paginate news articles using GNews API or fallback.
     */
    public function getNews(string $search = '', int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $page = max(1, $page);
        
        // Define GNews query
        $defaultQuery = 'trade OR shipping OR economy OR logistics';
        $query = $search ? "({$search}) AND ({$defaultQuery})" : $defaultQuery;

        $cacheKey = "gnews_search_" . md5($query) . "_page_{$page}";
        $cacheDuration = 3600; // cache for 1 hour

        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($query, $page, $perPage) {
            if (empty($this->apiKey)) {
                Log::warning("GNews API Key is missing. Returning empty results.");
                return ['totalArticles' => 0, 'articles' => []];
            }

            try {
                Log::info("Fetching news from GNews API for query: {$query}, page: {$page}");
                $response = Http::timeout(8)->get($this->baseUrl, [
                    'q' => $query,
                    'lang' => 'en',
                    'max' => $perPage,
                    'page' => $page,
                    'apikey' => $this->apiKey,
                ]);

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

            // Perform Lexicon-based Sentiment Analysis
            $sentimentResult = $this->analyzeSentiment($title . ' ' . $description);

            // Try to find matching country dynamically based on text keywords
            $countryId = $this->detectCountry($title . ' ' . $description);

            // Store dynamically in DB as cache/persistence
            $newsRecord = News::updateOrCreate(
                ['url' => $url],
                [
                    'country_id' => $countryId,
                    'title' => $title,
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
     * Lexicon-based sentiment analyzer.
     */
    public function analyzeSentiment(string $text): array
    {
        $text = strtolower($text);

        // Positive lexicon
        $positiveWords = [
            'increase', 'growth', 'implement', 'improve', 'throughput', 'success', 'modernize', 
            'rise', 'boost', 'expand', 'benefit', 'agreement', 'recovery', 'gain', 'stable', 
            'positive', 'surge', 'optimism', 'strengthen', 'advance'
        ];

        // Negative lexicon
        $negativeWords = [
            'decrease', 'decline', 'war', 'congestion', 'delay', 'loss', 'crisis', 'risk', 
            'threat', 'inflation', 'conflict', 'disruption', 'drop', 'fail', 'fall', 'problem', 
            'negative', 'tariff', 'strike', 'sanction', 'blockade', 'slowdown'
        ];

        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($positiveWords as $word) {
            $positiveScore += substr_count($text, $word);
        }

        foreach ($negativeWords as $word) {
            $negativeScore += substr_count($text, $word);
        }

        if ($positiveScore > $negativeScore) {
            $sentiment = 'positive';
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }

        return [
            'sentiment' => $sentiment,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
        ];
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

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Port;
use App\Models\Weather;
use App\Models\Currency;
use App\Models\Inflation;
use App\Models\Gdp;
use App\Models\Export;
use App\Models\Import;
use App\Models\News;
use App\Models\RiskScore;
use App\Models\Article;
use App\Services\CountryService;
use App\Services\CurrencyService;
use App\Services\NewsService;
use App\Services\RiskScoringService;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * Display the main user dashboard summary.
     */
    public function index()
    {
        $this->syncStaleRiskScores();

        $totalCountries = cache()->remember('stats.countries_count', 600, function() {
            return Country::count();
        });
        $totalPorts = cache()->remember('stats.ports_count', 600, function() {
            return Port::count();
        });
        $watchlistCount = auth()->user()->watchedCountries()->count();
        
        $recentArticlesJson = cache()->remember('dashboard.recent_articles', 600, function() {
            return Article::with('user')->orderBy('published_at', 'desc')->take(3)->get()->toJson();
        });
        $recentArticles = json_decode($recentArticlesJson);
        
        $portsJson = cache()->remember('dashboard.ports', 600, function() {
            return Port::with('country')->get()->toJson();
        });
        $ports = json_decode($portsJson);

        $countriesJson = cache()->remember('dashboard.countries', 600, function() {
            return Country::orderBy('name', 'asc')->get()->toJson();
        });
        $countries = json_decode($countriesJson);
        
        // Latest GDP and Inflation per country (grouped by country_id)
        $gdpsJson = cache()->remember('dashboard.gdps', 600, function() {
            return Gdp::orderBy('year', 'desc')->get()->groupBy('country_id')->toJson();
        });
        $gdps = json_decode($gdpsJson, true);

        $inflationsJson = cache()->remember('dashboard.inflations', 600, function() {
            return Inflation::orderBy('year', 'desc')->get()->groupBy('country_id')->toJson();
        });
        $inflations = json_decode($inflationsJson, true);

        $weathersJson = cache()->remember('dashboard.weathers', 600, function() {
            return Weather::with('country')->get()->toJson();
        });
        $weathers = json_decode($weathersJson);

        $currenciesJson = cache()->remember('dashboard.currencies', 600, function() {
            return Currency::all()->toJson();
        });
        $currencies = json_decode($currenciesJson);
        
        $highRiskCountriesJson = cache()->remember('dashboard.high_risk_countries', 600, function() {
            return RiskScore::with('country')
                ->whereIn('id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('risk_scores')
                        ->groupBy('country_id');
                })
                ->orderBy('total_score', 'desc')
                ->get()
                ->toJson();
        });
        $highRiskCountries = collect(json_decode($highRiskCountriesJson));

        return view('user.dashboard', compact(
            'totalCountries',
            'totalPorts',
            'watchlistCount',
            'recentArticles',
            'highRiskCountries',
            'ports',
            'countries',
            'gdps',
            'inflations',
            'weathers',
            'currencies'
        ));
    }

    /**
     * Display indicators and stats for a specific selected country.
     */
    public function country(Request $request, CountryService $countryService)
    {
        $countries = Country::orderBy('name', 'asc')->get();
        $selectedCode = $request->get('code');
        $selectedCountry = null;
        $gdpData = collect();
        $inflationData = collect();
        $exportData = collect();
        $importData = collect();
        $latestRisk = null;

        if ($selectedCode) {
            $selectedCountry = $countryService->getCountryDetails($selectedCode);
            if ($selectedCountry) {
                $selectedCountry->load('weather');
                $gdpData = Gdp::where('country_id', $selectedCountry->id)->orderBy('year', 'asc')->get();
                $inflationData = Inflation::where('country_id', $selectedCountry->id)->orderBy('year', 'asc')->get();
                $exportData = Export::where('country_id', $selectedCountry->id)->orderBy('year', 'asc')->get();
                $importData = Import::where('country_id', $selectedCountry->id)->orderBy('year', 'asc')->get();
                $latestRisk = RiskScore::where('country_id', $selectedCountry->id)->orderBy('calculated_at', 'desc')->first();
            }
        }

        return view('user.country', compact(
            'countries',
            'selectedCountry',
            'selectedCode',
            'gdpData',
            'inflationData',
            'exportData',
            'importData',
            'latestRisk'
        ));
    }

    /**
     * Display weather conditions.
     */
    public function weather()
    {
        $countries = Country::with('weather')->get();
        return view('user.weather', compact('countries'));
    }

    /**
     * Display exchange rates trends.
     */
    public function currency(CurrencyService $currencyService)
    {
        try {
            $oldestCurrency = Currency::orderBy('fetched_at', 'asc')->first();
            if (!$oldestCurrency || !$oldestCurrency->fetched_at || \Carbon\Carbon::parse($oldestCurrency->fetched_at)->isBefore(now()->subHour())) {
                $currencyService->syncRatesToDatabase();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to sync currency rates on dashboard load: " . $e->getMessage());
        }

        $currencies = Currency::all();
        return view('user.currency', compact('currencies'));
    }

    /**
     * Display news feeds.
     */
    public function news(Request $request, NewsService $newsService)
    {
        $search = $request->get('q', '');
        $page = (int) $request->get('page', 1);

        $news = $newsService->getNews($search, $page, 10);

        $positiveCount = News::where('sentiment', 'positive')->count();
        $neutralCount = News::where('sentiment', 'neutral')->count();
        $negativeCount = News::where('sentiment', 'negative')->count();

        return view('user.news', compact('news', 'search', 'positiveCount', 'neutralCount', 'negativeCount'));
    }

    /**
     * Display global ports locations.
     */
    public function ports()
    {
        $ports = Port::with('country')->get();
        $countries = Country::orderBy('name', 'asc')->get();
        return view('user.ports', compact('ports', 'countries'));
    }

    /**
     * Display computed risk score records.
     */
    public function risk()
    {
        $this->syncStaleRiskScores();

        $riskScores = RiskScore::with('country')
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('risk_scores')
                    ->groupBy('country_id');
            })
            ->orderBy('total_score', 'desc')
            ->get();
        return view('user.risk', compact('riskScores'));
    }

    /**
     * Compare side-by-side performance of two countries.
     */
    public function compare(Request $request)
    {
        $countries = Country::orderBy('name', 'asc')->get();
        $code1 = $request->get('country1');
        $code2 = $request->get('country2');

        $country1 = null;
        $country2 = null;

        if ($code1) {
            $country1 = Country::with(['weather', 'gdps', 'inflations', 'exports', 'imports', 'riskScores'])->where('code', $code1)->first();
        }

        if ($code2) {
            $country2 = Country::with(['weather', 'gdps', 'inflations', 'exports', 'imports', 'riskScores'])->where('code', $code2)->first();
        }

        return view('user.compare', compact('countries', 'country1', 'country2', 'code1', 'code2'));
    }

    /**
     * Display the watchlist countries of the logged-in user.
     */
    public function watchlist()
    {
        $user = auth()->user();
        $watchedCountries = $user->watchedCountries()->with(['weather', 'riskScores'])->get();
        
        $watchedIds = $watchedCountries->pluck('id');
        $availableCountries = Country::whereNotIn('id', $watchedIds)->orderBy('name', 'asc')->get();

        return view('user.watchlist', compact('watchedCountries', 'availableCountries'));
    }

    /**
     * Add a country to the user's watchlist.
     */
    public function watchlistAdd(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
        ]);

        $user = auth()->user();
        $user->watchedCountries()->syncWithoutDetaching([$request->country_id]);

        return redirect()->back()->with('success', 'Negara berhasil ditambahkan ke daftar pantauan.');
    }

    /**
     * Remove a country from the user's watchlist.
     */
    public function watchlistRemove(Country $country)
    {
        $user = auth()->user();
        $user->watchedCountries()->detach($country->id);

        return redirect()->back()->with('success', 'Negara berhasil dihapus dari daftar pantauan.');
    }

    /**
     * Display analysis articles feed.
     */
    public function articles()
    {
        $articles = Article::with('user')->orderBy('published_at', 'desc')->get();
        return view('user.articles', compact('articles'));
    }

    /**
     * Display user profile and platform settings.
     */
    public function settings()
    {
        return view('user.settings');
    }

    /**
     * Helper to automatically check and calculate stale risk scores.
     */
    private function syncStaleRiskScores()
    {
        if (cache()->has('risk_scores_synced')) {
            return;
        }

        try {
            $oldestRisk = RiskScore::orderBy('calculated_at', 'asc')->first();
            if (!$oldestRisk || !$oldestRisk->calculated_at || \Carbon\Carbon::parse($oldestRisk->calculated_at)->isBefore(now()->subHour())) {
                app(RiskScoringService::class)->calculateAllCountries();
                cache()->put('risk_scores_synced', true, 3600);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to auto-update risk scores: " . $e->getMessage());
        }
    }
}

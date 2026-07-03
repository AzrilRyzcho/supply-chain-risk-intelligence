<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Port;
use App\Models\Weather;
use App\Models\Currency;
use App\Models\Inflation;
use App\Models\Gdp;
use App\Models\News;
use App\Models\RiskScore;
use App\Models\Article;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * Display the main user dashboard summary.
     */
    public function index()
    {
        $totalCountries = Country::count();
        $totalPorts = Port::count();
        $watchlistCount = auth()->user()->watchedCountries()->count();
        $recentArticles = Article::with('user')->orderBy('published_at', 'desc')->take(3)->get();
        
        // Fetch top high-risk countries based on latest calculations
        $highRiskCountries = RiskScore::with('country')
            ->orderBy('total_score', 'desc')
            ->take(4)
            ->get();

        return view('user.dashboard', compact(
            'totalCountries',
            'totalPorts',
            'watchlistCount',
            'recentArticles',
            'highRiskCountries'
        ));
    }

    /**
     * Display indicators and stats for a specific selected country.
     */
    public function country(Request $request)
    {
        $countries = Country::all();
        $selectedCode = $request->get('code');
        $selectedCountry = null;
        $gdpData = collect();
        $inflationData = collect();
        $latestRisk = null;

        if ($selectedCode) {
            $selectedCountry = Country::with(['weather'])->where('code', $selectedCode)->first();
            if ($selectedCountry) {
                $gdpData = Gdp::where('country_id', $selectedCountry->id)->orderBy('year', 'asc')->get();
                $inflationData = Inflation::where('country_id', $selectedCountry->id)->orderBy('year', 'asc')->get();
                $latestRisk = RiskScore::where('country_id', $selectedCountry->id)->orderBy('calculated_at', 'desc')->first();
            }
        }

        return view('user.country', compact(
            'countries',
            'selectedCountry',
            'selectedCode',
            'gdpData',
            'inflationData',
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
    public function currency()
    {
        $currencies = Currency::all();
        return view('user.currency', compact('currencies'));
    }

    /**
     * Display news feeds.
     */
    public function news()
    {
        $news = News::with('country')->orderBy('published_at', 'desc')->get();
        return view('user.news', compact('news'));
    }

    /**
     * Display global ports locations.
     */
    public function ports()
    {
        $ports = Port::with('country')->get();
        return view('user.ports', compact('ports'));
    }

    /**
     * Display computed risk score records.
     */
    public function risk()
    {
        $riskScores = RiskScore::with('country')->orderBy('calculated_at', 'desc')->get();
        return view('user.risk', compact('riskScores'));
    }

    /**
     * Compare side-by-side performance of two countries.
     */
    public function compare(Request $request)
    {
        $countries = Country::all();
        $code1 = $request->get('country1');
        $code2 = $request->get('country2');

        $country1 = null;
        $country2 = null;

        if ($code1) {
            $country1 = Country::with(['weather', 'gdps', 'inflations', 'riskScores'])->where('code', $code1)->first();
        }

        if ($code2) {
            $country2 = Country::with(['weather', 'gdps', 'inflations', 'riskScores'])->where('code', $code2)->first();
        }

        return view('user.compare', compact('countries', 'country1', 'country2', 'code1', 'code2'));
    }

    /**
     * Display the watchlist countries of the logged-in user.
     */
    public function watchlist()
    {
        $watchedCountries = auth()->user()->watchedCountries()->with(['weather', 'riskScores'])->get();
        return view('user.watchlist', compact('watchedCountries'));
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
}

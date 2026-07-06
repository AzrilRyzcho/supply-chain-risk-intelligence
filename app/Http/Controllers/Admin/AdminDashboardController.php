<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Article;
use App\Models\Port;
use App\Models\Country;
use App\Models\News;
use App\Models\RiskScore;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $articlesCount = Article::count();
        $portsCount = Port::count();
        $countriesCount = Country::count();
        $watchlistsCount = DB::table('watchlists')->count();
        $newsCount = News::count();

        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentArticles = Article::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        $latestRiskScores = RiskScore::with('country')->orderBy('calculated_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'usersCount',
            'articlesCount',
            'portsCount',
            'countriesCount',
            'watchlistsCount',
            'newsCount',
            'recentUsers',
            'recentArticles',
            'latestRiskScores'
        ));
    }
}

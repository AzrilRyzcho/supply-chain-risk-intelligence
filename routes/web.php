<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\PortController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CountryController;

// Guest Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authenticated User Routes (Dashboard & Tools)
Route::middleware('auth')->group(function () {
    Route::prefix('dashboard')->name('user.')->group(function () {
        Route::get('/main', [UserDashboardController::class, 'index'])->name('index');
        Route::get('/country', [UserDashboardController::class, 'country'])->name('country');
        Route::get('/compare', [UserDashboardController::class, 'compare'])->name('compare');
        Route::get('/watchlist', [UserDashboardController::class, 'watchlist'])->name('watchlist');
        Route::post('/watchlist/add', [UserDashboardController::class, 'watchlistAdd'])->name('watchlist.add');
        Route::post('/watchlist/remove/{country}', [UserDashboardController::class, 'watchlistRemove'])->name('watchlist.remove');
        Route::get('/weather', [UserDashboardController::class, 'weather'])->name('weather');
        Route::get('/currency', [UserDashboardController::class, 'currency'])->name('currency');
        Route::get('/api/currency/{code}', [\App\Http\Controllers\Api\CurrencyApiController::class, 'show'])->name('api.currency');
        Route::get('/news', [UserDashboardController::class, 'news'])->name('news');
        Route::get('/ports', [UserDashboardController::class, 'ports'])->name('ports');
        Route::get('/risk', [UserDashboardController::class, 'risk'])->name('risk');
        Route::get('/articles', [UserDashboardController::class, 'articles'])->name('articles');
        Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');
    });

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Panel Routes (Protected by auth and custom admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('articles', ArticleController::class);
    Route::resource('ports', PortController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
    
    // News Cache Management
    Route::get('/news-cache', [\App\Http\Controllers\Admin\NewsCacheController::class, 'index'])->name('news-cache.index');
    Route::delete('/news-cache/clear', [\App\Http\Controllers\Admin\NewsCacheController::class, 'clear'])->name('news-cache.clear');
    Route::delete('/news-cache/{news}', [\App\Http\Controllers\Admin\NewsCacheController::class, 'destroy'])->name('news-cache.destroy');

    // Global Watchlist Management
    Route::get('/watchlists', [\App\Http\Controllers\Admin\WatchlistController::class, 'index'])->name('watchlists.index');
    Route::delete('/watchlists/{id}', [\App\Http\Controllers\Admin\WatchlistController::class, 'destroy'])->name('watchlists.destroy');
});

// Fallback Dashboard Route for compatibility (resolves route('dashboard'))
Route::middleware('auth')->get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.index');
})->name('dashboard');

require __DIR__.'/auth.php';

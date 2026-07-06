<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryApiController;
use App\Http\Controllers\Api\RiskApiController;
use App\Http\Controllers\Api\PortApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\CurrencyApiController;
use App\Http\Controllers\Api\WatchlistApiController;

// Authenticated API (v1 Group)
Route::middleware(['auth', 'api.cache'])->prefix('v1')->group(function () {
    Route::get('/countries', [CountryApiController::class, 'index']);
    Route::get('/countries/{code}', [CountryApiController::class, 'show']);
    Route::post('/countries/{code}/sync', [CountryApiController::class, 'sync']);
    Route::get('/risk/{code}', [RiskApiController::class, 'show']);
    Route::get('/ports', [PortApiController::class, 'index']);
    Route::get('/news/{code}', [NewsApiController::class, 'show']);
    Route::get('/currency/{code}', [CurrencyApiController::class, 'show']);
    Route::post('/watchlist/toggle', [WatchlistApiController::class, 'toggle']);
});

// Public Internal REST API
Route::get('/countries', [\App\Http\Controllers\Api\CountryRestController::class, 'index']);
Route::get('/risk', [\App\Http\Controllers\Api\RiskRestController::class, 'index']);
Route::get('/news', [\App\Http\Controllers\Api\NewsRestController::class, 'index']);
Route::get('/currency', [\App\Http\Controllers\Api\CurrencyRestController::class, 'index']);
Route::get('/ports', [\App\Http\Controllers\Api\PortRestController::class, 'index']);
Route::get('/docs', [\App\Http\Controllers\Api\ApiDocsController::class, 'index'])->name('api.docs');
Route::get('/', function () {
    return redirect()->route('api.docs');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryApiController;
use App\Http\Controllers\Api\RiskApiController;
use App\Http\Controllers\Api\PortApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\CurrencyApiController;
use App\Http\Controllers\Api\WatchlistApiController;

Route::middleware(['auth', 'api.cache'])->prefix('v1')->group(function () {
    Route::get('/countries', [CountryApiController::class, 'index']);
    Route::get('/risk/{code}', [RiskApiController::class, 'show']);
    Route::get('/ports', [PortApiController::class, 'index']);
    Route::get('/news/{code}', [NewsApiController::class, 'show']);
    Route::get('/currency/{code}', [CurrencyApiController::class, 'show']);
    Route::post('/watchlist/toggle', [WatchlistApiController::class, 'toggle']);
});

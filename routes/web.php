<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\PortController;
use App\Http\Controllers\Admin\UserController;

// Guest / Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User Dashboards & Tools
    Route::prefix('dashboard')->name('user.')->group(function () {
        Route::get('/', [UserDashboardController::class, 'index'])->name('index');
        Route::get('/country', [UserDashboardController::class, 'country'])->name('country');
        Route::get('/compare', [UserDashboardController::class, 'compare'])->name('compare');
        Route::get('/watchlist', [UserDashboardController::class, 'watchlist'])->name('watchlist');
    });
});

// Admin Panel Routes (Protected by auth and custom admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('articles', ArticleController::class);
    Route::resource('ports', PortController::class);
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
});

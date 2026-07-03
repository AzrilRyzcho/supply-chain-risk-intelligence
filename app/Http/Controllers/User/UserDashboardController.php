<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        return view('user.dashboard');
    }

    public function country()
    {
        return view('user.country');
    }

    public function compare()
    {
        return view('user.compare');
    }

    public function watchlist()
    {
        return view('user.watchlist');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryApiController extends Controller
{
    public function index()
    {
        return response()->json([
            ['id' => 1, 'name' => 'Jerman', 'code' => 'DE', 'risk_score' => 22],
            ['id' => 2, 'name' => 'Indonesia', 'code' => 'ID', 'risk_score' => 35],
            ['id' => 3, 'name' => 'China', 'code' => 'CN', 'risk_score' => 47],
            ['id' => 4, 'name' => 'Australia', 'code' => 'AU', 'risk_score' => 18],
        ]);
    }
}

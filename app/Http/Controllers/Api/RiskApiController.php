<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiskApiController extends Controller
{
    public function show($code)
    {
        return response()->json([
            'country_code' => strtoupper($code),
            'risk_score' => 28,
            'breakdown' => [
                'weather_risk' => 30, // 30%
                'inflation_risk' => 20, // 20%
                'political_news_risk' => 40, // 40%
                'currency_risk' => 10, // 10%
            ],
            'status' => 'Low Risk'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    public function show($code)
    {
        return response()->json([
            'country_code' => strtoupper($code),
            'news' => [
                [
                    'title' => 'Inflation increases while exports decrease due to war.',
                    'source' => 'Global Logistics News',
                    'sentiment' => 'Negative',
                    'positive_score' => 1,
                    'negative_score' => 3
                ],
                [
                    'title' => 'Port of Hamburg implements new smart logistics systems to improve throughput.',
                    'source' => 'Shipping Weekly',
                    'sentiment' => 'Positive',
                    'positive_score' => 2,
                    'negative_score' => 0
                ]
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyApiController extends Controller
{
    public function show($code)
    {
        return response()->json([
            'base' => 'USD',
            'target' => strtoupper($code),
            'rate' => $code === 'IDR' ? 15450.00 : ($code === 'EUR' ? 0.92 : 1.48),
            'trend' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                'values' => [1.46, 1.47, 1.45, 1.48, 1.48]
            ]
        ]);
    }
}

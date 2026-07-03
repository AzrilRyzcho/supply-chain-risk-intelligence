<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortApiController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            [
                'name' => 'Port of Hamburg',
                'country' => 'Germany',
                'latitude' => 53.545,
                'longitude' => 9.948,
            ],
            [
                'name' => 'Port of Shanghai',
                'country' => 'China',
                'latitude' => 31.222,
                'longitude' => 121.492,
            ],
            [
                'name' => 'Tanjung Priok',
                'country' => 'Indonesia',
                'latitude' => -6.103,
                'longitude' => 106.879,
            ],
            [
                'name' => 'Port of Sydney',
                'country' => 'Australia',
                'latitude' => -33.868,
                'longitude' => 151.209,
            ]
        ]);
    }
}

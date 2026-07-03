<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WatchlistApiController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:2',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status watchlist untuk negara ' . strtoupper($request->country_code) . ' berhasil diubah.',
            'in_watchlist' => true
        ]);
    }
}

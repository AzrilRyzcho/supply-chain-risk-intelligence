<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Http\Resources\CurrencyResource;

class CurrencyRestController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:10',
        ]);

        $search = $request->get('search');

        $currencies = Currency::query()
            ->when($search, function ($query, $search) {
                return $query->where('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->get();

        return CurrencyResource::collection($currencies);
    }
}

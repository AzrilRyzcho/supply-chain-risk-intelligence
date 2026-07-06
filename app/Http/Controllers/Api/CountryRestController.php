<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Http\Resources\CountryResource;

class CountryRestController extends Controller
{
    /**
     * Display a listing of countries.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
        ]);

        $search = $request->get('search');
        $region = $request->get('region');

        $countries = Country::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($region, function ($query, $region) {
                return $query->where('region', $region);
            })
            ->orderBy('name', 'asc')
            ->get();

        return CountryResource::collection($countries);
    }
}

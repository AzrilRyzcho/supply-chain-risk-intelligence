<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Port;
use App\Http\Resources\PortResource;

class PortRestController extends Controller
{
    /**
     * Display a listing of ports.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
            'country_id' => 'nullable|integer|exists:countries,id',
        ]);

        $search = $request->get('search');
        $countryId = $request->get('country_id');

        $ports = Port::query()
            ->with('country')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($countryId, function ($query, $countryId) {
                return $query->where('country_id', $countryId);
            })
            ->orderBy('name', 'asc')
            ->get();

        return PortResource::collection($ports);
    }
}

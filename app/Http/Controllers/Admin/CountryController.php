<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        // Verify valid sorting column to prevent SQL injection
        $allowedSorts = ['name', 'code', 'currency_code', 'region', 'latitude', 'longitude'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'name';
        }

        // Verify direction
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $countries = Country::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('admin.countries.index', compact('countries', 'search', 'sortBy', 'direction'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'size:2', 'unique:countries,code', 'alpha'],
            'currency_code' => ['required', 'string', 'size:3', 'alpha'],
            'region' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        // Standardize code to uppercase
        $validated['code'] = strtoupper($validated['code']);
        $validated['currency_code'] = strtoupper($validated['currency_code']);

        Country::create($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Negara berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 
                'string', 
                'size:2', 
                Rule::unique('countries')->ignore($country->id),
                'alpha'
            ],
            'currency_code' => ['required', 'string', 'size:3', 'alpha'],
            'region' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['currency_code'] = strtoupper($validated['currency_code']);

        $country->update($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Negara berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('admin.countries.index')
            ->with('success', 'Negara berhasil dihapus!');
    }
}

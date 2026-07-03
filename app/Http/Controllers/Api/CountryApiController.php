<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryApiController extends Controller
{
    /**
     * Display a listing of all countries.
     */
    public function index()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        return response()->json($countries);
    }

    /**
     * Display the details of a specific country (using REST Countries API / Caching).
     */
    public function show(string $code, CountryService $countryService)
    {
        try {
            $country = $countryService->getCountryDetails($code);
            
            if (!$country) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Negara dengan kode [{$code}] tidak ditemukan."
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $country
            ]);
            
        } catch (\Exception $e) {
            Log::error("API show failed for country [{$code}]: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat data dari REST Countries API.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force sync country details from REST Countries API to database.
     */
    public function sync(string $code, CountryService $countryService)
    {
        try {
            $country = $countryService->syncCountry($code);
            
            return response()->json([
                'status' => 'success',
                'message' => "Sinkronisasi data negara [{$code}] berhasil diselesaikan.",
                'data' => $country
            ]);
            
        } catch (\Exception $e) {
            Log::error("API sync failed for country [{$code}]: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Sinkronisasi data dengan REST Countries API gagal.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}

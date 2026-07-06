<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskScore;
use App\Http\Resources\RiskScoreResource;

class RiskRestController extends Controller
{
    /**
     * Display a listing of risk scores.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
        ]);

        $search = $request->get('search');

        $riskScores = RiskScore::query()
            ->with('country')
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('risk_scores')
                    ->groupBy('country_id');
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('country', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('total_score', 'desc')
            ->get();

        return RiskScoreResource::collection($riskScores);
    }
}

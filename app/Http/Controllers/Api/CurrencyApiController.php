<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CurrencyApiController extends Controller
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function show(string $code, Request $request)
    {
        $targetCode = strtoupper($code);
        $baseCode = strtoupper($request->get('base', 'USD'));

        // 1. Fetch latest rate
        $latestRates = $this->currencyService->getLatestRates($baseCode);
        $rate = null;
        if (isset($latestRates['rates'][$targetCode])) {
            $rate = (double) $latestRates['rates'][$targetCode];
        }

        // Fallback to database local rates if base is USD
        if ($rate === null && $baseCode === 'USD') {
            $localCurrency = \App\Models\Currency::where('code', $targetCode)->first();
            if ($localCurrency) {
                $rate = $localCurrency->rate_to_usd;
            }
        }

        // 2. Fetch historical rates (last 30 days)
        $historicalData = $this->currencyService->getHistoricalRates($baseCode, $targetCode, 30);
        
        $labels = [];
        $values = [];
        
        if (isset($historicalData['rates']) && is_array($historicalData['rates'])) {
            foreach ($historicalData['rates'] as $date => $rates) {
                if (isset($rates[$targetCode])) {
                    $labels[] = Carbon::parse($date)->format('d M');
                    $values[] = (double) $rates[$targetCode];
                }
            }
        }

        // Fallback chart points if no historical data is retrieved
        if (empty($labels)) {
            $labels = [Carbon::now()->subDays(1)->format('d M'), Carbon::now()->format('d M')];
            $values = [$rate ?? 1.0, $rate ?? 1.0];
        }

        return response()->json([
            'status' => 'success',
            'base' => $baseCode,
            'target' => $targetCode,
            'rate' => $rate ?? 1.0,
            'trend' => [
                'labels' => $labels,
                'values' => $values
            ]
        ]);
    }
}

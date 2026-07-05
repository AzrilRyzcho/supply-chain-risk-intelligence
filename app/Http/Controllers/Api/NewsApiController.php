<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\News;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    public function show(string $code)
    {
        $code = strtoupper($code);
        $country = Country::where('code', $code)->first();

        if (!$country) {
            return response()->json([
                'status' => 'error',
                'message' => "Negara dengan kode [{$code}] tidak ditemukan."
            ], 404);
        }

        $news = News::where('country_id', $country->id)
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'country_code' => $code,
            'country_name' => $country->name,
            'news' => $news->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'source' => $item->source,
                    'url' => $item->url,
                    'sentiment' => ucfirst($item->sentiment),
                    'positive_score' => $item->positive_score,
                    'negative_score' => $item->negative_score,
                    'published_at' => $item->published_at,
                ];
            })
        ]);
    }
}

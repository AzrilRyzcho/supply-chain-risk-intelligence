<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Http\Resources\NewsResource;

class NewsRestController extends Controller
{
    /**
     * Display a listing of news.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
            'sentiment' => 'nullable|in:positive,neutral,negative',
        ]);

        $search = $request->get('search');
        $sentiment = $request->get('sentiment');

        $news = News::query()
            ->with('country')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%");
            })
            ->when($sentiment, function ($query, $sentiment) {
                return $query->where('sentiment', $sentiment);
            })
            ->orderBy('published_at', 'desc')
            ->get();

        return NewsResource::collection($news);
    }
}

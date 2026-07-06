<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Cache;

class NewsCacheController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $news = News::query()
            ->with('country')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhereHas('country', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.news_cache.index', compact('news', 'search'));
    }

    public function clear()
    {
        News::truncate();
        Cache::flush();

        return redirect()->back()->with('success', 'Seluruh cache berita berhasil dibersihkan.');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->back()->with('success', 'Artikel berita berhasil dihapus dari cache.');
    }
}

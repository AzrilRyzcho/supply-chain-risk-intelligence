<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['title', 'slug', 'published_at', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $articles = Article::query()
            ->with('user')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('admin.articles.index', compact('articles', 'search', 'sortBy', 'direction'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug'],
            'content' => ['required', 'string'],
            'published_now' => ['nullable', 'boolean'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Article::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        // Set published_at if published_now is checked
        $validated['published_at'] = $request->has('published_now') ? now() : null;
        $validated['user_id'] = auth()->id();

        Article::create($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diterbitkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 
                'string', 
                'max:255', 
                Rule::unique('articles')->ignore($article->id)
            ],
            'content' => ['required', 'string'],
            'published_now' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Article::where('slug', $validated['slug'])->where('id', '!=', $article->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        // Update published_at
        if ($request->has('published_now')) {
            if (empty($article->published_at)) {
                $validated['published_at'] = now();
            }
        } else {
            $validated['published_at'] = null;
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\WikiArticle;
use App\Models\WikiCategory;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    public function index()
    {
        $categories = WikiCategory::withCount('articles')->orderBy('order')->get();
        $recent = WikiArticle::where('published', true)->orderByDesc('updated_at')->limit(10)->get();
        return view('theme::wiki.index', ['categories' => $categories, 'recent' => $recent]);
    }

    public function category(WikiCategory $category)
    {
        $articles = $category->articles()->where('published', true)->orderBy('order')->orderBy('title')->get();
        return view('theme::wiki.category', ['category' => $category, 'articles' => $articles]);
    }

    public function show(string $slug)
    {
        $article = WikiArticle::where('published', true)->where('slug', $slug)->with('category')->firstOrFail();
        return view('theme::wiki.show', ['article' => $article]);
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $articles = collect();
        if (strlen($q) >= 2) {
            // Escape LIKE wildcards to avoid injection and unintended matches
            $like = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
            $articles = WikiArticle::where('published', true)
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', '%' . $like . '%')
                        ->orWhere('body', 'like', '%' . $like . '%');
                })
                ->orderBy('title')
                ->limit(50)
                ->get();
        }
        return view('theme::wiki.search', ['q' => $q, 'articles' => $articles]);
    }
}

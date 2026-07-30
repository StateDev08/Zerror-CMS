<?php

namespace App\Http\Controllers;

use App\Models\WikiArticle;
use App\Models\WikiCategory;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    public function index()
    {
        $categories = WikiCategory::query()
            ->withCount(['articles' => fn ($q) => $q->where('published', true)])
            ->orderBy('order')
            ->get();
        $recent = WikiArticle::where('published', true)->orderByDesc('updated_at')->limit(10)->get();

        return view('theme::wiki.index', ['categories' => $categories, 'recent' => $recent]);
    }

    public function category(WikiCategory $category)
    {
        $articles = $category->articles()->where('published', true)->orderBy('order')->orderBy('title')->get();

        // Kingshot-Guides: Card-Grid mit Cover-Bildern statt Text-only Wiki-Karten
        if (
            $category->slug === 'kingshot-guides'
            && function_exists('system_module_enabled')
            && system_module_enabled('kingshot_guides')
            && class_exists(\ZerroSystem\KingshotGuides\KingshotGuide::class)
        ) {
            $guides = \ZerroSystem\KingshotGuides\KingshotGuide::query()
                ->orderByDesc('published_at')
                ->orderBy('title')
                ->get();

            if ($guides->isNotEmpty()) {
                return view('sysmod_kingshot_guides::wiki-category', [
                    'category' => $category,
                    'guides' => $guides,
                    'articles' => $articles,
                ]);
            }
        }

        return view('theme::wiki.category', ['category' => $category, 'articles' => $articles]);
    }

    public function show(string $slug)
    {
        $article = WikiArticle::where('published', true)->where('slug', $slug)->with('category')->firstOrFail();

        // Kingshot-Guide-Artikel: Detail mit Cover, Tags und Videos
        if (
            function_exists('system_module_enabled')
            && system_module_enabled('kingshot_guides')
            && class_exists(\ZerroSystem\KingshotGuides\KingshotGuide::class)
        ) {
            $guide = \ZerroSystem\KingshotGuides\KingshotGuide::query()
                ->where(function ($q) use ($article, $slug) {
                    $q->where('wiki_article_id', $article->id)
                        ->orWhere('slug', $slug);
                })
                ->first();

            if ($guide) {
                return view('sysmod_kingshot_guides::show', ['guide' => $guide]);
            }
        }

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
                ->with('category')
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

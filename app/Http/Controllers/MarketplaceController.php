<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;

class MarketplaceController extends Controller
{
    public function index()
    {
        $categories = MarketplaceCategory::withCount('listings')->orderBy('order')->get();
        $featured = MarketplaceListing::where('published', true)->orderByDesc('created_at')->limit(8)->get();
        return view('theme::marketplace.index', ['categories' => $categories, 'featured' => $featured]);
    }

    public function category(MarketplaceCategory $category)
    {
        $listings = $category->listings()->where('published', true)->orderByDesc('created_at')->paginate(12);
        return view('theme::marketplace.category', ['category' => $category, 'listings' => $listings]);
    }

    public function show(MarketplaceListing $listing)
    {
        if (! $listing->published) {
            abort(404);
        }
        $listing->load('category');
        return view('theme::marketplace.show', ['listing' => $listing]);
    }
}

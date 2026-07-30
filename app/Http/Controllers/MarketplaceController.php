<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Support\HtmlContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        if (! $listing->published && ! $this->canManage($listing)) {
            abort(404);
        }
        $listing->load('category');
        return view('theme::marketplace.show', ['listing' => $listing]);
    }

    public function create()
    {
        $categories = MarketplaceCategory::orderBy('order')->get();
        return view('theme::marketplace.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:marketplace_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:50000'],
            'price_type' => ['required', 'in:free,fixed,negotiable'],
            'price_value' => ['nullable', 'numeric', 'min:0'],
            'contact_info' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['description'] = HtmlContent::sanitizeOptional($validated['description'] ?? null);

        $listing = MarketplaceListing::create([
            ...$validated,
            'user_id' => auth()->id(),
            'slug' => $this->uniqueSlug($validated['title']),
            'published' => true,
        ]);

        return redirect()->route('marketplace.show', $listing)->with('success', __('marketplace.created'));
    }

    public function edit(MarketplaceListing $listing)
    {
        abort_unless($this->canManage($listing), 403);
        $categories = MarketplaceCategory::orderBy('order')->get();

        return view('theme::marketplace.edit', ['listing' => $listing, 'categories' => $categories]);
    }

    public function update(Request $request, MarketplaceListing $listing)
    {
        abort_unless($this->canManage($listing), 403);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:marketplace_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:50000'],
            'price_type' => ['required', 'in:free,fixed,negotiable'],
            'price_value' => ['nullable', 'numeric', 'min:0'],
            'contact_info' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['description'] = HtmlContent::sanitizeOptional($validated['description'] ?? null);

        if ($listing->title !== $validated['title']) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $listing->id);
        }

        $listing->update($validated);

        return redirect()->route('marketplace.show', $listing)->with('success', __('marketplace.updated'));
    }

    public function destroy(MarketplaceListing $listing)
    {
        abort_unless($this->canManage($listing), 403);
        $listing->delete();

        return redirect()->route('marketplace.index')->with('success', __('marketplace.deleted'));
    }

    protected function canManage(MarketplaceListing $listing): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $listing->user_id === $user->id
            || $user->can('access_admin')
            || $user->hasRole('super-admin');
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'listing';
        $slug = $base;
        $i = 1;

        while (
            MarketplaceListing::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}

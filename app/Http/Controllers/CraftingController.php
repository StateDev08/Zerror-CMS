<?php

namespace App\Http\Controllers;

use App\Models\CraftableItem;
use App\Models\ItemRequest;
use Illuminate\Http\Request;

class CraftingController extends Controller
{
    public function index()
    {
        $items = CraftableItem::where('active', true)->orderBy('order')->orderBy('name')->get()->groupBy('category');
        $myRequests = [];
        if (auth()->check()) {
            $myRequests = ItemRequest::where('user_id', auth()->id())
                ->with('craftableItem')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }
        return view('theme::crafting.index', ['items' => $items, 'myRequests' => $myRequests]);
    }

    public function create()
    {
        $items = CraftableItem::where('active', true)->orderBy('order')->orderBy('name')->get()->groupBy('category');
        return view('theme::crafting.create', ['items' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'craftable_item_id' => ['nullable', 'exists:craftable_items,id'],
            'custom_request' => ['nullable', 'string', 'max:5000'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'desired_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', 'in:low,normal,high'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ], [], [
            'craftable_item_id' => __('crafting.select_item'),
            'custom_request' => __('crafting.custom_request'),
            'max_price' => __('crafting.max_price'),
            'desired_date' => __('crafting.desired_date'),
            'priority' => __('crafting.priority'),
            'quantity' => __('crafting.quantity'),
        ]);

        if (empty($request->craftable_item_id) && trim((string) $request->custom_request) === '') {
            return back()->withErrors(['craftable_item_id' => __('crafting.choose_item_or_text')])->withInput();
        }

        ItemRequest::create([
            'user_id' => $request->user()->id,
            'craftable_item_id' => $request->craftable_item_id ?: null,
            'custom_request' => trim((string) $request->custom_request) ?: null,
            'max_price' => $request->filled('max_price') ? $request->max_price : null,
            'desired_date' => $request->filled('desired_date') ? $request->desired_date : null,
            'priority' => $request->input('priority', ItemRequest::PRIORITY_NORMAL),
            'quantity' => (int) $request->input('quantity', 1),
            'status' => ItemRequest::STATUS_PENDING,
        ]);

        return redirect()->route('crafting.index')->with('success', __('crafting.success'));
    }
}

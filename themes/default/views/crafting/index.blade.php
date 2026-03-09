@extends('theme::layouts.app')

@section('title', __('crafting.title') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-2">{{ __('crafting.title') }}</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-8">{{ __('crafting.intro') }}</p>

    @if(session('success'))
        <p class="alert-success mb-6">{{ session('success') }}</p>
    @endif
    @if($errors->any())
        <ul class="mb-6 list-disc list-inside text-red-600 dark:text-red-400 text-sm rounded-2xl bg-red-50 dark:bg-red-900/10 p-4 border border-red-200/80 dark:border-red-800/50">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    @endif

    @if($items->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-6">{{ __('crafting.no_items') }}</p>
    @else
        @php
            $categoryLabels = ['Waffe' => __('crafting.category_weapon'), 'Rüstung' => __('crafting.category_armor'), 'Sonstiges' => __('crafting.category_other')];
        @endphp
        @foreach($items as $category => $categoryItems)
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ $categoryLabels[$category] ?? $category }}</h2>
                <ul class="space-y-3">
                    @foreach($categoryItems as $item)
                        <li class="card flex flex-wrap items-start gap-2">
                            <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $item->name }}</span>
                            @if($item->description)
                                <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ Str::limit($item->description, 120) }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    @endif

    @auth
        <div class="mt-8">
            <a href="{{ route('crafting.create') }}" class="btn-primary">{{ __('crafting.create_order') }}</a>
        </div>

        @if(isset($myRequests) && $myRequests->isNotEmpty())
            <div class="card mt-10">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-5">{{ __('crafting.my_requests') }}</h2>
                <div class="overflow-x-auto rounded-xl border border-neutral-200/80 dark:border-neutral-700/80">
                    <table class="w-full">
                        <thead class="bg-neutral-100/80 dark:bg-neutral-800/80">
                            <tr>
                                <th class="text-left p-3 text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ __('crafting.item') }}</th>
                                <th class="text-left p-3 text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ __('crafting.custom_request') }}</th>
                                <th class="text-left p-3 text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ __('crafting.status') }}</th>
                                <th class="text-left p-3 text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ __('crafting.max_price') }}</th>
                                <th class="text-left p-3 text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ __('crafting.date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($myRequests as $req)
                                <tr>
                                    <td class="p-3 text-neutral-900 dark:text-neutral-100">{{ $req->craftableItem?->name ?? '–' }}</td>
                                    <td class="p-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $req->custom_request ? Str::limit($req->custom_request, 40) : '–' }}</td>
                                    <td class="p-3">{{ \App\Models\ItemRequest::statusLabels()[$req->status] ?? $req->status }}</td>
                                    <td class="p-3 text-sm">{{ $req->max_price !== null ? number_format($req->max_price, 0, ',', '.') : '–' }}</td>
                                    <td class="p-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $req->created_at->format(__('general.date_format')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-5">
                    <a href="{{ route('usercp.item-requests') }}" class="text-sm font-medium theme-link-primary hover:underline">{{ __('crafting.show_all_requests') }}</a>
                </p>
            </div>
        @endif
    @else
        <div class="alert-warning mt-8">
            <a href="{{ route('login') }}" class="font-semibold theme-link-primary hover:underline">{{ __('crafting.login_required') }}</a>
        </div>
    @endauth
@endsection

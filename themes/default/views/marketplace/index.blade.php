@extends('theme::layouts.app')

@section('title', __('nav.marketplace') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.marketplace') }}</h1>
    <section class="mb-8">
        <h2 class="text-xl font-semibold mb-3">{{ __('marketplace.categories') }}</h2>
        @if($categories->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">{{ __('marketplace.no_categories') }}</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $cat)
                    <a href="{{ route('marketplace.category', $cat) }}" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">{{ $cat->name }} ({{ $cat->listings_count }})</a>
                @endforeach
            </div>
        @endif
    </section>
    <section>
        <h2 class="text-xl font-semibold mb-3">{{ __('marketplace.latest') }}</h2>
        @if($featured->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">{{ __('marketplace.no_listings') }}</p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($featured as $listing)
                    <a href="{{ route('marketplace.show', $listing) }}" class="block border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <h3 class="font-semibold">{{ $listing->title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @if($listing->price_type === 'free') {{ __('marketplace.price_free') }}
                            @elseif($listing->price_type === 'fixed') {{ number_format($listing->price_value, 2, ',', '.') }} €
                            @else {{ __('marketplace.price_negotiable') }}
                            @endif
                        </p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection

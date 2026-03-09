@extends('theme::layouts.app')

@section('title', $category->name . ' - ' . __('nav.marketplace') . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('marketplace.index') }}" class="hover:underline">{{ __('nav.marketplace') }}</a> / {{ $category->name }}
    </nav>
    <h1 class="text-2xl font-bold mb-4">{{ $category->name }}</h1>
    @if($listings->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('marketplace.no_listings') }}</p>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($listings as $listing)
                <a href="{{ route('marketplace.show', $listing) }}" class="block border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <h2 class="font-semibold">{{ $listing->title }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        @if($listing->price_type === 'free') {{ __('marketplace.price_free') }}
                        @elseif($listing->price_type === 'fixed') {{ number_format($listing->price_value, 2, ',', '.') }} €
                        @else {{ __('marketplace.price_negotiable') }}
                        @endif
                    </p>
                </a>
            @endforeach
        </div>
        {{ $listings->links() }}
    @endif
@endsection

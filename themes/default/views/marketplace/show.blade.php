@extends('theme::layouts.app')

@section('title', $listing->title . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('marketplace.index') }}" class="hover:underline">{{ __('nav.marketplace') }}</a>
        @if($listing->category)
            / <a href="{{ route('marketplace.category', $listing->category) }}" class="hover:underline">{{ $listing->category->name }}</a>
        @endif
        / {{ $listing->title }}
    </nav>
    <article class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-2">{{ $listing->title }}</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">
            @if($listing->price_type === 'free') {{ __('marketplace.price_free') }}
            @elseif($listing->price_type === 'fixed') {{ number_format($listing->price_value, 2, ',', '.') }} €
            @else {{ __('marketplace.price_negotiable') }}
            @endif
        </p>
        <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap">{{ $listing->description }}</div>
        @if($listing->contact_info)
            <p class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"><strong>{{ __('marketplace.contact') }}</strong> {{ $listing->contact_info }}</p>
        @endif
    </article>
@endsection

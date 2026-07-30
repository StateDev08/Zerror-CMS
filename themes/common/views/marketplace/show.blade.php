@extends('theme::layouts.app')

@section('title', $listing->title . ' - ' . site_name())

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('marketplace.index') }}" class="hover:underline">{{ __('nav.marketplace') }}</a>
        @if($listing->category)
            / <a href="{{ route('marketplace.category', $listing->category) }}" class="hover:underline">{{ $listing->category->name }}</a>
        @endif
        / {{ $listing->title }}
    </nav>
    @php
        $canManage = auth()->check() && (
            $listing->user_id === auth()->id()
            || auth()->user()->can('access_admin')
            || auth()->user()->hasRole('super-admin')
        );
    @endphp
    @if(session('success'))
        <p class="mb-4 p-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded text-sm">{{ session('success') }}</p>
    @endif
    <article class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
            <h1 class="text-2xl font-bold">{{ $listing->title }}</h1>
            @if($canManage)
                <div class="flex gap-2 text-sm">
                    <a href="{{ route('marketplace.edit', $listing) }}" class="px-3 py-1.5 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('marketplace.edit') }}</a>
                    <form action="{{ route('marketplace.destroy', $listing) }}" method="POST" onsubmit="return confirm('{{ __('marketplace.confirm_delete') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20">{{ __('marketplace.delete') }}</button>
                    </form>
                </div>
            @endif
        </div>
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">
            @if($listing->price_type === 'free') {{ __('marketplace.price_free') }}
            @elseif($listing->price_type === 'fixed') {{ number_format($listing->price_value, 2, ',', '.') }} €
            @else {{ __('marketplace.price_negotiable') }}
            @endif
        </p>
        <div class="prose dark:prose-invert max-w-none">{{ html_content($listing->description) }}</div>
        @if($listing->contact_info)
            <p class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"><strong>{{ __('marketplace.contact') }}</strong> {{ $listing->contact_info }}</p>
        @endif
    </article>
@endsection

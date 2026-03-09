@extends('theme::layouts.app')

@section('title', $album->name . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('gallery.index') }}" class="hover:underline">{{ __('nav.gallery') }}</a> / {{ $album->name }}
    </nav>
    <h1 class="text-2xl font-bold mb-4">{{ $album->name }}</h1>
    @if($album->description)
        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $album->description }}</p>
    @endif
    @if($images->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('gallery.no_images') }}</p>
    @else
        <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-4">
            @foreach($images as $image)
                <a href="{{ storage_asset($image->path) }}" target="_blank" rel="noopener" class="block rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <img src="{{ storage_asset($image->path) }}" alt="{{ $image->title ?? $image->path }}" class="w-full h-40 object-cover">
                    @if($image->title)
                        <p class="p-2 text-sm">{{ $image->title }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection

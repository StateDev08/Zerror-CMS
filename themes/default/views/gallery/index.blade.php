@extends('theme::layouts.app')

@section('title', __('nav.gallery') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.gallery') }}</h1>
    @if($albums->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-8">{{ __('gallery.no_albums') }}</p>
    @else
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($albums as $album)
                <a href="{{ route('gallery.album', $album) }}" class="card block hover:shadow-lg transition-shadow">
                    <h2 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $album->name }}</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $album->images_count }} {{ __('gallery.images') }}</p>
                    @if($album->description)
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">{{ Str::limit($album->description, 80) }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection

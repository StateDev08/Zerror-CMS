@extends('theme::layouts.app')

@section('title', __('nav.downloads') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.downloads') }}</h1>
    @forelse($categories as $category)
        <section class="mb-10">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ $category->name }}</h2>
            @if($category->downloads->isEmpty())
                <p class="text-neutral-500 dark:text-neutral-400 text-sm rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-4">{{ __('downloads.no_files') }}</p>
            @else
                <ul class="space-y-3">
                    @foreach($category->downloads as $dl)
                        <li class="card flex items-center justify-between gap-4 flex-wrap">
                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $dl->name }}@if($dl->version)<span class="text-neutral-500 dark:text-neutral-400 text-sm font-normal"> ({{ $dl->version }})</span>@endif</span>
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ $dl->download_count }} {{ __('downloads.downloads') }}</span>
                                <a href="{{ route('downloads.file', $dl) }}" class="btn-primary text-sm !py-2">{{ __('downloads.download') }}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @empty
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-8">{{ __('downloads.no_categories') }}</p>
    @endforelse
@endsection

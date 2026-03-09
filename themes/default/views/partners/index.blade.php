@extends('theme::layouts.app')

@section('title', __('nav.partners') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.partners') }}</h1>
    @if($partners->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-8">{{ __('partners.none') }}</p>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($partners as $partner)
                <a href="{{ $partner->url }}" target="_blank" rel="noopener" class="card flex items-center gap-4 hover:shadow-lg transition-shadow">
                    @if($partner->logo)
                        <img src="{{ storage_asset($partner->logo) }}" alt="{{ $partner->name }}" class="h-14 w-auto object-contain shrink-0">
                    @endif
                    <div class="min-w-0">
                        <h2 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $partner->name }}</h2>
                        @if($partner->description)
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">{{ Str::limit($partner->description, 80) }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection

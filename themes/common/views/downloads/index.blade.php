@extends('theme::layouts.app')

@section('title', __('nav.downloads') . ' - ' . site_name())

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.downloads') }}</h1>

    @forelse($categories as $category)
        <section class="mb-10">
            <h2 class="font-display text-xl font-semibold mb-4" style="color: var(--theme-primary);">{{ $category->name }}</h2>

            @if($category->downloads->isEmpty())
                <div class="clan-frame panel-box rounded-xl px-5 py-6 text-center max-w-xl">
                    <p style="color: var(--theme-muted);">{{ __('downloads.no_files') }}</p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($category->downloads as $dl)
                        <article class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-3 min-h-[8.5rem]">
                            <div class="min-w-0">
                                <h3 class="font-display font-semibold text-lg leading-snug" style="color: var(--theme-text);">
                                    {{ $dl->name }}
                                    @if($dl->version)
                                        <span class="text-sm font-normal" style="color: var(--theme-muted);">({{ $dl->version }})</span>
                                    @endif
                                </h3>
                            </div>

                            <div class="mt-auto pt-3 flex items-center justify-between gap-3" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 18%, transparent);">
                                <a
                                    href="{{ route('downloads.file', $dl) }}"
                                    class="dl-btn"
                                    title="{{ __('downloads.download') }}"
                                    aria-label="{{ __('downloads.download') }}: {{ $dl->name }}"
                                    data-same-tab
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3v12" />
                                        <path d="M7 10l5 5 5-5" />
                                        <path d="M5 19h14" />
                                    </svg>
                                </a>
                                <span class="text-sm" style="color: var(--theme-muted);">
                                    <span class="font-semibold" style="color: var(--theme-primary);">{{ $dl->download_count }}</span>
                                    {{ __('downloads.downloads') }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @empty
        <div class="clan-frame panel-box rounded-xl px-5 py-8 text-center max-w-xl mx-auto">
            <p style="color: var(--theme-muted);">{{ __('downloads.no_categories') }}</p>
        </div>
    @endforelse

    <style>
        .dl-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 999px;
            flex-shrink: 0;
            background: var(--theme-primary);
            color: #1a1408;
            box-shadow:
                0 0 0 1px color-mix(in srgb, var(--theme-primary) 40%, transparent),
                0 6px 18px color-mix(in srgb, var(--theme-primary) 28%, transparent);
            transition: filter .2s ease, transform .2s ease, box-shadow .2s ease;
        }
        .dl-btn:hover {
            filter: brightness(1.06);
            transform: translateY(-1px);
            box-shadow:
                0 0 0 1px color-mix(in srgb, var(--theme-primary) 50%, transparent),
                0 8px 22px color-mix(in srgb, var(--theme-primary) 35%, transparent);
        }
        .dl-btn svg {
            width: 1.05rem;
            height: 1.05rem;
            display: block;
        }
    </style>
@endsection

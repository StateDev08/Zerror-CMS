{{-- Top-Nav Links aus Menüeinträgen (Position: top) --}}
@php
    $topItems = \App\Models\MenuItem::position('top')->visible()->ordered()->get();
    $navFallback = \App\Support\SiteContent::navHardcodedFallback();
@endphp
@forelse($topItems as $item)
    <a href="{{ $item->resolved_url }}" data-same-tab>{{ $item->label }}</a>
@empty
    @if($navFallback)
        <a href="{{ url('/') }}" data-same-tab>{{ __('nav.home') }}</a>
        <a href="{{ route('news.index') }}" data-same-tab>{{ __('nav.news') }}</a>
        <a href="{{ route('roster.index') }}" data-same-tab>{{ __('nav.roster') }}</a>
        <a href="{{ route('calendar.index') }}" data-same-tab>{{ __('nav.calendar') }}</a>
        <a href="{{ route('wiki.index') }}" data-same-tab>{{ __('nav.wiki') }}</a>
        <a href="{{ route('apply.index') }}" data-same-tab>{{ __('nav.apply') }}</a>
        <a href="{{ route('forum.index') }}" data-same-tab>{{ __('nav.forum') }}</a>
    @endif
@endforelse

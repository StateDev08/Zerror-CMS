@php
    $contextLinks = $contextLinks ?? app(\App\Support\OnboardingChecklist::class)->contextLinks();
@endphp
@if(count($contextLinks))
    <nav class="zc-context" aria-label="{{ __('zerrocms.context.nav_label') }}">
        <span class="zc-context__label">{{ __('zerrocms.context.label') }}</span>
        @foreach($contextLinks as $i => $link)
            @if($i > 0)<span class="zc-context__sep" aria-hidden="true">·</span>@endif
            <a href="{{ $link['url'] }}" class="zc-inline-link">{{ $link['label'] }}</a>
        @endforeach
    </nav>
@endif

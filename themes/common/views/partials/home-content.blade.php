{{-- Startseite: nur Willkommen (ACP). Widgets global links/rechts im Layout. --}}
@php
    use App\Support\SiteContent;

    $welcomeTitle = trim((string) setting('home_welcome_title', ''));
    if ($welcomeTitle === '') {
        $welcomeTitle = (string) site_name();
    }
    $welcomeText = trim((string) setting('home_welcome_text', ''));
    if ($welcomeText === '') {
        $welcomeText = (string) __('home.intro');
    }
    $showCta = (bool) filter_var(setting('home_show_cta', '1'), FILTER_VALIDATE_BOOLEAN);
@endphp

<section class="panel-box home-welcome p-4 mb-0 w-full">
    <h1 class="font-display text-2xl md:text-3xl m-0" style="color: var(--theme-text, inherit)">{{ $welcomeTitle }}</h1>
    <div class="home-welcome__text mt-3 text-sm md:text-base leading-relaxed" style="color: var(--theme-muted, #888)">
        {!! html_content($welcomeText) !!}
    </div>
    @if($showCta)
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ SiteContent::homeCtaPrimaryUrl() }}" class="theme-bg-primary px-4 py-2 rounded-lg text-sm uppercase tracking-wider font-semibold">{{ SiteContent::homeCtaPrimaryLabel() }}</a>
            <a href="{{ SiteContent::homeCtaSecondaryUrl() }}" class="px-4 py-2 rounded-lg text-sm uppercase tracking-wider border font-semibold" style="border-color: var(--theme-primary); color: var(--theme-primary)">{{ SiteContent::homeCtaSecondaryLabel() }}</a>
        </div>
    @endif
</section>

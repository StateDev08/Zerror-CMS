{{-- Einheitlicher Footer-Inhalt aus ACP (Site Settings → Footer). --}}
@php
    use App\Support\SiteContent;

    $variant = $footerVariant ?? 'default';
    $siteName = site_name();
    $tagline = SiteContent::footerTagline();
    $copyright = SiteContent::footerCopyright();
    $credit = SiteContent::footerCredit();
    $pages = SiteContent::footerPages();

    $contactAddress = setting('contact_address', '');
    $contactEmail = setting('contact_email', '');
    $contactPhone = setting('contact_phone', '');
    $socialDiscord = setting('social_discord', '');
    $socialFacebook = setting('social_facebook', '');
    $socialTwitter = setting('social_twitter', '');
    $socialYoutube = setting('social_youtube', '');
    $socialTwitch = setting('social_twitch', '');
    $socialInstagram = setting('social_instagram', '');
    $socialTiktok = setting('social_tiktok', '');
    $socialSteam = setting('social_steam', '');
    $hasContact = $contactAddress || $contactEmail || $contactPhone
        || $socialDiscord || $socialFacebook || $socialTwitter || $socialYoutube || $socialTwitch || $socialInstagram
        || $socialTiktok || $socialSteam;
@endphp

@if($variant === 'bluebyte')
    <p class="font-display text-lg text-white mb-1">{{ $siteName }}</p>
    @if($tagline !== '')
        <p class="text-xs tracking-[0.2em] uppercase mb-3" style="color: var(--theme-primary)">{{ $tagline }}</p>
    @endif
    <p>
        &copy; {{ date('Y') }}
        @if($copyright !== '')
            <span class="mx-1">·</span>{{ $copyright }}
        @endif
        @if($credit !== '')
            <span class="mx-1">·</span>{{ $credit }}
        @endif
        @foreach($pages as $slug => $label)
            <span class="mx-2">·</span>
            <a href="{{ route('page.show', ['slug' => $slug]) }}" class="theme-link-primary hover:underline">{{ $label }}</a>
        @endforeach
    </p>
@elseif($variant === 'game')
    {{ $siteName }} &copy; {{ date('Y') }}
    @if($copyright !== '')
        <span class="mx-1">·</span>{{ $copyright }}
    @endif
    @if($credit !== '')
        <span class="mx-1">·</span>{{ $credit }}
    @endif
    @foreach($pages as $slug => $label)
        <span class="mx-2">·</span>
        <a href="{{ route('page.show', ['slug' => $slug]) }}" class="theme-link-primary hover:underline">{{ $label }}</a>
    @endforeach
@else
    <div class="text-center">
        {{ $siteName }} &copy; {{ date('Y') }}.
        @if($copyright !== '')
            {{ $copyright }}
        @endif
        @if($credit !== '')
            · {{ $credit }}
        @endif
        @if($tagline !== '')
            <div class="mt-1 text-xs uppercase tracking-wider opacity-80">{{ $tagline }}</div>
        @endif
        @foreach($pages as $slug => $label)
            <span class="mx-2">·</span>
            <a href="{{ route('page.show', ['slug' => $slug]) }}" class="theme-link-primary hover:underline">{{ $label }}</a>
        @endforeach
    </div>
    @if($hasContact)
        <div class="mt-4 pt-4 border-t border-neutral-200/80 dark:border-neutral-700/80 flex flex-wrap justify-center gap-x-5 gap-y-1">
            @if($contactAddress)<span>{{ $contactAddress }}</span>@endif
            @if($contactEmail)<a href="mailto:{{ e($contactEmail) }}" class="theme-link-primary hover:underline">{{ $contactEmail }}</a>@endif
            @if($contactPhone)<span>{{ $contactPhone }}</span>@endif
            @if($socialDiscord)<a href="{{ e($socialDiscord) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Discord</a>@endif
            @if($socialFacebook)<a href="{{ e($socialFacebook) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Facebook</a>@endif
            @if($socialTwitter)<a href="{{ e($socialTwitter) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Twitter</a>@endif
            @if($socialYoutube)<a href="{{ e($socialYoutube) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">YouTube</a>@endif
            @if($socialTwitch)<a href="{{ e($socialTwitch) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Twitch</a>@endif
            @if($socialInstagram)<a href="{{ e($socialInstagram) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Instagram</a>@endif
            @if($socialTiktok)<a href="{{ e($socialTiktok) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">TikTok</a>@endif
            @if($socialSteam)<a href="{{ e($socialSteam) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Steam</a>@endif
        </div>
    @endif
@endif

@includeIf('mod_visitor_counter::footer')

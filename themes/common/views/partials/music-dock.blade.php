{{-- Music Dock — Texte/Sichtbarkeit aus ACP (Site Settings → Theme). --}}
@php
    use App\Support\SiteContent;
@endphp
@if(SiteContent::musicDockEnabled())
    @php
        $title = SiteContent::musicDockTitle();
        $subtitle = SiteContent::musicDockSubtitle();
        $stream = SiteContent::musicDockStreamUrl();
    @endphp
    <aside class="music-dock hidden md:block rounded px-3 py-2 text-xs" style="color: var(--theme-muted)">
        <div class="flex items-center gap-2">
            <span class="inline-flex w-7 h-7 items-center justify-center rounded-full border" style="border-color: var(--theme-primary); color: var(--theme-primary)">♪</span>
            <div>
                <p class="uppercase tracking-wider font-semibold" style="color: var(--theme-primary)">{{ $title }}</p>
                @if($subtitle !== '')
                    <p>{{ $subtitle }}</p>
                @endif
                @if($stream !== '')
                    <a href="{{ e($stream) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary underline">{{ __('settings.music_dock_listen') }}</a>
                @endif
            </div>
        </div>
    </aside>
@endif

{{-- User-Menü rechts: Login-Icon / Avatar + Hover-Dropdown --}}
@php
    $user = auth()->user();
    $avatarUrl = $user?->avatar_url;
    $canAdmin = false;
    if ($user) {
        try {
            $panel = \Filament\Facades\Filament::getPanel('admin');
            $canAdmin = $panel && method_exists($user, 'canAccessPanel') && $user->canAccessPanel($panel);
        } catch (\Throwable $e) {
            $canAdmin = $user->can('access_admin') || $user->hasRole('super-admin');
        }
    }
    $initial = $user ? mb_strtoupper(mb_substr((string) $user->name, 0, 1)) : '';
@endphp

<style>
.cms-user-menu { position: relative; flex-shrink: 0; z-index: 60; }
.cms-user-menu__trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.3rem 0.55rem 0.3rem 0.3rem;
    border-radius: 999px;
    border: 1px solid color-mix(in srgb, var(--theme-primary, #3dd5ff) 35%, transparent);
    background: color-mix(in srgb, var(--theme-surface, #0c1524) 70%, #000);
    color: var(--theme-text, #eaf4ff);
    text-decoration: none;
    cursor: pointer;
    transition: border-color .2s, box-shadow .2s, background .2s;
}
.cms-user-menu__trigger:hover,
.cms-user-menu:hover .cms-user-menu__trigger,
.cms-user-menu:focus-within .cms-user-menu__trigger {
    border-color: color-mix(in srgb, var(--theme-primary, #3dd5ff) 70%, transparent);
    box-shadow: 0 0 18px color-mix(in srgb, var(--theme-primary, #3dd5ff) 22%, transparent);
    background: color-mix(in srgb, var(--theme-surface, #0c1524) 88%, #000);
}
.cms-user-menu__avatar {
    width: 1.85rem;
    height: 1.85rem;
    border-radius: 999px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid color-mix(in srgb, var(--theme-primary, #3dd5ff) 40%, transparent);
}
.cms-user-menu__avatar--fallback,
.cms-user-menu__icon {
    width: 1.85rem;
    height: 1.85rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: color-mix(in srgb, var(--theme-primary, #3dd5ff) 18%, transparent);
    color: var(--theme-primary, #3dd5ff);
    font-size: 0.75rem;
    font-weight: 700;
}
.cms-user-menu__icon svg { width: 1.05rem; height: 1.05rem; }
.cms-user-menu__label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--theme-muted, #9bb4cc);
    max-width: 7.5rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.cms-user-menu__chevron {
    width: 0.85rem;
    height: 0.85rem;
    color: var(--theme-muted, #9bb4cc);
    transition: transform .2s;
}
.cms-user-menu:hover .cms-user-menu__chevron,
.cms-user-menu:focus-within .cms-user-menu__chevron { transform: rotate(180deg); }
.cms-user-menu__dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + 0.45rem);
    min-width: 12.5rem;
    padding: 0.4rem;
    border-radius: 0.85rem;
    border: 1px solid color-mix(in srgb, var(--theme-primary, #3dd5ff) 30%, transparent);
    background: color-mix(in srgb, var(--theme-surface, #0b1422) 94%, #000);
    box-shadow:
        0 18px 40px rgba(0,0,0,.45),
        0 0 0 1px color-mix(in srgb, #fff 4%, transparent),
        0 0 28px color-mix(in srgb, var(--theme-primary, #3dd5ff) 12%, transparent);
    backdrop-filter: blur(14px);
    /* display:none wenn zu — sonst erzeugt das absolute Menü unsichtbaren Seiten-Overflow/Scroll */
    display: none;
    transform-origin: top right;
    z-index: 70;
}
.cms-user-menu:hover .cms-user-menu__dropdown,
.cms-user-menu:focus-within .cms-user-menu__dropdown {
    display: block;
    animation: cms-user-menu-in .16s ease both;
}
@keyframes cms-user-menu-in {
    from { opacity: 0; transform: translateY(-6px) scale(0.98); }
    to { opacity: 1; transform: none; }
}
.cms-user-menu__dropdown a,
.cms-user-menu__dropdown button {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    width: 100%;
    padding: 0.65rem 0.75rem;
    border: 0;
    border-radius: 0.6rem;
    background: transparent;
    color: var(--theme-text, #eaf4ff);
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
    transition: background .15s, color .15s;
}
.cms-user-menu__dropdown a:hover,
.cms-user-menu__dropdown button:hover {
    background: color-mix(in srgb, var(--theme-primary, #3dd5ff) 14%, transparent);
    color: #fff;
}
.cms-user-menu__dropdown svg {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
    opacity: 0.85;
    color: var(--theme-primary, #3dd5ff);
}
.cms-user-menu__divider {
    height: 1px;
    margin: 0.3rem 0.4rem;
    background: color-mix(in srgb, var(--theme-primary, #3dd5ff) 20%, transparent);
}
.cms-user-menu__admin {
    color: color-mix(in srgb, var(--theme-primary, #3dd5ff) 90%, #fff) !important;
}
.cms-user-menu__logout { color: #fca5a5 !important; }
.cms-user-menu__logout svg { color: #f87171 !important; }
.cms-user-menu__logout:hover {
    background: color-mix(in srgb, #ef4444 16%, transparent) !important;
    color: #fecaca !important;
}
@media (max-width: 767px) {
    .cms-user-menu__label { display: none; }
}
</style>

<div class="cms-user-menu">
    @auth
        <button type="button" class="cms-user-menu__trigger" aria-haspopup="true" aria-expanded="false">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="" class="cms-user-menu__avatar" width="32" height="32">
            @else
                <span class="cms-user-menu__avatar--fallback" aria-hidden="true">{{ $initial }}</span>
            @endif
            <span class="cms-user-menu__label">{{ $user->name }}</span>
            <svg class="cms-user-menu__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </button>
        <div class="cms-user-menu__dropdown" role="menu">
            <a href="{{ route('usercp.index') }}" role="menuitem" data-same-tab>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0"/></svg>
                {{ __('nav.usercp') }}
            </a>
            @if($canAdmin)
                <a href="{{ url('/admin') }}" role="menuitem" class="cms-user-menu__admin" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.174.1.33.23.46.39l1.21-.5c.516-.213 1.1.05 1.32.56l1.297 2.247c.22.51.04 1.11-.45 1.36l-1.15.59c.01.2.01.4 0 .59l1.15.59c.49.25.67.85.45 1.36l-1.297 2.247c-.22.51-.804.773-1.32.56l-1.21-.5a1.99 1.99 0 01-.46.39c-.332.184-.582.496-.645.87l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.063-.374-.313-.686-.645-.87a1.99 1.99 0 01-.46-.39l-1.21.5c-.516.213-1.1-.05-1.32-.56L3.53 15.1c-.22-.51-.04-1.11.45-1.36l1.15-.59a4.3 4.3 0 010-.59l-1.15-.59c-.49-.25-.67-.85-.45-1.36L4.877 8.3c.22-.51.804-.773 1.32-.56l1.21.5c.13-.16.286-.29.46-.39.332-.184.582-.496.645-.87l.213-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('nav.admin') }}
                </a>
            @endif
            <div class="cms-user-menu__divider"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="cms-user-menu__logout" role="menuitem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H9"/></svg>
                    {{ __('auth.logout') }}
                </button>
            </form>
        </div>
    @else
        <a href="{{ route('login') }}" class="cms-user-menu__trigger" data-same-tab>
            <span class="cms-user-menu__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0"/></svg>
            </span>
            <span class="cms-user-menu__label">{{ __('auth.login') }}</span>
        </a>
    @endauth
</div>

@extends('theme::layouts.app')

@section('title', site_name() . ' - ' . __('nav.usercp'))

@section('content')
<style>
    /* UserCP: themensichere Kontraste (keine weißen Karten mit ererbter Hellschrift) */
    .usercp {
        --ucp-bg: var(--theme-surface, #1a1f1c);
        --ucp-text: var(--theme-text, #e8f0e9);
        --ucp-muted: var(--theme-muted, var(--theme-text-muted, #9aada0));
        --ucp-primary: var(--theme-primary, #d97706);
        --ucp-border: color-mix(in srgb, var(--ucp-primary) 38%, transparent);
        --ucp-hover: color-mix(in srgb, var(--ucp-primary) 14%, transparent);
        --ucp-input-bg: color-mix(in srgb, var(--ucp-bg) 70%, #000);
        color: var(--ucp-text);
    }
    /* Common-Theme Light-Mode: dunkle Schrift auf heller Fläche */
    html:not(.dark) body:not([class*="theme-"]) .usercp,
    html:not(.dark) body.bg-neutral-50 .usercp {
        --ucp-bg: #ffffff;
        --ucp-text: #171717;
        --ucp-muted: #525252;
        --ucp-border: #e5e5e5;
        --ucp-hover: #f5f5f5;
        --ucp-input-bg: #ffffff;
    }
    .usercp a:not(.usercp-btn):not(.usercp-btn-primary):not(.usercp-nav-link) {
        color: var(--ucp-primary);
    }
    .usercp-nav {
        background: var(--ucp-bg);
        border: 1px solid var(--ucp-border);
        border-radius: 0.75rem;
        padding: 0.75rem;
    }
    .usercp-nav-link {
        display: block;
        padding: 0.55rem 0.75rem;
        border-radius: 0.5rem;
        color: var(--ucp-text) !important;
        text-decoration: none;
        font-weight: 500;
        transition: background .15s ease, color .15s ease;
    }
    .usercp-nav-link:hover {
        background: var(--ucp-hover);
        color: var(--ucp-text) !important;
    }
    .usercp-nav-link.is-active {
        background: color-mix(in srgb, var(--ucp-primary) 22%, transparent);
        color: var(--ucp-primary) !important;
        font-weight: 700;
    }
    .usercp-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 1rem;
        color: var(--ucp-text);
    }
    .usercp-card {
        background: var(--ucp-bg);
        border: 1px solid var(--ucp-border);
        border-radius: 0.75rem;
        padding: 1.35rem;
        color: var(--ucp-text);
    }
    .usercp-muted { color: var(--ucp-muted) !important; }
    .usercp-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
        color: var(--ucp-text);
    }
    .usercp-input {
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid var(--ucp-border);
        background: var(--ucp-input-bg);
        color: var(--ucp-text);
        padding: 0.55rem 0.75rem;
    }
    .usercp-input:focus {
        outline: none;
        border-color: var(--ucp-primary);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--ucp-primary) 25%, transparent);
    }
    .usercp-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 1rem;
        border-radius: 0.5rem;
        background: var(--ucp-primary);
        color: #111 !important;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        border: 0;
        cursor: pointer;
    }
    .usercp-btn-primary:hover { filter: brightness(1.08); color: #111 !important; }
    .usercp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 1rem;
        border-radius: 0.5rem;
        background: transparent;
        color: var(--ucp-text) !important;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        border: 1px solid var(--ucp-border);
        cursor: pointer;
    }
    .usercp-btn:hover {
        background: var(--ucp-hover);
        color: var(--ucp-text) !important;
    }
    .usercp-btn-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 1rem;
        border-radius: 0.5rem;
        background: #dc2626;
        color: #fff !important;
        font-weight: 700;
        font-size: 0.875rem;
        border: 0;
        cursor: pointer;
    }
    .usercp-alert-ok {
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        background: color-mix(in srgb, #22c55e 18%, transparent);
        border: 1px solid color-mix(in srgb, #22c55e 40%, transparent);
        color: #86efac;
    }
    html:not(.dark) body.bg-neutral-50 .usercp-alert-ok {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }
    .usercp-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--ucp-border);
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .usercp-table th,
    .usercp-table td {
        padding: 0.55rem 0.65rem;
        text-align: left;
        border-top: 1px solid var(--ucp-border);
        color: var(--ucp-text);
    }
    .usercp-table thead th {
        background: color-mix(in srgb, var(--ucp-primary) 12%, transparent);
        font-weight: 700;
        border-top: 0;
    }
    .usercp-avatar {
        height: 5rem;
        width: 5rem;
        border-radius: 9999px;
        object-fit: cover;
        border: 1px solid var(--ucp-border);
        flex-shrink: 0;
    }
    .usercp-avatar-fallback {
        height: 5rem;
        width: 5rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 700;
        background: color-mix(in srgb, var(--ucp-primary) 20%, transparent);
        color: var(--ucp-text);
        border: 1px solid var(--ucp-border);
        flex-shrink: 0;
    }
</style>

<div class="usercp flex flex-col md:flex-row gap-6">
    <nav class="usercp-nav md:w-56 shrink-0" aria-label="{{ __('nav.usercp') }}">
        <ul class="space-y-1 list-none m-0 p-0">
            <li><a href="{{ route('usercp.index') }}" class="usercp-nav-link {{ request()->routeIs('usercp.index') ? 'is-active' : '' }}">{{ __('usercp.overview') }}</a></li>
            <li><a href="{{ route('usercp.profile') }}" class="usercp-nav-link {{ request()->routeIs('usercp.profile') ? 'is-active' : '' }}">{{ __('usercp.profile') }}</a></li>
            <li><a href="{{ route('usercp.password') }}" class="usercp-nav-link {{ request()->routeIs('usercp.password') ? 'is-active' : '' }}">{{ __('usercp.password') }}</a></li>
            <li><a href="{{ route('usercp.discord') }}" class="usercp-nav-link {{ request()->routeIs('usercp.discord*') ? 'is-active' : '' }}">{{ __('usercp.discord') }}</a></li>
            <li><a href="{{ route('usercp.notifications') }}" class="usercp-nav-link {{ request()->routeIs('usercp.notifications*') ? 'is-active' : '' }}">{{ __('usercp.notifications') }}</a></li>
            <li><a href="{{ route('usercp.item-requests') }}" class="usercp-nav-link {{ request()->routeIs('usercp.item-requests') ? 'is-active' : '' }}">{{ __('crafting.my_requests') }}</a></li>
        </ul>
    </nav>
    <div class="flex-1 min-w-0">
        @yield('usercp_content')
    </div>
</div>
@endsection

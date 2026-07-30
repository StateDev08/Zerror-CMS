{{-- SEO-Meta aus Seiteneinstellungen (alle Themes) --}}
@php
    $siteName = site_name();
    $seoTitle = trim((string) setting('seo_default_title', '')) ?: $siteName;
    $seoDescription = trim((string) setting('seo_default_description', ''));
    $seoOgImage = trim((string) setting('seo_og_image', ''));
    $seoKeywords = trim((string) setting('seo_keywords', ''));
    $seoOgLocale = trim((string) setting('seo_og_locale', '')) ?: (app()->getLocale() === 'en' ? 'en_US' : 'de_DE');
    $seoRobots = trim((string) setting('seo_robots', 'index,follow')) ?: 'index,follow';
    $canonical = url()->current();
    $pageTitle = trim($__env->yieldContent('title'));
    $ogTitle = $pageTitle !== '' ? $pageTitle : $seoTitle;
@endphp
<title>{{ $pageTitle !== '' ? $pageTitle : $seoTitle }}</title>
@if($seoDescription !== '')
    <meta name="description" content="{{ e($seoDescription) }}">
@endif
@if($seoKeywords !== '')
    <meta name="keywords" content="{{ e($seoKeywords) }}">
@endif
<meta name="robots" content="{{ e($seoRobots) }}">
<link rel="canonical" href="{{ e($canonical) }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ e($siteName) }}">
<meta property="og:title" content="{{ e($ogTitle) }}">
<meta property="og:url" content="{{ e($canonical) }}">
<meta property="og:locale" content="{{ e($seoOgLocale) }}">
@if($seoDescription !== '')
    <meta property="og:description" content="{{ e($seoDescription) }}">
@endif
@if($seoOgImage !== '')
    <meta property="og:image" content="{{ e($seoOgImage) }}">
@endif

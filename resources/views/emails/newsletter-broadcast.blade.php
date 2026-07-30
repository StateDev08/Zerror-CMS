<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $mailSubject }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <div style="line-height: 1.6;">{!! \App\Support\HtmlContent::toHtml($body) !!}</div>
        <hr style="margin: 32px 0; border: none; border-top: 1px solid #ddd;">
        <p style="font-size: 13px; color: #666;">
            {{ __('newsletter.broadcast_unsubscribe_hint') }}
            <a href="{{ $unsubscribeUrl }}">{{ __('newsletter.broadcast_unsubscribe') }}</a>
        </p>
        <p style="font-size: 12px; color: #999;">{{ site_name() }}</p>
    </div>
</body>
</html>

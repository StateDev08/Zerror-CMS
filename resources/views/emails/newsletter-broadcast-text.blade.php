{{ trim(html_entity_decode(strip_tags($body))) }}

---
{{ __('newsletter.broadcast_unsubscribe_hint') }}
{{ $unsubscribeUrl }}

{{ site_name() }}

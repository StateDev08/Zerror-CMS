{{ __('mail.application_received_intro') }}

{{ __('apply.name') }}: {{ $application->name }}
{{ __('apply.email') }}: {{ $application->email }}
{{ __('apply.message') }}:

{{ trim(html_entity_decode(strip_tags($application->message), ENT_QUOTES | ENT_HTML5, 'UTF-8')) }}

---
{{ site_name() }}

{{ __('mail.application_received_intro') }}

{{ __('apply.name') }}: {{ $application->name }}
{{ __('apply.email') }}: {{ $application->email }}
{{ __('apply.message') }}:

{{ $application->message }}

---
{{ config('clan.name') }}

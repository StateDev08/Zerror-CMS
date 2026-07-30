{{-- Global CMS Toasts: Session-Flash + window.CmsToast() --}}
@php
    $cmsToasts = [];

    $push = static function (array &$bag, string $type, mixed $message): void {
        $message = trim(strip_tags((string) $message));
        if ($message === '') {
            return;
        }
        foreach ($bag as $existing) {
            if ($existing['type'] === $type && $existing['message'] === $message) {
                return;
            }
        }
        $bag[] = ['type' => $type, 'message' => $message];
    };

    foreach (['success' => 'success', 'error' => 'error', 'danger' => 'error', 'warning' => 'warning', 'status' => 'success', 'info' => 'info'] as $key => $type) {
        if (session()->has($key)) {
            $push($cmsToasts, $type, session($key));
        }
    }

    if (session('feedback_sent')) {
        $push($cmsToasts, 'success', __('clan.feedback_sent'));
    }
    if (session('application_sent')) {
        $push($cmsToasts, 'success', __('apply.sent'));
    }
    if (session('voted')) {
        $push($cmsToasts, 'success', __('polls.thank_you'));
    }
    if (session('newsletter_status') === 'subscribed') {
        $push($cmsToasts, 'success', __('newsletter.subscribed'));
    }
    if (session('newsletter_status') === 'already') {
        $push($cmsToasts, 'info', __('newsletter.already_subscribed'));
    }

    if (isset($errors) && $errors->any()) {
        $push($cmsToasts, 'error', $errors->first());
    }
@endphp
<link rel="stylesheet" href="{{ asset('css/cms-toasts.css') }}?v=2">
<script src="{{ asset('js/cms-toasts.js') }}?v=2" defer></script>
<script>
    window.__cmsToastQueue = @json($cmsToasts);
    (function () {
        function flush() {
            if (!window.CmsToast || !window.__cmsToastQueue || !window.__cmsToastQueue.length) return;
            var q = window.__cmsToastQueue;
            window.__cmsToastQueue = [];
            window.CmsToast.fromQueue(q);
        }
        document.addEventListener('DOMContentLoaded', flush);
        document.addEventListener('turbo:load', flush);
        document.addEventListener('livewire:navigated', flush);
        if (document.readyState !== 'loading') setTimeout(flush, 0);
    })();
</script>

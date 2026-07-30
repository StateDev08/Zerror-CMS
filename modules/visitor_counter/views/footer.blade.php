@php
    $cfg = module_config('visitor_counter');
    $showLabel = filter_var($cfg['show_label'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $label = trim((string) ($cfg['label'] ?? 'Besucher')) ?: 'Besucher';
    $count = ZerroVisitorCounter::hitAndCount();
    $formatted = number_format($count, 0, ',', '.');
@endphp
<div class="vc-footer" aria-label="{{ $label }}: {{ $formatted }}">
    @if($showLabel)
        <span class="vc-footer__label">{{ $label }}</span>
    @endif
    <span class="vc-footer__count">{{ $formatted }}</span>
</div>

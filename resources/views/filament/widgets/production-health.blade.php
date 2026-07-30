@php
    $issues = $this->getIssues();
@endphp

@if(count($issues) > 0)
    <div class="zc-page" style="margin-bottom:0.5rem">
        <div class="zc-help">
            <p><strong>Public-Launch-Check</strong></p>
            <ul style="margin:0.5rem 0 0;padding-left:1.1rem;font-size:0.875rem;line-height:1.5">
                @foreach($issues as $issue)
                    <li style="color: {{ $issue['level'] === 'danger' ? '#dc2626' : ($issue['level'] === 'warning' ? '#d97706' : '#64748b') }}">
                        {{ $issue['text'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

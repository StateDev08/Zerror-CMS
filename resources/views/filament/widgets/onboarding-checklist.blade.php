@php
    $data = $this->getChecklistData();
    $steps = $data['steps'];
    $done = $data['done'];
    $total = $data['total'];
    $pct = $total > 0 ? (int) round(($done / $total) * 100) : 0;
@endphp

<div class="zc-page" style="margin-bottom:0.75rem">
    <section class="zc-onboard">
        <div class="zc-onboard__head">
            <div>
                <h2 class="zc-onboard__title">{{ __('zerrocms.onboarding.title') }}</h2>
                <p class="zc-onboard__intro">{{ __('zerrocms.onboarding.intro') }}</p>
            </div>
            <div class="zc-onboard__meta">
                <span class="zc-badge zc-badge--ok">{{ __('zerrocms.onboarding.progress', ['done' => $done, 'total' => $total]) }}</span>
                <button type="button" class="zc-onboard__dismiss" wire:click="dismissOnboarding" wire:confirm="{{ __('zerrocms.onboarding.dismiss_confirm') }}">
                    {{ __('zerrocms.onboarding.dismiss') }}
                </button>
            </div>
        </div>

        <div class="zc-onboard__bar" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
            <span style="width: {{ $pct }}%"></span>
        </div>

        <ol class="zc-onboard__list">
            @foreach($steps as $step)
                <li class="zc-onboard__item {{ $step['done'] ? 'is-done' : '' }}">
                    <span class="zc-onboard__check" aria-hidden="true">{{ $step['done'] ? '✓' : ($loop->iteration) }}</span>
                    <div class="zc-onboard__body">
                        <strong>{{ $step['label'] }}</strong>
                        <span>{{ $step['hint'] }}</span>
                    </div>
                    @if(! $step['done'] && ! empty($step['url']))
                        <a href="{{ $step['url'] }}" class="zc-inline-link">{{ __('zerrocms.onboarding.open') }}</a>
                    @elseif($step['done'])
                        <span class="zc-onboard__done-label">{{ __('zerrocms.onboarding.done') }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </section>
</div>

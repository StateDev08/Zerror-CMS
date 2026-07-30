@extends('theme::layouts.app')

@section('title', __('nav.calendar') . ' - ' . site_name())

@section('content')
    @php
        $locale = app()->getLocale();
        $titleLabel = match ($viewMode) {
            'day' => $focus->locale($locale)->translatedFormat('l, d. F Y'),
            'week' => $focus->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->locale($locale)->translatedFormat('d. M')
                .' – '.$focus->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->locale($locale)->translatedFormat('d. M Y'),
            default => $focus->locale($locale)->translatedFormat('F Y'),
        };
        $selectClass = 'rounded-lg px-3 py-2 text-sm font-semibold';
        $selectStyle = 'background: color-mix(in srgb, var(--theme-surface) 85%, #000); border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent); color: var(--theme-text);';
        $btnBase = 'inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-bold uppercase tracking-wider no-underline';
        $btnIdle = 'border: 1px solid color-mix(in srgb, var(--theme-primary) 40%, transparent); color: var(--theme-text); background: transparent;';
    @endphp

    <style>
        .calendar-grid-table { table-layout: fixed; width: 100%; border-collapse: collapse; }
        .calendar-grid-table th,
        .calendar-grid-table td { width: 14.2857%; max-width: 0; vertical-align: top; }
        .calendar-day-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.75rem;
            height: 1.75rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .calendar-day-num--today {
            background: var(--theme-primary);
            color: #1a1408 !important;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--theme-primary) 40%, transparent), 0 0 18px color-mix(in srgb, var(--theme-primary) 35%, transparent);
        }
        .calendar-cell--today {
            background: color-mix(in srgb, var(--theme-primary) 18%, var(--theme-surface)) !important;
            box-shadow: inset 0 0 0 2px var(--theme-primary);
        }
        .calendar-event-chip {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.7rem;
            line-height: 1.25;
            padding: 0.25rem 0.4rem;
            border-radius: 0.35rem;
            text-decoration: none;
            background: color-mix(in srgb, var(--theme-primary) 22%, transparent);
            color: var(--theme-text);
        }
        .calendar-view-tabs { display: inline-flex; gap: 0.35rem; flex-wrap: wrap; }
    </style>

    <div class="mb-5">
        <h1 class="page-title mb-1">{{ __('nav.calendar') }}</h1>
        <p class="font-display text-lg font-semibold" style="color: var(--theme-primary);">{{ $titleLabel }}</p>
    </div>

    <div class="clan-frame panel-box rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('calendar.index') }}" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            @if($viewMode !== 'day')
                <input type="hidden" name="day" value="{{ $focus->day }}">
            @endif

            <div>
                <label for="cal-month" class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--theme-muted);">{{ __('calendar.month') }}</label>
                <select id="cal-month" name="month" class="{{ $selectClass }}" style="{{ $selectStyle }}" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($focus->month === $m)>
                            {{ \Carbon\Carbon::createFromDate($focus->year, $m, 1)->locale($locale)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="cal-year" class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--theme-muted);">{{ __('calendar.year') }}</label>
                <select id="cal-year" name="year" class="{{ $selectClass }}" style="{{ $selectStyle }}" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($focus->year === $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            @if($viewMode === 'day')
                <div>
                    <label for="cal-day" class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--theme-muted);">{{ __('calendar.day') }}</label>
                    <select id="cal-day" name="day" class="{{ $selectClass }}" style="{{ $selectStyle }}" onchange="this.form.submit()">
                        @for($d = 1; $d <= $focus->daysInMonth; $d++)
                            <option value="{{ $d }}" @selected($focus->day === $d)>{{ $d }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-2 ml-auto">
                <a href="{{ $prevUrl }}" class="{{ $btnBase }}" style="{{ $btnIdle }}">←</a>
                <a href="{{ $todayUrl }}" class="theme-bg-primary {{ $btnBase }}">{{ __('calendar.today') }}</a>
                <a href="{{ $nextUrl }}" class="{{ $btnBase }}" style="{{ $btnIdle }}">→</a>
            </div>
        </form>

        <div class="calendar-view-tabs mt-4 pt-4" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 18%, transparent);">
            @foreach(['day' => __('calendar.view_day'), 'week' => __('calendar.view_week'), 'month' => __('calendar.view_month')] as $mode => $label)
                @if($viewMode === $mode)
                    <span class="theme-bg-primary {{ $btnBase }}">{{ $label }}</span>
                @else
                    <a href="{{ route('calendar.index', ['view' => $mode, 'year' => $focus->year, 'month' => $focus->month, 'day' => $focus->day]) }}"
                       class="{{ $btnBase }}" style="{{ $btnIdle }}">{{ $label }}</a>
                @endif
            @endforeach
        </div>
    </div>

    @if($viewMode === 'month')
        <div class="clan-frame panel-box rounded-xl overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="calendar-grid-table min-w-[640px]">
                    <thead>
                        <tr>
                            @foreach($weekdayKeys as $dayKey)
                                <th class="px-2 py-3 text-xs font-semibold uppercase tracking-wider text-center" style="color: var(--theme-muted); border-bottom: 1px solid color-mix(in srgb, var(--theme-primary) 22%, transparent);">
                                    {{ __('calendar.weekday.'.$dayKey) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weeks as $week)
                            <tr>
                                @foreach($week as $cell)
                                    @php
                                        $bg = $cell['inMonth']
                                            ? 'color-mix(in srgb, var(--theme-surface) 70%, transparent)'
                                            : 'color-mix(in srgb, var(--theme-surface) 35%, transparent)';
                                        $dayUrl = route('calendar.index', ['view' => 'day', 'year' => $cell['date']->year, 'month' => $cell['date']->month, 'day' => $cell['date']->day]);
                                    @endphp
                                    <td class="p-2 h-28 md:h-32 {{ $cell['isToday'] ? 'calendar-cell--today' : '' }}" style="background: {{ $bg }}; border: 1px solid color-mix(in srgb, var(--theme-primary) 12%, transparent); {{ $cell['inMonth'] ? '' : 'opacity: 0.5;' }}">
                                        <div class="flex items-center justify-between gap-1 mb-1.5">
                                            <a href="{{ $dayUrl }}" class="calendar-day-num {{ $cell['isToday'] ? 'calendar-day-num--today' : '' }} no-underline" style="color: var(--theme-text);">
                                                {{ $cell['date']->day }}
                                            </a>
                                            @if($cell['isToday'])
                                                <span class="text-[0.6rem] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded" style="background: var(--theme-primary); color: #1a1408;">{{ __('calendar.today') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col gap-1 min-w-0">
                                            @foreach(array_slice($cell['events'], 0, 3) as $event)
                                                <a href="{{ route('calendar.show', $event->id) }}" class="calendar-event-chip" title="{{ $event->title }}">
                                                    {{ $event->starts_at->timezone(config('app.timezone'))->format('H:i') }} {{ $event->title }}
                                                </a>
                                            @endforeach
                                            @if(count($cell['events']) > 3)
                                                <span class="text-[0.65rem] px-1" style="color: var(--theme-muted);">+{{ count($cell['events']) - 3 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($viewMode === 'week')
        <div class="clan-frame panel-box rounded-xl overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="calendar-grid-table min-w-[720px]">
                    <thead>
                        <tr>
                            @foreach($weekDays as $i => $cell)
                                <th class="px-2 py-3 text-xs font-semibold uppercase tracking-wider text-center {{ $cell['isToday'] ? 'calendar-cell--today' : '' }}" style="color: var(--theme-muted); border-bottom: 1px solid color-mix(in srgb, var(--theme-primary) 22%, transparent);">
                                    {{ __('calendar.weekday.'.$weekdayKeys[$i]) }}
                                    <div class="mt-1">
                                        <span class="calendar-day-num {{ $cell['isToday'] ? 'calendar-day-num--today' : '' }}" style="color: var(--theme-text);">{{ $cell['date']->day }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($weekDays as $cell)
                                <td class="p-2 h-64 align-top {{ $cell['isToday'] ? 'calendar-cell--today' : '' }}" style="background: color-mix(in srgb, var(--theme-surface) 70%, transparent); border: 1px solid color-mix(in srgb, var(--theme-primary) 12%, transparent);">
                                    <div class="flex flex-col gap-1.5 min-w-0">
                                        @forelse($cell['events'] as $event)
                                            <a href="{{ route('calendar.show', $event->id) }}" class="calendar-event-chip" title="{{ $event->title }}">
                                                {{ $event->starts_at->timezone(config('app.timezone'))->format('H:i') }} {{ $event->title }}
                                            </a>
                                        @empty
                                            <span class="text-xs" style="color: var(--theme-muted);">—</span>
                                        @endforelse
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="clan-frame panel-box rounded-xl p-5 mb-8 {{ $focus->isSameDay($now) ? 'calendar-cell--today' : '' }}">
            <div class="flex items-center gap-3 mb-4">
                <span class="calendar-day-num {{ $focus->isSameDay($now) ? 'calendar-day-num--today' : '' }}" style="color: var(--theme-text); width: 2.5rem; height: 2.5rem; font-size: 1rem;">
                    {{ $focus->day }}
                </span>
                <div>
                    <p class="font-display font-semibold text-lg" style="color: var(--theme-text);">{{ $focus->locale($locale)->translatedFormat('l') }}</p>
                    @if($focus->isSameDay($now))
                        <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded" style="background: var(--theme-primary); color: #1a1408;">{{ __('calendar.today') }}</span>
                    @endif
                </div>
            </div>
            @if($dayEvents->isEmpty())
                <p style="color: var(--theme-muted);">{{ __('calendar.no_events_day') }}</p>
            @else
                <div class="grid gap-2">
                    @foreach($dayEvents as $event)
                        <a href="{{ route('calendar.show', $event->id) }}" class="block rounded-lg px-3 py-3 no-underline" style="background: color-mix(in srgb, var(--theme-primary) 14%, transparent); color: var(--theme-text);">
                            <span class="font-semibold" style="color: var(--theme-primary);">{{ $event->starts_at->timezone(config('app.timezone'))->format('H:i') }}</span>
                            {{ $event->title }}
                            @if($event->location)
                                <span class="text-sm" style="color: var(--theme-muted);"> · {{ $event->location }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <section>
        <h2 class="font-display text-xl font-semibold mb-4" style="color: var(--theme-primary);">
            {{ $viewMode === 'day' ? __('calendar.day_events') : ($viewMode === 'week' ? __('calendar.week_events') : __('calendar.month_events')) }}
        </h2>
        @if($listEvents->isEmpty())
            <div class="clan-frame panel-box rounded-xl px-5 py-6 text-center max-w-xl">
                <p style="color: var(--theme-muted);">
                    {{ $viewMode === 'day' ? __('calendar.no_events_day') : ($viewMode === 'week' ? __('calendar.no_events_week') : __('calendar.no_events_month')) }}
                </p>
            </div>
        @else
            <div class="grid gap-3">
                @foreach($listEvents as $event)
                    <a href="{{ route('calendar.show', $event->id) }}" class="clan-frame panel-box rounded-xl px-5 py-4 flex flex-wrap items-center justify-between gap-3 no-underline hover:opacity-95" style="color: inherit;">
                        <div class="min-w-0">
                            <span class="font-display font-semibold text-base" style="color: var(--theme-text);">{{ $event->title }}</span>
                            @if($event->location)
                                <p class="text-sm mt-0.5" style="color: var(--theme-muted);">{{ $event->location }}</p>
                            @endif
                        </div>
                        <div class="text-sm shrink-0" style="color: var(--theme-primary);">
                            {{ $event->starts_at->timezone(config('app.timezone'))->format(__('general.date_format').' H:i') }}
                            @if($event->ends_at)
                                – {{ $event->ends_at->timezone(config('app.timezone'))->format(__('general.date_format').' H:i') }}
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection

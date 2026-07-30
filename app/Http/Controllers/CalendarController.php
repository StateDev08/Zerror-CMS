<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $tz = config('app.timezone');
        $now = now($tz);

        $view = $request->query('view', 'month');
        if (! in_array($view, ['day', 'week', 'month'], true)) {
            $view = 'month';
        }

        $year = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);
        $day = (int) $request->query('day', $now->day);

        if ($month < 1 || $month > 12 || $year < 1970 || $year > 2100) {
            $year = $now->year;
            $month = $now->month;
        }

        $daysInMonth = Carbon::create($year, $month, 1, 0, 0, 0, $tz)->daysInMonth;
        if ($day < 1 || $day > $daysInMonth) {
            $day = min($now->day, $daysInMonth);
        }

        $focus = Carbon::create($year, $month, $day, 0, 0, 0, $tz);

        [$rangeStart, $rangeEnd] = match ($view) {
            'day' => [$focus->copy()->startOfDay(), $focus->copy()->endOfDay()],
            'week' => [
                $focus->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $focus->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
            ],
            default => [
                $focus->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $focus->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
            ],
        };

        $events = Event::query()
            ->where('visible', true)
            ->where(function ($q) use ($rangeStart, $rangeEnd) {
                $q->whereBetween('starts_at', [$rangeStart, $rangeEnd])
                    ->orWhere(function ($q2) use ($rangeStart, $rangeEnd) {
                        $q2->whereNotNull('ends_at')
                            ->where('starts_at', '<=', $rangeEnd)
                            ->where('ends_at', '>=', $rangeStart);
                    });
            })
            ->orderBy('starts_at')
            ->get();

        $eventsByDay = $this->groupEventsByDay($events, $rangeStart, $rangeEnd, $tz);

        $weeks = [];
        $weekDays = [];
        $dayEvents = collect();

        if ($view === 'month') {
            $weeks = $this->buildWeeks($rangeStart, $rangeEnd, $focus, $now, $eventsByDay);
        } elseif ($view === 'week') {
            $weekDays = $this->buildWeekDays($rangeStart, $now, $eventsByDay);
        } else {
            $key = $focus->format('Y-m-d');
            $dayEvents = collect($eventsByDay[$key] ?? []);
        }

        $listStart = match ($view) {
            'day' => $focus->copy()->startOfDay(),
            'week' => $rangeStart->copy(),
            default => $focus->copy()->startOfMonth(),
        };
        $listEnd = match ($view) {
            'day' => $focus->copy()->endOfDay(),
            'week' => $rangeEnd->copy(),
            default => $focus->copy()->endOfMonth(),
        };

        $listEvents = $events->filter(function (Event $e) use ($listStart, $listEnd, $tz) {
            $start = $e->starts_at->copy()->timezone($tz);
            $end = ($e->ends_at ?: $e->starts_at)->copy()->timezone($tz);

            return $start->lte($listEnd) && $end->gte($listStart);
        })->values();

        $prev = match ($view) {
            'day' => $focus->copy()->subDay(),
            'week' => $focus->copy()->subWeek(),
            default => $focus->copy()->subMonthNoOverflow()->day(1),
        };
        $next = match ($view) {
            'day' => $focus->copy()->addDay(),
            'week' => $focus->copy()->addWeek(),
            default => $focus->copy()->addMonthNoOverflow()->day(1),
        };

        $years = range(max(1970, $now->year - 5), min(2100, $now->year + 5));

        return view('theme::calendar.index', [
            'viewMode' => $view,
            'focus' => $focus,
            'now' => $now,
            'weeks' => $weeks,
            'weekDays' => $weekDays,
            'dayEvents' => $dayEvents,
            'listEvents' => $listEvents,
            'years' => $years,
            'prevUrl' => $this->calendarUrl($view, $prev),
            'nextUrl' => $this->calendarUrl($view, $next),
            'todayUrl' => $this->calendarUrl($view, $now),
            'weekdayKeys' => ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
        ]);
    }

    public function show(int $id): View
    {
        $event = Event::where('visible', true)->findOrFail($id);

        return view('theme::calendar.show', ['event' => $event]);
    }

    /**
     * @param  Collection<int, Event>  $events
     * @return array<string, list<Event>>
     */
    protected function groupEventsByDay(Collection $events, Carbon $rangeStart, Carbon $rangeEnd, string $tz): array
    {
        $eventsByDay = [];
        foreach ($events as $event) {
            $from = $event->starts_at->copy()->timezone($tz)->startOfDay();
            $to = ($event->ends_at ?: $event->starts_at)->copy()->timezone($tz)->startOfDay();
            if ($to->lt($from)) {
                $to = $from->copy();
            }
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                if ($d->lt($rangeStart->copy()->startOfDay()) || $d->gt($rangeEnd->copy()->startOfDay())) {
                    continue;
                }
                $key = $d->format('Y-m-d');
                $eventsByDay[$key] ??= [];
                $eventsByDay[$key][] = $event;
            }
        }

        return $eventsByDay;
    }

    /**
     * @param  array<string, list<Event>>  $eventsByDay
     * @return list<list<array{date: Carbon, inMonth: bool, isToday: bool, events: list<Event>}>>
     */
    protected function buildWeeks(Carbon $rangeStart, Carbon $rangeEnd, Carbon $focus, Carbon $now, array $eventsByDay): array
    {
        $weeks = [];
        $day = $rangeStart->copy()->startOfDay();
        $end = $rangeEnd->copy()->startOfDay();
        while ($day->lte($end)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $key = $day->format('Y-m-d');
                $week[] = [
                    'date' => $day->copy(),
                    'inMonth' => $day->month === $focus->month && $day->year === $focus->year,
                    'isToday' => $day->isSameDay($now),
                    'events' => $eventsByDay[$key] ?? [],
                ];
                $day->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    /**
     * @param  array<string, list<Event>>  $eventsByDay
     * @return list<array{date: Carbon, isToday: bool, events: list<Event>}>
     */
    protected function buildWeekDays(Carbon $weekStart, Carbon $now, array $eventsByDay): array
    {
        $days = [];
        $day = $weekStart->copy()->startOfDay();
        for ($i = 0; $i < 7; $i++) {
            $key = $day->format('Y-m-d');
            $days[] = [
                'date' => $day->copy(),
                'isToday' => $day->isSameDay($now),
                'events' => $eventsByDay[$key] ?? [],
            ];
            $day->addDay();
        }

        return $days;
    }

    protected function calendarUrl(string $view, Carbon $date): string
    {
        return route('calendar.index', [
            'view' => $view,
            'year' => $date->year,
            'month' => $date->month,
            'day' => $date->day,
        ]);
    }
}

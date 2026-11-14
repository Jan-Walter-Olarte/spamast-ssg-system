<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    // A month grid with the events in their day cells (Officers, Advisers and Students).
    public function index(Request $request)
    {
        // ?month=2026-11 . Anything that does not look like YYYY-MM falls back to this month.
        $month = (string) $request->query('month');

        $first = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)
            ? Carbon::createFromFormat('!Y-m', $month)
            : today()->startOfMonth();

        // The grid starts on the Sunday on or before the 1st, and ends on the Saturday on or after the last day.
        $gridStart = $first->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd   = $first->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        // One query for every event shown in the grid, grouped by date ("2026-11-20" => events).
        $events = Event::whereBetween('event_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->orderBy('event_date')
            ->orderBy('id')
            ->get(['id', 'title', 'venue', 'event_date', 'day_type'])
            ->groupBy(fn ($event) => $event->event_date->toDateString());

        // One entry per day of the grid.
        $days = collect();

        for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay()) {
            $days->push([
                'date'     => $day->copy(),
                'in_month' => $day->month === $first->month,
                'is_today' => $day->isToday(),
                'events'   => $events->get($day->toDateString(), collect()),
            ]);
        }

        return view('calendar.index', [
            'first'    => $first,
            'weeks'    => $days->chunk(7), // rows of 7 days
            'previous' => $first->copy()->subMonthNoOverflow()->format('Y-m'),
            'next'     => $first->copy()->addMonthNoOverflow()->format('Y-m'),
            'count'    => Event::whereYear('event_date', $first->year)
                              ->whereMonth('event_date', $first->month)
                              ->count(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Support\DashboardCache;

class EventController extends Controller
{
    // Show the empty create form.
    public function create()
    {
        return view('officer.events.create');
    }

    // Validate, then save the event and its sessions together.
    public function store(Request $request): RedirectResponse
    {
        $data    = $request->validate($this->rules($request));
        $periods = $this->periods($data['day_type']);

        // One transaction: the event and its sessions are saved, or nothing is.
        DB::transaction(function () use ($data, $periods, $request) {
            $event = Event::create([
                'title'         => $data['title'],
                'description'   => $data['description'] ?? null,
                'venue'         => $data['venue'],
                'event_date'    => $data['event_date'],
                'day_type'      => $data['day_type'],
                'availability'  => $data['availability'],
                'fine_per_miss' => $data['fine_per_miss'],
                'created_by'    => $request->user()->id,
            ]);

            foreach ($periods as $period) {
                EventSession::create(['event_id' => $event->id, 'period' => $period] + $this->times($data, $period));
            }
        });

        DashboardCache::events();

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    // Show the edit form.
    public function edit(Event $event)
    {
        Gate::authorize('update', $event);

        return view('officer.events.edit', [
            'event'      => $event,
            'sessionMap' => $event->sessions->keyBy('period'),
        ]);
    }

    // Validate and save changes.
    public function update(Request $request, Event $event): RedirectResponse
    {
        Gate::authorize('update', $event);

        $data    = $request->validate($this->rules($request));
        $periods = $this->periods($data['day_type']);

        $dayTypeChanged = $event->day_type !== $data['day_type'];

        // Changing whole day / half day would delete sessions and their scans.
        if ($dayTypeChanged && $event->attendances()->exists()) {
            return back()->withInput()
                ->with('error', 'The day type cannot be changed because attendance has already been recorded for this event.');
        }

        DB::transaction(function () use ($event, $data, $periods, $dayTypeChanged) {
            $event->update([
                'title'         => $data['title'],
                'description'   => $data['description'] ?? null,
                'venue'         => $data['venue'],
                'event_date'    => $data['event_date'],
                'day_type'      => $data['day_type'],
                'availability'  => $data['availability'],
                'fine_per_miss' => $data['fine_per_miss'],
            ]);

            // Only when the day type changed do we replace the sessions.
            if ($dayTypeChanged) {
                $event->sessions()->delete();
            }

            // Otherwise the existing session rows (and their attendance) are kept, only the times change.
            foreach ($periods as $period) {
                EventSession::updateOrCreate(
                    ['event_id' => $event->id, 'period' => $period],
                    $this->times($data, $period)
                );
            }
        });

        DashboardCache::events();

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    // Delete an event, but only if nobody has attended it.
    public function destroy(Event $event): RedirectResponse
    {
        if ($event->attendances()->exists()) {
            return redirect()->route('events.index')
                ->with('error', 'Cannot delete this event because attendance has already been recorded.');
        }

        Gate::authorize('delete', $event);

        $event->delete(); // its sessions are removed by cascade

        DashboardCache::events();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted.');
    }

    // The session periods an event needs.
    private function periods(string $dayType): array
    {
        return $dayType === 'whole_day' ? ['morning', 'afternoon'] : ['half_day'];
    }

    // Pick the four time values of one session from the validated data.
    private function times(array $data, string $period): array
    {
        $session = $data['sessions'][$period] ?? [];

        return [
            'check_in_start'  => $session['check_in_start'] ?? null,
            'check_in_end'    => $session['check_in_end'] ?? null,
            'check_out_start' => $session['check_out_start'] ?? null,
            'check_out_end'   => $session['check_out_end'] ?? null,
        ];
    }

    // Validation rules, including the session time rules.
    private function rules(Request $request): array
    {
        $rules = [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'venue'         => ['required', 'string', 'max:255'],
            'event_date'    => ['required', 'date'],
            'day_type'      => ['required', 'in:whole_day,half_day'],
            'availability'  => ['required', 'in:scheduled,manual'],
            'fine_per_miss' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];

        $dayType = $request->input('day_type');

        if (! in_array($dayType, ['whole_day', 'half_day'], true)) {
            return $rules; // the day_type rule above will report the problem
        }

        // Times are required only for scheduled events.
        $required = $request->input('availability') === 'scheduled' ? 'required' : 'nullable';

        foreach ($this->periods($dayType) as $period) {
            $rules["sessions.$period.check_in_start"]  = [$required, 'date_format:H:i'];
            $rules["sessions.$period.check_in_end"]    = [$required, 'date_format:H:i', "after:sessions.$period.check_in_start"];
            $rules["sessions.$period.check_out_start"] = [$required, 'date_format:H:i'];
            $rules["sessions.$period.check_out_end"]   = [$required, 'date_format:H:i', "after:sessions.$period.check_out_start"];
        }

        return $rules;
    }
}

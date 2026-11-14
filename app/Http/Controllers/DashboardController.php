<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Institute;
use App\Models\Penalty;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    // Admin: users per role, pending approvals, institutes and programs.
    public function admin()
    {
        $stats = Cache::remember('admin.stats', now()->addMinutes(10), function () {
            // A plain array of role name => number of users (no models are cached).
            $roles = Role::withCount('users')->get()
                ->mapWithKeys(fn ($role) => [$role->name => $role->users_count])
                ->all();

            return [
                'roles'      => $roles,
                'total'      => array_sum($roles),
                'pending'    => User::where('is_active', false)->count(),
                'institutes' => Institute::count(),
                'programs'   => Program::count(),
            ];
        });

        return view('dashboards.admin', ['stats' => $stats]);
    }

    // Officer: students, upcoming events, unpaid fines, announcements and charts.
    public function officer()
    {
        $stats = Cache::remember('officer.stats', now()->addMinutes(10), function () {
            return [
                'students'      => Student::count(),
                'events'        => Event::whereDate('event_date', '>=', today())->count(),
                'unpaid'        => Penalty::where('status', 'unpaid')->count(),
                'announcements' => Announcement::count(),
            ];
        });

        return view('dashboards.officer', [
            'stats'               => $stats,
            'charts'              => $this->eventCharts(),
            'latestAnnouncements' => $this->latestAnnouncements(),
        ]);
    }

    // Adviser: upcoming events, attendance records, unpaid fines and charts.
    public function adviser()
    {
        $stats = Cache::remember('adviser.stats', now()->addMinutes(10), function () {
            return [
                'events'     => Event::whereDate('event_date', '>=', today())->count(),
                'attendance' => Attendance::count(),
                'unpaid'     => Penalty::where('status', 'unpaid')->count(),
            ];
        });

        return view('dashboards.adviser', [
            'stats'               => $stats,
            'charts'              => $this->eventCharts(),
            'latestAnnouncements' => $this->latestAnnouncements(),
        ]);
    }

    // Student: upcoming events and their own attendance count.
    public function student(Request $request)
    {
        $user = $request->user();

        // A key per user, because this data is personal.
        $stats = Cache::remember("student.stats.{$user->id}", now()->addMinutes(10), function () use ($user) {
            $student = $user->student;

            return [
                'events'     => Event::whereDate('event_date', '>=', today())->count(),
                'attendance' => $student ? Attendance::where('student_id', $student->id)->count() : 0,
            ];
        });

        return view('dashboards.student', [
            'stats'               => $stats,
            'latestAnnouncements' => $this->latestAnnouncements(),
        ]);
    }

    // The numbers for the Officer and Adviser charts, saved for 10 minutes as plain arrays.
    private function eventCharts(): array
    {
        return Cache::remember('charts.events', now()->addMinutes(10), function () {
            // Chart 1: students who attended each of the latest 6 events.
            $events = Event::orderByDesc('event_date')
                ->orderByDesc('id')
                ->take(6)
                ->get(['id', 'title', 'event_date']);

            // Distinct students per event: a student with two sessions counts once.
            $counts = DB::table('attendances as a')
                ->join('event_sessions as es', 'es.id', '=', 'a.event_session_id')
                ->whereIn('es.event_id', $events->pluck('id'))
                ->whereNotNull('a.check_in_at')
                ->groupBy('es.event_id')
                ->selectRaw('es.event_id, COUNT(DISTINCT a.student_id) AS total')
                ->pluck('total', 'event_id');

            $attendance = $events->reverse()->values()->map(fn ($event) => [
                'label' => Str::limit($event->title, 22),
                'sub'   => $event->event_date->format('M d'),
                'value' => (int) ($counts[$event->id] ?? 0),
            ])->all();

            // Chart 2: unpaid fine money for each program (biggest first).
            $fines = DB::table('penalties as p')
                ->join('students as s', 's.id', '=', 'p.student_id')
                ->join('programs as pr', 'pr.id', '=', 's.program_id')
                ->where('p.status', 'unpaid')
                ->groupBy('pr.code')
                ->orderByRaw('SUM(p.amount) DESC')
                ->limit(8)
                ->selectRaw('pr.code AS label, SUM(p.amount) AS value')
                ->get()
                ->map(fn ($row) => [
                    'label' => $row->label,
                    'sub'   => '',
                    'value' => round((float) $row->value, 2),
                ])->all();

            return ['attendance' => $attendance, 'fines' => $fines];
        });
    }

    // The three newest announcements. Fetched fresh each time, so a new post shows at once.
    private function latestAnnouncements()
    {
        return Announcement::with('user')
            ->latest()
            ->orderByDesc('id')
            ->take(3)
            ->get();
    }
}

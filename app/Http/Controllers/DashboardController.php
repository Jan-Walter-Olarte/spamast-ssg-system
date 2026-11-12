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

    // Officer: students, upcoming events, unpaid fines and announcements.
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

        return view('dashboards.officer', ['stats' => $stats]);
    }

    // Adviser: upcoming events, attendance records and unpaid fines.
    public function adviser()
    {
        $stats = Cache::remember('adviser.stats', now()->addMinutes(10), function () {
            return [
                'events'     => Event::whereDate('event_date', '>=', today())->count(),
                'attendance' => Attendance::count(),
                'unpaid'     => Penalty::where('status', 'unpaid')->count(),
            ];
        });

        return view('dashboards.adviser', ['stats' => $stats]);
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

        return view('dashboards.student', ['stats' => $stats]);
    }
}

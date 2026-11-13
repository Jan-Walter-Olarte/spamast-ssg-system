<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Penalty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    // List the events that have a report.
    public function index(Request $request)
    {
        Gate::authorize('view-reports');

        $search = trim((string) $request->query('search'));

        $events = Event::withCount('attendances')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('venue', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('event_date')
            ->paginate(10)
            ->withQueryString();

        return view('reports.index', [
            'events' => $events,
            'search' => $search,
        ]);
    }

    // The printable report of one event.
    public function show(Event $event)
    {
        Gate::authorize('view-reports');

        // Query Builder: join five tables, keep this event and rows with a check-in,
        // then count distinct students per institute and program.
        $rows = DB::table('attendances as a')
            ->join('event_sessions as es', 'es.id', '=', 'a.event_session_id')
            ->join('students as s', 's.id', '=', 'a.student_id')
            ->join('programs as p', 'p.id', '=', 's.program_id')
            ->join('institutes as i', 'i.id', '=', 'p.institute_id')
            ->where('es.event_id', $event->id)
            ->whereNotNull('a.check_in_at')
            ->groupBy('i.code', 'p.code')
            ->orderBy('i.code')
            ->orderBy('p.code')
            ->selectRaw("
                i.code AS institute_code,
                p.code AS program_code,
                COUNT(DISTINCT a.student_id) AS total,
                COUNT(DISTINCT CASE WHEN s.sex = 'Male' THEN a.student_id END) AS male,
                COUNT(DISTINCT CASE WHEN s.sex = 'Female' THEN a.student_id END) AS female
            ")
            ->get();

        // Group the program rows by institute and add a subtotal for each institute.
        $institutes = $rows->groupBy('institute_code')->map(function ($programs) {
            return [
                'programs' => $programs,
                'total'    => $programs->sum('total'),
                'male'     => $programs->sum('male'),
                'female'   => $programs->sum('female'),
            ];
        });

        $grand = [
            'total'  => $rows->sum('total'),
            'male'   => $rows->sum('male'),
            'female' => $rows->sum('female'),
        ];

        // Per-student table: students who have a penalty for this event.
        $penalties = Penalty::with(['student.user', 'student.program'])
            ->where('event_id', $event->id)
            ->get()
            ->sortBy(fn ($penalty) => $penalty->student->student_no);

        // ---------- NEW: fine summary by program (Support Collection) ----------

        // Step 1: load this event's penalties together with each student's program.
        $eventPenalties = $event->penalties()->with('student.program')->get();

        // Step 2: put the penalties into a plain collection.
        $fineSummary = collect($eventPenalties)
            // Step 3: group the penalties by the student's program code (BSIT, BSA, ...).
            ->groupBy(fn ($penalty) => $penalty->student->program->code)
            // Step 4: turn each group into one summary row: program, number of students, total amount.
            ->map(fn ($group, $program) => [
                'program'  => $program,
                'students' => $group->count(),
                'total'    => round((float) $group->sum('amount'), 2),
            ])
            // Step 5: put the biggest totals first.
            ->sortByDesc('total')
            // Step 6: renumber the rows 0, 1, 2 ... after sorting.
            ->values();

        // Step 7: add up the program totals for the grand total.
        $grandTotal = round((float) $fineSummary->sum('total'), 2);

        return view('reports.show', [
            'event'       => $event->loadCount('sessions'),
            'institutes'  => $institutes,
            'grand'       => $grand,
            'penalties'   => $penalties,
            'fineTotal'   => $penalties->sum('amount'),
            'fineSummary' => $fineSummary,
            'grandTotal'  => $grandTotal,
        ]);
    }
}
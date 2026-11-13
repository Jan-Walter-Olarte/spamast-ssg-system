<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Penalty;
use Illuminate\Http\Request;

class MyRecordsController extends Controller
{
    // The logged-in student's own attendance, newest first.
    public function attendance(Request $request)
    {
        $student = $request->user()->student;

        $attendances = null;
        $complete    = 0;
        $incomplete  = 0;

        if ($student) {
            $base = Attendance::where('student_id', $student->id);

            // Complete = both a check-in and a check-out were recorded.
            $complete   = (clone $base)->whereNotNull('check_in_at')->whereNotNull('check_out_at')->count();
            $incomplete = (clone $base)->count() - $complete;

            $attendances = (clone $base)
                ->with('session.event')
                ->orderByDesc('check_in_at')
                ->orderByDesc('id')
                ->paginate(15);
        }

        return view('student.my-attendance', [
            'student'     => $student,
            'attendances' => $attendances,
            'complete'    => $complete,
            'incomplete'  => $incomplete,
        ]);
    }

    // The logged-in student's own fines and sanctions, newest first.
    public function fines(Request $request)
    {
        $student = $request->user()->student;

        $penalties   = null;
        $totalAll    = 0;
        $totalUnpaid = 0;

        if ($student) {
            $base = Penalty::where('student_id', $student->id);

            $totalAll    = (float) (clone $base)->sum('amount');
            $totalUnpaid = (float) (clone $base)->where('status', 'unpaid')->sum('amount');

            $penalties = (clone $base)
                ->with('event')
                ->latest()
                ->orderByDesc('id')
                ->paginate(15);
        }

        return view('student.my-fines', [
            'student'     => $student,
            'penalties'   => $penalties,
            'totalAll'    => $totalAll,
            'totalUnpaid' => $totalUnpaid,
        ]);
    }
}

<?php

namespace App\Support;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class DashboardCache
{
    // The Admin dashboard numbers changed.
    public static function admin(): void
    {
        Cache::forget('admin.stats');
    }

    // The Officer dashboard numbers (and the shared event charts) changed.
    public static function officer(): void
    {
        Cache::forget('officer.stats');
        Cache::forget('charts.events');
    }

    // The Adviser dashboard numbers (and the shared event charts) changed.
    public static function adviser(): void
    {
        Cache::forget('adviser.stats');
        Cache::forget('charts.events');
    }

    // One student's dashboard numbers changed (the key belongs to the user id).
    public static function student(int $userId): void
    {
        Cache::forget("student.stats.{$userId}");
    }

    // Every student's dashboard changed (for example, a new upcoming event).
    public static function allStudents(): void
    {
        Student::pluck('user_id')->each(fn ($userId) => self::student($userId));
    }

    // An event was added, changed or deleted: it affects the Officer, Adviser and every Student.
    public static function events(): void
    {
        self::officer();
        self::adviser();
        self::allStudents();
    }

    // Users or students were added or removed: it affects the Admin and Officer counts.
    public static function people(): void
    {
        self::admin();
        self::officer();
    }
}

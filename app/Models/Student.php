<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'program_id', 'student_no', 'sex', 'year_level'])]
class Student extends Model
{
    /**
     * The login account of this student (students.user_id points to users.id).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The program this student is enrolled in (students.program_id points to programs.id).
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * A student has many attendance records.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * A student has many penalties (one per event at most).
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }
}

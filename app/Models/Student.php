<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}

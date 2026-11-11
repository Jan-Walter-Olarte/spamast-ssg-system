<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['event_session_id', 'student_id', 'check_in_at', 'check_out_at'])]
class Attendance extends Model
{
    protected $table = 'attendances';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_at'  => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    // Relationships are added in a later step.
}

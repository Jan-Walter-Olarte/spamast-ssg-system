<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'program_id', 'student_no', 'sex', 'year_level'])]
class Student extends Model
{
    // Relationships are added in a later step.
}

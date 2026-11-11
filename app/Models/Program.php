<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['institute_id', 'code', 'name'])]
class Program extends Model
{
    // Relationships are added in a later step.
}

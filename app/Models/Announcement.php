<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'title', 'body', 'attachment'])]
class Announcement extends Model
{
    // Relationships are added in a later step.
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'description',
    'venue',
    'event_date',
    'day_type',
    'availability',
    'fine_per_miss',
    'created_by',
])]
class Event extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date'    => 'date',
            'fine_per_miss' => 'decimal:2',
        ];
    }

    // Relationships are added in a later step.
}

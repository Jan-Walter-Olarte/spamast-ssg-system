<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'event_id',
    'period',
    'check_in_start',
    'check_in_end',
    'check_out_start',
    'check_out_end',
    'check_in_open',
    'check_out_open',
])]
class EventSession extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_open'  => 'boolean',
            'check_out_open' => 'boolean',
        ];
    }

    // Relationships are added in a later step.
}

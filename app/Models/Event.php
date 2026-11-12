<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    /**
     * An event has many sessions (morning, afternoon or half day).
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(EventSession::class);
    }

    /**
     * An event reaches its attendance records through its sessions.
     */
    public function attendances(): HasManyThrough
    {
        return $this->hasManyThrough(Attendance::class, EventSession::class);
    }

    /**
     * An event has many penalties.
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    /**
     * The user who created the event (events.created_by points to users.id).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

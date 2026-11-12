<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * The event this session belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * A session has many attendance records.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Is check-in open right now?
     */
    public function isCheckInOpen(): bool
    {
        return $this->isOpen('check_in');
    }

    /**
     * Is check-out open right now?
     */
    public function isCheckOutOpen(): bool
    {
        return $this->isOpen('check_out');
    }

    /**
     * One shared rule for both. $type is 'check_in' or 'check_out',
     * which matches the column names (check_in_open, check_in_start, check_in_end).
     */
    private function isOpen(string $type): bool
    {
        $openColumn  = $type . '_open';
        $startColumn = $type . '_start';
        $endColumn   = $type . '_end';

        // Manual event: an Officer opens and closes it by hand.
        if ($this->event->availability === 'manual') {
            return (bool) $this->$openColumn;
        }

        // Scheduled event: it must be today ...
        if (! $this->event->event_date->isToday()) {
            return false;
        }

        // ... the times must be set ...
        if (! $this->$startColumn || ! $this->$endColumn) {
            return false;
        }

        // ... and the current time must be inside the window.
        $now = now()->format('H:i:s');

        return $now >= $this->$startColumn && $now <= $this->$endColumn;
    }
}

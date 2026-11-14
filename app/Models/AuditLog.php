<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'action',
    'description',
    'subject_type',
    'subject_id',
    'ip_address',
    'created_at',
])]
class AuditLog extends Model
{
    // The table has only created_at, and we set it ourselves in record().
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * The person who did it.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Write one entry: AuditLog::record('user.deleted', 'Deleted the account of ...', $user);
     * The actor is always the logged-in user. A failure here never stops the real action.
     */
    public static function record(string $action, string $description, ?Model $subject = null): void
    {
        try {
            static::create([
                'user_id'      => auth()->id(),
                'action'       => $action,
                'description'  => Str::limit($description, 480),
                'subject_type' => $subject ? class_basename($subject) : null,
                'subject_id'   => $subject?->getKey(),
                'ip_address'   => request()->ip(),
                'created_at'   => now(), // set here, so it uses the app's Asia/Manila time
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'password',
    'role_id',
    'is_active',
    'is_admin',
    'photo',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
            'is_admin'  => 'boolean',
        ];
    }

    /**
     * A user belongs to one role (users.role_id points to roles.id).
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * A user who is a Student has one students row.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * A user can write many announcements.
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * The full name for display: first, middle and last name with clean spacing.
     * Use it as $user->full_name. It is calculated, not stored in the database.
     */
    public function getFullNameAttribute(): string
    {
        // squish() joins the words with single spaces, so a blank middle name leaves no gap.
        return Str::squish("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Does this user have any of the given roles?
     * Example: $user->hasRole('Officer', 'Adviser')
     */
    public function hasRole(string ...$names): bool
    {
        return in_array($this->role?->name, $names, true);
    }

    /**
     * The route name of this user's own dashboard.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role?->name) {
            'Admin'   => 'admin.dashboard',
            'Officer' => 'officer.dashboard',
            'Adviser' => 'adviser.dashboard',
            default   => 'student.dashboard',
        };
    }
}

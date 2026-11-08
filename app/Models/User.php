<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'password',
    'role_id',
    'is_active',
    'is_admin',
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
            'is_active' => 'boolean', // 1/0 in MySQL becomes true/false in PHP
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
}

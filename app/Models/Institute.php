<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['code', 'name'])]
class Institute extends Model
{
    /**
     * An institute has many programs.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * An institute reaches its students through its programs.
     */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, Program::class);
    }
}

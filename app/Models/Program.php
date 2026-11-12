<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['institute_id', 'code', 'name'])]
class Program extends Model
{
    /**
     * The institute this program belongs to (programs.institute_id points to institutes.id).
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * A program has many students.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}

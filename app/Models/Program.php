<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}

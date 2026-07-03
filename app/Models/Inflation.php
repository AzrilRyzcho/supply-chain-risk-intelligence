<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['country_id', 'year', 'rate'])]
class Inflation extends Model
{
    /**
     * Get the country that owns the inflation record.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

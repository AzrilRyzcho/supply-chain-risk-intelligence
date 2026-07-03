<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'code', 'country_id', 'latitude', 'longitude'])]
class Port extends Model
{
    /**
     * Get the country where the port is located.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['country_id', 'temperature', 'rain', 'wind_speed', 'storm_risk', 'fetched_at'])]
class Weather extends Model
{
    protected $table = 'weather';

    /**
     * Get the country that owns the weather data.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

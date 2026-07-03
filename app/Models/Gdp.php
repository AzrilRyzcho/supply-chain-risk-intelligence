<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['country_id', 'year', 'value'])]
class Gdp extends Model
{
    protected $table = 'gdps';

    /**
     * Get the country that owns the GDP record.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

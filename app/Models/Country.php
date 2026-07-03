<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'code', 'currency_code', 'region', 'latitude', 'longitude'])]
class Country extends Model
{
    /**
     * Get the weather record associated with the country.
     */
    public function weather(): HasOne
    {
        return $this->hasOne(Weather::class);
    }

    /**
     * Get the inflation rates for the country.
     */
    public function inflations(): HasMany
    {
        return $this->hasMany(Inflation::class);
    }

    /**
     * Get the GDP records for the country.
     */
    public function gdps(): HasMany
    {
        return $this->hasMany(Gdp::class);
    }

    /**
     * Get the news articles for the country.
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    /**
     * Get the ports for the country.
     */
    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    /**
     * Get the risk scores for the country.
     */
    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    /**
     * Get the users that watchlist this country.
     */
    public function watchlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'watchlists');
    }
}

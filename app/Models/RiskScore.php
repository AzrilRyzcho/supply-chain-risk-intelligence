<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['country_id', 'weather_score', 'inflation_score', 'currency_score', 'sentiment_score', 'total_score', 'calculated_at'])]
class RiskScore extends Model
{
    protected $table = 'risk_scores';

    /**
     * Get the country that owns the risk score history.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the risk category label (Low, Medium, High).
     */
    public function getCategoryAttribute(): string
    {
        if ($this->total_score >= 50) {
            return 'High';
        } elseif ($this->total_score >= 25) {
            return 'Medium';
        }
        return 'Low';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['country_id', 'title', 'source', 'url', 'sentiment', 'positive_score', 'negative_score', 'published_at'])]
class News extends Model
{
    protected $table = 'news';

    protected $appends = ['risk_score'];

    /**
     * Get the country associated with the news.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Compute dynamic risk score.
     */
    public function getRiskScoreAttribute(): int
    {
        if ($this->sentiment === 'negative') {
            $score = 60 + ($this->negative_score * 8);
            return min(95, max(60, $score));
        } elseif ($this->sentiment === 'positive') {
            $score = 40 - ($this->positive_score * 8);
            return min(40, max(15, $score));
        } else {
            return 45;
        }
    }
}

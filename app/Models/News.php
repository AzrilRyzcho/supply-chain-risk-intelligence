<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['country_id', 'title', 'source', 'url', 'sentiment', 'positive_score', 'negative_score', 'published_at'])]
class News extends Model
{
    protected $table = 'news';

    /**
     * Get the country associated with the news.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

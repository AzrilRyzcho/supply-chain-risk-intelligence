<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'slug', 'content', 'published_at'])]
class Article extends Model
{
    /**
     * Get the admin author who wrote the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

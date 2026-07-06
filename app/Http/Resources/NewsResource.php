<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'country_name' => $this->country ? $this->country->name : null,
            'title' => $this->title,
            'source' => $this->source,
            'url' => $this->url,
            'sentiment' => $this->sentiment,
            'positive_score' => (float) $this->positive_score,
            'negative_score' => (float) $this->negative_score,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
        ];
    }
}

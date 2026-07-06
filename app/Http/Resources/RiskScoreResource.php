<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiskScoreResource extends JsonResource
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
            'country_code' => $this->country ? $this->country->code : null,
            'weather_score' => (int) $this->weather_score,
            'inflation_score' => (int) $this->inflation_score,
            'currency_score' => (int) $this->currency_score,
            'sentiment_score' => (int) $this->sentiment_score,
            'total_score' => (int) $this->total_score,
            'calculated_at' => $this->calculated_at,
            'created_at' => $this->created_at,
        ];
    }
}

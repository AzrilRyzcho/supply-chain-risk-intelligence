<?php

namespace Tests\Feature;

use App\Services\SentimentService;
use Tests\TestCase;

class SentimentServiceTest extends TestCase
{
    public function test_lexicon_sentiment_service_classifies_positive_correctly(): void
    {
        $service = app(SentimentService::class);
        $result = $service->analyze("Great growth, stable profit, and successful implementation.");

        $this->assertEquals('positive', $result['sentiment']);
        $this->assertGreaterThan(0, $result['positive_score']);
        $this->assertEquals(0, $result['negative_score']);
    }

    public function test_lexicon_sentiment_service_classifies_negative_correctly(): void
    {
        $service = app(SentimentService::class);
        $result = $service->analyze("Severe recession, delays, and supply chain collapse due to war.");

        $this->assertEquals('negative', $result['sentiment']);
        $this->assertEquals(0, $result['positive_score']);
        $this->assertGreaterThan(0, $result['negative_score']);
    }

    public function test_lexicon_sentiment_service_classifies_neutral_correctly(): void
    {
        $service = app(SentimentService::class);
        $result = $service->analyze("This is a simple daily logistical report showing route numbers.");

        $this->assertEquals('neutral', $result['sentiment']);
        $this->assertEquals(0, $result['positive_score']);
        $this->assertEquals(0, $result['negative_score']);
    }
}

<?php

namespace App\Services;

class SentimentService
{
    // Lexicon arrays
    protected array $positiveWords = [
        'increase', 'growth', 'implement', 'improve', 'throughput', 'success', 'modernize', 
        'rise', 'boost', 'expand', 'benefit', 'agreement', 'recovery', 'gain', 'stable', 
        'positive', 'surge', 'optimism', 'strengthen', 'advance', 'achieve', 'secure',
        'safe', 'profit', 'surplus', 'resolve', 'innovate', 'cooperate'
    ];

    protected array $negativeWords = [
        'decrease', 'decline', 'war', 'congestion', 'delay', 'loss', 'crisis', 'risk', 
        'threat', 'inflation', 'conflict', 'disruption', 'drop', 'fail', 'fall', 'problem', 
        'negative', 'tariff', 'strike', 'sanction', 'blockade', 'slowdown', 'bottleneck',
        'shortage', 'tension', 'dispute', 'collapse', 'recession', 'escalate'
    ];

    /**
     * Analyze text sentiment using lexicon word counts.
     * Returns positive_score, negative_score, and sentiment (positive, neutral, negative).
     */
    public function analyze(string $text): array
    {
        $text = strtolower($text);
        
        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($this->positiveWords as $word) {
            $positiveScore += substr_count($text, $word);
        }

        foreach ($this->negativeWords as $word) {
            $negativeScore += substr_count($text, $word);
        }

        if ($positiveScore > $negativeScore) {
            $sentiment = 'positive';
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }

        return [
            'sentiment' => $sentiment,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
        ];
    }
}

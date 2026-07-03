<?php

if (!function_exists('format_percentage')) {
    /**
     * Format a float number as a percentage string.
     */
    function format_percentage(float $value, int $decimals = 1): string
    {
        return number_format($value, $decimals) . '%';
    }
}

if (!function_exists('get_risk_badge_class')) {
    /**
     * Get the Bootstrap badge CSS class based on the risk score.
     */
    function get_risk_badge_class(int $score): string
    {
        if ($score <= 30) {
            return 'bg-success'; // Low risk
        } elseif ($score <= 60) {
            return 'bg-warning text-dark'; // Medium risk
        }
        return 'bg-danger'; // High risk
    }
}

if (!function_exists('get_sentiment_badge_class')) {
    /**
     * Get the Bootstrap badge CSS class based on the sentiment.
     */
    function get_sentiment_badge_class(string $sentiment): string
    {
        $sentiment = strtolower($sentiment);
        if ($sentiment === 'positive') {
            return 'bg-success';
        } elseif ($sentiment === 'negative') {
            return 'bg-danger';
        }
        return 'bg-secondary';
    }
}

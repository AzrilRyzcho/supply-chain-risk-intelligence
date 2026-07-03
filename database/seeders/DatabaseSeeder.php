<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use App\Models\Weather;
use App\Models\Currency;
use App\Models\Inflation;
use App\Models\Gdp;
use App\Models\News;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Mitra User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // 2. Seed Countries
        $germany = Country::create([
            'name' => 'Jerman',
            'code' => 'DE',
            'currency_code' => 'EUR',
            'region' => 'Europe',
            'latitude' => 52.5200,
            'longitude' => 13.4050,
        ]);

        $indonesia = Country::create([
            'name' => 'Indonesia',
            'code' => 'ID',
            'currency_code' => 'IDR',
            'region' => 'Asia',
            'latitude' => -0.7893,
            'longitude' => 113.9213,
        ]);

        $china = Country::create([
            'name' => 'China',
            'code' => 'CN',
            'currency_code' => 'CNY',
            'region' => 'Asia',
            'latitude' => 35.8617,
            'longitude' => 104.1954,
        ]);

        $australia = Country::create([
            'name' => 'Australia',
            'code' => 'AU',
            'currency_code' => 'AUD',
            'region' => 'Oceania',
            'latitude' => -25.2744,
            'longitude' => 133.7751,
        ]);

        // 3. Seed Weather
        Weather::create([
            'country_id' => $germany->id,
            'temperature' => 18.5,
            'rain' => 2.3,
            'wind_speed' => 15.4,
            'storm_risk' => 10.0,
            'fetched_at' => now(),
        ]);

        Weather::create([
            'country_id' => $indonesia->id,
            'temperature' => 28.2,
            'rain' => 12.5,
            'wind_speed' => 8.2,
            'storm_risk' => 5.0,
            'fetched_at' => now(),
        ]);

        Weather::create([
            'country_id' => $china->id,
            'temperature' => 22.1,
            'rain' => 4.2,
            'wind_speed' => 12.1,
            'storm_risk' => 15.0,
            'fetched_at' => now(),
        ]);

        Weather::create([
            'country_id' => $australia->id,
            'temperature' => 24.6,
            'rain' => 0.5,
            'wind_speed' => 22.1,
            'storm_risk' => 8.0,
            'fetched_at' => now(),
        ]);

        // 4. Seed Currencies (against USD base)
        Currency::create(['code' => 'USD', 'rate_to_usd' => 1.0000, 'fetched_at' => now()]);
        Currency::create(['code' => 'EUR', 'rate_to_usd' => 0.9200, 'fetched_at' => now()]);
        Currency::create(['code' => 'IDR', 'rate_to_usd' => 15450.0000, 'fetched_at' => now()]);
        Currency::create(['code' => 'CNY', 'rate_to_usd' => 7.2500, 'fetched_at' => now()]);
        Currency::create(['code' => 'AUD', 'rate_to_usd' => 1.4800, 'fetched_at' => now()]);

        // 5. Seed Inflation Trends (5 Years: 2021-2025)
        $inflationData = [
            $germany->id => [2021 => 3.1, 2022 => 6.9, 2023 => 5.9, 2024 => 2.2, 2025 => 2.0],
            $indonesia->id => [2021 => 1.6, 2022 => 4.2, 2023 => 3.7, 2024 => 2.8, 2025 => 2.5],
            $china->id => [2021 => 0.9, 2022 => 2.0, 2023 => 0.2, 2024 => 0.5, 2025 => 1.0],
            $australia->id => [2021 => 2.8, 2022 => 6.6, 2023 => 5.6, 2024 => 3.2, 2025 => 2.8],
        ];

        foreach ($inflationData as $countryId => $years) {
            foreach ($years as $year => $rate) {
                Inflation::create([
                    'country_id' => $countryId,
                    'year' => $year,
                    'rate' => $rate,
                ]);
            }
        }

        // 6. Seed GDP Trends (5 Years: 2021-2025) in Billions USD
        $gdpData = [
            $germany->id => [2021 => 4200.0, 2022 => 4070.0, 2023 => 4450.0, 2024 => 4500.0, 2025 => 4600.0],
            $indonesia->id => [2021 => 1180.0, 2022 => 1320.0, 2023 => 1370.0, 2024 => 1420.0, 2025 => 1480.0],
            $china->id => [2021 => 17800.0, 2022 => 17900.0, 2023 => 18500.0, 2024 => 19200.0, 2025 => 20000.0],
            $australia->id => [2021 => 1550.0, 2022 => 1690.0, 2023 => 1720.0, 2024 => 1780.0, 2025 => 1840.0],
        ];

        foreach ($gdpData as $countryId => $years) {
            foreach ($years as $year => $value) {
                Gdp::create([
                    'country_id' => $countryId,
                    'year' => $year,
                    'value' => $value,
                ]);
            }
        }

        // 7. Seed Ports
        Port::create([
            'name' => 'Port of Hamburg',
            'code' => 'DEHAM',
            'country_id' => $germany->id,
            'latitude' => 53.5450,
            'longitude' => 9.9480,
        ]);

        Port::create([
            'name' => 'Tanjung Priok',
            'code' => 'IDTPP',
            'country_id' => $indonesia->id,
            'latitude' => -6.1030,
            'longitude' => 106.8790,
        ]);

        Port::create([
            'name' => 'Port of Shanghai',
            'code' => 'CNSHA',
            'country_id' => $china->id,
            'latitude' => 31.2222,
            'longitude' => 121.4928,
        ]);

        Port::create([
            'name' => 'Port of Sydney',
            'code' => 'AUSYD',
            'country_id' => $australia->id,
            'latitude' => -33.8688,
            'longitude' => 151.2093,
        ]);

        // 8. Seed News & Sentiments
        News::create([
            'country_id' => $germany->id,
            'title' => 'Inflation increases while exports decrease due to war.',
            'source' => 'Global Logistics News',
            'url' => 'https://example.com/germany-inflation',
            'sentiment' => 'negative',
            'positive_score' => 1,
            'negative_score' => 3,
            'published_at' => now()->subDay(),
        ]);

        News::create([
            'country_id' => $germany->id,
            'title' => 'Port of Hamburg implements new smart logistics systems to improve throughput.',
            'source' => 'Shipping Weekly',
            'url' => 'https://example.com/hamburg-smart',
            'sentiment' => 'positive',
            'positive_score' => 2,
            'negative_score' => 0,
            'published_at' => now(),
        ]);

        News::create([
            'country_id' => $indonesia->id,
            'title' => 'Tanjung Priok modernizes digital container yard management.',
            'source' => 'Jakarta Shipping Gazette',
            'url' => 'https://example.com/priok-modern',
            'sentiment' => 'positive',
            'positive_score' => 3,
            'negative_score' => 0,
            'published_at' => now()->subDays(2),
        ]);

        News::create([
            'country_id' => $china->id,
            'title' => 'China shipping lanes experience minor congestion ahead of winter rush.',
            'source' => 'Asia Logistics',
            'url' => 'https://example.com/china-congest',
            'sentiment' => 'neutral',
            'positive_score' => 1,
            'negative_score' => 1,
            'published_at' => now()->subHours(12),
        ]);

        // 9. Seed Risk Scores (Sample index log history)
        RiskScore::create([
            'country_id' => $germany->id,
            'weather_score' => 20,
            'inflation_score' => 30,
            'currency_score' => 15,
            'sentiment_score' => 40,
            'total_score' => 27,
            'calculated_at' => now(),
        ]);

        RiskScore::create([
            'country_id' => $indonesia->id,
            'weather_score' => 15,
            'inflation_score' => 40,
            'currency_score' => 10,
            'sentiment_score' => 20,
            'total_score' => 22,
            'calculated_at' => now(),
        ]);

        RiskScore::create([
            'country_id' => $china->id,
            'weather_score' => 25,
            'inflation_score' => 20,
            'currency_score' => 12,
            'sentiment_score' => 15,
            'total_score' => 18,
            'calculated_at' => now(),
        ]);

        RiskScore::create([
            'country_id' => $australia->id,
            'weather_score' => 10,
            'inflation_score' => 35,
            'currency_score' => 8,
            'sentiment_score' => 25,
            'total_score' => 20,
            'calculated_at' => now(),
        ]);

        // 10. Seed Articles (Admin-written)
        Article::create([
            'user_id' => $admin->id,
            'title' => 'Analisis Dampak Hambatan Logistik Laut Merah Terhadap Rantai Pasok Global',
            'slug' => 'analisis-dampak-hambatan-logistik-laut-merah',
            'content' => 'Hambatan geopolitik di Laut Merah memaksa banyak kapal kontainer memutar melalui Tanjung Harapan, menyebabkan lonjakan biaya bahan bakar dan keterlambatan logistik global sebesar 10-14 hari...',
            'published_at' => now()->subDays(3),
        ]);

        Article::create([
            'user_id' => $admin->id,
            'title' => 'Strategi Diversifikasi Pemasok untuk Mengurangi Ketergantungan Ekspor dari China',
            'slug' => 'strategi-diversifikasi-pemasok',
            'content' => 'Diversifikasi pemasok ke negara-negara Asia Tenggara seperti Indonesia dan Vietnam menjadi langkah mitigasi penting bagi perusahaan manufaktur untuk menghindari ketergantungan sepihak...',
            'published_at' => now()->subDays(1),
        ]);

        // 11. Seed Watchlist relations
        $user->watchedCountries()->attach([$indonesia->id, $germany->id]);
    }
}

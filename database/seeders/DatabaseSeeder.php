<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use App\Models\Weather;
use App\Models\Currency;
use App\Models\Inflation;
use App\Models\Gdp;
use App\Models\Export;
use App\Models\Import;
use App\Models\News;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\Article;
use App\Models\ImportShipment;
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
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
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

        // Seed Export Trends (5 Years: 2021-2025) in Billions USD
        $exportData = [
            $germany->id => [2021 => 1600.0, 2022 => 1650.0, 2023 => 1700.0, 2024 => 1750.0, 2025 => 1800.0],
            $indonesia->id => [2021 => 230.0, 2022 => 290.0, 2023 => 260.0, 2024 => 270.0, 2025 => 280.0],
            $china->id => [2021 => 3300.0, 2022 => 3600.0, 2023 => 3400.0, 2024 => 3500.0, 2025 => 3600.0],
            $australia->id => [2021 => 340.0, 2022 => 410.0, 2023 => 370.0, 2024 => 390.0, 2025 => 410.0],
        ];

        foreach ($exportData as $countryId => $years) {
            foreach ($years as $year => $value) {
                Export::create([
                    'country_id' => $countryId,
                    'year' => $year,
                    'value' => $value,
                ]);
            }
        }

        // Seed Import Trends (5 Years: 2021-2025) in Billions USD
        $importData = [
            $germany->id => [2021 => 1400.0, 2022 => 1450.0, 2023 => 1500.0, 2024 => 1550.0, 2025 => 1600.0],
            $indonesia->id => [2021 => 190.0, 2022 => 240.0, 2023 => 220.0, 2024 => 230.0, 2025 => 240.0],
            $china->id => [2021 => 2700.0, 2022 => 2800.0, 2023 => 2600.0, 2024 => 2700.0, 2025 => 2800.0],
            $australia->id => [2021 => 290.0, 2022 => 310.0, 2023 => 310.0, 2024 => 330.0, 2025 => 350.0],
        ];

        foreach ($importData as $countryId => $years) {
            foreach ($years as $year => $value) {
                Import::create([
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
            'name' => 'Port of Bremen',
            'code' => 'DEBRE',
            'country_id' => $germany->id,
            'latitude' => 53.0793,
            'longitude' => 8.8017,
        ]);

        Port::create([
            'name' => 'Port of Wilhelmshaven',
            'code' => 'DEWVN',
            'country_id' => $germany->id,
            'latitude' => 53.5167,
            'longitude' => 8.1333,
        ]);

        Port::create([
            'name' => 'Port of Rostock',
            'code' => 'DERSK',
            'country_id' => $germany->id,
            'latitude' => 54.0833,
            'longitude' => 12.1333,
        ]);

        Port::create([
            'name' => 'Tanjung Priok',
            'code' => 'IDTPP',
            'country_id' => $indonesia->id,
            'latitude' => -6.1030,
            'longitude' => 106.8790,
        ]);

        Port::create([
            'name' => 'Tanjung Perak',
            'code' => 'IDTPE',
            'country_id' => $indonesia->id,
            'latitude' => -7.2053,
            'longitude' => 112.7264,
        ]);

        Port::create([
            'name' => 'Port of Belawan',
            'code' => 'IDBLW',
            'country_id' => $indonesia->id,
            'latitude' => 3.7833,
            'longitude' => 98.6833,
        ]);

        Port::create([
            'name' => 'Port of Makassar',
            'code' => 'IDMAK',
            'country_id' => $indonesia->id,
            'latitude' => -5.1167,
            'longitude' => 119.4167,
        ]);

        Port::create([
            'name' => 'Tanjung Emas',
            'code' => 'IDTEM',
            'country_id' => $indonesia->id,
            'latitude' => -6.9500,
            'longitude' => 110.4333,
        ]);

        Port::create([
            'name' => 'Port of Shanghai',
            'code' => 'CNSHA',
            'country_id' => $china->id,
            'latitude' => 31.2222,
            'longitude' => 121.4928,
        ]);

        Port::create([
            'name' => 'Port of Shenzhen',
            'code' => 'CNSZX',
            'country_id' => $china->id,
            'latitude' => 22.5083,
            'longitude' => 113.8833,
        ]);

        Port::create([
            'name' => 'Port of Ningbo-Zhoushan',
            'code' => 'CNNGB',
            'country_id' => $china->id,
            'latitude' => 29.8667,
            'longitude' => 121.5500,
        ]);

        Port::create([
            'name' => 'Port of Guangzhou',
            'code' => 'CNCAN',
            'country_id' => $china->id,
            'latitude' => 23.1167,
            'longitude' => 113.2500,
        ]);

        Port::create([
            'name' => 'Port of Qingdao',
            'code' => 'CNTAO',
            'country_id' => $china->id,
            'latitude' => 36.0667,
            'longitude' => 120.3000,
        ]);

        Port::create([
            'name' => 'Port of Sydney',
            'code' => 'AUSYD',
            'country_id' => $australia->id,
            'latitude' => -33.8688,
            'longitude' => 151.2093,
        ]);

        Port::create([
            'name' => 'Port of Melbourne',
            'code' => 'AUMEL',
            'country_id' => $australia->id,
            'latitude' => -37.8136,
            'longitude' => 144.9631,
        ]);

        Port::create([
            'name' => 'Port of Brisbane',
            'code' => 'AUBNE',
            'country_id' => $australia->id,
            'latitude' => -27.4698,
            'longitude' => 153.0251,
        ]);

        Port::create([
            'name' => 'Port of Fremantle',
            'code' => 'AUFRE',
            'country_id' => $australia->id,
            'latitude' => -32.0569,
            'longitude' => 115.7439,
        ]);

        Port::create([
            'name' => 'Port of Adelaide',
            'code' => 'AUADL',
            'country_id' => $australia->id,
            'latitude' => -34.8422,
            'longitude' => 138.5028,
        ]);

        // 8. Seed News & Sentiments
        $now = now();
        
        // Indonesia (ID) News
        $indonesiaNews = [
            [
                'title' => 'Trade: Indonesia Integrasikan Sistem Logistik Nasional Lewat National Logistics Ecosystem (NLE)',
                'source' => 'Antara News',
                'url' => 'https://www.antaranews.com/search?q=National+Logistics+Ecosystem',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(1),
            ],
            [
                'title' => 'Shipping: Pelindo Multi Terminal Pacu Standardisasi Layanan Pelabuhan Seluruh Indonesia',
                'source' => 'Bisnis Indonesia',
                'url' => 'https://search.bisnis.com/?q=Pelindo+Multi+Terminal',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(2),
            ],
            [
                'title' => 'Logistics: Kemacetan Jalur Distribusi Logistik di Pelabuhan Merak Menjelang Libur Panjang',
                'source' => 'Detik Finance',
                'url' => 'https://www.detik.com/search/search_all?query=Merak+logistik',
                'sentiment' => 'negative',
                'positive_score' => 1,
                'negative_score' => 4,
                'published_at' => (clone $now)->subDays(3),
            ],
            [
                'title' => 'Economy: Dampak Depresiasi Rupiah Terhadap Biaya Impor Bahan Baku Industri Manufaktur',
                'source' => 'Kontan',
                'url' => 'https://www.kontan.co.id/search/?search=rupiah+impor',
                'sentiment' => 'negative',
                'positive_score' => 1,
                'negative_score' => 3,
                'published_at' => (clone $now)->subDays(4),
            ],
            [
                'title' => 'Shipping: Rute Pelayaran Baru Direct Call Indonesia - Amerika Serikat Resmi Dibuka dari Tanjung Priok',
                'source' => 'Jakarta Shipping Gazette',
                'url' => 'https://shippingsg.com/?s=direct+call',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(5),
            ],
            [
                'title' => 'Trade: Kementerian Perhubungan Operasikan Pelabuhan Patimban Tahap 2 untuk Perluas Ekspor Otomotif',
                'source' => 'Kompas',
                'url' => 'https://search.kompas.com/?q=Pelabuhan+Patimban',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(6),
            ],
            [
                'title' => 'Economy: Inflasi RI Terjaga pada Sasaran 2,5 Persen di Tengah Pemulihan Ekonomi Nasional',
                'source' => 'Bank Indonesia',
                'url' => 'https://www.bi.go.id/id/statistik/indikator/inflasi.aspx',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(7),
            ],
            [
                'title' => 'Logistics: Pemerintah Tingkatkan Infrastruktur Konektivitas Udara Guna Tekan Biaya Logistik di Timur Indonesia',
                'source' => 'Tempo',
                'url' => 'https://www.tempo.co/search?q=konektivitas+udara+logistik',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 1,
                'published_at' => (clone $now)->subDays(8),
            ],
        ];

        foreach ($indonesiaNews as $art) {
            News::create(array_merge($art, ['country_id' => $indonesia->id]));
        }

        // Germany (DE) News
        $germanyNews = [
            [
                'title' => 'Economy: German economy stagnates as exports to major trading partners decline in 2026',
                'source' => 'Reuters',
                'url' => 'https://www.reuters.com/german-economy-stagnates-2026',
                'sentiment' => 'negative',
                'positive_score' => 1,
                'negative_score' => 3,
                'published_at' => (clone $now)->subDays(1),
            ],
            [
                'title' => 'Shipping: Port of Hamburg reports increase in container throughput driven by European trade recovery',
                'source' => 'Shipping Weekly',
                'url' => 'https://shippingweekly.com/hamburg-throughput-rise',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(2),
            ],
            [
                'title' => 'Logistics: Germany invests 12 billion euros in upgrading national railway logistics corridors',
                'source' => 'Handelsblatt',
                'url' => 'https://www.handelsblatt.com/germany-railway-investment',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(3),
            ],
            [
                'title' => 'Trade: Rising energy costs continue to pressure Germany\'s chemical manufacturing exports',
                'source' => 'Deutsche Welle',
                'url' => 'https://www.dw.com/german-chemical-exports-pressure',
                'sentiment' => 'negative',
                'positive_score' => 1,
                'negative_score' => 3,
                'published_at' => (clone $now)->subDays(4),
            ],
            [
                'title' => 'Logistics: Deutsche Bahn logistics strike causes widespread delays across central European supply chains',
                'source' => 'Logistics Manager',
                'url' => 'https://logisticsmanager.com/deutsche-bahn-strike-delays',
                'sentiment' => 'negative',
                'positive_score' => 1,
                'negative_score' => 4,
                'published_at' => (clone $now)->subDays(5),
            ],
            [
                'title' => 'Trade: Germany and France sign new trade accord to secure critical raw materials logistics',
                'source' => 'Les Echos',
                'url' => 'https://www.lesechos.fr/germany-france-raw-materials',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(6),
            ],
            [
                'title' => 'Economy: German inflation cools to 2.0 percent, aligning with European Central Bank target',
                'source' => 'Frankfurter Allgemeine',
                'url' => 'https://www.faz.net/german-inflation-cools-2-0',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(7),
            ],
            [
                'title' => 'Shipping: Hamburg Port Authority deploys AI-powered drone fleet for real-time vessel monitoring',
                'source' => 'Port Technology',
                'url' => 'https://www.porttechnology.org/hamburg-drone-monitoring',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(8),
            ],
        ];

        foreach ($germanyNews as $art) {
            News::create(array_merge($art, ['country_id' => $germany->id]));
        }

        // China (CN) News
        $chinaNews = [
            [
                'title' => 'Trade: China\'s exports hit record high despite global trade barriers and rising tariffs',
                'source' => 'South China Morning Post',
                'url' => 'https://www.scmp.com/china-exports-record-high',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 1,
                'published_at' => (clone $now)->subDays(1),
            ],
            [
                'title' => 'Shipping: Port of Shanghai retains position as world\'s busiest container port with 49 million TEUs',
                'source' => 'Xinhua',
                'url' => 'http://www.xinhuanet.com/shanghai-busiest-port',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(2),
            ],
            [
                'title' => 'Shipping: China introduces new shipping safety regulations in the South China Sea routes',
                'source' => 'Maritime Executive',
                'url' => 'https://maritime-executive.com/china-shipping-regulations',
                'sentiment' => 'neutral',
                'positive_score' => 2,
                'negative_score' => 2,
                'published_at' => (clone $now)->subDays(3),
            ],
            [
                'title' => 'Logistics: Congestion at Ningbo-Zhoushan port eases as digital logistics systems go live',
                'source' => 'Asia Logistics',
                'url' => 'https://asialogistics.com/ningbo-zhoushan-congestion-eases',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(4),
            ],
            [
                'title' => 'Economy: China\'s GDP growth targets set at 5 percent with focus on advanced manufacturing exports',
                'source' => 'Bloomberg',
                'url' => 'https://www.bloomberg.com/china-gdp-target-manufacturing',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(5),
            ],
            [
                'title' => 'Trade: China and ASEAN countries expand maritime trade routes with new container services',
                'source' => 'Caixin',
                'url' => 'https://www.caixinglobal.com/china-asean-trade-routes',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(6),
            ],
            [
                'title' => 'Economy: Chinese yuan stabilizes against USD following central bank intervention',
                'source' => 'Financial Times',
                'url' => 'https://www.ft.com/yuan-stabilizes-central-bank',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(7),
            ],
            [
                'title' => 'Logistics: New high-speed freight railway connects western China directly to Central Asia corridors',
                'source' => 'China Daily',
                'url' => 'http://www.chinadaily.com.cn/freight-railway-central-asia',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(8),
            ],
        ];

        foreach ($chinaNews as $art) {
            News::create(array_merge($art, ['country_id' => $china->id]));
        }

        // Australia (AU) News
        $australiaNews = [
            [
                'title' => 'Trade: Australia\'s iron ore exports to Asia surge by 15 percent amid high manufacturing demand',
                'source' => 'The Sydney Morning Herald',
                'url' => 'https://www.smh.com.au/australia-iron-ore-surge',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(1),
            ],
            [
                'title' => 'Shipping: Port of Melbourne advances expansion plan to accommodate ultra-large container vessels',
                'source' => 'Daily Cargo News',
                'url' => 'https://www.dcn.com.au/melbourne-port-expansion',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(2),
            ],
            [
                'title' => 'Shipping: Severe storm warnings along Australia\'s eastern coast disrupt maritime cargo shipping',
                'source' => 'ABC News',
                'url' => 'https://www.abc.net.au/storm-warnings-shipping-disrupted',
                'sentiment' => 'negative',
                'positive_score' => 1,
                'negative_score' => 4,
                'published_at' => (clone $now)->subDays(3),
            ],
            [
                'title' => 'Trade: Australia and India implement comprehensive economic cooperation and trade agreement',
                'source' => 'The Australian',
                'url' => 'https://www.theaustralian.com.au/australia-india-trade-deal',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(4),
            ],
            [
                'title' => 'Economy: Reserve Bank of Australia keeps interest rates steady to combat persistent inflation',
                'source' => 'Australian Financial Review',
                'url' => 'https://www.afr.com/rba-rates-steady-inflation',
                'sentiment' => 'neutral',
                'positive_score' => 2,
                'negative_score' => 2,
                'published_at' => (clone $now)->subDays(5),
            ],
            [
                'title' => 'Logistics: Australian agricultural exports face logistics delays due to regional rail maintenance',
                'source' => 'Stock & Land',
                'url' => 'https://www.stockandland.com.au/grain-logistics-delays',
                'sentiment' => 'negative',
                'positive_score' => 1,
                'negative_score' => 3,
                'published_at' => (clone $now)->subDays(6),
            ],
            [
                'title' => 'Trade: Port of Brisbane records major growth in automotive imports during first half of 2026',
                'source' => 'Courier Mail',
                'url' => 'https://www.couriermail.com.au/brisbane-automotive-imports-growth',
                'sentiment' => 'positive',
                'positive_score' => 3,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(7),
            ],
            [
                'title' => 'Shipping: Australia invests in hydrogen-powered shipping corridors to meet net-zero trade goals',
                'source' => 'Renew Economy',
                'url' => 'https://reneweconomy.com.au/australia-hydrogen-shipping',
                'sentiment' => 'positive',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => (clone $now)->subDays(8),
            ],
        ];

        foreach ($australiaNews as $art) {
            News::create(array_merge($art, ['country_id' => $australia->id]));
        }

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

        Article::create([
            'user_id' => $admin->id,
            'title' => 'Penerapan Green Logistics untuk Efisiensi Operasional Pelabuhan',
            'slug' => 'penerapan-green-logistics-pelabuhan',
            'content' => 'Inisiatif pelabuhan hijau (green ports) kini diadopsi secara masal di Uni Eropa dan Asia Pasifik. Regulasi dekarbonisasi maritim menuntut kapal menurunkan emisi karbon, sekaligus mendorong efisiensi rantai pasok energi alternatif...',
            'published_at' => now()->subDays(5),
        ]);

        Article::create([
            'user_id' => $admin->id,
            'title' => 'Dampak Kenaikan Tarif Kontainer Global Terhadap Biaya Logistik Impor',
            'slug' => 'dampak-kenaikan-tarif-kontainer-global',
            'content' => 'Indeks kontainer global Drewry menunjukkan kenaikan sebesar 25% dalam kurun waktu satu kuartal terakhir. Hal ini disebabkan oleh ketidakseimbangan alokasi kontainer kosong di pelabuhan-pelabuhan utama Asia Timur...',
            'published_at' => now()->subDays(7),
        ]);

        Article::create([
            'user_id' => $admin->id,
            'title' => 'Potensi Kerugian Akibat Cuaca Ekstrem La Nina pada Rute Pelayaran Pasifik',
            'slug' => 'potensi-kerugian-cuaca-ekstrem-la-nina',
            'content' => 'Fenomena La Nina diproyeksikan akan meningkatkan intensitas badai tropis di kawasan Asia Pasifik bagian utara. Perusahaan pelayaran dihimbau untuk memperbarui rute pelayaran alternatif untuk meminimalkan keterlambatan cargo...',
            'published_at' => now()->subDays(10),
        ]);

        Article::create([
            'user_id' => $admin->id,
            'title' => 'Integrasi IoT dan AI dalam Pelacakan Kontainer Secara Real-time',
            'slug' => 'integrasi-iot-dan-ai-pelacakan-kontainer',
            'content' => 'Teknologi sensor Internet of Things (IoT) yang digabungkan dengan analitik prediktif berbasis kecerdasan buatan (AI) terbukti mampu mengurangi kerugian kargo akibat fluktuasi suhu dan kelembaban hingga 40%...',
            'published_at' => now()->subDays(12),
        ]);

        // 11. Seed Watchlist relations
        $user->watchedCountries()->attach([$indonesia->id, $germany->id]);

        // 12. Seed Sample Import Shipments
        ImportShipment::create([
            'user_id' => $user->id,
            'shipment_number' => 'SHP-2026-0001',
            'origin_port_id' => Port::where('code', 'DEHAM')->first()->id,
            'destination_port_id' => Port::where('code', 'IDTPP')->first()->id,
            'status' => 'In Transit',
            'transport_mode' => 'Sea Freight',
        ]);

        ImportShipment::create([
            'user_id' => $user->id,
            'shipment_number' => 'SHP-2026-0002',
            'origin_port_id' => Port::where('code', 'CNSHA')->first()->id,
            'destination_port_id' => Port::where('code', 'IDTPP')->first()->id,
            'status' => 'Pending',
            'transport_mode' => 'Sea Freight',
        ]);
    }
}

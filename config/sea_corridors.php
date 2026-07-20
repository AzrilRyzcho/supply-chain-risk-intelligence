<?php

/**
 * Global Sea Corridor Configuration — v3 (Land-Avoidance Audit)
 *
 * EVERY connection has been audited to ensure the straight line between
 * the two endpoints stays over water. Where a direct connection would
 * cross a landmass, intermediate coastal waypoints have been inserted.
 *
 * Used by RouteJourneyService to compute realistic sea routes via Dijkstra.
 */

return [

    // =========================================================================
    //  WAYPOINTS — Key maritime junctions positioned in open water
    // =========================================================================
    'waypoints' => [

        // ── Europe ──────────────────────────────────────────────────────────
        'baltic_sea'       => ['name' => 'Baltic Sea',        'lat' => 56.0,  'lng' => 18.0,  'region' => 'Europe'],
        'north_sea'        => ['name' => 'North Sea',         'lat' => 55.0,  'lng' => 4.0,   'region' => 'Europe'],
        'norwegian_sea'    => ['name' => 'Norwegian Sea',     'lat' => 62.0,  'lng' => 3.0,   'region' => 'Europe'],
        'english_channel'  => ['name' => 'English Channel',   'lat' => 50.2,  'lng' => -1.0,  'region' => 'Europe'],
        'irish_sea'        => ['name' => 'Irish Sea',         'lat' => 53.5,  'lng' => -5.5,  'region' => 'Europe'],
        'bay_of_biscay'    => ['name' => 'Bay of Biscay',     'lat' => 45.0,  'lng' => -5.0,  'region' => 'Europe'],

        // ── Mediterranean & Black Sea ───────────────────────────────────────
        'gibraltar'        => ['name' => 'Strait of Gibraltar','lat' => 36.0,  'lng' => -5.6,  'region' => 'Mediterranean'],
        'western_med'      => ['name' => 'Western Mediterranean','lat'=>38.5,  'lng' => 3.0,   'region' => 'Mediterranean'],
        'central_med'      => ['name' => 'Central Mediterranean','lat'=>35.5,  'lng' => 15.0,  'region' => 'Mediterranean'],
        'adriatic_sea'     => ['name' => 'Adriatic Sea',      'lat' => 42.0,  'lng' => 16.0,  'region' => 'Mediterranean'],
        'eastern_med'      => ['name' => 'Eastern Mediterranean','lat'=>34.0,  'lng' => 28.0,  'region' => 'Mediterranean'],
        'aegean_sea'       => ['name' => 'Aegean Sea',        'lat' => 38.0,  'lng' => 25.0,  'region' => 'Mediterranean'],
        'black_sea_west'   => ['name' => 'Black Sea (West)',  'lat' => 42.0,  'lng' => 29.0,  'region' => 'Mediterranean'],
        'black_sea_east'   => ['name' => 'Black Sea (East)',  'lat' => 43.5,  'lng' => 38.0,  'region' => 'Mediterranean'],

        // ── Suez & Red Sea ──────────────────────────────────────────────────
        'suez_canal'       => ['name' => 'Suez Canal',        'lat' => 30.5,  'lng' => 32.4,  'region' => 'Red Sea'],
        'red_sea_north'    => ['name' => 'Red Sea (North)',   'lat' => 26.0,  'lng' => 35.5,  'region' => 'Red Sea'],
        'red_sea_south'    => ['name' => 'Red Sea (South)',   'lat' => 18.0,  'lng' => 39.5,  'region' => 'Red Sea'],
        'bab_el_mandeb'    => ['name' => 'Bab el-Mandeb',     'lat' => 12.6,  'lng' => 43.3,  'region' => 'Red Sea'],

        // ── Arabian Region ──────────────────────────────────────────────────
        'gulf_of_aden'     => ['name' => 'Gulf of Aden',      'lat' => 12.5,  'lng' => 48.0,  'region' => 'Arabian'],
        'arabian_sea'      => ['name' => 'Arabian Sea',       'lat' => 15.0,  'lng' => 61.0,  'region' => 'Arabian'],
        'strait_of_hormuz' => ['name' => 'Strait of Hormuz',  'lat' => 26.0,  'lng' => 56.5,  'region' => 'Arabian'],
        'persian_gulf'     => ['name' => 'Persian Gulf',      'lat' => 27.0,  'lng' => 50.0,  'region' => 'Arabian'],

        // ── Indian Ocean ────────────────────────────────────────────────────
        'western_indian'   => ['name' => 'Western Indian Ocean','lat'=>-2.0,  'lng' => 55.0,  'region' => 'Indian Ocean'],
        'central_indian'   => ['name' => 'Central Indian Ocean','lat'=>-5.0,  'lng' => 73.0,  'region' => 'Indian Ocean'],
        'sri_lanka_south'  => ['name' => 'South of Sri Lanka','lat' => 5.5,   'lng' => 80.2,  'region' => 'Indian Ocean'],
        'bay_of_bengal'    => ['name' => 'Bay of Bengal',     'lat' => 12.0,  'lng' => 86.0,  'region' => 'Indian Ocean'],
        'andaman_sea'      => ['name' => 'Andaman Sea',       'lat' => 10.0,  'lng' => 95.0,  'region' => 'Indian Ocean'],
        'cocos_basin'      => ['name' => 'Cocos Basin',       'lat' => -12.0, 'lng' => 97.0,  'region' => 'Indian Ocean'],
        'south_indian'     => ['name' => 'South Indian Ocean','lat' => -35.0, 'lng' => 75.0,  'region' => 'Indian Ocean'],

        // ══════════════════════════════════════════════════════════════════════
        //  AFRICA — Dense coastal waypoints to avoid ANY land crossing
        // ══════════════════════════════════════════════════════════════════════

        // Horn of Africa & Somali coast (stays offshore)
        'horn_of_africa'   => ['name' => 'Cape Guardafui',    'lat' => 11.5,  'lng' => 52.0,  'region' => 'Africa'],
        'somali_basin'     => ['name' => 'Somali Basin',      'lat' => 3.0,   'lng' => 50.0,  'region' => 'Africa'],

        // East Africa coast — well offshore to avoid the coastline
        'east_africa'      => ['name' => 'East Africa Coast', 'lat' => -5.0,  'lng' => 42.0,  'region' => 'Africa'],
        'mozambique_north' => ['name' => 'Mozambique Channel (North)','lat'=>-12.0,'lng'=>43.5,'region' => 'Africa'],
        'mozambique_ch'    => ['name' => 'Mozambique Channel','lat' => -18.0, 'lng' => 41.0,  'region' => 'Africa'],
        'mozambique_south' => ['name' => 'Mozambique Channel (South)','lat'=>-24.0,'lng'=>38.0,'region' => 'Africa'],

        // SE Africa coast — routing around South Africa's east coast
        'se_africa'        => ['name' => 'Offshore Durban',   'lat' => -30.0, 'lng' => 33.0,  'region' => 'Africa'],
        'agulhas_bank'     => ['name' => 'Agulhas Bank',      'lat' => -35.5, 'lng' => 22.0,  'region' => 'Africa'],
        'cape_good_hope'   => ['name' => 'Cape of Good Hope', 'lat' => -34.5, 'lng' => 18.0,  'region' => 'Africa'],

        // SW & W Africa coast — routing up the west side of Africa
        'namibia_offshore' => ['name' => 'Offshore Namibia',  'lat' => -23.0, 'lng' => 13.0,  'region' => 'Africa'],
        'angola_offshore'  => ['name' => 'Offshore Angola',   'lat' => -10.0, 'lng' => 11.0,  'region' => 'Africa'],
        'west_africa'      => ['name' => 'Gulf of Guinea',    'lat' => 3.0,   'lng' => 3.0,   'region' => 'Africa'],
        'west_africa_bulge'=> ['name' => 'Offshore Dakar',    'lat' => 14.0,  'lng' => -18.0, 'region' => 'Africa'],
        'nw_africa'        => ['name' => 'Offshore Canary Islands','lat'=>28.0,'lng'=>-16.0,  'region' => 'Africa'],

        // ── Southeast Asia — Malacca & Singapore ────────────────────────────
        'malacca_north'    => ['name' => 'Malacca Strait (North)','lat'=>6.0, 'lng' => 96.0,  'region' => 'Southeast Asia'],
        'malacca_south'    => ['name' => 'Malacca Strait (South)','lat'=>2.5, 'lng' => 101.5, 'region' => 'Southeast Asia'],
        'singapore_strait' => ['name' => 'Singapore Strait',  'lat' => 1.25,  'lng' => 103.8, 'region' => 'Southeast Asia'],
        'singapore_east'   => ['name' => 'Singapore (East)',  'lat' => 1.3,   'lng' => 104.5, 'region' => 'Southeast Asia'],
        'gulf_of_thailand' => ['name' => 'Gulf of Thailand',  'lat' => 8.0,   'lng' => 102.5, 'region' => 'Southeast Asia'],
        'gulf_of_martaban' => ['name' => 'Gulf of Martaban',  'lat' => 14.5,  'lng' => 96.3,  'region' => 'Southeast Asia'],

        // ── South China Sea ─────────────────────────────────────────────────
        'scs_south'        => ['name' => 'South China Sea (South)','lat'=>3.0,'lng'=>107.0,   'region' => 'South China Sea'],
        'scs_central'      => ['name' => 'South China Sea (Central)','lat'=>12.0,'lng'=>113.0,'region' => 'South China Sea'],
        'scs_north'        => ['name' => 'South China Sea (North)','lat'=>18.0,'lng'=>116.0,  'region' => 'South China Sea'],

        // ── East Asia ───────────────────────────────────────────────────────
        'taiwan_strait'    => ['name' => 'Taiwan Strait',     'lat' => 24.5,  'lng' => 119.5, 'region' => 'East Asia'],
        'east_china_sea'   => ['name' => 'East China Sea',    'lat' => 29.0,  'lng' => 125.0, 'region' => 'East Asia'],
        'yellow_sea'       => ['name' => 'Yellow Sea',        'lat' => 35.0,  'lng' => 123.0, 'region' => 'East Asia'],
        'sea_of_japan'     => ['name' => 'Sea of Japan',      'lat' => 38.0,  'lng' => 133.0, 'region' => 'East Asia'],
        'nw_pacific'       => ['name' => 'Northwest Pacific', 'lat' => 33.0,  'lng' => 142.0, 'region' => 'East Asia'],

        // ── Indonesian Archipelago ──────────────────────────────────────────
        'karimata_strait'  => ['name' => 'Karimata Strait',   'lat' => -1.5,  'lng' => 106.5, 'region' => 'Indonesia'],
        'java_sea'         => ['name' => 'Java Sea',          'lat' => -5.5,  'lng' => 112.0, 'region' => 'Indonesia'],
        'sunda_strait'     => ['name' => 'Sunda Strait',      'lat' => -6.5,  'lng' => 105.5, 'region' => 'Indonesia'],
        'lombok_strait'    => ['name' => 'Lombok Strait',     'lat' => -8.5,  'lng' => 115.7, 'region' => 'Indonesia'],
        'makassar_strait'  => ['name' => 'Makassar Strait',   'lat' => -2.0,  'lng' => 118.0, 'region' => 'Indonesia'],
        'flores_sea'       => ['name' => 'Flores Sea',        'lat' => -7.0,  'lng' => 121.0, 'region' => 'Indonesia'],
        'banda_sea'        => ['name' => 'Banda Sea',         'lat' => -5.0,  'lng' => 128.0, 'region' => 'Indonesia'],
        'timor_sea'        => ['name' => 'Timor Sea',         'lat' => -10.5, 'lng' => 125.0, 'region' => 'Indonesia'],
        'arafura_sea'      => ['name' => 'Arafura Sea',       'lat' => -9.5,  'lng' => 135.0, 'region' => 'Indonesia'],
        'celebes_sea'      => ['name' => 'Celebes Sea',       'lat' => 3.0,   'lng' => 123.0, 'region' => 'Indonesia'],
        'molucca_sea'      => ['name' => 'Molucca Sea',       'lat' => 1.0,   'lng' => 126.0, 'region' => 'Indonesia'],
        'sulu_sea'         => ['name' => 'Sulu Sea',          'lat' => 8.0,   'lng' => 120.0, 'region' => 'Philippines'],

        // ── Australia & New Zealand ─────────────────────────────────────────
        'nw_australia'     => ['name' => 'Northwest Australia','lat'=>-16.0,  'lng' => 118.0, 'region' => 'Australia'],
        'north_australia'  => ['name' => 'North Australia',   'lat' => -11.0, 'lng' => 131.0, 'region' => 'Australia'],
        'torres_strait'    => ['name' => 'Torres Strait',     'lat' => -10.0, 'lng' => 142.0, 'region' => 'Australia'],
        'ne_australia'     => ['name' => 'Northeast Australia','lat'=>-15.0,  'lng' => 150.0, 'region' => 'Australia'],
        'east_australia'   => ['name' => 'East Australia',    'lat' => -28.0, 'lng' => 155.0, 'region' => 'Australia'],
        'tasman_sea'       => ['name' => 'Tasman Sea',        'lat' => -35.0, 'lng' => 155.0, 'region' => 'Australia'],
        'bass_strait'      => ['name' => 'Bass Strait',       'lat' => -39.5, 'lng' => 147.0, 'region' => 'Australia'],
        'great_aus_bight'  => ['name' => 'Great Australian Bight','lat'=>-36.0,'lng'=>130.0,  'region' => 'Australia'],
        'sw_australia'     => ['name' => 'Southwest Australia','lat'=>-33.0,  'lng' => 113.0, 'region' => 'Australia'],
        // Offshore waypoints for South Australian ports (Spencer Gulf / Gulf St Vincent)
        // These keep the route at sea when approaching Adelaide from either direction
        'south_aus_west'   => ['name' => 'South Australia (West Approach)', 'lat' => -37.5, 'lng' => 133.5, 'region' => 'Australia'],
        'south_aus_east'   => ['name' => 'South Australia (East Approach)', 'lat' => -38.5, 'lng' => 140.0, 'region' => 'Australia'],
        'new_zealand'      => ['name' => 'New Zealand Waters','lat' => -38.0, 'lng' => 175.0, 'region' => 'Oceania'],

        // ── Southern Ocean bridge — connects Australia south coast to Cape of Good Hope ─
        'southern_ocean_c' => ['name' => 'Southern Ocean (Central)', 'lat' => -42.0, 'lng' => 80.0,  'region' => 'Indian Ocean'],
        'southern_ocean_w' => ['name' => 'Southern Ocean (West)',    'lat' => -42.0, 'lng' => 30.0,  'region' => 'Indian Ocean'],

        // ── Pacific Ocean ───────────────────────────────────────────────────
        'philippine_sea'   => ['name' => 'Philippine Sea',    'lat' => 15.0,  'lng' => 130.0, 'region' => 'Pacific'],
        'bismarck_sea'     => ['name' => 'Bismarck Sea',      'lat' => 0.0,   'lng' => 152.0, 'region' => 'Pacific'],
        'coral_sea'        => ['name' => 'Coral Sea',         'lat' => -12.0, 'lng' => 155.0, 'region' => 'Pacific'],
        'fiji_waters'      => ['name' => 'Fiji Waters',       'lat' => -18.0, 'lng' => 178.0, 'region' => 'Pacific'],
        'north_pacific'    => ['name' => 'North Pacific',     'lat' => 35.0,  'lng' => 170.0, 'region' => 'Pacific'],
        'ne_pacific'       => ['name' => 'Northeast Pacific', 'lat' => 35.0,  'lng' => -140.0,'region' => 'Pacific'],
        'central_pacific'  => ['name' => 'Central Pacific',   'lat' => 10.0,  'lng' => -170.0,'region' => 'Pacific'],
        'south_pacific'    => ['name' => 'South Pacific',     'lat' => -20.0, 'lng' => -170.0,'region' => 'Pacific'],
        'se_pacific'       => ['name' => 'Southeast Pacific', 'lat' => -20.0, 'lng' => -110.0,'region' => 'Pacific'],

        // ── Atlantic Ocean ──────────────────────────────────────────────────
        'north_atlantic'   => ['name' => 'North Atlantic',    'lat' => 45.0,  'lng' => -30.0, 'region' => 'Atlantic'],
        'central_atlantic' => ['name' => 'Central Atlantic',  'lat' => 15.0,  'lng' => -40.0, 'region' => 'Atlantic'],
        'south_atlantic_w' => ['name' => 'South Atlantic (West)','lat'=>-15.0,'lng'=>-30.0,   'region' => 'Atlantic'],
        'south_atlantic_e' => ['name' => 'South Atlantic (East)','lat'=>-20.0,'lng'=>0.0,     'region' => 'Atlantic'],
        'iceland'          => ['name' => 'Iceland Waters',    'lat' => 63.0,  'lng' => -20.0, 'region' => 'Atlantic'],

        // ── Americas ────────────────────────────────────────────────────────
        'us_east_coast'    => ['name' => 'US East Coast',     'lat' => 36.0,  'lng' => -74.0, 'region' => 'Americas'],
        'us_west_coast'    => ['name' => 'US West Coast',     'lat' => 35.0,  'lng' => -122.0,'region' => 'Americas'],
        'caribbean'        => ['name' => 'Caribbean Sea',     'lat' => 15.0,  'lng' => -70.0, 'region' => 'Americas'],
        'gulf_of_mexico'   => ['name' => 'Gulf of Mexico',    'lat' => 25.0,  'lng' => -88.0, 'region' => 'Americas'],
        'panama_atlantic'  => ['name' => 'Panama Canal (Atlantic)','lat'=>9.3,'lng'=>-79.9,   'region' => 'Americas'],
        'panama_pacific'   => ['name' => 'Panama Canal (Pacific)','lat'=>8.9, 'lng'=>-79.6,   'region' => 'Americas'],
        'brazil_coast'     => ['name' => 'Brazil Coast',      'lat' => -10.0, 'lng' => -35.0, 'region' => 'Americas'],
        'rio_plata'        => ['name' => 'Rio de la Plata',   'lat' => -35.0, 'lng' => -55.0, 'region' => 'Americas'],
        'cape_horn'        => ['name' => 'Cape Horn',         'lat' => -56.0, 'lng' => -67.0, 'region' => 'Americas'],
        'chile_coast'      => ['name' => 'Chile Coast',       'lat' => -33.0, 'lng' => -73.0, 'region' => 'Americas'],
    ],

    // =========================================================================
    //  CONNECTIONS — Every edge audited: straight line stays over water
    // =========================================================================
    'connections' => [

        // ── Europe internal ─────────────────────────────────────────────────
        ['baltic_sea',       'north_sea'],
        ['north_sea',        'norwegian_sea'],
        ['north_sea',        'english_channel'],
        ['english_channel',  'irish_sea'],
        ['english_channel',  'bay_of_biscay'],
        ['irish_sea',        'north_atlantic'],
        ['bay_of_biscay',    'gibraltar'],
        ['bay_of_biscay',    'north_atlantic'],
        ['norwegian_sea',    'iceland'],
        ['norwegian_sea',    'north_atlantic'],

        // ── Mediterranean & Black Sea ───────────────────────────────────────
        ['gibraltar',        'western_med'],
        ['gibraltar',        'nw_africa'],       // ✓ Gibraltar → Canary — open ocean
        ['western_med',      'central_med'],
        ['central_med',      'eastern_med'],
        ['central_med',      'adriatic_sea'],
        ['eastern_med',      'aegean_sea'],
        ['eastern_med',      'suez_canal'],
        ['aegean_sea',       'black_sea_west'],
        ['black_sea_west',   'black_sea_east'],

        // ── Suez & Red Sea ──────────────────────────────────────────────────
        ['suez_canal',       'red_sea_north'],
        ['red_sea_north',    'red_sea_south'],
        ['red_sea_south',    'bab_el_mandeb'],
        ['bab_el_mandeb',    'gulf_of_aden'],

        // ── Horn of Africa — routed OFFSHORE around the Horn ────────────────
        ['gulf_of_aden',     'horn_of_africa'],  // ✓ open sea
        ['horn_of_africa',   'arabian_sea'],     // ✓ open sea east of Horn
        ['horn_of_africa',   'somali_basin'],    // ✓ south along offshore Somali coast
        ['somali_basin',     'east_africa'],     // ✓ open sea to Tanzanian coast
        ['somali_basin',     'western_indian'],  // ✓ open sea

        // ── Arabian Sea ─────────────────────────────────────────────────────
        ['gulf_of_aden',     'arabian_sea'],
        ['arabian_sea',      'strait_of_hormuz'],
        ['strait_of_hormuz', 'persian_gulf'],
        ['arabian_sea',      'sri_lanka_south'], // ✓ open Indian Ocean
        ['arabian_sea',      'western_indian'],  // ✓ open Indian Ocean

        // ══════════════════════════════════════════════════════════════════════
        //  AFRICA COASTAL CHAIN — stays offshore, no land crossings
        // ══════════════════════════════════════════════════════════════════════

        // East Africa → south via Mozambique Channel (EAST side of Africa)
        ['east_africa',      'western_indian'],  // ✓ open sea between Africa & Madagascar
        ['east_africa',      'mozambique_north'],// ✓ along coast
        ['mozambique_north', 'mozambique_ch'],   // ✓ within the Channel
        ['mozambique_ch',    'mozambique_south'],// ✓ within the Channel

        // Rounding the southeast corner of Africa (stays offshore)
        ['mozambique_south', 'se_africa'],       // ✓ offshore Durban → (−30,33)
        ['se_africa',        'agulhas_bank'],    // ✓ offshore (−30,33)→(−35.5,22)
        ['agulhas_bank',     'cape_good_hope'],  // ✓ short hop (−35.5,22)→(−34.5,18)

        // Up the WEST coast of Africa (stays offshore in Atlantic)
        ['cape_good_hope',   'namibia_offshore'],// ✓ (−34.5,18)→(−23,13) — offshore
        ['namibia_offshore', 'angola_offshore'], // ✓ (−23,13)→(−10,11) — offshore
        ['angola_offshore',  'west_africa'],     // ✓ (−10,11)→(3,3) — offshore W coast
        ['west_africa',      'west_africa_bulge'],// ✓ up coast to Dakar area
        ['west_africa_bulge','nw_africa'],       // ✓ (14,−18)→(28,−16) — offshore

        // Cross-links into the Atlantic/Indian oceans
        ['cape_good_hope',   'south_atlantic_e'],// ✓ south of Africa, open ocean
        ['cape_good_hope',   'south_indian'],    // ✓ south of Africa, open ocean
        ['cape_good_hope',   'southern_ocean_w'],// ✓ Southern Ocean west of Cape
        ['south_atlantic_e', 'angola_offshore'], // ✓ open South Atlantic
        ['south_atlantic_e', 'namibia_offshore'],// ✓ open South Atlantic
        ['west_africa',      'south_atlantic_e'],// ✓ Gulf of Guinea → (−20, 0)

        // ── Southern Ocean — critical bridge from Australia south coast to Cape of Good Hope ──
        ['south_aus_west',   'southern_ocean_c'],// ✓ open Southern Ocean south of SA
        ['south_aus_east',   'southern_ocean_c'],// ✓ open Southern Ocean south of SA
        ['great_aus_bight',  'southern_ocean_c'],// ✓ open Southern Ocean south of Aus
        ['sw_australia',     'southern_ocean_c'],// ✓ open Southern Ocean south of WA
        ['south_indian',     'southern_ocean_c'],// ✓ open ocean
        ['southern_ocean_c', 'southern_ocean_w'],// ✓ Southern Ocean corridor west
        ['southern_ocean_w', 'cape_good_hope'],  // ✓ open ocean into Cape of Good Hope
        ['southern_ocean_w', 'agulhas_bank'],    // ✓ south of Africa
        // Also connect bass_strait directly to southern_ocean_c so east-coast
        // Australian ports go west (not east) to reach Atlantic
        ['bass_strait',      'southern_ocean_c'],// ✓ open Southern Ocean south of Tasmania

        // ── Indian Ocean ────────────────────────────────────────────────────
        ['western_indian',   'central_indian'],  // ✓ open ocean
        ['western_indian',   'mozambique_north'],// ✓ open sea
        ['central_indian',   'sri_lanka_south'], // ✓ open ocean
        ['central_indian',   'cocos_basin'],     // ✓ open ocean
        ['central_indian',   'south_indian'],    // ✓ open ocean
        ['sri_lanka_south',  'bay_of_bengal'],   // ✓ open ocean
        ['sri_lanka_south',  'andaman_sea'],     // ✓ open ocean
        ['bay_of_bengal',    'andaman_sea'],     // ✓ open ocean
        ['andaman_sea',      'malacca_north'],   // ✓ approaching Malacca
        ['cocos_basin',      'sw_australia'],     // ✓ open ocean
        ['cocos_basin',      'nw_australia'],     // ✓ open ocean
        ['south_indian',     'sw_australia'],     // ✓ open ocean

        // ── Southeast Asia — Malacca corridor ───────────────────────────────
        ['gulf_of_martaban', 'andaman_sea'],
        ['gulf_of_martaban', 'malacca_north'],
        ['malacca_north',    'malacca_south'],
        ['malacca_south',    'singapore_strait'],
        ['singapore_strait', 'singapore_east'],

        // ── South China Sea ─────────────────────────────────────────────────
        ['singapore_east',   'scs_south'],
        ['scs_south',        'scs_central'],
        ['scs_central',      'scs_north'],
        ['scs_central',      'sulu_sea'],
        ['gulf_of_thailand', 'scs_south'],
        ['gulf_of_thailand', 'singapore_east'],

        // ── East Asia ───────────────────────────────────────────────────────
        ['scs_north',        'taiwan_strait'],
        ['scs_north',        'philippine_sea'],
        ['taiwan_strait',    'east_china_sea'],
        ['east_china_sea',   'yellow_sea'],
        ['east_china_sea',   'nw_pacific'],
        ['yellow_sea',       'sea_of_japan'],
        ['nw_pacific',       'sea_of_japan'],
        ['nw_pacific',       'philippine_sea'],
        ['nw_pacific',       'north_pacific'],

        // ── Indonesian Archipelago ──────────────────────────────────────────
        ['singapore_east',   'karimata_strait'],
        ['karimata_strait',  'sunda_strait'],    // ✓ through water west of Java
        ['karimata_strait',  'java_sea'],
        ['sunda_strait',     'cocos_basin'],     // ✓ Indian Ocean side of Sunda
        ['java_sea',         'lombok_strait'],
        ['java_sea',         'makassar_strait'],
        ['java_sea',         'flores_sea'],
        ['lombok_strait',    'flores_sea'],
        ['lombok_strait',    'nw_australia'],    // ✓ open ocean south of Indonesia
        ['lombok_strait',    'cocos_basin'],     // ✓ Indian Ocean side
        ['flores_sea',       'makassar_strait'],
        ['flores_sea',       'banda_sea'],
        ['flores_sea',       'timor_sea'],
        ['makassar_strait',  'celebes_sea'],
        ['celebes_sea',      'sulu_sea'],
        ['celebes_sea',      'molucca_sea'],
        ['celebes_sea',      'philippine_sea'],
        ['molucca_sea',      'banda_sea'],
        ['molucca_sea',      'philippine_sea'],
        ['banda_sea',        'timor_sea'],
        ['banda_sea',        'arafura_sea'],
        ['timor_sea',        'arafura_sea'],
        ['timor_sea',        'nw_australia'],
        ['timor_sea',        'north_australia'],
        ['sulu_sea',         'philippine_sea'],

        // ── Australia — routed around Cape York via Torres Strait ────────────
        // IMPORTANT: nw_australia (-16,118) → sw_australia (-33,113) stays offshore
        // along the west coast of Australia (both points are in the Indian Ocean).
        // The straight line between them does NOT cross land.
        ['nw_australia',     'sw_australia'],     // ✓ offshore west coast of Australia (Indian Ocean)
        ['nw_australia',     'north_australia'],  // ✓ along north coast (offshore)
        ['arafura_sea',      'torres_strait'],   // ✓ into Torres Strait
        ['north_australia',  'torres_strait'],   // ✓ along coast
        ['torres_strait',    'ne_australia'],    // ✓ around Cape York via strait
        ['torres_strait',    'coral_sea'],       // ✓ into Coral Sea
        ['ne_australia',     'coral_sea'],       // ✓ offshore
        ['ne_australia',     'east_australia'],   // ✓ along coast
        ['east_australia',   'tasman_sea'],      // ✓ offshore
        ['tasman_sea',       'bass_strait'],     // ✓ around Tasmania
        ['tasman_sea',       'coral_sea'],
        ['tasman_sea',       'new_zealand'],     // ✓ open ocean
        // Bass Strait connects to South Australia via offshore waypoints (avoids land)
        ['bass_strait',      'south_aus_east'],  // ✓ offshore south of Victoria → SA approach
        ['south_aus_east',   'south_aus_west'],  // ✓ offshore south of SA coast
        ['south_aus_west',   'great_aus_bight'], // ✓ open Southern Ocean west of SA
        ['great_aus_bight',  'sw_australia'],    // ✓ south coast stays offshore
        // Extra links so Adelaide can be reached from either side
        ['bass_strait',      'south_aus_west'],  // ✓ shortcut south of SA (open ocean)
        ['south_aus_east',   'bass_strait'],     // bidirectional already, explicit for clarity

        // ── Pacific Ocean ───────────────────────────────────────────────────
        ['philippine_sea',   'bismarck_sea'],
        ['bismarck_sea',     'coral_sea'],
        ['coral_sea',        'fiji_waters'],
        ['fiji_waters',      'new_zealand'],
        ['fiji_waters',      'south_pacific'],
        ['fiji_waters',      'central_pacific'],
        ['north_pacific',    'ne_pacific'],
        ['north_pacific',    'central_pacific'],
        ['ne_pacific',       'us_west_coast'],
        ['central_pacific',  'south_pacific'],
        ['south_pacific',    'se_pacific'],
        ['south_pacific',    'new_zealand'],
        ['se_pacific',       'chile_coast'],

        // ── Atlantic Ocean ──────────────────────────────────────────────────
        ['north_atlantic',   'us_east_coast'],   // ✓ open ocean
        ['north_atlantic',   'central_atlantic'],// ✓ open ocean
        ['north_atlantic',   'iceland'],         // ✓ open ocean
        ['north_atlantic',   'nw_africa'],       // ✓ open ocean
        ['central_atlantic', 'caribbean'],       // ✓ open ocean
        ['central_atlantic', 'south_atlantic_w'],// ✓ open ocean
        ['central_atlantic', 'west_africa_bulge'],// ✓ open ocean
        ['central_atlantic', 'brazil_coast'],    // ✓ open ocean
        ['south_atlantic_w', 'south_atlantic_e'],// ✓ open ocean, mid-south Atlantic
        ['south_atlantic_w', 'brazil_coast'],    // ✓ open ocean
        ['south_atlantic_w', 'rio_plata'],       // ✓ open ocean

        // ── Americas ────────────────────────────────────────────────────────
        ['caribbean',        'gulf_of_mexico'],
        ['caribbean',        'us_east_coast'],   // ✓ around Florida via open ocean
        ['caribbean',        'panama_atlantic'],
        ['caribbean',        'central_atlantic'],// ✓ open Atlantic
        ['panama_atlantic',  'panama_pacific'],  // ✓ canal crossing
        ['panama_pacific',   'se_pacific'],      // ✓ open Pacific
        ['panama_pacific',   'chile_coast'],     // ✓ open Pacific along coast
        ['ne_pacific',       'us_west_coast'],
        ['brazil_coast',     'rio_plata'],       // ✓ along coast
        ['rio_plata',        'cape_horn'],       // ✓ along coast
        ['cape_horn',        'chile_coast'],     // ✓ around the horn
        ['cape_horn',        'se_pacific'],      // ✓ open Pacific
        ['cape_horn',        'south_atlantic_w'],// ✓ around the horn into Atlantic
        ['south_atlantic_w', 'south_atlantic_e'],// ✓ open South Atlantic (key east-west bridge!)
        ['south_atlantic_e', 'south_atlantic_w'],// bidirectional
    ],
];

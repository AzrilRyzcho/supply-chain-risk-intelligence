<?php

namespace App\Services;

use App\Models\ImportShipment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * RouteJourneyService — Graph-based Global Sea Corridor Routing
 *
 * Uses Dijkstra's shortest-path algorithm over a global maritime waypoint graph
 * to compute realistic sea routes between any two ports worldwide.
 *
 * No external API dependency. All routing is computed locally using the
 * sea corridor configuration in config/sea_corridors.php.
 */
class RouteJourneyService
{
    /** @var array<string, array{name: string, lat: float, lng: float, region: string}> */
    private array $waypoints = [];

    /** @var array<string, list<array{node: string, dist: float}>> */
    private array $adjacency = [];

    /** @var list<string> Waypoint keys that are considered major/important choke points */
    private array $importantWaypointKeys = [
        'gibraltar',
        'suez_canal',
        'bab_el_mandeb',
        'gulf_of_aden',
        'sri_lanka_south',
        'malacca_north',
        'malacca_south',
        'singapore_strait',
        'sunda_strait',
        'lombok_strait',
        'torres_strait',
        'cape_good_hope',
        'panama_atlantic',
        'panama_pacific',
        'cape_horn',
        'strait_of_hormuz',
        'english_channel',
        'karimata_strait',
        'gulf_of_martaban',
        'gulf_of_thailand'
    ];

    public function __construct()
    {
        $this->buildGraph();
    }

    // =========================================================================
    //  PUBLIC API
    // =========================================================================

    /**
     * Get complete route data for a shipment (cached for 24 hours).
     */
    public function getRouteData(ImportShipment $shipment): array
    {
        $shipment->load(['originPort.country', 'destinationPort.country']);

        $origin      = $shipment->originPort;
        $destination  = $shipment->destinationPort;
        $cacheKey     = "sea_corridor_route_{$shipment->id}_v2";

        return Cache::remember($cacheKey, 86400, function () use ($shipment, $origin, $destination) {
            $oLat = (float) $origin->latitude;
            $oLng = (float) $origin->longitude;
            $dLat = (float) $destination->latitude;
            $dLng = (float) $destination->longitude;

            // Find the sea route via the corridor graph
            $result = $this->findSeaRoute($oLat, $oLng, $dLat, $dLng);

            $oLngUnwrapped = $result['coordinates'][0][1];
            $dLngUnwrapped = $result['coordinates'][count($result['coordinates']) - 1][1];

            // Calculate distance and duration
            $distanceKm   = $this->totalDistance($result['coordinates']);
            $speedKmh     = 37.0; // ~20 knots average cargo vessel speed
            $durationHours = $distanceKm / $speedKmh;

            // Filter waypoints to only show important ones (Simplification)
            $filteredWaypoints = [];
            foreach ($result['waypoints'] as $wp) {
                if (in_array($wp['key'], $this->importantWaypointKeys)) {
                    $filteredWaypoints[] = $wp;
                }
            }

            // Generate risk events along all path waypoints using DB Weather & News
            $riskEvents = $this->getRiskEvents($result['waypoints']);

            return [
                'coordinates'      => $result['coordinates'],
                'waypoints_passed' => $filteredWaypoints, // Simplified waypoints for UI
                'distance_km'      => round($distanceKm, 2),
                'duration_hours'   => round($durationHours, 1),
                'duration_formatted' => $this->formatDuration($durationHours),
                'is_simulated'     => true,
                'origin' => [
                    'name'      => $origin->name,
                    'code'      => $origin->code,
                    'country'   => $origin->country->name,
                    'latitude'  => $oLat,
                    'longitude' => $oLngUnwrapped,
                ],
                'destination' => [
                    'name'      => $destination->name,
                    'code'      => $destination->code,
                    'country'   => $destination->country->name,
                    'latitude'  => $dLat,
                    'longitude' => $dLngUnwrapped,
                ],
                'transport_mode' => 'Sea Freight',
                'status'         => $shipment->status,
                'created_at'     => $shipment->created_at->toIso8601String(),
                'risk_events'    => $riskEvents, // New risk events array
            ];
        });
    }

    /**
     * Clear route cache for a specific shipment.
     */
    public function clearRouteCache(int $shipmentId): void
    {
        Cache::forget("sea_corridor_route_{$shipmentId}_v2");
    }

    /**
     * Public wrapper around findSeaRoute — used by routePreview endpoint
     * so callers outside this service can compute a sea route directly from coords.
     *
     * @return array{coordinates: list<array{float,float}>, waypoints: list<array>}
     */
    public function computeSeaRoute(float $oLat, float $oLng, float $dLat, float $dLng): array
    {
        return $this->findSeaRoute($oLat, $oLng, $dLat, $dLng);
    }

    // =========================================================================
    //  GRAPH CONSTRUCTION
    // =========================================================================

    /**
     * Build the sea corridor graph from config/sea_corridors.php.
     */
    private function buildGraph(): void
    {
        $config = config('sea_corridors');

        if (!$config) {
            Log::warning('RouteJourneyService: sea_corridors config not found.');
            return;
        }

        $this->waypoints = $config['waypoints'] ?? [];

        // Initialize adjacency list
        foreach (array_keys($this->waypoints) as $key) {
            $this->adjacency[$key] = [];
        }

        // Build bidirectional edges with haversine distance weights
        foreach ($config['connections'] ?? [] as [$a, $b]) {
            if (!isset($this->waypoints[$a], $this->waypoints[$b])) {
                continue;
            }
            $dist = $this->haversine(
                $this->waypoints[$a]['lat'], $this->waypoints[$a]['lng'],
                $this->waypoints[$b]['lat'], $this->waypoints[$b]['lng']
            );
            $this->adjacency[$a][] = ['node' => $b, 'dist' => $dist];
            $this->adjacency[$b][] = ['node' => $a, 'dist' => $dist];
        }
    }

    /**
     * Find the optimal sea route between two coordinates.
     *
     * 1. Finds the single closest allowed sea node (waypoint) for each port.
     * 2. Finds the shortest path in the sea corridor graph between these two sea nodes.
     * 3. Calculates the spline curves ONLY between the sea nodes.
     * 4. Connects the ports to the spline endpoints with straight lines.
     *
     * @return array{coordinates: list<array{float,float}>, waypoints: list<array>}
     */
    private function findSeaRoute(float $oLat, float $oLng, float $dLat, float $dLng): array
    {
        $originNearest = $this->nearestWaypoints($oLat, $oLng, 1);
        $destNearest   = $this->nearestWaypoints($dLat, $dLng, 1);

        if (empty($originNearest) || empty($destNearest)) {
            return [
                'coordinates' => [[$oLat, $oLng], [$dLat, $dLng]],
                'waypoints'   => [],
            ];
        }

        $entryKey = array_key_first($originNearest);
        $exitKey  = array_key_first($destNearest);

        if ($entryKey === $exitKey) {
            $path = [$entryKey];
        } else {
            $path = $this->dijkstra($entryKey, $exitKey);
        }

        if (empty($path)) {
            return [
                'coordinates' => [[$oLat, $oLng], [$dLat, $dLng]],
                'waypoints'   => [],
            ];
        }

        // Build sea nodes coordinates array for spline smoothing
        $seaNodeCoords = [];
        $waypointsMeta = [];

        foreach ($path as $key) {
            $wp = $this->waypoints[$key];
            $seaNodeCoords[] = [$wp['lat'], $wp['lng']];
            $waypointsMeta[] = [
                'key'    => $key,
                'name'   => $wp['name'],
                'lat'    => $wp['lat'],
                'lng'    => $wp['lng'],
                'region' => $wp['region'],
            ];
        }

        // Smooth coordinates ONLY between sea nodes
        $smoothCoords = $this->smoothRoute($seaNodeCoords);

        // Prepend origin port and append destination port as straight lines
        array_unshift($smoothCoords, [$oLat, $oLng]);
        $smoothCoords[] = [$dLat, $dLng];

        // Unwrap the entire path including the origin and destination ports
        $smoothCoords = $this->unwrapCoordinates($smoothCoords);

        // Also unwrap waypointsMeta longitudes to align them with the route coordinates
        $unwrappedWaypoints = $this->unwrapCoordinates($seaNodeCoords);
        foreach ($waypointsMeta as $i => &$wp) {
            $wp['lng'] = $unwrappedWaypoints[$i][1];
        }
        unset($wp);

        return [
            'coordinates' => $smoothCoords,
            'waypoints'   => $waypointsMeta,
        ];
    }

    /**
     * Find the N nearest allowed sea nodes (waypoints) to a coordinate.
     *
     * Geographic zoning filters are applied to avoid crossing major landmasses.
     *
     * @return array<string, float>  key => distance_km
     */
    private function nearestWaypoints(float $lat, float $lng, int $count = 1): array
    {
        $distances = [];
        foreach ($this->waypoints as $key => $wp) {
            // Apply geographic zoning to prevent land-crossing shortcuts
            if (!$this->isWaypointAllowedForPort($lat, $lng, $key, $wp)) {
                continue;
            }

            $distances[$key] = $this->haversine($lat, $lng, $wp['lat'], $wp['lng']);
        }
        asort($distances);
        return array_slice($distances, 0, $count, true);
    }

    /**
     * Geographical zoning to prevent ports in enclosed seas or opposite coasts
     * from connecting directly to waypoints across land barriers.
     */
    private function isWaypointAllowedForPort(float $pLat, float $pLng, string $wpKey, array $wp): bool
    {
        $wLat = $wp['lat'];
        $wLng = $wp['lng'];
        $wRegion = $wp['region'] ?? '';

        // 1. Northern Europe / Baltic Zone
        // Ports in Northern Europe (lat > 45N, lng between -15W and 35E)
        if ($pLat > 45.0 && $pLng > -15.0 && $pLng < 35.0) {
            // Cannot connect directly to Mediterranean or Red Sea waypoints
            if (in_array($wRegion, ['Mediterranean', 'Red Sea'])) {
                return false;
            }
            // Baltic ports vs North Sea ports divide
            if ($pLng < 10.0 && $wpKey === 'baltic_sea') {
                return false;
            }
        }

        // 2. Mediterranean Zone
        // Ports inside Mediterranean/Black Sea (lat 30N to 48N, lng -6W to 42E)
        $inMedPort = ($pLat > 30.0 && $pLat < 48.0 && $pLng > -6.0 && $pLng < 42.0);
        $inMedWp   = ($wLat > 30.0 && $wLat < 48.0 && $wLng > -6.0 && $wLng < 42.0 && in_array($wRegion, ['Mediterranean', 'Europe']));
        if ($inMedPort && !$inMedWp && !in_array($wpKey, ['suez_canal', 'gibraltar'])) {
            return false;
        }
        // Non-Med ports cannot connect directly to Med waypoints
        if (!$inMedPort && $wRegion === 'Mediterranean' && !in_array($wpKey, ['gibraltar'])) {
            return false;
        }

        // 3. Red Sea Zone
        // Ports inside Red Sea (lat 12N to 30N, lng 32E to 44E)
        $inRedPort = ($pLat > 12.0 && $pLat < 30.0 && $pLng > 32.0 && $pLng < 44.0);
        if ($inRedPort) {
            if ($wRegion !== 'Red Sea' && !in_array($wpKey, ['suez_canal', 'bab_el_mandeb'])) {
                return false;
            }
        }
        // Non-Red Sea ports cannot connect directly to Red Sea waypoints
        if (!$inRedPort && $wRegion === 'Red Sea' && !in_array($wpKey, ['suez_canal', 'bab_el_mandeb'])) {
            return false;
        }

        // 4. Persian Gulf Zone
        // Ports inside Persian Gulf (lat 23N to 31N, lng 47E to 57E)
        $inGulfPort = ($pLat > 23.0 && $pLat < 31.0 && $pLng > 47.0 && $pLng < 57.0);
        if ($inGulfPort) {
            if (!in_array($wpKey, ['persian_gulf', 'strait_of_hormuz'])) {
                return false;
            }
        }
        // Non-Gulf ports cannot connect directly to Persian Gulf waypoint
        if (!$inGulfPort && $wpKey === 'persian_gulf') {
            return false;
        }

        // 5. Americas Divide (Pacific vs Atlantic/Caribbean)
        // Ports on the ATLANTIC side of the Americas: lng -100W to -35W
        // This includes Caribbean, US East Coast, Brazil, Colombia (Cartagena lng=-75)
        $inAmericasAtlanticPort = ($pLng > -100.0 && $pLng < -35.0);
        if ($inAmericasAtlanticPort) {
            // Cannot connect directly to Pacific-side waypoints (would cross Central America)
            if (in_array($wpKey, [
                'panama_pacific', 'us_west_coast', 'ne_pacific', 'se_pacific',
                'chile_coast', 'central_pacific', 'south_pacific', 'north_pacific',
                'south_indian', 'southern_ocean_c', 'southern_ocean_w',
            ])) {
                return false;
            }
        }
        // Ports on the PACIFIC side of the Americas (lng < -100W, lat > -60)
        $inUSPacificPort = ($pLng < -100.0 && $pLat > -60.0);
        if ($inUSPacificPort) {
            if (in_array($wpKey, [
                'panama_atlantic', 'caribbean', 'gulf_of_mexico',
                'us_east_coast', 'brazil_coast', 'rio_plata', 'central_atlantic',
                'south_atlantic_w', 'south_atlantic_e',
            ])) {
                return false;
            }
        }
        // Ports inside Gulf of Thailand (lat 8N to 15N, lng 99E to 105E)
        $inGulfThailandPort = ($pLat > 8.0 && $pLat < 15.0 && $pLng > 99.0 && $pLng < 105.0);
        if ($inGulfThailandPort) {
            if ($wpKey !== 'gulf_of_thailand') {
                return false;
            }
        }
        // Non-Gulf of Thailand ports cannot connect directly to Gulf of Thailand waypoint
        if (!$inGulfThailandPort && $wpKey === 'gulf_of_thailand') {
            return false;
        }

        // 7. Australia East Coast Zone
        // Ports on the east/southeast coast of Australia (lat -45S to -10S, lng > 140E)
        // These ports must exit via Torres Strait / Coral Sea, NOT cross the continent.
        $inAusEastPort = ($pLat < -10.0 && $pLat > -45.0 && $pLng > 140.0);
        if ($inAusEastPort) {
            // Block any waypoint on the west/northwest/south-west side of Australia
            if (in_array($wpKey, [
                'nw_australia', 'sw_australia', 'great_aus_bight',
                'cocos_basin', 'south_indian', 'central_indian',
                'timor_sea', 'north_australia', 'arafura_sea',
            ])) {
                return false;
            }
        }

        // 8. Australia West Coast Zone
        // Ports on the west/northwest coast of Australia (lat -35S to -10S, lng < 120E)
        // These ports must exit westward into the Indian Ocean, NOT cross the continent.
        $inAusWestPort = ($pLat < -10.0 && $pLat > -35.0 && $pLng < 120.0);
        if ($inAusWestPort) {
            // Block any waypoint on the east/northeast coast of Australia
            if (in_array($wpKey, [
                'ne_australia', 'east_australia', 'tasman_sea',
                'bass_strait', 'coral_sea', 'torres_strait',
                'north_australia', 'arafura_sea',
            ])) {
                return false;
            }
        }

        // 9. Australia South Coast Zone
        // Ports on the south coast of Australia (lat -40S to -30S, lng 120E to 140E)
        // Must route via Great Australian Bight or Bass Strait, not overland.
        $inAusSouthPort = ($pLat < -30.0 && $pLat > -40.0 && $pLng > 120.0 && $pLng < 140.0);
        if ($inAusSouthPort) {
            // Block waypoints that would require crossing Australia
            if (in_array($wpKey, [
                'nw_australia', 'ne_australia', 'east_australia',
                'north_australia', 'arafura_sea', 'timor_sea',
                'cocos_basin', 'lombok_strait', 'java_sea',
            ])) {
                return false;
            }
        }

        // 10. Indonesian Archipelago Zone
        // Ports in Java Sea / Indonesian region (lat -10S to 5N, lng 105E to 140E)
        // Must use proper straits, not jump directly to Australian east coast waypoints.
        $inIndonesiaPort = ($pLat < 5.0 && $pLat > -10.0 && $pLng > 105.0 && $pLng < 140.0);
        if ($inIndonesiaPort) {
            // Cannot jump directly across Australia to east coast waypoints
            if (in_array($wpKey, ['east_australia', 'tasman_sea', 'bass_strait', 'new_zealand'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Dijkstra's shortest-path algorithm on the sea corridor graph.
     *
     * @return list<string>  Ordered list of waypoint keys from start to end.
     */
    private function dijkstra(string $start, string $end): array
    {
        $dist    = [];
        $prev    = [];
        $visited = [];

        foreach (array_keys($this->waypoints) as $key) {
            $dist[$key] = PHP_FLOAT_MAX;
            $prev[$key] = null;
        }

        $dist[$start] = 0.0;

        // SplPriorityQueue (max-heap) — use negative values for min-heap
        $queue = new \SplPriorityQueue();
        $queue->insert($start, 0);

        while (!$queue->isEmpty()) {
            $current = $queue->extract();

            if (isset($visited[$current])) {
                continue;
            }
            $visited[$current] = true;

            if ($current === $end) {
                break;
            }

            foreach ($this->adjacency[$current] ?? [] as $edge) {
                $neighbor = $edge['node'];
                $newDist  = $dist[$current] + $edge['dist'];

                if ($newDist < $dist[$neighbor]) {
                    $dist[$neighbor] = $newDist;
                    $prev[$neighbor] = $current;
                    $queue->insert($neighbor, -$newDist);
                }
            }
        }

        // Unreachable
        if ($prev[$end] === null && $start !== $end) {
            return [];
        }

        // Reconstruct path
        $path    = [];
        $current = $end;
        while ($current !== null) {
            array_unshift($path, $current);
            $current = $prev[$current];
        }

        return ($path[0] === $start) ? $path : [];
    }

    // =========================================================================
    //  PATH SMOOTHING (Great-Circle Interpolation)
    // =========================================================================

    /**
     * Smooth a raw coordinate path by inserting intermediate points along each
     * segment using linear interpolation. Linear interpolation is used instead of
     * Catmull-Rom splines because splines can curve *through landmasses* when
     * waypoints sit close to coastlines.
     *
     * Antimeridian handling: when consecutive waypoints differ by more than 180°
     * in longitude, we "unwrap" the longitude so the interpolation goes the short
     * way around the globe (e.g. 175° → 185° instead of 175° → -175°).
     * Leaflet handles unwrapped longitudes (>180 or <-180) correctly with
     * worldCopyJump: true.
     */
    /**
     * Unwrap longitudes so consecutive points differ by < 180°, allowing
     * smooth antimeridian crossings without global wrapping lines.
     */
    private function unwrapCoordinates(array $coordinates): array
    {
        if (count($coordinates) < 2) {
            return $coordinates;
        }

        $unwrapped = [$coordinates[0]];
        for ($i = 1; $i < count($coordinates); $i++) {
            $prevLng = $unwrapped[$i - 1][1];
            $currLng = $coordinates[$i][1];
            // Normalise the difference to [-180, 180]
            $diff = fmod($currLng - $prevLng + 540, 360) - 180;
            $unwrapped[] = [$coordinates[$i][0], $prevLng + $diff];
        }

        return $unwrapped;
    }

    private function smoothRoute(array $coordinates): array
    {
        if (count($coordinates) < 2) {
            return $coordinates;
        }

        // Step 1 — unwrap longitudes so consecutive points differ by < 180°
        $unwrapped = $this->unwrapCoordinates($coordinates);

        // Step 2 — linear interpolation with ~150 km steps
        $smoothed = [];
        for ($i = 0; $i < count($unwrapped) - 1; $i++) {
            $p1 = $unwrapped[$i];
            $p2 = $unwrapped[$i + 1];

            $segDist = $this->haversine($p1[0], $p1[1], $p2[0], $p2[1]);
            $steps   = max(2, (int) ceil($segDist / 150));

            for ($j = 0; $j < $steps; $j++) {
                $t = $j / $steps;
                $smoothed[] = [
                    round($p1[0] + ($p2[0] - $p1[0]) * $t, 5),
                    round($p1[1] + ($p2[1] - $p1[1]) * $t, 5),
                ];
            }
        }
        $last = end($unwrapped);
        $smoothed[] = [round($last[0], 5), round($last[1], 5)];
        return $smoothed;
    }

    /**
     * Spherical (great-circle / Slerp) interpolation between two [lat, lng] pairs.
     */
    private function greatCircleInterpolate(array $from, array $to, float $f): array
    {
        $lat1 = deg2rad($from[0]);
        $lng1 = deg2rad($from[1]);
        $lat2 = deg2rad($to[0]);
        $lng2 = deg2rad($to[1]);

        $d = 2 * asin(sqrt(
            sin(($lat1 - $lat2) / 2) ** 2 +
            cos($lat1) * cos($lat2) * sin(($lng1 - $lng2) / 2) ** 2
        ));

        if ($d < 1e-6) {
            return $from;
        }

        $a = sin((1 - $f) * $d) / sin($d);
        $b = sin($f * $d) / sin($d);

        $x = $a * cos($lat1) * cos($lng1) + $b * cos($lat2) * cos($lng2);
        $y = $a * cos($lat1) * sin($lng1) + $b * cos($lat2) * sin($lng2);
        $z = $a * sin($lat1) + $b * sin($lat2);

        return [
            round(rad2deg(atan2($z, sqrt($x * $x + $y * $y))), 5),
            round(rad2deg(atan2($y, $x)), 5),
        ];
    }

    // =========================================================================
    //  DISTANCE CALCULATION
    // =========================================================================

    /**
     * Calculate total distance along a coordinate array.
     */
    private function totalDistance(array $coordinates): float
    {
        $total = 0.0;
        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $total += $this->haversine(
                $coordinates[$i][0], $coordinates[$i][1],
                $coordinates[$i + 1][0], $coordinates[$i + 1][1]
            );
        }
        return $total;
    }

    /**
     * Calculate total distance along a graph path (array of waypoint keys).
     */
    private function graphPathDistance(array $keys): float
    {
        $total = 0.0;
        for ($i = 0; $i < count($keys) - 1; $i++) {
            $a = $this->waypoints[$keys[$i]];
            $b = $this->waypoints[$keys[$i + 1]];
            $total += $this->haversine($a['lat'], $a['lng'], $b['lat'], $b['lng']);
        }
        return $total;
    }

    /**
     * Haversine formula — great-circle distance in kilometres.
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    // =========================================================================
    //  FORMATTING & RISKS
    // =========================================================================

    /**
     * Format duration from hours into human-readable Indonesian string.
     */
    private function formatDuration(float $hours): string
    {
        $days = (int) floor($hours / 24);
        $rem  = (int) round($hours - ($days * 24));

        return $days > 0 ? "{$days} hari {$rem} jam" : "{$rem} jam";
    }

    /**
     * Dynamically scan route waypoints and check weather and negative news in the database.
     * Generates a list of map-renderable risk events (Storm, Congestion, Conflict).
     */
    private function getRiskEvents(array $waypoints): array
    {
        $events = [];
        $seenCountries = [];

        foreach ($waypoints as $wp) {
            $country = $this->findNearestCountry((float)$wp['lat'], (float)$wp['lng']);
            if (!$country || isset($seenCountries[$country->id])) {
                continue;
            }
            $seenCountries[$country->id] = true;

            // 1. Weather Storm Risk
            $weather = \App\Models\Weather::where('country_id', $country->id)->first();
            if ($weather && ($weather->storm_risk > 65 || $weather->wind_speed > 30)) {
                $events[] = [
                    'type' => 'Storm',
                    'lat' => (float)$wp['lat'] + 0.4, // offset slightly
                    'lng' => (float)$wp['lng'] - 0.4,
                    'title' => "Peringatan Badai: Pesisir " . $country->name,
                    'description' => "Risiko badai tinggi ({$weather->storm_risk}%) dengan kecepatan angin {$weather->wind_speed} knot. Navigasi disarankan berhati-hati.",
                    'severity' => $weather->storm_risk > 80 ? 'High' : 'Medium',
                ];
            }

            // 2. News Conflict & Congestion Risk
            $newsItems = \App\Models\News::where('country_id', $country->id)
                ->where('sentiment', 'negative')
                ->get();

            foreach ($newsItems as $news) {
                $title = $news->title;
                $type = null;
                $severity = $news->risk_score > 80 ? 'High' : 'Medium';
                $description = $news->title;

                if (stripos($title, 'strike') !== false || stripos($title, 'logistics') !== false || stripos($title, 'congestion') !== false) {
                    $type = 'Congestion';
                    $eventTitle = "Kepadatan Pelabuhan: " . $country->name;
                } elseif (stripos($title, 'conflict') !== false || stripos($title, 'tensions') !== false || stripos($title, 'military') !== false || stripos($title, 'war') !== false || stripos($title, 'trade') !== false) {
                    $type = 'Conflict';
                    $eventTitle = "Ketegangan Wilayah: " . $country->name;
                }

                if ($type) {
                    $events[] = [
                        'type' => $type,
                        'lat' => (float)$wp['lat'] - 0.4, // offset slightly
                        'lng' => (float)$wp['lng'] + 0.4,
                        'title' => $eventTitle,
                        'description' => $description,
                        'severity' => $severity,
                    ];
                    break; // limit to 1 news warning per country
                }
            }
        }

        return $events;
    }

    /**
     * Find the country nearest to a set of sea coordinates using haversine calculation (cached).
     */
    private function findNearestCountry(float $lat, float $lng)
    {
        // Normalize longitude to [-180, 180] range
        $lng = fmod($lng + 180, 360);
        if ($lng < 0) {
            $lng += 360;
        }
        $lng -= 180;

        $countryId = Cache::remember("nearest_country_id_{$lat}_{$lng}", 604800, function() use ($lat, $lng) {
            $countries = \App\Models\Country::all();
            $bestId   = null;
            $bestDist = PHP_FLOAT_MAX;

            foreach ($countries as $c) {
                if ($c->latitude === null || $c->longitude === null) {
                    continue;
                }
                $d = $this->haversine($lat, $lng, (float)$c->latitude, (float)$c->longitude);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $bestId   = $c->id;
                }
            }
            return $bestId; // Cache only an integer — safe to serialize/unserialize
        });

        return $countryId ? \App\Models\Country::find($countryId) : null;
    }
}

<?php

namespace App\Services\Geo;

class PointInPolygonService
{
    /**
     * Determine if a point ($lat, $lng) lies inside a polygon.
     *
     * @param float $lat Target latitude
     * @param float $lng Target longitude
     * @param mixed $boundary GeoJSON structure or array of points
     * @return bool
     */
    public function contains(float $lat, float $lng, mixed $boundary): bool
    {
        if (empty($boundary)) {
            return false;
        }

        $polygonPoints = $this->extractPolygonPoints($boundary);

        if (count($polygonPoints) < 3) {
            return false;
        }

        return $this->rayCasting($lat, $lng, $polygonPoints);
    }

    /**
     * Standard Ray-Casting Algorithm for Point-in-Polygon.
     *
     * @param float $lat
     * @param float $lng
     * @param array<array{lat: float, lng: float}> $polygon
     * @return bool
     */
    protected function rayCasting(float $lat, float $lng, array $polygon): bool
    {
        $inside = false;
        $numPoints = count($polygon);

        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $lng) !== ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / (($yj - $yi) ?: 0.00000001) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Normalizes various GeoJSON or custom polygon structures to [ ['lat' => float, 'lng' => float], ... ].
     */
    public function extractPolygonPoints(mixed $boundary): array
    {
        if (is_string($boundary)) {
            $decoded = json_decode($boundary, true);
            if (is_array($decoded)) {
                $boundary = $decoded;
            }
        }

        if (!is_array($boundary)) {
            return [];
        }

        // 1. GeoJSON Feature or FeatureCollection
        if (isset($boundary['geometry'])) {
            $boundary = $boundary['geometry'];
        }

        // 2. GeoJSON Geometry with type = Polygon
        if (isset($boundary['coordinates']) && is_array($boundary['coordinates'])) {
            $coords = $boundary['coordinates'];
            // GeoJSON Polygon coordinates is an array of linear rings: [ [ [lng, lat], ... ] ]
            if (isset($coords[0]) && is_array($coords[0])) {
                $ring = (isset($coords[0][0]) && is_array($coords[0][0])) ? $coords[0] : $coords;
                $points = [];
                foreach ($ring as $pair) {
                    if (is_array($pair) && count($pair) >= 2) {
                        // GeoJSON standard is [longitude, latitude]
                        $points[] = [
                            'lat' => (float) $pair[1],
                            'lng' => (float) $pair[0],
                        ];
                    }
                }
                return $points;
            }
        }

        // 3. Array of points: [ ['lat' => ..., 'lng' => ...], ... ]
        if (isset($boundary[0]['lat']) && isset($boundary[0]['lng'])) {
            return array_map(fn($pt) => [
                'lat' => (float) $pt['lat'],
                'lng' => (float) $pt['lng'],
            ], $boundary);
        }

        // 4. Array of pairs: [ [lat, lng], [lat, lng], ... ]
        if (isset($boundary[0]) && is_array($boundary[0]) && count($boundary[0]) >= 2) {
            return array_map(fn($pair) => [
                'lat' => (float) $pair[0],
                'lng' => (float) $pair[1],
            ], $boundary);
        }

        return [];
    }

    /**
     * Check circular distance using Haversine formula (km) as fallback.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}

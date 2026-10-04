<?php

namespace Tests\Unit;

use App\Services\Geo\PointInPolygonService;
use PHPUnit\Framework\TestCase;

class PointInPolygonTest extends TestCase
{
    private PointInPolygonService $geoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geoService = new PointInPolygonService();
    }

    public function test_it_correctly_identifies_points_inside_polygon(): void
    {
        // Simple square in Shiraz: [ [29.60, 52.50], [29.60, 52.60], [29.70, 52.60], [29.70, 52.50] ]
        $polygon = [
            ['lat' => 29.60, 'lng' => 52.50],
            ['lat' => 29.60, 'lng' => 52.60],
            ['lat' => 29.70, 'lng' => 52.60],
            ['lat' => 29.70, 'lng' => 52.50],
        ];

        // Center point: (29.65, 52.55) -> inside
        $this->assertTrue($this->geoService->contains(29.65, 52.55, $polygon));

        // Outside point: (29.80, 52.55) -> outside
        $this->assertFalse($this->geoService->contains(29.80, 52.55, $polygon));

        // Another outside point: (29.50, 52.55) -> outside
        $this->assertFalse($this->geoService->contains(29.50, 52.55, $polygon));
    }

    public function test_it_handles_geojson_polygon_geometry_coordinates(): void
    {
        // GeoJSON standard coordinates [ [ [lng, lat], ... ] ]
        $geoJson = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [52.50, 29.60],
                    [52.60, 29.60],
                    [52.60, 29.70],
                    [52.50, 29.70],
                    [52.50, 29.60],
                ]
            ]
        ];

        $this->assertTrue($this->geoService->contains(29.65, 52.55, $geoJson));
        $this->assertFalse($this->geoService->contains(29.75, 52.55, $geoJson));
    }

    public function test_it_calculates_haversine_distance_accurately(): void
    {
        // Distance between Eram Garden (29.6360, 52.5050) and Hafezieh (29.6258, 52.5583) ~ 5.3 km
        $distance = $this->geoService->haversineDistance(29.6360, 52.5050, 29.6258, 52.5583);

        $this->assertGreaterThan(4.5, $distance);
        $this->assertLessThan(6.0, $distance);
    }

    public function test_it_returns_false_for_empty_or_invalid_boundaries(): void
    {
        $this->assertFalse($this->geoService->contains(29.65, 52.55, null));
        $this->assertFalse($this->geoService->contains(29.65, 52.55, []));
        $this->assertFalse($this->geoService->contains(29.65, 52.55, [['lat' => 29.0, 'lng' => 52.0]])); // < 3 points
    }
}

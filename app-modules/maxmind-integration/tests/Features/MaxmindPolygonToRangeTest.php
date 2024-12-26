<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Tests\Features;

use GeoJson\Geometry\Polygon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Location\Contracts\PolygonToRangeInterface;
use XbNz\MaxmindIntegration\MaxmindPolygonToRange;
use XbNz\Shared\ValueObjects\Coordinates;

final class MaxmindPolygonToRangeTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_takes_polygons_and_resolves_them_to_correct_ip_addresses(): void
    {
        // Arrange
        $polygonA = Polygon::jsonUnserialize([
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [
                        10.725855855133034,
                        59.91762954898607,
                    ],
                    [
                        10.72253916528544,
                        59.90107982791031,
                    ],
                    [
                        10.765212198793307,
                        59.900046186238825,
                    ],
                    [
                        10.765859605341802,
                        59.91777820027971,
                    ],
                    [
                        10.725855855133034,
                        59.91762954898607,
                    ],
                ],
            ],
        ]);
        $polygonB = Polygon::jsonUnserialize([
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [
                        12.545507251097547,
                        55.701805970738775,
                    ],
                    [
                        12.513679653013781,
                        55.6560213884556,
                    ],
                    [
                        12.600655851339013,
                        55.6528418058428,
                    ],
                    [
                        12.614247799178827,
                        55.70235447965635,
                    ],
                    [
                        12.545507251097547,
                        55.701805970738775,
                    ],
                ],
            ],
        ]);

        $pointFallingWithinPolygonA = 'POINT(59.91158561885328 10.739314523850254)';
        $pointFallingWithinPolygonB = 'POINT(55.6732501966992 12.593978441088609)';

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v4_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['1.1.1.0', '1.1.1.255', $pointFallingWithinPolygonA]
            );

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v6_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['2001:4110:4860:0000:0000:0000:0000:8888', '2001:4110:4860:0000:0000:0000:0000:888F', $pointFallingWithinPolygonA]
            );

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v4_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['2.2.2.0', '2.2.2.255', $pointFallingWithinPolygonB]
            );

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v6_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860:0000:0000:0000:0000:8889', $pointFallingWithinPolygonB]
            );

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v4_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['3.3.3.0', '3.3.3.255', 'POINT(100.739314523850254 10.91158561885328)']
            );

        $polygonToRange = $this->app->make(MaxmindPolygonToRange::class);

        // Act
        $ranges = $polygonToRange
            ->filterIpType(PolygonToRangeInterface::FILTER_IPV4 | PolygonToRangeInterface::FILTER_IPV6)
            ->addPolygon($polygonA)
            ->addPolygon($polygonB)
            ->execute();

        // Assert
        $this->assertCount(4, $ranges);

        $expectedRanges = [
            ['1.1.1.0', '1.1.1.255'],
            ['2.2.2.0', '2.2.2.255'],
            ['2001:4110:4860:0000:0000:0000:0000:8888', '2001:4110:4860:0000:0000:0000:0000:888F'],
            ['2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860:0000:0000:0000:0000:8889'],
        ];

        foreach ($ranges as $range) {
            $this->assertContains([$range->startIp, $range->endIp], $expectedRanges);
        }

        $this->assertEquals(59.911586, $ranges[0]->coordinates->latitude);
        $this->assertEquals(10.739315, $ranges[0]->coordinates->longitude);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_filters_to_ipv4_only(): void
    {
        // Arrange
        $polygon = Polygon::jsonUnserialize([
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [
                        10.725855855133034,
                        59.91762954898607,
                    ],
                    [
                        10.72253916528544,
                        59.90107982791031,
                    ],
                    [
                        10.765212198793307,
                        59.900046186238825,
                    ],
                    [
                        10.765859605341802,
                        59.91777820027971,
                    ],
                    [
                        10.725855855133034,
                        59.91762954898607,
                    ],
                ],
            ],
        ]);

        $pointFallingWithinPolygon = 'POINT(59.91158561885328 10.739314523850254)';

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v4_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['1.1.1.0', '1.1.1.255', $pointFallingWithinPolygon]
            );

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v6_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860:0000:0000:0000:0000:8889', $pointFallingWithinPolygon]
            );

        $polygonToRange = $this->app->make(MaxmindPolygonToRange::class);

        // Act
        $ranges = $polygonToRange
            ->filterIpType(PolygonToRangeInterface::FILTER_IPV4)
            ->addPolygon($polygon)
            ->execute();

        // Assert
        $this->assertCount(1, $ranges);

        $expectedRanges = [
            ['1.1.1.0', '1.1.1.255'],
        ];

        foreach ($ranges as $range) {
            $this->assertContains([$range->startIp, $range->endIp], $expectedRanges);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_filters_to_ipv6_only(): void
    {
        // Arrange
        $polygon = Polygon::jsonUnserialize([
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [
                        10.725855855133034,
                        59.91762954898607,
                    ],
                    [
                        10.72253916528544,
                        59.90107982791031,
                    ],
                    [
                        10.765212198793307,
                        59.900046186238825,
                    ],
                    [
                        10.765859605341802,
                        59.91777820027971,
                    ],
                    [
                        10.725855855133034,
                        59.91762954898607,
                    ],
                ],
            ],
        ]);

        $pointFallingWithinPolygon = 'POINT(59.91158561885328 10.739314523850254)';

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v4_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                ['1.1.1.0', '1.1.1.255', $pointFallingWithinPolygon]
            );

        $this->app->make(DatabaseManager::class)
            ->statement(
                'INSERT INTO maxmind_v6_geolocations (start_ip, end_ip, coordinates) VALUES (?, ?, ST_GeomFromText(?))',
                [
                    '2001:4860:4860:0000:0000:0000:0000:8888',
                    '2001:4860:4860:0000:0000:0000:0000:8889',
                    $pointFallingWithinPolygon,
                ]
            );

        $polygonToRange = $this->app->make(MaxmindPolygonToRange::class);

        // Act
        $ranges = $polygonToRange
            ->filterIpType(PolygonToRangeInterface::FILTER_IPV6)
            ->addPolygon($polygon)
            ->execute();

        // Assert
        $this->assertCount(1, $ranges);

        $expectedRanges = [
            ['2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860:0000:0000:0000:0000:8889'],
        ];

        foreach ($ranges as $range) {
            $this->assertContains([$range->startIp, $range->endIp], $expectedRanges);
        }
    }
}

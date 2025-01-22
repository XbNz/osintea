<?php

declare(strict_types=1);

namespace XbNz\Location\Tests\Feature\Livewire;

use GeoJson\Geometry\Polygon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Ip\Models\IpAddress;
use XbNz\Location\Contracts\PolygonToRangeInterface;
use XbNz\Location\Enums\Provider;
use XbNz\Location\Fakes\PolygonToRangeFake;
use XbNz\Location\Livewire\LocationToRange;
use XbNz\Location\ValueObjects\IpRange;
use XbNz\Shared\Enums\IpType;
use XbNz\Shared\ValueObjects\Coordinates;

final class LocationToRangeTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_a_geojson_object_and_finds_ip_addresses_within_its_polygon(): void
    {
        // Arrange
        $this->app->singleton(PolygonToRangeFake::class, fn () => new PolygonToRangeFake());
        $this->app->tag([PolygonToRangeFake::class], 'polygon-to-range');
        $fake = $this->app->make(PolygonToRangeFake::class);

        $fake->forceRangeReturn(Collection::make([
            new IpRange('1.1.1.0', '1.1.1.255', new Coordinates(1.1, 1.1), IpType::IPv4),
        ]));

        $coordinates = [
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
        ];

        $featureCollections = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [],
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [
                            $coordinates,
                        ],
                    ],
                ],
            ],
        ];

        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(LocationToRange::class)
            ->set('selectedProvider', 'Fake')
            ->call('addPolygon', $featureCollections)
            ->call('addToMyIpAddresses');

        // Assert
        $fake->assertProvider(Provider::Fake);
        $fake->assertExecuteCount(1);
        $fake->assertPolygon(
            fn (Polygon $polygon) => Arr::flatten($polygon->getCoordinates(), 1) === $coordinates
        );

        $response->assertSee('1.1.1.0 - 1.1.1.255');

        $this->assertDatabaseCount(IpAddress::class, 256);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_retrieves_ipv4_only(): void
    {
        // Arrange
        $this->app->singleton(PolygonToRangeFake::class, fn () => new PolygonToRangeFake());
        $this->app->tag([PolygonToRangeFake::class], 'polygon-to-range');
        $fake = $this->app->make(PolygonToRangeFake::class);

        $fake->forceRangeReturn(Collection::make([
            new IpRange('1.1.1.0', '1.1.1.255', new Coordinates(1.1, 1.1), IpType::IPv4),
        ]));

        $featureCollections = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [],
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [
                        ],
                    ],
                ],
            ],
        ];

        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(LocationToRange::class)
            ->set('selectedProvider', 'Fake')
            ->call('addPolygon', $featureCollections);

        // Assert
        $fake->assertExecuteCount(1);
        $fake->assertFilterIpType(PolygonToRangeInterface::FILTER_IPV4);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_retrieves_ipv6_only(): void
    {
        // Arrange
        $this->app->singleton(PolygonToRangeFake::class, fn () => new PolygonToRangeFake());
        $this->app->tag([PolygonToRangeFake::class], 'polygon-to-range');
        $fake = $this->app->make(PolygonToRangeFake::class);

        $featureCollections = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [],
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [
                        ],
                    ],
                ],
            ],
        ];

        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(LocationToRange::class)
            ->set('selectedProvider', 'Fake')
            ->set('ipTypeMask', PolygonToRangeInterface::FILTER_IPV6)
            ->call('addPolygon', $featureCollections);

        // Assert
        $fake->assertExecuteCount(1);
        $fake->assertFilterIpType(PolygonToRangeInterface::FILTER_IPV6);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_retrieves_both(): void
    {
        // Arrange
        $this->app->singleton(PolygonToRangeFake::class, fn () => new PolygonToRangeFake());
        $this->app->tag([PolygonToRangeFake::class], 'polygon-to-range');
        $fake = $this->app->make(PolygonToRangeFake::class);

        $featureCollections = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [],
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [
                        ],
                    ],
                ],
            ],
        ];

        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(LocationToRange::class)
            ->set('selectedProvider', 'Fake')
            ->set('ipTypeMask', PolygonToRangeInterface::FILTER_IPV4 | PolygonToRangeInterface::FILTER_IPV6)
            ->call('addPolygon', $featureCollections);

        // Assert
        $fake->assertExecuteCount(1);
        $fake->assertFilterIpType(PolygonToRangeInterface::FILTER_IPV4 | PolygonToRangeInterface::FILTER_IPV6);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_limits_results_to_sample_size(): void
    {
        // Arrange
        $this->app->singleton(PolygonToRangeFake::class, fn () => new PolygonToRangeFake());

        $this->app->tag([PolygonToRangeFake::class], 'polygon-to-range');

        $fake = $this->app->make(PolygonToRangeFake::class);

        $fake->forceRangeReturn(Collection::make([
            new IpRange('1.1.1.0', '1.1.1.255', new Coordinates(1.1, 1.1), IpType::IPv4),
        ]));

        $featureCollections = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [],
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [
                        ],
                    ],
                ],
            ],
        ];

        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(LocationToRange::class)
            ->set('selectedProvider', 'Fake')
            ->set('sampleSizeTotal', 10)
            ->call('addPolygon', $featureCollections)
            ->call('addToMyIpAddresses');

        // Assert
        $this->assertDatabaseCount(IpAddress::class, 10);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Location\Fakes;

use Closure;
use GeoJson\Geometry\Polygon;
use Illuminate\Support\Collection;
use XbNz\Location\Contracts\PolygonToRangeInterface;
use XbNz\Location\Enums\Provider;
use XbNz\Location\ValueObjects\IpRange;
use PHPUnit\Framework\Assert as PHPUnit;

class PolygonToRangeFake implements PolygonToRangeInterface
{
    public private(set) int $executeCount = 0;

    /**
     * @var array<int, Provider>
     */
    public private(set) array $providers = [];

    /**
     * @var Collection<int, IpRange>
     */
    public private(set) Collection $forceRangeReturn;

    /**
     * @var array<int, Polygon>
     */
    public private(set) array $polygons = [];

    /**
     * @var array<int, int>
     */
    public private(set) array $filterIpTypes = [];

    public function filterIpType(int $filterMask): PolygonToRangeInterface
    {
        $this->filterIpTypes[] = $filterMask;

        return $this;
    }

    public function addPolygon(Polygon $polygon): PolygonToRangeInterface
    {
        $this->polygons[] = $polygon;

        return $this;
    }

    public function execute(): Collection
    {
        $this->executeCount++;

        return $this->forceRangeReturn ?? Collection::make();
    }

    public function supports(Provider $provider): bool
    {
        $this->providers[] = $provider;

        return $provider === Provider::Fake;
    }

    /**
     * @param  Collection<int, IpRange>  $ranges
     */
    public function forceRangeReturn(Collection $ranges): PolygonToRangeFake
    {
        $this->forceRangeReturn = $ranges;

        return $this;
    }

    public function assertExecuteCount(int $expected): void
    {
        PHPUnit::assertSame($expected, $this->executeCount);
    }

    public function assertProvider(Provider $expected): void
    {
        PHPUnit::assertContains($expected, $this->providers);
    }

    public function assertFilterIpType(int $expected): void
    {
        PHPUnit::assertContains($expected, $this->filterIpTypes);
    }

    public function assertPolygon(Closure $closure): void
    {
        $hit = empty(
            array_filter(
                $this->polygons,
                fn (Polygon $polygon) => $closure($polygon) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }
}

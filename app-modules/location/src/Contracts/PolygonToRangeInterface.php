<?php

declare(strict_types=1);

namespace XbNz\Location\Contracts;

use GeoJson\Geometry\Polygon;
use Illuminate\Support\Collection;
use XbNz\Location\Enums\Provider;
use XbNz\Location\ValueObjects\IpRange;

interface PolygonToRangeInterface
{
    const int FILTER_IPV4 = 0x01;

    const int FILTER_IPV6 = 0x02;

    public function filterIpType(int $filterMask): self;

    public function addPolygon(Polygon $polygon): self;

    /**
     * @return Collection<int, IpRange>
     */
    public function execute(): Collection;

    public function supports(Provider $provider): bool;
}

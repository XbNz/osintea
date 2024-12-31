<?php

declare(strict_types=1);

namespace XbNz\Location\Steps\BulkGeolocate;

use Illuminate\Support\Collection;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Location\Enums\Provider;

final class Transporter
{
    /**
     * @param  Collection<int, IpAddressDto>  $ipAddressDtos
     */
    public function __construct(
        public readonly Collection $ipAddressDtos,
        public readonly Provider $provider,
        public int $completedCount = 0,
    ) {}
}

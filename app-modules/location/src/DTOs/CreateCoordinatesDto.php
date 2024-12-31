<?php

declare(strict_types=1);

namespace XbNz\Location\DTOs;

use Spatie\LaravelData\Data;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Shared\ValueObjects\Coordinates;

final class CreateCoordinatesDto extends Data
{
    public function __construct(
        public readonly IpAddressDto $ipAddressDto,
        public readonly Coordinates $coordinates,
    ) {}
}

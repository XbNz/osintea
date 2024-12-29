<?php

declare(strict_types=1);

namespace XbNz\Location\Contracts;

use XbNz\Shared\ValueObjects\Coordinates;

interface IpToCoordinatesInterface
{
    public function execute(string $ip): ?Coordinates;
}

<?php

declare(strict_types=1);

namespace XbNz\Location\ValueObjects;

use XbNz\Shared\Enums\IpType;
use XbNz\Shared\IpValidator;
use XbNz\Shared\ValueObjects\Coordinates;

final class IpRange
{
    public function __construct(
        public readonly string $startIp,
        public readonly string $endIp,
        public readonly Coordinates $coordinates,
        public IpType $ipType,
    ) {
        IpValidator::make($startIp)->assertValid();
        IpValidator::make($endIp)->assertValid();
    }
}

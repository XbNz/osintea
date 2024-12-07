<?php

declare(strict_types=1);

namespace XbNz\Asn\ValueObject;

use XbNz\Shared\IpValidator;
use XbNz\Shared\ValueObjects\IpType;

final class IpRange
{
    public function __construct(
        public readonly string $startIp,
        public readonly string $endIp,
        public readonly Asn $asn,
        public IpType $ipType,
    ) {
        IpValidator::make($startIp)->assertValid();
        IpValidator::make($endIp)->assertValid();
    }
}

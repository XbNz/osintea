<?php

declare(strict_types=1);

namespace XbNz\Asn\DTOs;

use Spatie\LaravelData\Data;
use XbNz\Asn\ValueObject\Asn as AsnValueObject;
use XbNz\Ip\DTOs\IpAddressDto;

final class CreateAsnDto extends Data
{
    public function __construct(
        public readonly IpAddressDto $ip,
        public readonly AsnValueObject $asn,
    ) {}
}

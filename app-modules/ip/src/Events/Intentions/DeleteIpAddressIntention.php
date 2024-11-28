<?php

declare(strict_types=1);

namespace XbNz\Ip\Events\Intentions;

use XbNz\Ip\DTOs\IpAddressDto;

final class DeleteIpAddressIntention
{
    public function __construct(
        public readonly IpAddressDto $record,
    ) {}
}

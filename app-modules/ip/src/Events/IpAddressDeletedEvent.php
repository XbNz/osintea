<?php

declare(strict_types=1);

namespace XbNz\Ip\Events;

use XbNz\Ip\DTOs\IpAddressDto;

final class IpAddressDeletedEvent
{
    public function __construct(
        public readonly IpAddressDto $record,
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Ping\Steps\BulkPing;

use Illuminate\Support\Collection;
use XbNz\Ip\DTOs\IpAddressDto;

final class Transporter
{
    /**
     * @param  Collection<int, IpAddressDto>  $ipAddressDtos
     */
    public function __construct(
        public readonly Collection $ipAddressDtos,
        public int $completedCount = 0,
    ) {}
}

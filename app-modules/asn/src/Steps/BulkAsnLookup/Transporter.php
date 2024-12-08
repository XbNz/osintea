<?php

declare(strict_types=1);

namespace XbNz\Asn\Steps\BulkAsnLookup;

use Illuminate\Support\Collection;
use XbNz\Asn\Enums\Provider;
use XbNz\Ip\DTOs\IpAddressDto;

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

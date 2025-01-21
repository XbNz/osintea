<?php

declare(strict_types=1);

namespace XbNz\Port\DTOs;

use XbNz\Shared\Enums\IpType;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;

final class PortScanResultDto
{
    /**
     * @param  array<int, PortState>  $ports
     */
    public function __construct(
        public readonly string $ip,
        public readonly IpType $ipType,
        public readonly ProtocolType $protocol,
        public readonly array $ports
    ) {}
}

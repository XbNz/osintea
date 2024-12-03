<?php

declare(strict_types=1);

namespace XbNz\Ping\DTOs;

use XbNz\Ping\ValueObjects\Sequence;
use XbNz\Shared\ValueObjects\IpType;

final class PingResultDto
{
    /**
     * @param  array<int, Sequence>  $sequences
     */
    public function __construct(
        public readonly string $ip,
        public readonly IpType $ipType,
        public readonly array $sequences
    ) {}
}

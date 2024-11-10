<?php

declare(strict_types=1);

namespace XbNz\Fping\DTOs;

use XbNz\Shared\ValueObjects\IpType;

final class FpingResultDTO
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

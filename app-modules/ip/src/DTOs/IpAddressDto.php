<?php

declare(strict_types=1);

namespace XbNz\Ip\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use XbNz\Ip\Models\IpAddress;
use XbNz\Shared\ValueObjects\IpType;

final class IpAddressDto extends Data
{
    public function __construct(
        public readonly string $ip,
        public readonly IpType $type,
        public readonly CarbonImmutable $created_at,
    ) {}

    public static function fromModel(IpAddress $ipAddress): self
    {
        return new self(
            $ipAddress->ip,
            $ipAddress->type,
            $ipAddress->created_at,
        );
    }
}

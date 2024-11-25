<?php

declare(strict_types=1);

namespace XbNz\Ip\ViewModels;

use Spatie\LaravelData\Data;
use XbNz\Ip\Models\IpAddress;

final class ListIpAddressesViewModel extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $ip,
        public readonly int $type,
        public readonly string $created_at,
        public readonly ?string $average_rtt,
    ) {}

    public static function fromModel(IpAddress $ipAddress): self
    {
        return new self(
            $ipAddress->id,
            $ipAddress->ip,
            $ipAddress->type->value,
            $ipAddress->created_at->format('Y-m-d H:i:s'),
            $ipAddress->pingSequences->avg('round_trip_time') === null
                ? null
                : number_format($ipAddress->pingSequences->avg('round_trip_time'), 2),
        );
    }
}

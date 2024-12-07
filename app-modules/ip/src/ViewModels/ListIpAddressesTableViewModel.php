<?php

declare(strict_types=1);

namespace XbNz\Ip\ViewModels;

use Spatie\LaravelData\Data;
use XbNz\Ip\Models\IpAddress;

final class ListIpAddressesTableViewModel extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $ip,
        public readonly int $type,
        public readonly string $created_at,
        public readonly string $loss_percent,
        public readonly int $total_sequences,
        public readonly int $lost_sequences,
        public readonly ?string $average_rtt,
    ) {}

    public static function fromModel(IpAddress $ipAddress): self
    {
        $ipAddressDto = $ipAddress->getData();

        return new self(
            $ipAddressDto->id,
            $ipAddressDto->ip,
            $ipAddressDto->type->value,
            $ipAddressDto->created_at->format('Y-m-d H:i:s'),
            $ipAddressDto->ping_sequences->count() === 0
                ? '0.00'
                : number_format(
                    $ipAddressDto->ping_sequences->where('loss', true)->count()
                    /
                    $ipAddressDto->ping_sequences->count()
                    * 100,
                    2
                ),
            $ipAddressDto->ping_sequences->count(),
            $ipAddressDto->ping_sequences->where('loss', true)->count(),
            $ipAddressDto->ping_sequences->avg('round_trip_time') === null
                ? null
                : number_format($ipAddressDto->ping_sequences->avg('round_trip_time'), 2),
        );
    }
}

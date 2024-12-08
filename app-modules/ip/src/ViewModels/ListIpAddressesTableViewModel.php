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
        public readonly ?string $organization,
        public readonly ?int $as_number,
        public readonly ?string $average_rtt,
    ) {}

    public static function fromModel(IpAddress $ipAddress): self
    {
        return new self(
            $ipAddress->id,
            $ipAddress->ip,
            $ipAddress->type->value,
            $ipAddress->created_at->format('Y-m-d H:i:s'),
            $ipAddress->pingSequences->count() === 0
                ? '0.00'
                : number_format(
                    $ipAddress->pingSequences->where('loss', true)->count()
                    /
                    $ipAddress->pingSequences->count()
                    * 100,
                    2
                ),
            $ipAddress->pingSequences->count(),
            $ipAddress->pingSequences->where('loss', true)->count(),
            $ipAddress->asn?->organization,
            $ipAddress->asn?->as_number,
            $ipAddress->pingSequences->avg('round_trip_time') === null
                ? null
                : number_format($ipAddress->pingSequences->avg('round_trip_time'), 2),
        );
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Ping\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ping\Models\PingSequence;

final class PingSequenceDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly Lazy|IpAddressDto $ip,
        public readonly ?float $round_trip_time,
        public readonly bool $loss,
        public readonly CarbonImmutable $created_at,
    ) {}

    public static function fromModel(PingSequence $pingSequence): self
    {
        return new self(
            $pingSequence->id,
            Lazy::whenLoaded('ipAddress', $pingSequence, fn () => IpAddressDto::fromModel($pingSequence->ipAddress)),
            $pingSequence->round_trip_time,
            $pingSequence->loss,
            $pingSequence->created_at,
        );
    }
}

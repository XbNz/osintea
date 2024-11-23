<?php

declare(strict_types=1);

namespace XbNz\Ip\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\DTOs\PingSequenceDto;
use XbNz\Shared\ValueObjects\IpType;

final class IpAddressDto extends Data
{
    /**
     * @param  Lazy|Collection<int, PingSequenceDto>  $ping_sequences
     */
    public function __construct(
        public readonly int $id,
        public readonly string $ip,
        public readonly IpType $type,
        public readonly Lazy|Collection $ping_sequences,
        public readonly CarbonImmutable $created_at,
    ) {}

    public static function fromModel(IpAddress $ipAddress): self
    {
        return new self(
            $ipAddress->id,
            $ipAddress->ip,
            $ipAddress->type,
            Lazy::whenLoaded('pingSequences', $ipAddress, fn () => PingSequenceDto::collect($ipAddress->pingSequences, Collection::class)),
            $ipAddress->created_at,
        );
    }
}

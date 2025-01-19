<?php

declare(strict_types=1);

namespace XbNz\Ip\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Webmozart\Assert\Assert;
use XbNz\Asn\DTOs\AsnDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\DTOs\PingSequenceDto;
use XbNz\Shared\Enums\IpType;

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
        public readonly Lazy|AsnDto $asn,
        public readonly CarbonImmutable $created_at,
    ) {}

    public static function fromModel(IpAddress $ipAddress): self
    {
        Assert::notNull($ipAddress->type);

        return new self(
            $ipAddress->id,
            $ipAddress->ip,
            $ipAddress->type,
            Lazy::whenLoaded('pingSequences', $ipAddress, fn () => PingSequenceDto::collect($ipAddress->pingSequences, Collection::class)),
            Lazy::whenLoaded('asn', $ipAddress, fn () => AsnDto::fromModel($ipAddress->asn)),
            $ipAddress->created_at,
        );
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Ping\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ping\ValueObjects\Sequence;

final class CreatePingSequenceDto extends Data
{
    public function __construct(
        #[WithoutValidation]
        public readonly IpAddressDto $ip_address_dto,
        public readonly Sequence $sequence,
        public CarbonImmutable $created_at,
    ) {}
}

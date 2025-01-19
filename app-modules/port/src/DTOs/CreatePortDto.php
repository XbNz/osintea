<?php

declare(strict_types=1);

namespace XbNz\Port\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\ValueObjects\Port;

final class CreatePortDto extends Data
{
    public function __construct(
        #[WithoutValidation]
        public readonly IpAddressDto $ip_address_dto,
        public readonly Port $port,
        public readonly PortState $state,
        public readonly CarbonImmutable $created_at,
    ) {}
}

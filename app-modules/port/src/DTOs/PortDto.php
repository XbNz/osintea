<?php

declare(strict_types=1);

namespace XbNz\Port\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Port\Models\Port as PortModel;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\ValueObjects\Port;

final class PortDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly Lazy|IpAddressDto $ip,
        public readonly Port $port,
        public readonly PortState $state,
        public readonly CarbonImmutable $created_at,
    ) {}

    public static function fromModel(PortModel $port): self
    {
        return new self(
            $port->id,
            Lazy::whenLoaded('ipAddress', $port, fn () => IpAddressDto::fromModel($port->ipAddress)),
            new Port(
                $port->port,
                $port->protocol,
            ),
            $port->state,
            $port->created_at,
        );
    }
}

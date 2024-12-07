<?php

declare(strict_types=1);

namespace XbNz\Asn\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use XbNz\Asn\Model\Asn;
use XbNz\Ip\DTOs\IpAddressDto;

final class AsnDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly Lazy|IpAddressDto $ip,
        public readonly string $organization,
        public readonly int $as_number,
        public readonly CarbonImmutable $created_at,
        public readonly CarbonImmutable $updated_at,
    ) {}

    public static function fromModel(Asn $asn): self
    {
        return new self(
            $asn->id,
            Lazy::whenLoaded('ipAddress', $asn, fn () => IpAddressDto::fromModel($asn->ipAddress)),
            $asn->organization,
            $asn->as_number,
            $asn->created_at,
            $asn->updated_at,
        );
    }
}

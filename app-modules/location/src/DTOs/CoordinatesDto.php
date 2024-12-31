<?php

declare(strict_types=1);

namespace XbNz\Location\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Location\Models\Coordinates;
use XbNz\Shared\ValueObjects\Coordinates as CoordinatesValueObject;

final class CoordinatesDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly Lazy|IpAddressDto $ip,
        public readonly CoordinatesValueObject $coordinates,
        public readonly CarbonImmutable $created_at,
    ) {}

    public static function fromModel(Coordinates $coordinates): self
    {
        $coordinatesArray = Str::of($coordinates->coordinates)->after('POINT(')->before(')')
            ->explode(' ')
            ->map(fn (string $coordinate) => (float) $coordinate)
            ->toArray();

        $coordinatesValueObject = new CoordinatesValueObject(
            $coordinatesArray[1],
            $coordinatesArray[0],
        );

        return new self(
            $coordinates->id,
            Lazy::whenLoaded('ipAddress', $coordinates, fn () => IpAddressDto::fromModel($coordinates->ipAddress)),
            $coordinatesValueObject,
            $coordinates->created_at,
        );
    }
}

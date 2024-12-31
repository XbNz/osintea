<?php

declare(strict_types=1);

namespace XbNz\Location\Actions;

use Illuminate\Database\Query\Expression;
use XbNz\Location\DTOs\CoordinatesDto;
use XbNz\Location\DTOs\CreateCoordinatesDto;
use XbNz\Location\Models\Coordinates;

final class CreateCoordinatesAction
{
    public function handle(CreateCoordinatesDto $dto): CoordinatesDto
    {
        return Coordinates::query()
            ->create([
                'ip_address_id' => $dto->ipAddressDto->id,
                'coordinates' => new Expression("ST_GeomFromText('POINT({$dto->coordinates->longitude} {$dto->coordinates->latitude})')"),
            ])
            ->refresh()
            ->getData();
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Location\Actions;

use XbNz\Location\DTOs\CoordinatesDto;
use XbNz\Location\DTOs\CreateCoordinatesDto;
use XbNz\Location\Models\Coordinates;

final class CreateCoordinatesAction
{
    public function handle(CreateCoordinatesDto $dto): CoordinatesDto
    {
        Coordinates::query()
            ->getConnection()
            ->insert(
                'INSERT INTO coordinates (ip_address_id, coordinates) VALUES (?, ST_GeomFromText(?))',
                [
                    $dto->ipAddressDto->id,
                    "POINT({$dto->coordinates->longitude} {$dto->coordinates->latitude})",
                ]
            );

        return Coordinates::query()
            ->where('ip_address_id', $dto->ipAddressDto->id)
            ->firstOrFail()
            ->getData();
    }
}

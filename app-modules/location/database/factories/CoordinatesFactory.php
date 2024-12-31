<?php

declare(strict_types=1);

namespace XbNz\Location\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use XbNz\Ip\Models\IpAddress;
use XbNz\Location\Models\Coordinates;

final class CoordinatesFactory extends Factory
{
    protected $model = Coordinates::class;

    public function definition(): array
    {
        return [
            'ip_address_id' => IpAddress::factory(),
        ];
    }
}

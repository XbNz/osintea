<?php

declare(strict_types=1);

namespace XbNz\Ip\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use XbNz\Ip\Models\IpAddress;

final class IpAddressFactory extends Factory
{
    protected $model = IpAddress::class;

    public function definition(): array
    {
        return [
            'ip' => $this->faker->unique()->ipv4(),
        ];
    }
}

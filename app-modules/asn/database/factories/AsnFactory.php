<?php

declare(strict_types=1);

namespace XbNz\Asn\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use XbNz\Asn\Model\Asn;
use XbNz\Ip\Models\IpAddress;

final class AsnFactory extends Factory
{
    protected $model = Asn::class;

    public function definition(): array
    {
        return [
            'ip_address_id' => IpAddress::factory(),
            'organization' => $this->faker->word(),
            'as_number' => $this->faker->randomNumber(),
        ];
    }
}

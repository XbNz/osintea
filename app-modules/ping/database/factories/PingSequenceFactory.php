<?php

declare(strict_types=1);

namespace XbNz\Ping\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Models\PingSequence;

final class PingSequenceFactory extends Factory
{
    protected $model = PingSequence::class;

    public function definition(): array
    {
        return [
            'ip_address_id' => IpAddress::factory(),
            'round_trip_time' => $this->faker->randomFloat(min: 1, max: 100),
            'loss' => $this->faker->boolean(),
            'created_at' => (int) microtime(true),
        ];
    }
}

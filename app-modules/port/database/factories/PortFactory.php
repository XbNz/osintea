<?php

declare(strict_types=1);

namespace XbNz\Port\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use XbNz\Ip\Models\IpAddress;
use XbNz\Port\Models\Port;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;

final class PortFactory extends Factory
{
    protected $model = Port::class;

    public function definition(): array
    {
        return [
            'ip_address_id' => IpAddress::factory(),
            'protocol' => $this->faker->randomElement(array_column(ProtocolType::cases(), 'value')),
            'port' => $this->faker->numberBetween(0, 65535),
            'state' => $this->faker->randomElement(array_column(PortState::cases(), 'value')),
            'created_at' => (int) microtime(true),
        ];
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use XbNz\Preferences\Models\FpingPreferences;

final class FpingPreferencesFactory extends Factory
{
    protected $model = FpingPreferences::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'size' => 56,
            'backoff' => 1.5,
            'count' => 1,
            'ttl' => 64,
            'interval' => 10,
            'interval_per_target' => 1000,
            'type_of_service' => '0x00',
            'retries' => 0,
            'timeout' => 500,
            'dont_fragment' => false,
            'send_random_data' => false,
            'enabled' => true,
        ];
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use XbNz\Preferences\Models\MasscanPreferences;

final class MasscanPreferencesFactory extends Factory
{
    protected $model = MasscanPreferences::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'rate' => 1000,
            'retries' => 0,
            'ttl' => 64,
            'enabled' => $this->faker->boolean(),
        ];
    }
}

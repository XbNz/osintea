<?php

declare(strict_types=1);

namespace App\Steps\OnBooted;

use Illuminate\Support\Str;
use XbNz\Fping\Actions\CreateFpingPreferencesAction;
use XbNz\Fping\DTOs\CreateFpingPreferencesDto;
use XbNz\Fping\Models\FpingPreferences;

final class CreateDefaultFpingPreferences
{
    public function __construct(
        private readonly CreateFpingPreferencesAction $createFpingPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        if (FpingPreferences::query()->exists() === true) {
            return $transporter;
        }

        $transporter->defaultFpingPreferences = $this->createFpingPreferencesAction->handle(
            new CreateFpingPreferencesDto(
                Str::random(5),
                56,
                1.5,
                1,
                64,
                100,
                500,
                '0x00',
                0,
                500,
                false,
                false
            )
        );

        return $transporter;
    }
}

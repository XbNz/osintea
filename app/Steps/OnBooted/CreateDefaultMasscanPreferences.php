<?php

declare(strict_types=1);

namespace App\Steps\OnBooted;

use Illuminate\Support\Str;
use XbNz\Preferences\Actions\CreateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;
use XbNz\Preferences\Models\MasscanPreferences;

final class CreateDefaultMasscanPreferences
{
    public function __construct(
        private readonly CreateMasscanPreferencesAction $createMasscanPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        if (MasscanPreferences::query()->exists() === true) {
            return $transporter;
        }

        $transporter->defaultMasscanPreferences = $this->createMasscanPreferencesAction->handle(
            new CreateMasscanPreferencesDto(
                Str::random(5),
                55,
                10000,
                null,
                0,
            )
        );

        return $transporter;
    }
}

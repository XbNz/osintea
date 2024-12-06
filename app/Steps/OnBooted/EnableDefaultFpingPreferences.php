<?php

declare(strict_types=1);

namespace App\Steps\OnBooted;

use XbNz\Fping\Actions\UpdateFpingPreferencesAction;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;

final class EnableDefaultFpingPreferences
{
    public function __construct(
        private readonly UpdateFpingPreferencesAction $updateFpingPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->defaultFpingPreferences === null) {
            return $transporter;
        }

        $this->updateFpingPreferencesAction->handle(
            new UpdateFpingPreferencesDto(
                $transporter->defaultFpingPreferences->id,
                enabled: true
            )
        );

        return $transporter;
    }
}

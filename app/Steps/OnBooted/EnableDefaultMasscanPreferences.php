<?php

declare(strict_types=1);

namespace App\Steps\OnBooted;

use XbNz\Preferences\Actions\UpdateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;

final class EnableDefaultMasscanPreferences
{
    public function __construct(
        private readonly UpdateMasscanPreferencesAction $updateMasscanPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->defaultMasscanPreferences === null) {
            return $transporter;
        }

        $this->updateMasscanPreferencesAction->handle(
            new UpdateMasscanPreferencesDto(
                $transporter->defaultMasscanPreferences->id,
                enabled: true
            )
        );

        return $transporter;
    }
}

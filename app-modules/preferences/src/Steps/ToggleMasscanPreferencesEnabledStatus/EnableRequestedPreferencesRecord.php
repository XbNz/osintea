<?php

declare(strict_types=1);

namespace XbNz\Preferences\Steps\ToggleMasscanPreferencesEnabledStatus;

use XbNz\Preferences\Actions\UpdateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;

final class EnableRequestedPreferencesRecord
{
    public function __construct(
        private readonly UpdateMasscanPreferencesAction $updateMasscanPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $this->updateMasscanPreferencesAction->handle(
            new UpdateMasscanPreferencesDto(
                $transporter->record->id,
                enabled: true
            )
        );

        return $transporter;
    }
}

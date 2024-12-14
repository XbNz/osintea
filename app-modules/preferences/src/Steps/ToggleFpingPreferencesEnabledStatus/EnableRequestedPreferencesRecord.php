<?php

declare(strict_types=1);

namespace XbNz\Preferences\Steps\ToggleFpingPreferencesEnabledStatus;

use XbNz\Preferences\Actions\UpdateFpingPreferencesAction;
use XbNz\Preferences\DTOs\UpdateFpingPreferencesDto;

final class EnableRequestedPreferencesRecord
{
    public function __construct(
        private readonly UpdateFpingPreferencesAction $updateFpingPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $this->updateFpingPreferencesAction->handle(
            new UpdateFpingPreferencesDto(
                $transporter->record->id,
                enabled: true
            )
        );

        return $transporter;
    }
}

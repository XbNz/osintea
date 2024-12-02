<?php

declare(strict_types=1);

namespace XbNz\Fping\Steps\ToggleFpingPreferencesEnabledStatus;

use XbNz\Fping\Actions\UpdateFpingPreferencesAction;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;

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

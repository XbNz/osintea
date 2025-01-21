<?php

declare(strict_types=1);

namespace XbNz\Preferences\Steps\ToggleMasscanPreferencesEnabledStatus;

use XbNz\Preferences\Actions\UpdateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;
use XbNz\Preferences\Models\MasscanPreferences;

final class DisableAllPreferences
{
    public function __construct(
        private readonly UpdateMasscanPreferencesAction $updateMasscanPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        MasscanPreferencesDto::collect(MasscanPreferences::query()->get())
            ->each(function (MasscanPreferencesDto $preferences): void {
                $this->updateMasscanPreferencesAction->handle(
                    new UpdateMasscanPreferencesDto(
                        $preferences->id,
                        enabled: false
                    )
                );
            });

        return $transporter;
    }
}

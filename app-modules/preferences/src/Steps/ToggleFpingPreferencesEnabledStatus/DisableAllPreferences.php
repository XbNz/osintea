<?php

declare(strict_types=1);

namespace XbNz\Preferences\Steps\ToggleFpingPreferencesEnabledStatus;

use XbNz\Preferences\Steps\ToggleFpingPreferencesEnabledStatus\Transporter;
use XbNz\Preferences\Actions\UpdateFpingPreferencesAction;
use XbNz\Preferences\DTOs\FpingPreferencesDto;
use XbNz\Preferences\DTOs\UpdateFpingPreferencesDto;
use XbNz\Preferences\Models\FpingPreferences;

final class DisableAllPreferences
{
    public function __construct(
        private readonly UpdateFpingPreferencesAction $updateFpingPreferencesAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        FpingPreferencesDto::collect(FpingPreferences::query()->get())
            ->each(function (FpingPreferencesDto $preferences): void {
                $this->updateFpingPreferencesAction->handle(
                    new UpdateFpingPreferencesDto(
                        $preferences->id,
                        enabled: false
                    )
                );
            });

        return $transporter;
    }
}

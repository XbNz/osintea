<?php

declare(strict_types=1);

namespace XbNz\Fping\Steps\ToggleFpingPreferencesEnabledStatus;

use XbNz\Fping\Actions\UpdateFpingPreferencesAction;
use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;
use XbNz\Fping\Models\FpingPreferences;

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

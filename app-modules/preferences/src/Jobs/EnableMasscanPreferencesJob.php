<?php

declare(strict_types=1);

namespace XbNz\Preferences\Jobs;

use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Preferences\Actions\UpdateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\Steps\ToggleMasscanPreferencesEnabledStatus\DisableAllPreferences;
use XbNz\Preferences\Steps\ToggleMasscanPreferencesEnabledStatus\EnableRequestedPreferencesRecord;
use XbNz\Preferences\Steps\ToggleMasscanPreferencesEnabledStatus\Transporter;

final class EnableMasscanPreferencesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly MasscanPreferencesDto $record
    ) {}

    public function handle(UpdateMasscanPreferencesAction $updateMasscanPreferencesAction): void
    {
        $pipes = [
            DisableAllPreferences::class,
            EnableRequestedPreferencesRecord::class,
        ];

        Pipeline::make()
            ->withTransaction()
            ->send(new Transporter($this->record))
            ->through($pipes)
            ->thenReturn();
    }
}

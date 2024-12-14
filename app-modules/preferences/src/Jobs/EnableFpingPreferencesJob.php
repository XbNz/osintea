<?php

declare(strict_types=1);

namespace XbNz\Preferences\Jobs;

use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Preferences\Actions\UpdateFpingPreferencesAction;
use XbNz\Preferences\DTOs\FpingPreferencesDto;
use XbNz\Preferences\Steps\ToggleFpingPreferencesEnabledStatus\DisableAllPreferences;
use XbNz\Preferences\Steps\ToggleFpingPreferencesEnabledStatus\EnableRequestedPreferencesRecord;
use XbNz\Preferences\Steps\ToggleFpingPreferencesEnabledStatus\Transporter;

final class EnableFpingPreferencesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly FpingPreferencesDto $record
    ) {}

    public function handle(UpdateFpingPreferencesAction $updateFpingPreferencesAction): void
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

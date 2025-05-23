<?php

declare(strict_types=1);

namespace XbNz\Preferences\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Preferences\Actions\UpdateFpingPreferencesAction;
use XbNz\Preferences\DTOs\UpdateFpingPreferencesDto;

final class UpdateFpingPreferencesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly UpdateFpingPreferencesDto $dto
    ) {}

    public function handle(UpdateFpingPreferencesAction $updateFpingPreferencesAction): void
    {
        $updateFpingPreferencesAction->handle($this->dto);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Preferences\Actions\UpdateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;

final class UpdateMasscanPreferencesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly UpdateMasscanPreferencesDto $dto
    ) {}

    public function handle(UpdateMasscanPreferencesAction $updateMasscanPreferencesAction): void
    {
        $updateMasscanPreferencesAction->handle($this->dto);
    }
}

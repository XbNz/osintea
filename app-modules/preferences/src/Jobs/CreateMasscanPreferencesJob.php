<?php

declare(strict_types=1);

namespace XbNz\Preferences\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Preferences\Actions\CreateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;

final class CreateMasscanPreferencesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly CreateMasscanPreferencesDto $dto
    ) {}

    public function handle(CreateMasscanPreferencesAction $createMasscanPreferencesAction): void
    {
        $createMasscanPreferencesAction->handle($this->dto);
    }
}

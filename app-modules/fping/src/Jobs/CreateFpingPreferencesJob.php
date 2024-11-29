<?php

declare(strict_types=1);

namespace XbNz\Fping\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Fping\Actions\CreateFpingPreferencesAction;
use XbNz\Fping\DTOs\CreateFpingPreferencesDto;

final class CreateFpingPreferencesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly CreateFpingPreferencesDto $dto
    ) {}

    public function handle(CreateFpingPreferencesAction $createFpingPreferencesAction): void
    {
        $createFpingPreferencesAction->handle($this->dto);
    }
}

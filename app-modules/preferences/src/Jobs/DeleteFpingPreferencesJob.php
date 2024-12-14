<?php

declare(strict_types=1);

namespace XbNz\Preferences\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Preferences\Actions\DeleteFpingPreferencesAction;
use XbNz\Preferences\DTOs\FpingPreferencesDto;

final class DeleteFpingPreferencesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly FpingPreferencesDto $record
    ) {}

    public function handle(DeleteFpingPreferencesAction $deleteFpingPreferencesAction): void
    {
        $deleteFpingPreferencesAction->handle($this->record);
    }
}

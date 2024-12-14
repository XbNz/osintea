<?php

declare(strict_types=1);

namespace XbNz\Preferences\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Preferences\DTOs\FpingPreferencesDto;
use XbNz\Preferences\Events\FpingPreferencesDeletedEvent;
use XbNz\Preferences\Models\FpingPreferences;

final class DeleteFpingPreferencesAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function handle(FpingPreferencesDto $record): FpingPreferencesDto
    {
        FpingPreferences::query()->findOrFail($record->id)->delete();

        $this->dispatcher->dispatch(new FpingPreferencesDeletedEvent($record));

        return $record;
    }
}

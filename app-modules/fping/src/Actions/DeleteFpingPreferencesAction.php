<?php

declare(strict_types=1);

namespace XbNz\Fping\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\Events\FpingPreferencesDeletedEvent;
use XbNz\Fping\Models\FpingPreferences;

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

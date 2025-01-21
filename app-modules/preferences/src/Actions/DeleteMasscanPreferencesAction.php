<?php

declare(strict_types=1);

namespace XbNz\Preferences\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\Events\MasscanPreferencesDeletedEvent;
use XbNz\Preferences\Models\MasscanPreferences;

final class DeleteMasscanPreferencesAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function handle(MasscanPreferencesDto $record): MasscanPreferencesDto
    {
        MasscanPreferences::query()->findOrFail($record->id)->delete();

        $this->dispatcher->dispatch(new MasscanPreferencesDeletedEvent($record));

        return $record;
    }
}

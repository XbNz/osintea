<?php

declare(strict_types=1);

namespace XbNz\Preferences\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use XbNz\Preferences\Jobs\DatabaseUpdaterJob;
use XbNz\Shared\Enums\UpdatableDatabase;

final class DatabasePreferences extends Component
{
    public function updateDatabase(string $database): void
    {
        DatabaseUpdaterJob::dispatch(UpdatableDatabase::from($database));
    }

    public function render(): View
    {
        return view('preferences::livewire.database-preferences', [
            'updatableDatabases' => Collection::make(UpdatableDatabase::cases())
                ->filter(fn (UpdatableDatabase $database) => $database->canBeUsedInProduction())
                ->mapWithKeys(fn (UpdatableDatabase $database) => [$database->value => $database->friendlyName()]),
        ]);
    }
}

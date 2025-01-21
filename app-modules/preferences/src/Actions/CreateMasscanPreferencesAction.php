<?php

declare(strict_types=1);

namespace XbNz\Preferences\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\Events\MasscanPreferencesInsertedEvent;
use XbNz\Preferences\Models\MasscanPreferences;

final class CreateMasscanPreferencesAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function handle(CreateMasscanPreferencesDto $dto): MasscanPreferencesDto
    {
        $masscanPreferences = MasscanPreferences::query()
            ->create([
                'name' => $dto->name,
                'ttl' => $dto->ttl,
                'rate' => $dto->rate,
                'adapter' => $dto->adapter,
                'retries' => $dto->retries,
            ])->refresh()->getData();

        $this->dispatcher->dispatch(new MasscanPreferencesInsertedEvent($masscanPreferences));

        return $masscanPreferences;
    }
}

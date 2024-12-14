<?php

declare(strict_types=1);

namespace XbNz\Preferences\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Preferences\DTOs\CreateFpingPreferencesDto;
use XbNz\Preferences\DTOs\FpingPreferencesDto;
use XbNz\Preferences\Events\FpingPreferencesInsertedEvent;
use XbNz\Preferences\Models\FpingPreferences;

final class CreateFpingPreferencesAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function handle(CreateFpingPreferencesDto $dto): FpingPreferencesDto
    {
        $fpingPreferences = FpingPreferences::query()
            ->create([
                'name' => $dto->name,
                'size' => $dto->size,
                'backoff' => $dto->backoff,
                'count' => $dto->count,
                'ttl' => $dto->ttl,
                'interval' => $dto->interval,
                'interval_per_target' => $dto->interval_per_target,
                'type_of_service' => $dto->type_of_service,
                'retries' => $dto->retries,
                'timeout' => $dto->timeout,
                'dont_fragment' => $dto->dont_fragment,
                'send_random_data' => $dto->send_random_data,
            ])->refresh()->getData();

        $this->dispatcher->dispatch(new FpingPreferencesInsertedEvent($fpingPreferences));

        return $fpingPreferences;
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\Actions;

use XbNz\Fping\DTOs\CreateFpingPreferencesDto;
use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\Models\FpingPreferences;

final class CreateFpingPreferencesAction
{
    public function handle(CreateFpingPreferencesDto $dto): FpingPreferencesDto
    {
        return FpingPreferences::query()
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
            ])->fresh()->getData();
    }
}

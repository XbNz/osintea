<?php

declare(strict_types=1);

namespace XbNz\Fping\Actions;

use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;
use XbNz\Fping\Models\FpingPreferences;

final class UpdateFpingPreferencesAction
{
    public function handle(UpdateFpingPreferencesDto $dto): FpingPreferencesDto
    {
        $fpingPreferences = FpingPreferences::query()->findOrFail($dto->id);
        $beforeUpdate = $fpingPreferences->getData();

        $fpingPreferences->update($updated = array_filter([
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
            'enabled' => $dto->enabled,
        ], fn (mixed $value) => $value !== null));

        if (empty($updated) === true) {
            return $beforeUpdate;
        }

        return $fpingPreferences->fresh()->getData();
    }
}

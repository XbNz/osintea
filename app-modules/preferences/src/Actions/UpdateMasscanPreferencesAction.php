<?php

declare(strict_types=1);

namespace XbNz\Preferences\Actions;

use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;
use XbNz\Preferences\Models\MasscanPreferences;

final class UpdateMasscanPreferencesAction
{
    public function handle(UpdateMasscanPreferencesDto $dto): MasscanPreferencesDto
    {
        $masscanPreferences = MasscanPreferences::query()->findOrFail($dto->id);
        $beforeUpdate = $masscanPreferences->getData();

        $masscanPreferences->update($updated = array_filter([
            'name' => $dto->name,
            'ttl' => $dto->ttl,
            'rate' => $dto->rate,
            'adapter' => $dto->adapter,
            'retries' => $dto->retries,
            'enabled' => $dto->enabled,
        ], fn (mixed $value) => $value !== null));

        if (empty($updated) === true) {
            return $beforeUpdate;
        }

        return $masscanPreferences->refresh()->getData();
    }
}

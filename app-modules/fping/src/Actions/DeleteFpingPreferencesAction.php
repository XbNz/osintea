<?php

declare(strict_types=1);

namespace XbNz\Fping\Actions;

use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\Models\FpingPreferences;

final class DeleteFpingPreferencesAction
{
    public function handle(FpingPreferencesDto $record): FpingPreferencesDto
    {
        FpingPreferences::query()->findOrFail($record->id)->delete();

        return $record;
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\Events\Intentions;

use XbNz\Fping\DTOs\FpingPreferencesDto;

final class DeleteFpingPreferencesIntention
{
    public function __construct(
        public readonly FpingPreferencesDto $record
    ) {}
}

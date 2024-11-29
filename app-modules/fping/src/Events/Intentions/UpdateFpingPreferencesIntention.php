<?php

declare(strict_types=1);

namespace XbNz\Fping\Events\Intentions;

use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;

final class UpdateFpingPreferencesIntention
{
    public function __construct(
        public readonly UpdateFpingPreferencesDto $dto
    ) {}
}

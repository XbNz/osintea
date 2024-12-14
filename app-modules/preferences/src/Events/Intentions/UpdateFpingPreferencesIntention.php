<?php

declare(strict_types=1);

namespace XbNz\Preferences\Events\Intentions;

use XbNz\Preferences\DTOs\UpdateFpingPreferencesDto;

final class UpdateFpingPreferencesIntention
{
    public function __construct(
        public readonly UpdateFpingPreferencesDto $dto
    ) {}
}

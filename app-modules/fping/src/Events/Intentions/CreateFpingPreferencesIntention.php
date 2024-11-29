<?php

declare(strict_types=1);

namespace XbNz\Fping\Events\Intentions;

use XbNz\Fping\DTOs\CreateFpingPreferencesDto;

final class CreateFpingPreferencesIntention
{
    public function __construct(
        public readonly CreateFpingPreferencesDto $dto
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\Events\Intentions;

use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;

final class CreateMasscanPreferencesIntention
{
    public function __construct(
        public readonly CreateMasscanPreferencesDto $dto
    ) {}
}

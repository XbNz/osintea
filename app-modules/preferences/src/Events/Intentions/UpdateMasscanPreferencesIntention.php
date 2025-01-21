<?php

declare(strict_types=1);

namespace XbNz\Preferences\Events\Intentions;

use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;

final class UpdateMasscanPreferencesIntention
{
    public function __construct(
        public readonly UpdateMasscanPreferencesDto $dto
    ) {}
}

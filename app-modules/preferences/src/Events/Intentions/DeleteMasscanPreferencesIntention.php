<?php

declare(strict_types=1);

namespace XbNz\Preferences\Events\Intentions;

use XbNz\Preferences\DTOs\MasscanPreferencesDto;

final class DeleteMasscanPreferencesIntention
{
    public function __construct(
        public readonly MasscanPreferencesDto $record
    ) {}
}

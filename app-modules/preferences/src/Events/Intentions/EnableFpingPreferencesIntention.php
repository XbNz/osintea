<?php

declare(strict_types=1);

namespace XbNz\Preferences\Events\Intentions;

use XbNz\Preferences\DTOs\FpingPreferencesDto;

final class EnableFpingPreferencesIntention
{
    public function __construct(
        public readonly FpingPreferencesDto $record
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Steps\OnBooted;

use XbNz\Preferences\DTOs\FpingPreferencesDto;

final class Transporter
{
    public function __construct(
        public ?FpingPreferencesDto $defaultFpingPreferences = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\Steps\ToggleFpingPreferencesEnabledStatus;

use XbNz\Preferences\DTOs\FpingPreferencesDto;

final class Transporter
{
    public function __construct(
        public readonly FpingPreferencesDto $record,
    ) {}
}

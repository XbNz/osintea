<?php

declare(strict_types=1);

namespace XbNz\Fping\Steps\ToggleFpingPreferencesEnabledStatus;

use XbNz\Fping\DTOs\FpingPreferencesDto;

final class Transporter
{
    public function __construct(
        public readonly FpingPreferencesDto $record,
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\Steps\ToggleMasscanPreferencesEnabledStatus;

use XbNz\Preferences\DTOs\MasscanPreferencesDto;

final class Transporter
{
    public function __construct(
        public readonly MasscanPreferencesDto $record,
    ) {}
}

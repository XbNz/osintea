<?php

declare(strict_types=1);

namespace XbNz\Location\Enums;

enum Provider: string
{
    case Maxmind = 'Maxmind';
    case Fake = 'Fake';

    public function canBeUsedInProduction(): bool
    {
        return $this !== self::Fake;
    }
}

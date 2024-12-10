<?php

declare(strict_types=1);

namespace XbNz\Asn\Enums;

enum Provider: string
{
    case RouteViews = 'RouteViews';
    case Fake = 'Fake';

    public function canBeUsedInProduction(): bool
    {
        return $this !== self::Fake;
    }
}

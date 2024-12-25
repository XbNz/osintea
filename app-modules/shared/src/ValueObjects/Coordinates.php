<?php

declare(strict_types=1);

namespace XbNz\Shared\ValueObjects;

use Webmozart\Assert\Assert;

final class Coordinates
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
    ) {
        Assert::range($latitude, -90, 90);
        Assert::range($longitude, -180, 180);
    }
}

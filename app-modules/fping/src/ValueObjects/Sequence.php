<?php

declare(strict_types=1);

namespace XbNz\Fping\ValueObjects;

final class Sequence
{
    public function __construct(
        public readonly int $sequence,
        public readonly bool $lost,
        public readonly ?float $roundTripTime,
    ) {}
}

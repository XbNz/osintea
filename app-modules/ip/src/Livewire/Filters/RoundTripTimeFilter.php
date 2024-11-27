<?php

declare(strict_types=1);

namespace XbNz\Ip\Livewire\Filters;

use Illuminate\Support\Collection;
use Livewire\Wireable;

final class RoundTripTimeFilter implements Wireable
{
    public function __construct(
        public ?int $minFloor = null,
        public ?int $maxFloor = null,
        public ?int $minAverage = null,
        public ?int $maxAverage = null,
        public ?int $minCeiling = null,
        public ?int $maxCeiling = null,
    ) {}

    public function canBeApplied(): bool
    {
        return Collection::make($this)
            ->filter(fn (?int $value) => $value !== null)
            ->isNotEmpty();
    }

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value): self
    {
        return new self(...$value);
    }
}

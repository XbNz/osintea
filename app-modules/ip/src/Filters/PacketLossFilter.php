<?php

declare(strict_types=1);

namespace XbNz\Ip\Filters;

use Illuminate\Support\Collection;
use Livewire\Wireable;

final class PacketLossFilter implements Wireable
{
    public function __construct(
        public ?int $minPercent = null,
        public ?int $maxPercent = null,
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

<?php

declare(strict_types=1);

namespace XbNz\Ip\Filters;

use Livewire\Wireable;

final class IcmpAliveFilter implements Wireable
{
    public function __construct(
        public bool $apply = false,
    ) {}

    public function canBeApplied(): bool
    {
        return $this->apply;
    }

    /**
     * @return array<string, int|null>
     */
    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire(mixed $value): self
    {
        return new self(...$value);
    }
}

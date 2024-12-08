<?php

declare(strict_types=1);

namespace XbNz\Ip\Filters;

use Illuminate\Support\Collection;
use Livewire\Wireable;

final class OrganizationFilter implements Wireable
{
    public function __construct(
        public ?string $name = null,
        public ?int $asNumber = null,
    ) {}

    public function canBeApplied(): bool
    {
        return Collection::make((array) $this)
            ->filter(fn (mixed $value) => $value !== null)
            ->isNotEmpty();
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

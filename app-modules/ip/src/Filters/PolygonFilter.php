<?php

declare(strict_types=1);

namespace XbNz\Ip\Filters;

use Illuminate\Support\Collection;
use Livewire\Wireable;

final class PolygonFilter implements Wireable
{
    /**
     * @param  array<int, array<mixed>>|null  $geoJsons
     */
    public function __construct(
        public ?array $geoJsons = null,
    ) {}

    public function canBeApplied(): bool
    {
        return Collection::make((array) $this)
            ->filter(fn (mixed $value) => $value !== null)
            ->isNotEmpty();
    }

    /**
     * @return array<string, mixed>
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

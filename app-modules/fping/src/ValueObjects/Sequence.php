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

    public function toArray(): array
    {
        return [
            'sequence' => $this->sequence,
            'lost' => $this->lost,
            'roundTripTime' => $this->roundTripTime,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            sequence: $data['sequence'],
            lost: $data['lost'],
            roundTripTime: $data['roundTripTime'],
        );
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\DTOs;

use Spatie\LaravelData\Data;

final class CreateMasscanPreferencesDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $ttl,
        public readonly int $rate,
        public readonly ?string $adapter,
        public readonly int $retries,
    ) {}

    public static function sampleData(): self
    {
        return new self(
            'sample',
            10,
            1000,
            null,
            3,
        );
    }
}

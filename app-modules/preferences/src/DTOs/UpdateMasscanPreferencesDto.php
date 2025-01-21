<?php

declare(strict_types=1);

namespace XbNz\Preferences\DTOs;

use Spatie\LaravelData\Data;

final class UpdateMasscanPreferencesDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?int $ttl = null,
        public readonly ?int $rate = null,
        public readonly ?string $adapter = null,
        public readonly ?int $retries = null,
        public readonly ?bool $enabled = null,
    ) {}

    public static function sampleData(): self
    {
        return new self(
            1,
            'sample',
            10,
            1000,
            null,
            3,
            true,
        );
    }
}

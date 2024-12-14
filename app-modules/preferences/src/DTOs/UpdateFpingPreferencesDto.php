<?php

declare(strict_types=1);

namespace XbNz\Preferences\DTOs;

use Spatie\LaravelData\Data;

final class UpdateFpingPreferencesDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?int $size = null,
        public readonly ?float $backoff = null,
        public readonly ?int $count = null,
        public readonly ?int $ttl = null,
        public readonly ?int $interval = null,
        public readonly ?int $interval_per_target = null,
        public readonly ?string $type_of_service = null,
        public readonly ?int $retries = null,
        public readonly ?int $timeout = null,
        public readonly ?bool $dont_fragment = null,
        public readonly ?bool $send_random_data = null,
        public readonly ?bool $enabled = null,
    ) {}

    public static function sampleData(): self
    {
        return new self(
            1,
            'new name',
            44,
            2,
            66,
            62,
            15,
            1200,
            '0x10',
            12,
            5000,
            true,
            true,
            true,
        );
    }
}

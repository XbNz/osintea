<?php

declare(strict_types=1);

namespace XbNz\Fping\DTOs;

use Spatie\LaravelData\Data;

final class UpdateFpingPreferencesDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name,
        public readonly ?int $size,
        public readonly ?float $backoff,
        public readonly ?int $count,
        public readonly ?int $ttl,
        public readonly ?int $interval,
        public readonly ?int $interval_per_target,
        public readonly ?string $type_of_service,
        public readonly ?int $retries,
        public readonly ?int $timeout,
        public readonly ?bool $dont_fragment,
        public readonly ?bool $send_random_data,
        public readonly ?bool $enabled,
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use XbNz\Fping\Models\FpingPreferences;

final class FpingPreferencesDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $size,
        public readonly float $backoff,
        public readonly int $count,
        public readonly int $ttl,
        public readonly int $interval,
        public readonly int $interval_per_target,
        public readonly string $type_of_service,
        public readonly int $retries,
        public readonly int $timeout,
        public readonly bool $dont_fragment,
        public readonly bool $send_random_data,
        public readonly bool $enabled,
        public readonly CarbonImmutable $created_at,
        public readonly CarbonImmutable $updated_at,
    ) {}

    public static function fromModel(FpingPreferences $fpingPreferences): self
    {
        return new self(
            $fpingPreferences->id,
            $fpingPreferences->name,
            $fpingPreferences->size,
            $fpingPreferences->backoff,
            $fpingPreferences->count,
            $fpingPreferences->ttl,
            $fpingPreferences->interval,
            $fpingPreferences->interval_per_target,
            $fpingPreferences->type_of_service,
            $fpingPreferences->retries,
            $fpingPreferences->timeout,
            $fpingPreferences->dont_fragment,
            $fpingPreferences->send_random_data,
            $fpingPreferences->enabled,
            $fpingPreferences->created_at,
            $fpingPreferences->updated_at,
        );
    }
}

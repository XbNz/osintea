<?php

declare(strict_types=1);

namespace XbNz\Preferences\DTOs;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use XbNz\Preferences\Models\MasscanPreferences;

final class MasscanPreferencesDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $rate,
        public readonly ?string $adapter,
        public readonly int $retries,
        public readonly int $ttl,
        public readonly bool $enabled,
        public readonly CarbonImmutable $created_at,
        public readonly CarbonImmutable $updated_at,
    ) {}

    public static function fromModel(MasscanPreferences $masscanPreferences): self
    {
        return new self(
            $masscanPreferences->id,
            $masscanPreferences->name,
            $masscanPreferences->rate,
            $masscanPreferences->adapter,
            $masscanPreferences->retries,
            $masscanPreferences->ttl,
            $masscanPreferences->enabled,
            $masscanPreferences->created_at,
            $masscanPreferences->updated_at,
        );
    }
}

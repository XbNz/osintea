<?php

declare(strict_types=1);

namespace XbNz\Fping\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Fping\Database\Factories\FpingPreferencesFactory;
use XbNz\Fping\DTOs\FpingPreferencesDto;

/**
 * @mixin IdeHelperFpingPreferences
 */
final class FpingPreferences extends Model
{
    /**
     * @use HasFactory<FpingPreferencesFactory>
     */
    use HasFactory;

    /** @use WithData<FpingPreferencesDto> */
    use WithData;

    /**
     * @var class-string<Data>
     */
    protected string $dataClass = FpingPreferencesDto::class;

    protected function casts(): array
    {
        return [
            'size' => 'int',
            'backoff' => 'float',
            'count' => 'int',
            'ttl' => 'int',
            'interval' => 'int',
            'interval_per_target' => 'int',
            'type_of_service' => 'string',
            'retries' => 'int',
            'timeout' => 'int',
            'dont_fragment' => 'bool',
            'send_random_data' => 'bool',
            'enabled' => 'bool',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): FpingPreferencesFactory
    {
        return FpingPreferencesFactory::new();
    }
}

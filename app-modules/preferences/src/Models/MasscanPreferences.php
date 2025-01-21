<?php

declare(strict_types=1);

namespace XbNz\Preferences\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Preferences\Database\Factories\FpingPreferencesFactory;
use XbNz\Preferences\Database\Factories\MasscanPreferencesFactory;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;

final class MasscanPreferences extends Model
{
    /**
     * @use HasFactory<FpingPreferencesFactory>
     */
    use HasFactory;

    /** @use WithData<MasscanPreferencesDto> */
    use WithData;

    /**
     * @var class-string<Data>
     */
    protected string $dataClass = MasscanPreferencesDto::class;

    protected function casts(): array
    {
        return [
            'rate' => 'int',
            'retries' => 'int',
            'ttl' => 'int',
            'enabled' => 'bool',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): MasscanPreferencesFactory
    {
        return MasscanPreferencesFactory::new();
    }
}

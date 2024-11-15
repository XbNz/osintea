<?php

declare(strict_types=1);

namespace XbNz\Ip\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Ip\Database\Factories\IpAddressFactory;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ping\Models\PingSequence;
use XbNz\Shared\ValueObjects\IpType;

/**
 * @mixin IdeHelperIpAddress
 */
final class IpAddress extends Model
{
    /**
     * @use HasFactory<IpAddressFactory>
     */
    use HasFactory;

    /** @use WithData<IpAddressDto> */
    use WithData;

    public $timestamps = false;

    /**
     * @var class-string<Data>
     */
    protected string $dataClass = IpAddressDto::class;

    protected function casts(): array
    {
        return [
            'type' => IpType::class,
            'created_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return HasMany<PingSequence, IpAddress>
     */
    public function pingSequences(): HasMany
    {
        return $this->hasMany(PingSequence::class);
    }

    protected static function newFactory(): IpAddressFactory
    {
        return IpAddressFactory::new();
    }
}

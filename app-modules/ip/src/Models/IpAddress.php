<?php

declare(strict_types=1);

namespace XbNz\Ip\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Asn\Model\Asn;
use XbNz\Ip\Database\Factories\IpAddressFactory;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Location\Models\Coordinates;
use XbNz\Ping\Models\PingSequence;
use XbNz\Port\Models\Port;
use XbNz\Shared\Enums\IpType;

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

    /**
     * @return HasOne<Asn, IpAddress>
     */
    public function asn(): HasOne
    {
        return $this->hasOne(Asn::class);
    }

    /**
     * @return HasOne<Coordinates, IpAddress>
     */
    public function coordinates(): HasOne
    {
        return $this->hasOne(Coordinates::class);
    }

    /**
     * @return HasMany<Port, IpAddress>
     */
    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    protected static function newFactory(): IpAddressFactory
    {
        return IpAddressFactory::new();
    }
}

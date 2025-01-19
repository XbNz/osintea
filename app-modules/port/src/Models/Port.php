<?php

declare(strict_types=1);

namespace XbNz\Port\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Ip\Models\IpAddress;
use XbNz\Port\Database\Factories\PortFactory;
use XbNz\Port\DTOs\PortDto;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;

final class Port extends Model
{
    /**
     * @use HasFactory<PortFactory>
     */
    use HasFactory;

    /** @use WithData<PortDto> */
    use WithData;

    public $timestamps = false;

    /**
     * @var class-string<Data>
     */
    protected string $dataClass = PortDto::class;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'state' => PortState::class,
            'protocol' => ProtocolType::class,
            'created_at' => 'immutable_datetime:U.u',
        ];
    }

    /**
     * @return BelongsTo<IpAddress, Port>
     */
    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class);
    }

    protected static function newFactory(): PortFactory
    {
        return PortFactory::new();
    }
}

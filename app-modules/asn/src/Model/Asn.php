<?php

declare(strict_types=1);

namespace XbNz\Asn\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Asn\Database\Factories\AsnFactory;
use XbNz\Asn\DTOs\AsnDto;
use XbNz\Ip\Models\IpAddress;

final class Asn extends Model
{
    /**
     * @use HasFactory<AsnFactory>
     */
    use HasFactory;

    /** @use WithData<AsnDto> */
    use WithData;

    public $timestamps = false;

    /**
     * @var class-string<Data>
     */
    protected string $dataClass = AsnDto::class;

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<IpAddress, Asn>
     */
    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class);
    }

    protected static function newFactory(): AsnFactory
    {
        return AsnFactory::new();
    }
}

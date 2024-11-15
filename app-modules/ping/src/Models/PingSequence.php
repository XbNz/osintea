<?php

declare(strict_types=1);

namespace XbNz\Ping\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Database\Factories\PingSequenceFactory;
use XbNz\Ping\DTOs\PingSequenceDto;

/**
 * @mixin IdeHelperPingSequence
 */
final class PingSequence extends Model
{
    /**
     * @use HasFactory<PingSequenceFactory>
     */
    use HasFactory;

    /** @use WithData<PingSequenceDto> */
    use WithData;

    public $timestamps = false;

    /**
     * @var class-string<Data>
     */
    protected string $dataClass = PingSequenceDto::class;


    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'loss' => 'boolean',
            'created_at' => 'immutable_datetime:U.u',
        ];
    }

    /**
     * @return BelongsTo<IpAddress, PingSequence>
     */
    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class);
    }

    protected static function newFactory(): PingSequenceFactory
    {
        return PingSequenceFactory::new();
    }
}

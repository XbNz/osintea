<?php

declare(strict_types=1);

namespace XbNz\Location\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use XbNz\Ip\Models\IpAddress;
use XbNz\Location\Database\Factories\CoordinatesFactory;
use XbNz\Location\DTOs\CoordinatesDto;

final class Coordinates extends Model
{
    /**
     * @use HasFactory<CoordinatesFactory>
     */
    use HasFactory;

    /** @use WithData<CoordinatesDto> */
    use WithData;

    /**
     * @var class-string<Data>
     */
    protected string $dataClass = CoordinatesDto::class;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<IpAddress, Coordinates>
     */
    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class);
    }

    protected static function newFactory(): CoordinatesFactory
    {
        return CoordinatesFactory::new();
    }

    protected function newBaseQueryBuilder(): Builder
    {
        return parent::newBaseQueryBuilder()
            ->selectRaw('coordinates.*, ST_AsText(coordinates) as coordinates');
    }
}

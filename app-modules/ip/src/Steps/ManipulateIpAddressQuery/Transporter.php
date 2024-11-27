<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;
use XbNz\Ip\Filters\PacketLossFilter;
use XbNz\Ip\Filters\RoundTripTimeFilter;

final class Transporter
{
    public function __construct(
        public readonly string $direction,
        public Builder $query,
        public RoundTripTimeFilter $roundTripTimeFilter,
        public PacketLossFilter $packetLossFilter,
    ) {}
}

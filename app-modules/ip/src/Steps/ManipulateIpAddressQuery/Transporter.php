<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;
use XbNz\Ip\Filters\PacketLossFilter;
use XbNz\Ip\Filters\RoundTripTimeFilter;
use XbNz\Ip\Models\IpAddress;

final class Transporter
{
    /**
     * @param  Builder<IpAddress>  $query
     */
    public function __construct(
        public readonly string $direction,
        public Builder $query,
        public RoundTripTimeFilter $roundTripTimeFilter,
        public PacketLossFilter $packetLossFilter,
    ) {}
}

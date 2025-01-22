<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;

final class FilterIcmpAlive
{
    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->icmpAliveFilter->canBeApplied() === false) {
            return $transporter;
        }

        $transporter->query
            ->where(fn (Builder $query) => $query->whereHas('ports', function (Builder $query): void {
                $query
                    ->groupBy('ports.ip_address_id')
                    ->orderBy('created_at')
                    ->limit(1)
                    ->where('protocol', ProtocolType::ICMP->value)
                    ->where('state', PortState::Open->value);
            }));

        return $transporter;
    }
}

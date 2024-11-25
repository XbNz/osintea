<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Support\Facades\DB;

final class SortByAverageRtt
{
    public function handle(Transporter $transporter): Transporter
    {
        $transporter->query
            ->select('ip_addresses.*', DB::raw('AVG(ping_sequences.round_trip_time) as average_rtt'))
            ->leftJoin('ping_sequences', 'ip_addresses.id', '=', 'ping_sequences.ip_address_id')
            ->groupBy('ip_addresses.id')
            ->orderBy('average_rtt', $transporter->direction);

        return $transporter;
    }
}

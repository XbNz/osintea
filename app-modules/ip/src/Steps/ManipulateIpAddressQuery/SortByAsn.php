<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use XbNz\Asn\Model\Asn;

final class SortByAsn
{
    public function handle(Transporter $transporter): Transporter
    {
        $query = $transporter
            ->query
            ->addSelect([
                'as_number' => Asn::query()
                    ->select('as_number')
                    ->whereColumn('ip_addresses.id', 'asns.ip_address_id')
                    ->limit(1),
            ])
            ->orderBy('as_number', $transporter->direction);

        return $transporter;
    }
}

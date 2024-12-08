<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use XbNz\Asn\Model\Asn;

final class SortByOrganization
{
    public function handle(Transporter $transporter): Transporter
    {
        $query = $transporter
            ->query
            ->addSelect([
                'organization' => Asn::query()
                    ->select('organization')
                    ->whereColumn('ip_addresses.id', 'asns.ip_address_id')
                    ->limit(1),
            ])
            ->orderBy('organization', $transporter->direction);

        return $transporter;
    }
}

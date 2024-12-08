<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;

final class FilterOrganization
{
    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->organizationFilter->canBeApplied() === false) {
            return $transporter;
        }

        $transporter->query
            ->whereHas('asn', function (Builder $query) use ($transporter): void {
                $query
                    ->groupBy('asns.ip_address_id')
                    ->when($transporter->organizationFilter->name, function (Builder $query, $organization): void {
                        $query->having('asns.organization', $organization);
                    })
                    ->when($transporter->organizationFilter->asNumber, function (Builder $query, $asn): void {
                        $query->having('asns.as_number', $asn);
                    });
            });

        return $transporter;
    }
}

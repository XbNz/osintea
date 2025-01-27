<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

final class SortByGeolocated
{
    public function handle(Transporter $transporter): Transporter
    {
        $query = $transporter
            ->query
            ->withCount(['coordinates'])
            ->orderBy('coordinates_count', $transporter->direction);

        return $transporter;
    }
}

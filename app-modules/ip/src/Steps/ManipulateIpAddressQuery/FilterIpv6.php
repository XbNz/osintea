<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

final class FilterIpv6
{
    public function handle(Transporter $transporter): Transporter
    {
        $transporter->query->where('type', 6);

        return $transporter;
    }
}

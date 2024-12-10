<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;

final class LimitIpv4
{
    public function handle(Transporter $transporter): Transporter
    {
        $transporter->query->where(fn(Builder $query) => $query->where('type', 4));

        return $transporter;
    }
}

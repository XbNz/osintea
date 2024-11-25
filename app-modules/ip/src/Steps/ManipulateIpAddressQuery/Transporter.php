<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;

final class Transporter
{
    public function __construct(
        public readonly string $direction,
        public Builder $query,
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Asn\Contracts;

use XbNz\Asn\ValueObject\Asn;

interface IpToAsnInterface
{
    public function execute(string $ip): Asn;
}

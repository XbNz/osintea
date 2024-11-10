<?php

declare(strict_types=1);

namespace XbNz\Shared\ValueObjects;

enum IpType: string
{
    case IPv4 = 'IPv4';
    case IPv6 = 'IPv6';
}

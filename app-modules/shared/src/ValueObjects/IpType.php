<?php

declare(strict_types=1);

namespace XbNz\Shared\ValueObjects;

enum IpType: int
{
    case IPv4 = 4;
    case IPv6 = 6;
}

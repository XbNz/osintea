<?php

declare(strict_types=1);

namespace XbNz\Shared\Enums;

enum IpType: int
{
    case IPv4 = 4;
    case IPv6 = 6;
}

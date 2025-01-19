<?php

declare(strict_types=1);

namespace XbNz\Shared\Enums;

enum ProtocolType: string
{
    case TCP = 'tcp';
    case UDP = 'udp';
    case ICMP = 'icmp';
}

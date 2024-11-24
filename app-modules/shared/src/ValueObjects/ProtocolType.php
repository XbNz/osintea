<?php

declare(strict_types=1);

namespace XbNz\Shared\ValueObjects;

enum ProtocolType: string
{
    case TCP = 'tcp';
    case UDP = 'udp';
    case ICMP = 'icmp';
}

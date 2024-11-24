<?php

declare(strict_types=1);

namespace XbNz\Shared\Enums;

enum NativePhpWindow: string
{
    case CommandPalette = 'command-palette';

    case Ping = 'ping';
    case IpAddresses = 'ip-addresses';
}

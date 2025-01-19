<?php

declare(strict_types=1);

namespace XbNz\Shared\Enums;

enum PortState: string
{
    case Open = 'open';
    case Closed = 'closed';
}

<?php

declare(strict_types=1);

namespace XbNz\Shared\ValueObjects;

use Webmozart\Assert\Assert;
use XbNz\Shared\Enums\ProtocolType;

final class Port
{
    public function __construct(
        public readonly int $port,
        public readonly ProtocolType $protocol,
    ) {
        Assert::range($port, 0, 65535);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Ping\Events;

use XbNz\Ping\DTOs\PingSequenceDto;

final class PingSequenceDeletedEvent
{
    public function __construct(
        public readonly PingSequenceDto $record,
    ) {}
}

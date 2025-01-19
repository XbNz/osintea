<?php

declare(strict_types=1);

namespace XbNz\Port\Events;

use XbNz\Port\DTOs\PortDto;

final class PortDeletedEvent
{
    public function __construct(
        public readonly PortDto $record
    ) {}
}

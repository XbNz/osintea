<?php

declare(strict_types=1);

namespace XbNz\Fping\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use XbNz\Fping\DTOs\FpingPreferencesDto;

final class FpingPreferencesInsertedEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets;

    public function __construct(
        public readonly FpingPreferencesDto $record,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }
}

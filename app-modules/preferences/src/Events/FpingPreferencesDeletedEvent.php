<?php

declare(strict_types=1);

namespace XbNz\Preferences\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use XbNz\Preferences\DTOs\FpingPreferencesDto;

final class FpingPreferencesDeletedEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets;

    public function __construct(
        public readonly FpingPreferencesDto $record,
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }
}

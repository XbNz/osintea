<?php

declare(strict_types=1);

namespace XbNz\Preferences\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;

final class MasscanPreferencesDeletedEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets;

    public function __construct(
        public readonly MasscanPreferencesDto $record,
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

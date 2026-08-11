<?php

declare(strict_types=1);

namespace XbNz\Shared\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use XbNz\Shared\Enums\UpdatableDatabase;

final class DatabaseUpdateStatusEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }

    public function __construct(
        public readonly UpdatableDatabase $database,
        public readonly string $status,
        public readonly ?string $message = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Shared\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Webmozart\Assert\Assert;
use XbNz\Shared\Enums\UpdatableDatabase;

final class UpdateProgressReportEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;

    public float $percentage {
        get {
            if ($this->totalBytes === 0) {
                return 0;
            }

            return $this->downloadedBytes / $this->totalBytes * 100;
        }
    }

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
        public readonly int $totalBytes,
        public readonly int $downloadedBytes,
    ) {
    }
}

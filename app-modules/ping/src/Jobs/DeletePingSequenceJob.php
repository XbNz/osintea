<?php

declare(strict_types=1);

namespace XbNz\Ping\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Ping\Actions\DeletePingSequenceAction;
use XbNz\Ping\DTOs\PingSequenceDto;

final class DeletePingSequenceJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly PingSequenceDto $pingSequenceDto,
    ) {}

    public function handle(DeletePingSequenceAction $deletePingSequenceAction): void
    {
        $deletePingSequenceAction->handle($this->pingSequenceDto);
    }
}

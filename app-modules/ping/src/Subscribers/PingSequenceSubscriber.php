<?php

declare(strict_types=1);

namespace XbNz\Ping\Subscribers;

use Illuminate\Contracts\Bus\Dispatcher;
use XbNz\Ping\Events\Intentions\DeletePingSequenceIntention;
use XbNz\Ping\Jobs\DeletePingSequenceJob;
use XbNz\Shared\Attributes\ListensTo;

final class PingSequenceSubscriber
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    #[ListensTo(DeletePingSequenceIntention::class)]
    public function onDeletePingSequenceIntention(DeletePingSequenceIntention $intention): void
    {
        $this->dispatcher->dispatch(new DeletePingSequenceJob($intention->record));
    }
}

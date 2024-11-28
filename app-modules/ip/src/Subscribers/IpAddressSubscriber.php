<?php

declare(strict_types=1);

namespace XbNz\Ip\Subscribers;

use Illuminate\Contracts\Bus\Dispatcher;
use XbNz\Ip\Events\Intentions\DeleteIpAddressIntention;
use XbNz\Ip\Jobs\DeleteIpAddressJob;
use XbNz\Shared\Attributes\ListensTo;

final class IpAddressSubscriber
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    #[ListensTo(DeleteIpAddressIntention::class)]
    public function OnDeleteIpAddressIntention(DeleteIpAddressIntention $intention): void
    {
        $this->dispatcher->dispatch(new DeleteIpAddressJob($intention->record));
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Ip\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Ip\Actions\DeleteIpAddressAction;
use XbNz\Ip\DTOs\IpAddressDto;

final class DeleteIpAddressJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly IpAddressDto $ipAddress,
    ) {}

    public function handle(DeleteIpAddressAction $deleteIpAddressAction): void
    {
        $deleteIpAddressAction->handle($this->ipAddress);
    }
}

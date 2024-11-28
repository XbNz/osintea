<?php

declare(strict_types=1);

namespace XbNz\Ip\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Events\IpAddressDeletedEvent;
use XbNz\Ip\Models\IpAddress;

final class DeleteIpAddressAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function handle(IpAddressDto $ipAddressDto): IpAddressDto
    {
        IpAddress::query()->findOrFail($ipAddressDto->id)->deleteOrFail();

        $this->dispatcher->dispatch(new IpAddressDeletedEvent($ipAddressDto));

        return $ipAddressDto;
    }
}

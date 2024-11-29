<?php

declare(strict_types=1);

namespace XbNz\Ping\Actions;

use Illuminate\Support\Collection;
use XbNz\Ip\DTOs\IpAddressDto;

final class BulkPingAction
{
    /**
     * @param  Collection<IpAddressDto>  $ipAddressDtos
     */
    public function handle(Collection $ipAddressDtos): void {}
}

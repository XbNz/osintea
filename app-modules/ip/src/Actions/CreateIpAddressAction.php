<?php

declare(strict_types=1);

namespace XbNz\Ip\Actions;

use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Shared\IpValidator;

final class CreateIpAddressAction
{
    public function handle(string $ip): IpAddressDto
    {
        IpValidator::make($ip)->assertValid();

        return IpAddress::query()->createOrFirst([
            'ip' => $ip,
        ])->fresh()->getData();
    }
}

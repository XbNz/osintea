<?php

declare(strict_types=1);

namespace XbNz\Ping\Actions;

use Carbon\CarbonImmutable;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ping\DTOs\CreatePingSequenceDto;
use XbNz\Ping\DTOs\PingSequenceDto;

final class FulfillSequenceAction
{
    public function __construct(
        public readonly CreatePingSequenceAction $createPingSequenceAction,
        public readonly FpingInterface $fping,
    ) {}

    public function handle(IpAddressDto $ipAddressDto): PingSequenceDto
    {
        $pingResultDto = $this->fping
            ->count(1)
            ->intervalPerHost(1)
            ->target($ipAddressDto->ip)
            ->execute()[0];

        return $this->createPingSequenceAction->handle(
            new CreatePingSequenceDto(
                $ipAddressDto,
                $pingResultDto->sequences[0],
                CarbonImmutable::now(),
            )
        );
    }
}

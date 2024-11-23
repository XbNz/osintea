<?php

declare(strict_types=1);

namespace XbNz\Ping\Steps\LoopingStandardInPingWorker;

use XbNz\Ping\DTOs\AddTargetRequestDto;
use XbNz\Ping\DTOs\RemoveTargetRequestDto;

final class Transporter
{
    public function __construct(
        public readonly string $data,
        public ?AddTargetRequestDto $addTargetRequestDto = null,
        public ?RemoveTargetRequestDto $removeTargetRequestDto = null,
    ) {}
}

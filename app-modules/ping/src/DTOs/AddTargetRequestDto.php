<?php

declare(strict_types=1);

namespace XbNz\Ping\DTOs;

final class AddTargetRequestDto
{
    public function __construct(
        public string $target,
        public int $interval,
    ) {}
}

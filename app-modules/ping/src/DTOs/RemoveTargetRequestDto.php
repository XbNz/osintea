<?php

declare(strict_types=1);

namespace XbNz\Ping\DTOs;

final class RemoveTargetRequestDto
{
    public function __construct(
        public string $target,
    ) {}
}

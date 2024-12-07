<?php

declare(strict_types=1);

namespace XbNz\Asn\ValueObject;

final class Asn
{
    public function __construct(
        public readonly string $organization,
        public readonly int $asNumber,
    ) {}
}

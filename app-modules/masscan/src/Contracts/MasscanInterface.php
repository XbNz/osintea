<?php

declare(strict_types=1);

namespace XbNz\Masscan\Contracts;

use XbNz\Shared\ValueObjects\Port;

interface MasscanInterface
{
    public function binary(string $binaryPath): self;

    public function target(string $target): self;

    /**
     * @param  array<int, Port>  $ports
     */
    public function ports(array $ports): self;
}

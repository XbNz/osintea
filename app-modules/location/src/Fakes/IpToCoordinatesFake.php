<?php

declare(strict_types=1);

namespace XbNz\Location\Fakes;

use PHPUnit\Framework\Assert as PHPUnit;
use XbNz\Location\Contracts\IpToCoordinatesInterface;
use XbNz\Location\Enums\Provider;
use XbNz\Shared\ValueObjects\Coordinates;

final class IpToCoordinatesFake implements IpToCoordinatesInterface
{
    public int $executeCount = 0;

    /**
     * @var array<int, Provider>
     */
    public array $providers = [];

    public function execute(string $ip): Coordinates
    {
        $this->executeCount++;

        return new Coordinates(0.0, 0.0);
    }

    public function supports(Provider $provider): bool
    {
        $this->providers[] = $provider;

        return $provider === Provider::Fake;
    }

    public function assertExecuteCount(int $expected): void
    {
        PHPUnit::assertSame($expected, $this->executeCount);
    }

    public function assertProvider(Provider $expected): void
    {
        PHPUnit::assertContains($expected, $this->providers);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Asn\Fakes;

use PHPUnit\Framework\Assert as PHPUnit;
use XbNz\Asn\Contracts\IpToAsnInterface;
use XbNz\Asn\Enums\Provider;
use XbNz\Asn\ValueObject\Asn;

final class IpToAsnFake implements IpToAsnInterface
{
    public int $executeCount = 0;

    public array $providers = [];

    public ?Asn $forceAsnReturn = null;

    public function execute(string $ip): ?Asn
    {
        $this->executeCount++;

        return $this->forceAsnReturn ?? new Asn('test', 12345);
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

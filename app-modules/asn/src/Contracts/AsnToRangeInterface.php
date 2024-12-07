<?php

declare(strict_types=1);

namespace XbNz\Asn\Contracts;

use Illuminate\Support\Collection;
use XbNz\Asn\Enums\Provider;
use XbNz\Asn\ValueObject\IpRange;

interface AsnToRangeInterface
{
    const int FILTER_IPV4 = 0x01;

    const int FILTER_IPV6 = 0x02;

    public function filterIpType(int $filterMask): self;

    public function organization(string $organization): self;

    public function asNumber(int $asNumber): self;

    /**
     * @return Collection<int, IpRange>
     */
    public function execute(): Collection;

    public function supports(Provider $provider): bool;
}

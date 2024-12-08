<?php

declare(strict_types=1);

namespace XbNz\Asn\Jobs;

use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use XbNz\Asn\Enums\Provider;
use XbNz\Asn\Steps\BulkAsnLookup\BulkAsnLookup;
use XbNz\Asn\Steps\BulkAsnLookup\FireEvent;
use XbNz\Asn\Steps\BulkAsnLookup\Transporter;
use XbNz\Ip\DTOs\IpAddressDto;

final class BulkAsnLookupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * @param  Collection<int, IpAddressDto>  $ipAddressDtos
     */
    public function __construct(
        public readonly Collection $ipAddressDtos,
        public readonly Provider $provider,
    ) {}

    public function handle(): void
    {
        $pipes = [
            BulkAsnLookup::class,
            FireEvent::class,
        ];

        Pipeline::make()
            ->send(new Transporter($this->ipAddressDtos, $this->provider))
            ->through($pipes)
            ->thenReturn();
    }
}

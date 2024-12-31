<?php

declare(strict_types=1);

namespace XbNz\Location\Jobs;

use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Location\Enums\Provider;
use XbNz\Location\Steps\BulkGeolocate\BulkGeolocate;
use XbNz\Location\Steps\BulkGeolocate\FireEvent;
use XbNz\Location\Steps\BulkGeolocate\Transporter;

final class BulkGeolocateJob implements ShouldQueue
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
            BulkGeolocate::class,
            FireEvent::class,
        ];

        Pipeline::make()
            ->send(new Transporter($this->ipAddressDtos, $this->provider))
            ->through($pipes)
            ->thenReturn();
    }
}

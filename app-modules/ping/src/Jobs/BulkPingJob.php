<?php

declare(strict_types=1);

namespace XbNz\Ping\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ping\Steps\BulkPing\BulkPing;
use XbNz\Ping\Steps\BulkPing\FireEvent;
use XbNz\Ping\Steps\BulkPing\Transporter;
use XbNz\Shared\Pipeline;

final class BulkPingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * @param  Collection<int, IpAddressDto>  $ipAddressDtos
     */
    public function __construct(
        public readonly Collection $ipAddressDtos,
    ) {}

    public function handle(): void
    {
        $pipes = [
            BulkPing::class,
            FireEvent::class,
        ];

        Pipeline::make()
            ->send(new Transporter($this->ipAddressDtos))
            ->through($pipes)
            ->thenReturn();
    }
}

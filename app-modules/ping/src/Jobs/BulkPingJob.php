<?php

declare(strict_types=1);

namespace XbNz\Ping\Jobs;

use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ping\Actions\BulkPingAction;
use XbNz\Ping\Steps\BulkPing\BulkPing;
use XbNz\Ping\Steps\BulkPing\FireEvent;
use XbNz\Ping\Steps\BulkPing\Transporter;

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

    public function handle(BulkPingAction $bulkPingAction): void
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

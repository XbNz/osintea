<?php

declare(strict_types=1);

namespace XbNz\Port\Jobs;

use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use XbNz\Port\Steps\BulkIcmpScan\Transporter;

final class BulkIcmpScanJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * @param  array<int, int>  $ipAddressIds
     */
    public function __construct(
        public readonly array $ipAddressIds,
    ) {}

    public function handle(): void
    {
        $pipes = [
            BulkIcmpScan::class,
            FireEvent::class,
        ];

        Pipeline::make()
            ->send(new Transporter($this->ipAddressIds))
            ->through($pipes)
            ->thenReturn();
    }
}

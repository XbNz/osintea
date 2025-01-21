<?php

declare(strict_types=1);

namespace XbNz\Port\Steps\BulkIcmpScan;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Port\Events\BulkIcmpScanCompleted;

final class FireEvent
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $transporter->completedCount = count($transporter->ipAddressIds);

        $this->dispatcher->dispatch(new BulkIcmpScanCompleted($transporter->completedCount));

        return $transporter;
    }
}

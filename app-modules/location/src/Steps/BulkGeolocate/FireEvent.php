<?php

declare(strict_types=1);

namespace XbNz\Location\Steps\BulkGeolocate;

use Illuminate\Contracts\Events\Dispatcher;

final class FireEvent
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $transporter->completedCount = $transporter->ipAddressDtos->count();

        $this->dispatcher->dispatch(new BulkPingCompleted($transporter->completedCount));

        return $transporter;
    }
}

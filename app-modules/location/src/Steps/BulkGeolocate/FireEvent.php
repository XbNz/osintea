<?php

declare(strict_types=1);

namespace XbNz\Location\Steps\BulkGeolocate;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Location\Events\BulkGeolocationCompleted;

final class FireEvent
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $transporter->completedCount = $transporter->ipAddressDtos->count();

        $this->dispatcher->dispatch(new BulkGeolocationCompleted($transporter->completedCount));

        return $transporter;
    }
}

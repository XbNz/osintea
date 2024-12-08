<?php

declare(strict_types=1);

namespace XbNz\Asn\Steps\BulkAsnLookup;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Asn\Events\BulkAsnLookupCompleted;

final class FireEvent
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $transporter->completedCount = $transporter->ipAddressDtos->count();

        $this->dispatcher->dispatch(new BulkAsnLookupCompleted($transporter->completedCount));

        return $transporter;
    }
}

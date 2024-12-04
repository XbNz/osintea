<?php

declare(strict_types=1);

namespace XbNz\Ping\Steps\BulkPing;

use XbNz\Ping\Actions\BulkPingAction;

final class BulkPing
{
    public function __construct(
        private readonly BulkPingAction $bulkPingAction,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $this->bulkPingAction->handle($transporter->ipAddressDtos);

        return $transporter;
    }
}

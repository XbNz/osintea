<?php

declare(strict_types=1);

namespace XbNz\Port\Steps\BulkIcmpScan;

use Illuminate\Support\Collection;
use XbNz\Ip\Models\IpAddress;
use XbNz\Port\Actions\BulkIcmpScanAction;

final class BulkIcmpScan
{
    public function __construct(
        private readonly BulkIcmpScanAction $bulkIcmpScanAction
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $this->bulkIcmpScanAction->handle(
            Collection::make($transporter->ipAddressIds)
                ->filter(fn (int $ipAddressId) => IpAddress::query()->where('id', $ipAddressId)->exists())
                ->map(fn (int $ipAddressId) => IpAddress::query()->findOrFail($ipAddressId)->getData())
        );

        return $transporter;
    }
}

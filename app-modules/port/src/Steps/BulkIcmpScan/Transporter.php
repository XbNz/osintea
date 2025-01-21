<?php

declare(strict_types=1);

namespace XbNz\Port\Steps\BulkIcmpScan;

final class Transporter
{
    /**
     * @param  array<int, int>  $ipAddressIds
     */
    public function __construct(
        public readonly array $ipAddressIds,
        public int $completedCount = 0,
    ) {}
}

<?php

declare(strict_types=1);

namespace XbNz\Masscan\Contracts;

use XbNz\Port\DTOs\PortScanResultDto;

interface MasscanIcmpScannerInterface
{
    public function inputFilePath(string $inputFile): self;

    public function outputFilePath(string $outputFile): self;

    public function rate(int $rate): self;

    /**
     * @return array<int, PortScanResultDto>
     */
    public function execute(): array;
}

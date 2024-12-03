<?php

declare(strict_types=1);

namespace XbNz\Fping\Contracts;

use XbNz\Ping\DTOs\PingResultDto;

interface FpingInterface
{
    public function binary(string $binaryPath): self;

    public function target(string $target): self;

    public function inputFilePath(string $inputFile): self;

    public function outputFilePath(string $outputFile): self;

    public function size(int $bytes): self;

    public function backoffFactor(float $backoff): self;

    public function count(int $count): self;

    public function timeToLive(int $ttl): self;

    public function interval(int $interval): self;

    public function resolveAllHostnameIpAddresses(bool $bool = true): self;

    public function dontFragment(bool $bool = true): self;

    public function typeOfService(string $tos): self;

    public function intervalPerHost(int $interval): self;

    public function retries(int $retries): self;

    public function sendRandomData(bool $bool = true): self;

    public function sourceAddress(string $sourceAddress): self;

    public function timeout(int $timeout): self;

    /**
     * @return array<int, PingResultDto>
     */
    public function execute(): array;
}

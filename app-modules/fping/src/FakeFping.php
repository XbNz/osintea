<?php

declare(strict_types=1);

namespace XbNz\Fping;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Webmozart\Assert\Assert;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Ping\DTOs\PingResultDTO;

use function Psl\Filesystem\canonicalize;

final class FakeFping implements FpingInterface
{
    private int $count;

    private float $intervalPerHost;

    private bool $executed = false;

    private string $inputFilePath;

    /**
     * @var array<int, PingResultDTO>
     */
    private array $forceReturn = [];

    public function binary(string $binaryPath): FpingInterface
    {
        return $this;
    }

    public function inputFilePath(string $inputFile): FpingInterface
    {
        Assert::fileExists($inputFile, 'The input file could not be found at the given path');

        $canonicalized = canonicalize($inputFile);

        Assert::string($canonicalized);

        $this->inputFilePath = $canonicalized;

        return $this;
    }

    public function outputFilePath(string $outputFile): FpingInterface
    {
        return $this;
    }

    public function size(int $bytes): FpingInterface
    {
        return $this;
    }

    public function backoffFactor(float $backoff): FpingInterface
    {
        return $this;
    }

    public function count(int $count): FpingInterface
    {
        $this->count = $count;

        return $this;
    }

    public function timeToLive(int $ttl): FpingInterface
    {
        return $this;
    }

    public function interval(float $interval): FpingInterface
    {
        return $this;
    }

    public function resolveAllHostnameIpAddresses(bool $bool = true): FpingInterface
    {
        return $this;
    }

    public function dontFragment(bool $bool = true): FpingInterface
    {
        return $this;
    }

    public function typeOfService(string $tos): FpingInterface
    {
        return $this;
    }

    public function intervalPerHost(float $interval): FpingInterface
    {
        $this->intervalPerHost = $interval;

        return $this;
    }

    public function retries(int $retries): FpingInterface
    {
        return $this;
    }

    public function sendRandomData(bool $bool = true): FpingInterface
    {
        return $this;
    }

    public function sourceAddress(string $sourceAddress): FpingInterface
    {
        return $this;
    }

    public function timeout(int $timeout): FpingInterface
    {
        return $this;
    }

    /**
     * @return array<int, PingResultDTO>
     */
    public function execute(): array
    {
        $this->executed = true;

        return $this->forceReturn;
    }

    /**
     * @param  array<int, PingResultDTO>  $forceReturn
     */
    public function forceReturn(array $forceReturn): void
    {
        $this->forceReturn = $forceReturn;
    }

    public function assertCount(int $expectedCount): void
    {
        PHPUnitAssert::assertSame($expectedCount, $this->count);
    }

    public function assertIntervalPerHost(float $expectedInterval): void
    {
        PHPUnitAssert::assertSame($expectedInterval, $this->intervalPerHost);
    }

    public function assertExecuted(): void
    {
        PHPUnitAssert::assertTrue($this->executed);
    }

    public function assertInputFileIncludesTarget(string $target): void
    {
        $contents = file_get_contents($this->inputFilePath);

        PHPUnitAssert::assertIsString($contents);

        PHPUnitAssert::assertStringContainsString($target, $contents);
    }
}

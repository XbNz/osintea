<?php

declare(strict_types=1);

namespace XbNz\Fping;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Webmozart\Assert\Assert;
use XbNz\Fping\Contracts\FpingInterface;

use function Psl\Filesystem\canonicalize;

final class FakeFping implements FpingInterface
{
    private int $count;

    private float $intervalPerHost;

    private bool $executed = false;

    private string $inputFilePath;

    private array $forceReturn = [];

    public function binary(string $binaryPath): FpingInterface
    {
        // TODO: Implement binary() method.
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
        // TODO: Implement outputFilePath() method.
    }

    public function size(int $bytes): FpingInterface
    {
        // TODO: Implement size() method.
    }

    public function backoffFactor(float $backoff): FpingInterface
    {
        // TODO: Implement backoffFactor() method.
    }

    public function count(int $count): FpingInterface
    {
        $this->count = $count;

        return $this;
    }

    public function timeToLive(int $ttl): FpingInterface
    {
        // TODO: Implement timeToLive() method.
    }

    public function interval(float $interval): FpingInterface
    {
        // TODO: Implement interval() method.
    }

    public function resolveAllHostnameIpAddresses(bool $bool = true): FpingInterface
    {
        // TODO: Implement resolveAllHostnameIpAddresses() method.
    }

    public function dontFragment(bool $bool = true): FpingInterface
    {
        // TODO: Implement dontFragment() method.
    }

    public function typeOfService(string $tos): FpingInterface
    {
        // TODO: Implement typeOfService() method.
    }

    public function intervalPerHost(float $interval): FpingInterface
    {
        $this->intervalPerHost = $interval;

        return $this;
    }

    public function retries(int $retries): FpingInterface
    {
        // TODO: Implement retries() method.
    }

    public function sendRandomData(bool $bool = true): FpingInterface
    {
        // TODO: Implement sendRandomData() method.
    }

    public function sourceAddress(string $sourceAddress): FpingInterface
    {
        // TODO: Implement sourceAddress() method.
    }

    public function timeout(int $timeout): FpingInterface
    {
        // TODO: Implement timeout() method.
    }

    public function execute(): array
    {
        $this->executed = true;

        return $this->forceReturn;
    }

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

        PHPUnitAssert::assertStringContainsString($target, $contents);
    }
}

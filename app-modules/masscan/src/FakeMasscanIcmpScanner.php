<?php

declare(strict_types=1);

namespace XbNz\Masscan;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Webmozart\Assert\Assert;
use XbNz\Masscan\Contracts\MasscanIcmpScannerInterface;
use XbNz\Port\DTOs\PortScanResultDto;

use function Psl\Filesystem\canonicalize;

final class FakeMasscanIcmpScanner implements MasscanIcmpScannerInterface
{
    private string $inputFilePath = '';

    /**
     * @var array<int, int>
     */
    private array $ttls = [];

    /**
     * @var array<int, int>
     */
    private array $retries = [];

    /**
     * @var array<int, int>
     */
    private array $rates = [];

    /**
     * @var array<int, int>
     */
    private array $timeouts = [];

    /**
     * @var array<int, string>
     */
    private array $adapters = [];

    private int $executes = 0;

    /**
     * @var array<int, PortScanResultDto>
     */
    private array $forceReturn = [];

    public function inputFilePath(string $inputFile): self
    {
        Assert::fileExists($inputFile, 'The input file could not be found at the given path');

        $canonicalized = canonicalize($inputFile);

        Assert::string($canonicalized);

        $this->inputFilePath = $canonicalized;

        return $this;
    }

    public function outputFilePath(string $outputFile): self
    {
        return $this;
    }

    public function timeToLive(int $ttl): self
    {
        $this->ttls[] = $ttl;

        return $this;
    }

    public function rate(int $rate): self
    {
        $this->rates[] = $rate;

        return $this;
    }

    public function adapter(string $adapter): self
    {
        $this->adapters[] = $adapter;

        return $this;
    }

    public function retries(int $retries): self
    {
        $this->retries[] = $retries;

        return $this;
    }

    public function timeout(int $timeout): self
    {
        $this->timeouts[] = $timeout;

        return $this;
    }

    public function execute(): array
    {
        $this->executes++;

        return $this->forceReturn;
    }

    /**
     * @param  array<int, PortScanResultDto>  $forceReturn
     */
    public function forceReturn(array $forceReturn): void
    {
        $this->forceReturn = $forceReturn;
    }

    public function assertExecuted(int $times): void
    {
        PHPUnitAssert::assertGreaterThanOrEqual($times, $this->executes);
    }

    public function assertInputFileIncludesTarget(string $target): void
    {
        $contents = file_get_contents($this->inputFilePath);

        PHPUnitAssert::assertIsString($contents);

        PHPUnitAssert::assertStringContainsString($target, $contents);
    }

    public function assertTimeToLive(int $ttl): void
    {
        PHPUnitAssert::assertContains($ttl, $this->ttls);
    }

    public function assertRetries(int $retries): void
    {
        PHPUnitAssert::assertContains($retries, $this->retries);
    }

    public function assertRate(int $rate): void
    {
        PHPUnitAssert::assertContains($rate, $this->rates);
    }

    public function assertTimeout(int $timeout): void
    {
        PHPUnitAssert::assertContains($timeout, $this->timeouts);
    }

    public function assertAdapter(string $adapter): void
    {
        PHPUnitAssert::assertContains($adapter, $this->adapters);
    }
}

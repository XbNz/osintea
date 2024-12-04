<?php

declare(strict_types=1);

namespace XbNz\Fping;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Webmozart\Assert\Assert;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Ping\DTOs\PingResultDto;

use function Psl\Filesystem\canonicalize;

final class FakeFping implements FpingInterface
{
    private array $targets = [];
    private string $inputFilePath = '';
    private array $sizes = [];
    private array $backoffFactors = [];
    private array $counts = [];
    private array $ttls = [];
    private array $intervals = [];
    private array $dontFragments = [];
    private array $typesOfService = [];
    private array $intervalsPerHost = [];
    private array $retries = [];
    private array $sendRandomDatas = [];
    private array $sourceAddresses = [];
    private array $timeouts = [];
    private int $executes = 0;

    /**
     * @var array<int, PingResultDto>
     */
    private array $forceReturn = [];

    public function binary(string $binaryPath): FpingInterface
    {
        return $this;
    }

    public function target(string $target): FpingInterface
    {
        $this->targets[] = $target;

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
        $this->sizes[] = $bytes;

        return $this;
    }

    public function backoffFactor(float $backoff): FpingInterface
    {
        $this->backoffFactors[] = $backoff;

        return $this;
    }

    public function count(int $count): FpingInterface
    {
        $this->counts[] = $count;

        return $this;
    }

    public function timeToLive(int $ttl): FpingInterface
    {
        $this->ttls[] = $ttl;

        return $this;
    }

    public function interval(int $interval): FpingInterface
    {
        $this->intervals[] = $interval;

        return $this;
    }

    public function resolveAllHostnameIpAddresses(bool $bool = true): FpingInterface
    {
        return $this;
    }

    public function dontFragment(bool $bool = true): FpingInterface
    {
        $this->dontFragments[] = $bool;

        return $this;
    }

    public function typeOfService(string $tos): FpingInterface
    {
        $this->typesOfService[] = $tos;

        return $this;
    }

    public function intervalPerHost(int $interval): FpingInterface
    {
        $this->intervalsPerHost[] = $interval;

        return $this;
    }

    public function retries(int $retries): FpingInterface
    {
        $this->retries[] = $retries;

        return $this;
    }

    public function sendRandomData(bool $bool = true): FpingInterface
    {
        $this->sendRandomDatas[] = $bool;

        return $this;
    }

    public function sourceAddress(string $sourceAddress): FpingInterface
    {
        $this->sourceAddresses[] = $sourceAddress;

        return $this;
    }

    public function timeout(int $timeout): FpingInterface
    {
        $this->timeouts[] = $timeout;

        return $this;
    }

    /**
     * @return array<int, PingResultDto>
     */
    public function execute(): array
    {
        $this->executes++;

        return $this->forceReturn;
    }

    /**
     * @param  array<int, PingResultDto>  $forceReturn
     */
    public function forceReturn(array $forceReturn): void
    {
        $this->forceReturn = $forceReturn;
    }

    public function assertCount(int $expectedCount): void
    {
        PHPUnitAssert::assertSame($expectedCount, array_sum($this->counts));
    }

    public function assertIntervalPerHost(int $expectedInterval): void
    {
        PHPUnitAssert::assertContains($expectedInterval, $this->intervalsPerHost);
    }

    public function assertExecuted(): void
    {
        PHPUnitAssert::assertGreaterThanOrEqual(1, $this->executes);
    }

    public function assertInputFileIncludesTarget(string $target): void
    {
        $contents = file_get_contents($this->inputFilePath);

        PHPUnitAssert::assertIsString($contents);

        PHPUnitAssert::assertStringContainsString($target, $contents);
    }

    public function assertTarget(string $target): void
    {
        PHPUnitAssert::assertContains($target, $this->targets);
    }

    public function assertSize(int $size): void
    {
        PHPUnitAssert::assertContains($size, $this->sizes);
    }

    public function assertBackoffFactor(float $backoffFactor): void
    {
        PHPUnitAssert::assertContains($backoffFactor, $this->backoffFactors);
    }

    public function assertTimeToLive(int $ttl): void
    {
        PHPUnitAssert::assertContains($ttl, $this->ttls);
    }

    public function assertInterval(int $interval): void
    {
        PHPUnitAssert::assertContains($interval, $this->intervals);
    }

    public function assertDontFragment(bool $dontFragment): void
    {
        PHPUnitAssert::assertContains($dontFragment, $this->dontFragments);
    }

    public function assertTypeOfService(string $typeOfService): void
    {
        PHPUnitAssert::assertContains($typeOfService, $this->typesOfService);
    }

    public function assertRetries(int $retries): void
    {
        PHPUnitAssert::assertContains($retries, $this->retries);
    }

    public function assertSendRandomData(bool $sendRandomData): void
    {
        PHPUnitAssert::assertContains($sendRandomData, $this->sendRandomDatas);
    }

    public function assertSourceAddress(string $sourceAddress): void
    {
        PHPUnitAssert::assertContains($sourceAddress, $this->sourceAddresses);
    }

    public function assertTimeout(int $timeout): void
    {
        PHPUnitAssert::assertContains($timeout, $this->timeouts);
    }

    public function assertNotExecuted(): void
    {
        PHPUnitAssert::assertSame(0, $this->executes);
    }
}

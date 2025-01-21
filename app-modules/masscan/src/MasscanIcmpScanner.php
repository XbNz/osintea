<?php

declare(strict_types=1);

namespace XbNz\Masscan;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Webmozart\Assert\Assert;
use Psl\Type;

use XbNz\Masscan\Contracts\MasscanIcmpScannerInterface;

use XbNz\Masscan\Mappers\PortScanResultMapper;
use XbNz\Shared\BinFinder;

use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;

use function Psl\Filesystem\canonicalize;

final class MasscanIcmpScanner implements MasscanIcmpScannerInterface
{
    public private(set) string $inputFilePath;
    public private(set) string $outputFilePath;
    public private(set) int $rate;
    public private(set) int $timeToLive = 55;
    public private(set) int $timeout = 60;
    public private(set) int $retries = 0;
    public private(set) ?string $adapter = null;

    public function __construct(
        private readonly PendingProcess $process,
        private readonly BinFinder $binFinder,
        private readonly Repository $config,
        private readonly Filesystem $filesystem,
    ) {
        $this->generateOutputFilePath();
    }

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
        Assert::fileExists($outputFile, 'The output file could not be found at the given path');

        $canonicalized = canonicalize($outputFile);

        Assert::string($canonicalized);

        $this->outputFilePath = $canonicalized;

        return $this;
    }

    public function rate(int $rate): self
    {
        Assert::greaterThan($rate, 0, 'The rate must be greater than 0');

        $this->rate = $rate;

        return $this;
    }

    public function adapter(string $adapter): self
    {
        Assert::string($adapter, 'The adapter must be a string');

        $this->adapter = $adapter;

        return $this;
    }

    public function timeToLive(int $ttl): self
    {
        Assert::positiveInteger($ttl, 'The time to live must be a positive integer');

        $this->timeToLive = $ttl;

        return $this;
    }

    public function timeout(int $timeout): self
    {
        Assert::positiveInteger($timeout, 'The timeout must be a positive integer');

        $this->timeout = $timeout;

        return $this;
    }

    public function execute(): array
    {
        $this->pendingProcess()
            ->run()
            ->throw();

        $json = \Safe\json_decode($this->filesystem->get($this->outputFilePath), true);

        return array_map(
            fn (array $scan) => PortScanResultMapper::map($scan, ProtocolType::ICMP),
            $json
        );
    }

    public function retries(int $retries): MasscanIcmpScannerInterface
    {
        $this->retries = $retries;

        return $this;
    }

    private function pendingProcess(): PendingProcess
    {
        $masscanPrefix = $this->config->get('masscan.binaries.prefix');
        $masscanBinaryDirectory = $this->config->get('masscan.binaries.directory');

        Assert::string($masscanPrefix);
        Assert::string($masscanBinaryDirectory);

        $masscanBinary = $this->binFinder->prefix($masscanPrefix)
            ->inDirectory($masscanBinaryDirectory)
            ->find();

        $command = [
            $masscanBinary,
            '--ping',
            '-iL',
            $this->inputFilePath,
            '--rate',
            $this->rate,
            '--ttl',
            $this->timeToLive,
            '--retries',
            $this->retries,
            '--output-filename',
            $this->outputFilePath,
            '--output-format',
            'json',
            '--open',
            '--wait',
            '0',
        ];

        if ($this->adapter !== null) {
            $command[] = '--adapter';
            $command[] = $this->adapter;
        }

        return $this->process
            ->timeout($this->timeout)
            ->command($command);
    }

    private function generateOutputFilePath(): void
    {
        $this->outputFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('masscan_output_'.Str::random(10).'.txt');
    }

    public function __destruct()
    {
        $this->filesystem->delete($this->outputFilePath);
    }
}

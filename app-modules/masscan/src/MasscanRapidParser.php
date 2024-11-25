<?php

declare(strict_types=1);

namespace XbNz\Masscan;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Webmozart\Assert\Assert;
use XbNz\Shared\BinFinder;
use XbNz\Shared\Contracts\RapidParserInterface;
use XbNz\Shared\Exceptions\IpParserException;

use function Psl\Filesystem\canonicalize;

final class MasscanRapidParser implements RapidParserInterface
{
    private string $inputFilePath;

    private string $outputFilePath;

    private int $timeout = 5;

    public function __construct(
        private readonly Repository $config,
        private readonly BinFinder $binFinder,
        private readonly PendingProcess $process,
        private readonly FilesystemManager $filesystem,
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

    public function timeout(int $seconds): self
    {
        Assert::greaterThan($seconds, 0, 'The timeout must be greater than 0');

        $this->timeout = $seconds;

        return $this;
    }

    public function parse(): void
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
            '-iL',
            $this->inputFilePath,
            '-sL',
        ];

        try {
            $this->process
                ->timeout($this->timeout)
                ->command(implode(' ', $command)." > {$this->outputFilePath}")
                ->run()
                ->throw();
        } catch (ProcessFailedException $exception) {
            throw new IpParserException($exception->getMessage());
        }
    }

    private function generateOutputFilePath(): void
    {
        $this->outputFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('masscan_output_'.Str::random(10).'.txt');
    }
}

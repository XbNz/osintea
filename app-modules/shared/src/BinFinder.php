<?php

declare(strict_types=1);

namespace XbNz\Shared;

use Illuminate\Process\InvokedProcessPool;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Arr;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;
use XbNz\Shared\Exception\BinaryNotExecutableException;
use XbNz\Shared\Exception\BinaryNotFoundException;

use function Psl\Filesystem\canonicalize;

final class BinFinder
{
    private string $prefix = '';

    private string $directory;

    public function __construct(
        private readonly PendingProcess $process,
        private readonly Finder $finder,
    ) {}

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function inDirectory(string $directory): self
    {
        Assert::directory($directory);
        Assert::string($canonicalized = canonicalize($directory));

        $this->directory = $canonicalized;

        return $this;
    }

    public function find(): string
    {
        [$os, $arch] = (new InvokedProcessPool([
            $this->process->command(['uname', '-s'])->start(),
            $this->process->command(['uname', '-m'])->start(),
        ]))->wait();

        $strippedOs = mb_strtolower(trim($os->output()));
        $strippedArch = mb_strtolower(trim($arch->output()));

        $targetBinaries = $this->finder
            ->files()
            ->in($this->directory)
            ->name([$this->prefix.'_'.$strippedOs.'_'.$strippedArch]);

        if ($targetBinaries->hasResults() === false) {
            throw BinaryNotFoundException::for(
                $this->prefix,
                $this->directory,
                $strippedOs,
                $strippedArch
            );
        }

        if ($targetBinaries->count() > 1) {
            throw new RuntimeException('Multiple binaries found with the same prefix, OS and architecture');
        }

        $targetBinary = Arr::first(iterator_to_array($targetBinaries));

        Assert::isInstanceOf($targetBinary, SplFileInfo::class);

        if ($targetBinary->isExecutable() === false) {
            throw BinaryNotExecutableException::for(
                $this->prefix,
                $this->directory,
                $strippedOs,
                $strippedArch
            );
        }

        return $targetBinary->getRealPath();
    }
}

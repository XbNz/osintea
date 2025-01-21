<?php

declare(strict_types=1);

namespace XbNz\Port\Actions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Masscan\Contracts\MasscanIcmpScannerInterface;

final class BulkIcmpScanAction
{
    private string $inputFile;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly MasscanIcmpScannerInterface $masscanIcmpScanner,
        private readonly CreatePortAction $createPortAction,
    ) {
        $this->inputFile = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input_'.Str::random(5).'.txt');

        touch($this->inputFile);
    }

    public function handle(): void {}

    public function __destruct()
    {
        $this->filesystem->delete($this->inputFile);
    }
}

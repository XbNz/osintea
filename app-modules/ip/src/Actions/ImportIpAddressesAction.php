<?php

declare(strict_types=1);

namespace XbNz\Ip\Actions;

use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;
use Webmozart\Assert\Assert;

use function Psl\Filesystem\canonicalize;

final class ImportIpAddressesAction
{
    public function __construct(
        private readonly DatabaseManager $database,
        private readonly Filesystem $fileSystem,
    ) {}

    public function handle(string $filePath): void
    {
        $realPath = canonicalize($filePath);

        Assert::fileExists($realPath);

        $this->fileSystem
            ->lines($realPath)
            ->reject(fn (string $line) => empty($line))
            ->each(Assert::ip(...))
            ->map(fn (string $ip) => [
                'ip' => $ip,
            ])
            ->chunk(10_000)
            ->each($this->insertChunk(...));
    }

    private function insertChunk(LazyCollection $chunk): void
    {
        $this->database
            ->table('ip_addresses')
            ->insertOrIgnore($chunk->toArray());
    }
}

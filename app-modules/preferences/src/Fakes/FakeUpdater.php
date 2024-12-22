<?php

declare(strict_types=1);

namespace XbNz\Preferences\Fakes;

use PHPUnit\Framework\Assert as PHPUnit;
use XbNz\Shared\Contracts\UpdaterInterface;
use XbNz\Shared\Enums\UpdatableDatabase;

final class FakeUpdater implements UpdaterInterface
{
    public int $updates = 0;

    public function update(): void
    {
        $this->updates++;
    }

    public function supports(UpdatableDatabase $database): bool
    {
        return $database === UpdatableDatabase::Fake;
    }

    public function assertUpdated(int $times): void
    {
        PHPUnit::assertEquals($times, $this->updates);
    }
}

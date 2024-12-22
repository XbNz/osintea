<?php

declare(strict_types=1);

namespace XbNz\Shared\Contracts;

use XbNz\Shared\Enums\UpdatableDatabase;

interface UpdaterInterface
{
    public function update(): void;

    public function supports(UpdatableDatabase $database): bool;
}

<?php

declare(strict_types=1);

namespace XbNz\Preferences\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use XbNz\Shared\Contracts\UpdaterInterface;
use XbNz\Shared\Enums\UpdatableDatabase;

final class DatabaseUpdaterJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly UpdatableDatabase $database
    ) {}

    public function handle(Container $container): void
    {
        $updater = Collection::make(iterator_to_array($container->tagged('database-updaters')))
            ->sole(fn (UpdaterInterface $updater) => $updater->supports($this->database));

        $updater->update();
    }
}

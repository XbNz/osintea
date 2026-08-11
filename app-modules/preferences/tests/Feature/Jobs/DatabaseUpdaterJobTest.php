<?php

declare(strict_types=1);

namespace XbNz\Preferences\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Preferences\Fakes\FakeUpdater;
use XbNz\Preferences\Jobs\DatabaseUpdaterJob;
use XbNz\Shared\Enums\UpdatableDatabase;
use XbNz\Shared\Events\DatabaseUpdateStatusEvent;

final class DatabaseUpdaterJobTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_the_correct_database(): void
    {
        // Arrange
        Event::fake([DatabaseUpdateStatusEvent::class]);

        $this->app->singleton(
            FakeUpdater::class,
            fn () => new FakeUpdater,
        );

        $this->app->tag([
            FakeUpdater::class,
        ], 'database-updaters');

        $job = new DatabaseUpdaterJob(UpdatableDatabase::Fake);

        // Act
        $this->app->make(Dispatcher::class)->dispatch($job);

        // Assert
        $this->app->make(FakeUpdater::class)->assertUpdated(1);
        Event::assertDispatched(
            DatabaseUpdateStatusEvent::class,
            fn (DatabaseUpdateStatusEvent $event) => $event->database === UpdatableDatabase::Fake
                && $event->status === 'running',
        );
        Event::assertDispatched(
            DatabaseUpdateStatusEvent::class,
            fn (DatabaseUpdateStatusEvent $event) => $event->database === UpdatableDatabase::Fake
                && $event->status === 'completed',
        );
    }
}

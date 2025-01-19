<?php

declare(strict_types=1);

namespace XbNz\Port\Tests\Features\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Port\Actions\DeletePortAction;
use XbNz\Port\Events\PortDeletedEvent;
use XbNz\Port\Models\Port;

final class DeletePortActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_a_port(): void
    {
        // Arrange
        $port = Port::factory()->create();
        $action = $this->app->make(DeletePortAction::class);

        // Act
        $this->assertDatabaseCount(Port::class, 1);
        $action->handle($port->getData());

        // Assert
        $this->assertDatabaseMissing(Port::class, ['id' => $port->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_events(): void
    {
        // Arrange
        Event::fake();
        $port = Port::factory()->create();
        $action = $this->app->make(DeletePortAction::class);

        // Act
        $action->handle($port->getData());

        // Assert
        Event::assertDispatchedTimes(PortDeletedEvent::class, 1);
        Event::assertDispatched(PortDeletedEvent::class, fn (PortDeletedEvent $event) => $event->record->id === $port->id);
    }
}

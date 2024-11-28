<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Ping\Actions\DeletePingSequenceAction;
use XbNz\Ping\Events\PingSequenceDeletedEvent;
use XbNz\Ping\Models\PingSequence;

final class DeletePingSequenceActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_a_ping_sequence(): void
    {
        // Arrange
        $pingSequence = PingSequence::factory()->create();
        $action = $this->app->make(DeletePingSequenceAction::class);

        // Act
        $this->assertDatabaseCount(PingSequence::class, 1);
        $action->handle($pingSequence->getData());

        // Assert
        $this->assertDatabaseMissing(PingSequence::class, ['id' => $pingSequence->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_events(): void
    {
        // Arrange
        Event::fake();
        $dto = PingSequence::factory()->create()->getData();
        $action = $this->app->make(DeletePingSequenceAction::class);

        // Act
        $action->handle($dto);

        // Assert
        Event::assertDispatchedTimes(PingSequenceDeletedEvent::class, 1);
        Event::assertDispatched(PingSequenceDeletedEvent::class, fn (PingSequenceDeletedEvent $event) => $event->record->id === $dto->id);
    }
}

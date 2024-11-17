<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Actions\CreatePingSequenceAction;
use XbNz\Ping\DTOs\CreatePingSequenceDto;
use XbNz\Ping\DTOs\PingSequenceDto;
use XbNz\Ping\Events\PingSequenceInsertedEvent;
use XbNz\Ping\Models\PingSequence;
use XbNz\Ping\ValueObjects\Sequence;

final class CreatePingSequenceActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_ping_sequence_from_a_dto(): void
    {
        // Arrange
        $dto = CreatePingSequenceDto::validateAndCreate([
            'ip_address_dto' => IpAddress::factory()->create()->fresh()->getData(),
            'sequence' => new Sequence(1, false, 1.2),
            'created_at' => CarbonImmutable::now(),
        ]);

        $action = $this->app->make(CreatePingSequenceAction::class);

        // Act
        $pingSequence = $action->handle($dto);

        // Assert
        $this->assertDatabaseCount(PingSequence::class, 1);
        $this->assertInstanceOf(PingSequenceDto::class, $pingSequence);
        $this->assertDatabaseHas(PingSequence::class, [
            'ip_address_id' => $dto->ip_address_dto->id,
            'round_trip_time' => $dto->sequence->roundTripTime,
            'loss' => $dto->sequence->lost,
            'created_at' => $dto->created_at->format('U.u'),
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_events(): void
    {
        // Arrange
        Event::fake();
        $dto = CreatePingSequenceDto::validateAndCreate([
            'ip_address_dto' => IpAddress::factory()->create()->fresh()->getData(),
            'sequence' => new Sequence(1, false, 1.2),
            'created_at' => CarbonImmutable::now(),
        ]);

        $action = $this->app->make(CreatePingSequenceAction::class);

        // Act
        $action->handle($dto);

        // Assert
        Event::assertDispatchedTimes(PingSequenceInsertedEvent::class, 1);
        Event::assertDispatched(PingSequenceInsertedEvent::class, fn (PingSequenceInsertedEvent $event) => $event->record->round_trip_time === $dto->sequence->roundTripTime);
    }
}

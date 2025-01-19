<?php

declare(strict_types=1);

namespace XbNz\Port\Tests\Features\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Ip\Models\IpAddress;
use XbNz\Port\Actions\CreatePortAction;
use XbNz\Port\DTOs\CreatePortDto;
use XbNz\Port\DTOs\PortDto;
use XbNz\Port\Events\PortInsertedEvent;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;
use XbNz\Shared\ValueObjects\Port;
use XbNz\Port\Models\Port as PortModel;

final class CreatePortActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_port_from_a_dto(): void
    {
        // Arrange
        $dto = CreatePortDto::validateAndCreate([
            'ip_address_dto' => IpAddress::factory()->create()->fresh()->getData(),
            'port' => new Port(80, ProtocolType::TCP),
            'state' => PortState::Open,
            'created_at' => CarbonImmutable::now(),
        ]);

        $action = $this->app->make(CreatePortAction::class);

        // Act
        $port = $action->handle($dto);

        // Assert
        $this->assertDatabaseCount(PortModel::class, 1);
        $this->assertInstanceOf(PortDto::class, $port);
        $this->assertDatabaseHas(PortModel::class, [
            'ip_address_id' => $dto->ip_address_dto->id,
            'port' => $dto->port->port,
            'protocol' => $dto->port->protocol->value,
            'state' => $dto->state->value,
            'created_at' => $dto->created_at->format('U.u'),
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_events(): void
    {
        // Arrange
        Event::fake();
        $dto = CreatePortDto::validateAndCreate([
            'ip_address_dto' => IpAddress::factory()->create()->fresh()->getData(),
            'port' => new Port(80, ProtocolType::TCP),
            'state' => PortState::Open,
            'created_at' => CarbonImmutable::now(),
        ]);

        $action = $this->app->make(CreatePortAction::class);

        // Act
        $action->handle($dto);

        // Assert
        Event::assertDispatchedTimes(PortInsertedEvent::class, 1);
        Event::assertDispatched(PortInsertedEvent::class, fn (PortInsertedEvent $event) => $event->record->port->port === $dto->port->port);
    }
}

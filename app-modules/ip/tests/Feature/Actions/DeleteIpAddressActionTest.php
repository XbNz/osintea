<?php

declare(strict_types=1);

namespace XbNz\Ip\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Ip\Actions\DeleteIpAddressAction;
use XbNz\Ip\Events\IpAddressDeletedEvent;
use XbNz\Ip\Models\IpAddress;

final class DeleteIpAddressActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_an_ip_address(): void
    {
        // Arrange
        $ipAddress = IpAddress::factory()->create()->fresh()->getData();
        $action = $this->app->make(DeleteIpAddressAction::class);

        // Act
        $this->assertDatabaseCount(IpAddress::class, 1);
        $action->handle($ipAddress);

        // Assert
        $this->assertDatabaseMissing(IpAddress::class, ['id' => $ipAddress->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_events(): void
    {
        // Arrange
        Event::fake();
        $dto = IpAddress::factory()->create()->fresh()->getData();
        $action = $this->app->make(DeleteIpAddressAction::class);

        // Act
        $action->handle($dto);

        // Assert
        Event::assertDispatchedTimes(IpAddressDeletedEvent::class, 1);
        Event::assertDispatched(IpAddressDeletedEvent::class, fn (IpAddressDeletedEvent $event) => $event->record->id === $dto->id);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Actions;

use Tests\TestCase;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Fping\FakeFping;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Actions\FulfillSequenceAction;
use XbNz\Ping\DTOs\PingResultDTO;
use XbNz\Ping\Models\PingSequence;
use XbNz\Ping\ValueObjects\Sequence;

final class FulfillSequenceActionTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_takes_an_ip_address_dto_and_hits_fping_with_the_given_ip(): void
    {
        // Arrange
        $this->swap(FpingInterface::class, $fake = $this->app->make(FakeFping::class));

        $action = $this->app->make(FulfillSequenceAction::class);

        $ipAddress = IpAddress::factory()->create()->fresh()->getData();

        $fake->forceReturn([
            new PingResultDTO(
                $ipAddress->ip,
                $ipAddress->type,
                [
                    new Sequence(1, false, 1.11),
                ]
            )
        ]);

        // Act
        $action->handle($ipAddress);

        // Assert
        $fake->assertTarget($ipAddress->ip);
        $this->assertDatabaseHas(PingSequence::class, [
            'ip_address_id' => $ipAddress->id,
            'round_trip_time' => 1.11,
            'loss' => false,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function lost_packets_are_handled_properly(): void
    {
        // Arrange
        $this->swap(FpingInterface::class, $fake = $this->app->make(FakeFping::class));

        $action = $this->app->make(FulfillSequenceAction::class);

        $ipAddress = IpAddress::factory()->create()->fresh()->getData();

        $fake->forceReturn([
            new PingResultDTO(
                $ipAddress->ip,
                $ipAddress->type,
                [
                    new Sequence(1, true, null),
                ]
            )
        ]);

        // Act
        $action->handle($ipAddress);

        // Assert
        $fake->assertTarget($ipAddress->ip);
        $this->assertDatabaseHas(PingSequence::class, [
            'ip_address_id' => $ipAddress->id,
            'round_trip_time' => null,
            'loss' => true,
        ]);
    }
}

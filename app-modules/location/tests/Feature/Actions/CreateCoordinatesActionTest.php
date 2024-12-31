<?php

declare(strict_types=1);

namespace XbNz\Location\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Ip\Models\IpAddress;
use XbNz\Location\Actions\CreateCoordinatesAction;
use XbNz\Location\DTOs\CreateCoordinatesDto;
use XbNz\Location\Models\Coordinates as CoordinatesModel;
use XbNz\Shared\ValueObjects\Coordinates;

final class CreateCoordinatesActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_create_a_coordinates_record_from_a_dto(): void
    {
        // Arrange
        $dto = new CreateCoordinatesDto(
            IpAddress::factory()->create()->refresh()->getData(),
            new Coordinates(2.0, 1.0),
        );

        $action = $this->app->make(CreateCoordinatesAction::class);

        // Act
        $result = $action->handle($dto);

        // Assert
        $this->assertDatabaseHas(CoordinatesModel::class, [
            'ip_address_id' => $dto->ipAddressDto->id,
        ]);

        $this->assertEquals(2.0, $result->coordinates->latitude);
        $this->assertEquals(1.0, $result->coordinates->longitude);
    }
}

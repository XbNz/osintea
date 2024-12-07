<?php

declare(strict_types=1);

namespace XbNz\Asn\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Asn\Actions\CreateAsnAction;
use XbNz\Asn\DTOs\CreateAsnDto;
use XbNz\Asn\Model\Asn as AsnModel;
use XbNz\Asn\ValueObject\Asn;
use XbNz\Ip\Models\IpAddress;

final class CreateAsnActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_an_asn_record_from_a_data_object(): void
    {
        // Arrange
        $action = $this->app->make(CreateAsnAction::class);
        $dto = new CreateAsnDto(IpAddress::factory()->create()->refresh()->getData(), new Asn('Test Organization', 12345));

        // Act
        $result = $action->handle($dto);

        // Assert
        $this->assertDatabaseHas(AsnModel::class, [
            'ip_address_id' => $dto->ip->id,
            'organization' => 'Test Organization',
            'as_number' => 12345,
        ]);
    }
}

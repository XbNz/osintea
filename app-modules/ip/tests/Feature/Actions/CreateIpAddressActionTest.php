<?php

declare(strict_types=1);

namespace Feature\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Ip\Actions\CreateIpAddressAction;
use XbNz\Ip\Models\IpAddress;
use XbNz\Shared\ValueObjects\IpType;

final class CreateIpAddressActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_create_an_ip_address_record(): void
    {
        // Arrange
        $action = $this->app->make(CreateIpAddressAction::class);

        // Act
        $result = $action->handle('1.1.1.1');

        // Assert
        $this->assertDatabaseHas(IpAddress::class, [
            'ip' => '1.1.1.1',
            'type' => IpType::IPv4,
        ]);

        $result->created_at->isSameSecond(CarbonImmutable::now());
    }
}

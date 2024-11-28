<?php

declare(strict_types=1);

namespace XbNz\Ip\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Ip\Jobs\DeleteIpAddressJob;
use XbNz\Ip\Models\IpAddress;

final class DeleteIpAddressJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_an_ip_address(): void
    {
        // Arrange
        $ipAddress = IpAddress::factory()->create()->fresh();

        // Act
        $this->assertDatabaseCount(IpAddress::class, 1);
        $this->app->make(Dispatcher::class)->dispatch(new DeleteIpAddressJob($ipAddress->getData()));

        // Assert
        $this->assertDatabaseMissing(IpAddress::class, ['id' => $ipAddress->id]);
    }
}

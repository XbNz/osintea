<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Fping\FakeFping;
use XbNz\Fping\Models\FpingPreferences;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Actions\BulkPingAction;
use XbNz\Ping\Models\PingSequence;

final class BulkPingActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_receives_a_collection_of_ip_address_dtos_and_returns_an_array_of_results(): void
    {
        // Arrange
        FpingPreferences::query()->create([
            'name' => 'default',
            'size' => 56,
            'count' => 1,
            'interval' => 1,
            'timeout' => 1000,
            'retries' => 0,
            'ttl' => 64,
            'backoff' => 1.5,
            'interval_per_target' => 0,
            'type_of_service' => '0x00',
            'dont_fragment' => false,
            'send_random_data' => false,
            'enabled' => true,
        ]);

        IpAddress::query()->create(['ip' => '1.1.1.1']);
        IpAddress::query()->create(['ip' => '8.8.8.8']);

        $action = $this->app->make(BulkPingAction::class);

        // Act
        $this->assertDatabaseCount(PingSequence::class, 0);
        $action->handle(IpAddressDto::collect(IpAddress::query()->get()));

        // Assert
        $this->assertDatabaseCount(PingSequence::class, 2);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function if_the_ip_is_deleted_while_busy_pinging_it_discards_the_result_without_throwing_any_error(): void
    {
        // Arrange

        // Act

        // Assert
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_destroys_the_temporary_input_file_after_destruction(): void
    {
        // Arrange

        // Act

        // Assert
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function preferences_are_relayed_to_fping_correctly(): void
    {
        // Arrange
        $this->swap(FpingInterface::class, $fake = $this->app->make(FakeFping::class));

        // Act

        // Assert
    }
}

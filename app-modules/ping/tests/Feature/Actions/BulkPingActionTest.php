<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Actions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Fping\FakeFping;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Actions\BulkPingAction;
use XbNz\Ping\Models\PingSequence;
use XbNz\Preferences\Models\FpingPreferences;

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
            'timeout' => 1,
            'retries' => 0,
            'ttl' => 64,
            'backoff' => 1.5,
            'interval_per_target' => 0,
            'type_of_service' => '0x00',
            'dont_fragment' => false,
            'send_random_data' => false,
            'enabled' => true,
        ]);

        $ipAddressA = IpAddress::query()->create(['ip' => '1.1.1.1']);
        $ipAddressB = IpAddress::query()->create(['ip' => '8.8.8.8']);

        $action = $this->app->make(BulkPingAction::class);

        // Act
        $this->assertDatabaseCount(PingSequence::class, 0);
        $action->handle(IpAddressDto::collect(IpAddress::query()->get()));

        // Assert
        $this->assertDatabaseCount(PingSequence::class, 2);
        $this->assertDatabaseHas(PingSequence::class, [
            'ip_address_id' => $ipAddressA->id,
        ]);
        $this->assertDatabaseHas(PingSequence::class, [
            'ip_address_id' => $ipAddressB->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function if_the_ip_is_deleted_while_busy_pinging_it_discards_the_result_without_throwing_any_error(): void
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

        $ipAddressA = IpAddress::query()->create(['ip' => '1.1.1.1'])->refresh()->getData();
        $ipAddressB = IpAddress::query()->create(['ip' => '8.8.8.8'])->refresh()->getData();

        $action = $this->app->make(BulkPingAction::class);

        // Act
        IpAddress::query()->where('ip', '8.8.8.8')->delete();
        $action->handle(IpAddressDto::collect(IpAddress::query()->get()));

        // Assert
        $this->assertDatabaseCount(PingSequence::class, 1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_destroys_the_temporary_input_file_after_destruction(): void
    {
        // Arrange
        $filesystemMock = $this->partialMock(Filesystem::class);

        $filesystemMock->shouldReceive('delete')->twice();

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

        $action = $this->app->make(BulkPingAction::class);

        // Act
        $action->handle(Collection::make([
            IpAddress::query()->create(['ip' => '1.1.1.1'])->refresh()->getData(),
        ]));

        unset($action);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function preferences_are_relayed_to_fping_correctly(): void
    {
        // Arrange
        $this->swap(FpingInterface::class, $fake = $this->app->make(FakeFping::class));

        FpingPreferences::query()->create([
            'name' => 'default',
            'size' => 56,
            'count' => 1,
            'interval' => 1,
            'timeout' => 1000,
            'retries' => 0,
            'ttl' => 64,
            'backoff' => 1.5,
            'interval_per_target' => 1,
            'type_of_service' => '0x00',
            'dont_fragment' => false,
            'send_random_data' => false,
            'enabled' => true,
        ]);

        FpingPreferences::query()->create([
            'name' => 'should_not_be_used',
            'size' => 57,
            'count' => 2,
            'interval' => 2,
            'timeout' => 2000,
            'retries' => 1,
            'ttl' => 65,
            'backoff' => 2.5,
            'interval_per_target' => 0,
            'type_of_service' => '0x01',
            'dont_fragment' => true,
            'send_random_data' => true,
            'enabled' => false,
        ]);

        IpAddress::query()->create(['ip' => '1.1.1.1'])->refresh()->getData();

        $action = $this->app->make(BulkPingAction::class);

        // Act
        $action->handle(IpAddressDto::collect(IpAddress::query()->get()));

        // Assert
        $fake->assertSize(56);
        $fake->assertCount(1);
        $fake->assertInterval(1);
        $fake->assertTimeout(1000);
        $fake->assertRetries(0);
        $fake->assertTimeToLive(64);
        $fake->assertBackoffFactor(1.5);
        $fake->assertIntervalPerHost(1);
        $fake->assertTypeOfService('0x00');
        $fake->assertSendRandomData(false);
    }
}

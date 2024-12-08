<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Fping\Models\FpingPreferences;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Events\BulkPingCompleted;
use XbNz\Ping\Jobs\BulkPingJob;
use XbNz\Ping\Models\PingSequence;

final class BulkPingJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_a_collection_of_ips_and_pings_them(): void
    {
        // Arrange
        Event::fake([BulkPingCompleted::class]);
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

        $ipAddresses = IpAddress::factory()->count(2)->create()
            ->map(fn (IpAddress $ipAddress) => $ipAddress->refresh()->getData());

        // Act
        $this->assertDatabaseCount(PingSequence::class, 0);
        $this->app->make(Dispatcher::class)->dispatch(new BulkPingJob($ipAddresses));

        // Assert
        $this->assertDatabaseCount(PingSequence::class, 2);
        Event::assertDispatched(BulkPingCompleted::class, fn (BulkPingCompleted $event) => $event->completedCount === 2);
    }
}

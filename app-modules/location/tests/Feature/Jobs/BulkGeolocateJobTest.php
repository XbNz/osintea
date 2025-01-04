<?php

declare(strict_types=1);

namespace XbNz\Location\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Ip\Models\IpAddress;
use XbNz\Location\Enums\Provider;
use XbNz\Location\Events\BulkGeolocationCompleted;
use XbNz\Location\Fakes\IpToCoordinatesFake;
use XbNz\Location\Jobs\BulkGeolocateJob;

final class BulkGeolocateJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_the_requested_provider_fetched_from_the_container_tags(): void
    {
        // Arrange
        Event::fake([BulkGeolocationCompleted::class]);
        $this->app->singleton(IpToCoordinatesFake::class, fn () => new IpToCoordinatesFake());
        $this->app->tag([IpToCoordinatesFake::class], 'ip-to-coordinates');
        $fake = $this->app->make(IpToCoordinatesFake::class);

        $ipAddresses = IpAddress::factory()->count(2)->create()
            ->map(fn (IpAddress $ipAddress) => $ipAddress->refresh()->getData());

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new BulkGeolocateJob($ipAddresses, Provider::Fake));

        // Assert
        $fake->assertProvider(Provider::Fake);
        $fake->assertExecuteCount(2);
        Event::assertDispatched(BulkGeolocationCompleted::class, fn (BulkGeolocationCompleted $event) => $event->completedCount === 2);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Asn\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Asn\Enums\Provider;
use XbNz\Asn\Events\BulkAsnLookupCompleted;
use XbNz\Asn\Fakes\IpToAsnFake;
use XbNz\Asn\Jobs\BulkAsnLookupJob;
use XbNz\Asn\Model\Asn;
use XbNz\Ip\Models\IpAddress;

final class BulkAsnLookupJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_a_collection_of_ips_and_looks_up_asns(): void
    {
        // Arrange
        Event::fake([BulkAsnLookupCompleted::class]);
        $ipAddresses = IpAddress::factory()->count(2)
            ->sequence(
                ['ip' => '1.1.1.1'],
                ['ip' => '8.8.8.8'],
            )
            ->create()
            ->map(fn (IpAddress $ipAddress) => $ipAddress->refresh()->getData());

        // Act
        $this->assertDatabaseCount(Asn::class, 0);
        $this->app->make(Dispatcher::class)->dispatch(new BulkAsnLookupJob($ipAddresses, Provider::RouteViews));

        // Assert
        $this->assertDatabaseCount(Asn::class, 2);
        $this->assertDatabaseHas(Asn::class, [
            'ip_address_id' => $ipAddresses->first()->id,
        ]);
        $this->assertDatabaseHas(Asn::class, [
            'ip_address_id' => $ipAddresses->last()->id,
        ]);
        Event::assertDispatched(BulkAsnLookupCompleted::class, fn (BulkAsnLookupCompleted $event) => $event->completedCount === 2);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_the_requested_provider_fetched_from_the_container_tags(): void
    {
        // Arrange
        $this->app->singleton(IpToAsnFake::class, fn () => new IpToAsnFake());
        $this->app->tag([IpToAsnFake::class], 'ip-to-asn');
        $fake = $this->app->make(IpToAsnFake::class);

        $ipAddresses = IpAddress::factory()->count(2)->create()
            ->map(fn (IpAddress $ipAddress) => $ipAddress->refresh()->getData());

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new BulkAsnLookupJob($ipAddresses, Provider::Fake));

        // Assert
        $fake->assertProvider(Provider::Fake);
        $fake->assertExecuteCount(2);
    }
}

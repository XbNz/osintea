<?php

declare(strict_types=1);

namespace XbNz\Ip\Tests\Feature\Subscribers\IpAddress;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use XbNz\Ip\Events\Intentions\DeleteIpAddressIntention;
use XbNz\Ip\Jobs\DeleteIpAddressJob;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ip\Subscribers\IpAddressSubscriber;

final class OnDeleteIpAddressIntentionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_the_expected_jobs(): void
    {
        // Arrange
        Queue::fake();

        $event = new DeleteIpAddressIntention(
            IpAddress::factory()->create()->fresh()->getData(),
        );

        $subscriber = $this->app->make(IpAddressSubscriber::class);

        // Act
        $subscriber->onDeleteIpAddressIntention($event);

        // Assert
        Queue::assertPushed(DeleteIpAddressJob::class, function (DeleteIpAddressJob $job) use ($event) {
            $this->assertEquals($event->record, $job->ipAddress);

            return true;
        });
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Subscribers\PingSequenceSubscriber;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use XbNz\Ping\Events\Intentions\DeletePingSequenceIntention;
use XbNz\Ping\Jobs\DeletePingSequenceJob;
use XbNz\Ping\Models\PingSequence;
use XbNz\Ping\Subscribers\PingSequenceSubscriber;

final class OnDeletePingSequenceIntentionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_the_expected_jobs(): void
    {
        // Arrange
        Queue::fake();

        $event = new DeletePingSequenceIntention(
            PingSequence::factory()->create()->getData(),
        );

        $subscriber = $this->app->make(PingSequenceSubscriber::class);

        // Act
        $subscriber->onDeletePingSequenceIntention($event);

        // Assert
        Queue::assertPushed(DeletePingSequenceJob::class, function (DeletePingSequenceJob $job) use ($event) {
            $this->assertEquals($event->record, $job->pingSequenceDto);

            return true;
        });
    }
}

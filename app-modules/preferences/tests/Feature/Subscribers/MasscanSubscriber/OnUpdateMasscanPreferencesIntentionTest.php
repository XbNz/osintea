<?php

declare(strict_types=1);

namespace Feature\Subscribers\MasscanSubscriber;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;
use XbNz\Preferences\Events\Intentions\UpdateMasscanPreferencesIntention;
use XbNz\Preferences\Jobs\UpdateMasscanPreferencesJob;
use XbNz\Preferences\Subscribers\MasscanSubscriber;

final class OnUpdateMasscanPreferencesIntentionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_the_expected_jobs(): void
    {
        // Arrange
        Queue::fake();

        $event = new UpdateMasscanPreferencesIntention(
            UpdateMasscanPreferencesDto::sampleData(),
        );

        $subscriber = $this->app->make(MasscanSubscriber::class);

        // Act
        $subscriber->onUpdateMasscanPreferencesIntention($event);

        // Assert
        Queue::assertPushed(UpdateMasscanPreferencesJob::class, function (UpdateMasscanPreferencesJob $job) use ($event) {
            $this->assertEquals($event->dto, $job->dto);

            return true;
        });
    }
}

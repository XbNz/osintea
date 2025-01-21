<?php

declare(strict_types=1);

namespace Feature\Subscribers\MasscanSubscriber;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;
use XbNz\Preferences\Events\Intentions\CreateMasscanPreferencesIntention;
use XbNz\Preferences\Jobs\CreateMasscanPreferencesJob;
use XbNz\Preferences\Subscribers\MasscanSubscriber;

final class OnCreateMasscanPreferencesIntentionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_the_expected_jobs(): void
    {
        // Arrange
        Queue::fake();

        $event = new CreateMasscanPreferencesIntention(
            CreateMasscanPreferencesDto::sampleData(),
        );

        $subscriber = $this->app->make(MasscanSubscriber::class);

        // Act
        $subscriber->onCreateMasscanPreferencesIntention($event);

        // Assert
        Queue::assertPushed(CreateMasscanPreferencesJob::class, function (CreateMasscanPreferencesJob $job) use ($event) {
            $this->assertEquals($event->dto, $job->dto);

            return true;
        });
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Subscribers\FpingSubscriber;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;
use XbNz\Fping\Events\Intentions\UpdateFpingPreferencesIntention;
use XbNz\Fping\Jobs\UpdateFpingPreferencesJob;
use XbNz\Fping\Subscribers\FpingSubscriber;

final class OnUpdateFpingPreferencesIntentionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_the_expected_jobs(): void
    {
        // Arrange
        Queue::fake();

        $event = new UpdateFpingPreferencesIntention(
            UpdateFpingPreferencesDto::sampleData(),
        );

        $subscriber = $this->app->make(FpingSubscriber::class);

        // Act
        $subscriber->onUpdateFpingPreferencesIntention($event);

        // Assert
        Queue::assertPushed(UpdateFpingPreferencesJob::class, function (UpdateFpingPreferencesJob $job) use ($event) {
            $this->assertEquals($event->dto, $job->dto);

            return true;
        });
    }
}

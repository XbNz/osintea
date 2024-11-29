<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Subscribers\FpingSubscriber;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use XbNz\Fping\DTOs\CreateFpingPreferencesDto;
use XbNz\Fping\Events\Intentions\CreateFpingPreferencesIntention;
use XbNz\Fping\Jobs\CreateFpingPreferencesJob;
use XbNz\Fping\Subscribers\FpingSubscriber;

final class OnCreateFpingPreferencesIntentionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fires_the_expected_jobs(): void
    {
        // Arrange
        Queue::fake();

        $event = new CreateFpingPreferencesIntention(
            CreateFpingPreferencesDto::sampleData(),
        );

        $subscriber = $this->app->make(FpingSubscriber::class);

        // Act
        $subscriber->onCreateFpingPreferencesIntention($event);

        // Assert
        Queue::assertPushed(CreateFpingPreferencesJob::class, function (CreateFpingPreferencesJob $job) use ($event) {
            $this->assertEquals($event->dto, $job->dto);

            return true;
        });
    }
}

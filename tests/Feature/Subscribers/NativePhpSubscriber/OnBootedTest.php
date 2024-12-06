<?php

declare(strict_types=1);

namespace Tests\Feature\Subscribers\NativePhpSubscriber;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Laravel\Events\App\ApplicationBooted;
use Tests\TestCase;
use XbNz\Fping\Models\FpingPreferences;

final class OnBootedTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_create_a_sensible_default_fping_preferences_record_when_the_application_is_booted_for_the_first_time(): void
    {
        // Act
        $this->app->make(Dispatcher::class)->dispatch(new ApplicationBooted());

        // Assert
        $this->assertDatabaseCount(FpingPreferences::class, 1);
        $this->assertDatabaseHas(FpingPreferences::class, [
            'id' => 1,
            'size' => 56,
            'backoff' => 1.5,
            'count' => 1,
            'ttl' => 64,
            'interval' => 100,
            'interval_per_target' => 500,
            'type_of_service' => '0x00',
            'retries' => 0,
            'timeout' => 500,
            'dont_fragment' => false,
            'send_random_data' => false,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function nothing_happens_if_there_is_already_an_fping_preferences_record(): void
    {
        // Arrange
        FpingPreferences::factory()->create();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new ApplicationBooted());

        // Assert
        $this->assertDatabaseCount(FpingPreferences::class, 1);
    }
}

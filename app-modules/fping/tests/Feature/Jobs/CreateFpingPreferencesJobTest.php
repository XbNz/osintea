<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Fping\DTOs\CreateFpingPreferencesDto;
use XbNz\Fping\Jobs\CreateFpingPreferencesJob;
use XbNz\Fping\Models\FpingPreferences;

final class CreateFpingPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_fping_preferences(): void
    {
        // Arrange
        $this->assertDatabaseCount('fping_preferences', 0);

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new CreateFpingPreferencesJob(
                new CreateFpingPreferencesDto(
                    'test',
                    56,
                    1,
                    5,
                    64,
                    10,
                    1000,
                    '0x00',
                    5,
                    500,
                    false,
                    false,
                )
            )
        );

        // Assert
        $this->assertDatabaseCount(FpingPreferences::class, 1);
    }
}

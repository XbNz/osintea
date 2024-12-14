<?php

declare(strict_types=1);

namespace XbNz\Preferences\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Preferences\DTOs\CreateFpingPreferencesDto;
use XbNz\Preferences\Jobs\CreateFpingPreferencesJob;
use XbNz\Preferences\Models\FpingPreferences;

final class CreateFpingPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_fping_preferences(): void
    {
        // Arrange
        $this->assertDatabaseCount(FpingPreferences::class, 0);

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new CreateFpingPreferencesJob(
                $dto = CreateFpingPreferencesDto::sampleData()
            )
        );

        // Assert
        $this->assertDatabaseCount(FpingPreferences::class, 1);

        $this->assertDatabaseHas(FpingPreferences::class, [
            'name' => $dto->name,
            'size' => $dto->size,
            'backoff' => $dto->backoff,
            'count' => $dto->count,
            'ttl' => $dto->ttl,
            'interval' => $dto->interval,
            'interval_per_target' => $dto->interval_per_target,
            'type_of_service' => $dto->type_of_service,
            'retries' => $dto->retries,
            'timeout' => $dto->timeout,
            'dont_fragment' => $dto->dont_fragment,
            'send_random_data' => $dto->send_random_data,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;
use XbNz\Fping\Jobs\UpdateFpingPreferencesJob;
use XbNz\Fping\Models\FpingPreferences;

final class UpdateFpingPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_fping_preferences(): void
    {
        // Arrange
        $fpingPreferences = FpingPreferences::factory()->create()->fresh()->getData();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new UpdateFpingPreferencesJob(
                $dto = UpdateFpingPreferencesDto::sampleData()
            )
        );

        // Assert
        $this->assertDatabaseHas(FpingPreferences::class, [
            'id' => $fpingPreferences->id,
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
            'enabled' => $dto->enabled,
        ]);
    }
}

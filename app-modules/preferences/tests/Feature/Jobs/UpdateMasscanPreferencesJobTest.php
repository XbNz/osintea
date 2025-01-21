<?php

declare(strict_types=1);

namespace Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;
use XbNz\Preferences\Jobs\UpdateMasscanPreferencesJob;
use XbNz\Preferences\Models\MasscanPreferences;

final class UpdateMasscanPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_masscan_preferences(): void
    {
        // Arrange
        $masscanPreferences = MasscanPreferences::factory()->create()->fresh()->getData();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new UpdateMasscanPreferencesJob(
                $dto = UpdateMasscanPreferencesDto::sampleData()
            )
        );

        // Assert
        $this->assertDatabaseHas(MasscanPreferences::class, [
            'id' => $masscanPreferences->id,
            'name' => $dto->name,
            'ttl' => $dto->ttl,
            'rate' => $dto->rate,
            'adapter' => $dto->adapter,
            'retries' => $dto->retries,
            'enabled' => $dto->enabled,
        ]);
    }
}

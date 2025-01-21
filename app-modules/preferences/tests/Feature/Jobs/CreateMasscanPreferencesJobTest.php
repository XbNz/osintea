<?php

declare(strict_types=1);

namespace Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;
use XbNz\Preferences\Jobs\CreateMasscanPreferencesJob;
use XbNz\Preferences\Models\MasscanPreferences;

final class CreateMasscanPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_masscan_preferences(): void
    {
        // Arrange
        $this->assertDatabaseCount(MasscanPreferences::class, 0);

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new CreateMasscanPreferencesJob(
                $dto = CreateMasscanPreferencesDto::sampleData()
            )
        );

        // Assert
        $this->assertDatabaseCount(MasscanPreferences::class, 1);

        $this->assertDatabaseHas(MasscanPreferences::class, [
            'name' => $dto->name,
            'ttl' => $dto->ttl,
            'rate' => $dto->rate,
            'adapter' => $dto->adapter,
            'retries' => $dto->retries,
        ]);
    }
}
